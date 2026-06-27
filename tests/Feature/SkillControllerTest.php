<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests para SkillController
 * Cubre: habilidades técnicas, habilidades blandas, CRUD y vinculación con proyectos.
 */
class SkillControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Portfolio $portfolio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->portfolio = Portfolio::create([
            'user_id'   => $this->user->id,
            'slug'      => 'portfolio-' . $this->user->id,
            'title'     => 'Mi Portafolio',
            'is_public' => true,
            'show_email'=> false,
            'show_phone'=> false,
        ]);
    }

    // ─── VISTAS ───────────────────────────────────────────────────────────────

    public function test_vista_habilidades_tecnicas_requiere_auth(): void
    {
        $this->get('/habilidades-tecnicas')->assertRedirect('/login');
    }

    public function test_vista_habilidades_tecnicas_carga_correctamente(): void
    {
        $this->actingAs($this->user)
            ->get('/habilidades-tecnicas')
            ->assertOk()
            ->assertViewIs('habilidades-tecnicas');
    }

    public function test_vista_habilidades_blandas_carga_correctamente(): void
    {
        $this->actingAs($this->user)
            ->get('/habilidades-blandas')
            ->assertOk()
            ->assertViewIs('habilidades-blandas');
    }

    // ─── CREAR SKILL ──────────────────────────────────────────────────────────

    public function test_crear_habilidad_tecnica_exitosamente(): void
    {
        $this->actingAs($this->user)
            ->post('/skills', [
                'type'  => 'technical',
                'name'  => 'Laravel',
                'level' => 3,
            ])
            ->assertRedirect(route('skills.tecnicas'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('skills', [
            'user_id' => $this->user->id,
            'name'    => 'Laravel',
            'type'    => 'technical',
            'level'   => 3,
        ]);
    }

    public function test_crear_habilidad_blanda_exitosamente(): void
    {
        $this->actingAs($this->user)
            ->post('/skills', [
                'type' => 'soft',
                'name' => 'Liderazgo',
            ])
            ->assertRedirect(route('skills.blandas'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('skills', [
            'user_id' => $this->user->id,
            'name'    => 'Liderazgo',
            'type'    => 'soft',
        ]);
    }

    public function test_crear_skill_tecnico_sin_nivel_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/skills', [
                'type' => 'technical',
                'name' => 'Python',
                // sin level
            ])
            ->assertSessionHasErrors('level');
    }

    public function test_no_permite_skill_duplicado(): void
    {
        Skill::create([
            'user_id'       => $this->user->id,
            'type'          => 'technical',
            'name'          => 'PHP',
            'level'         => 2,
            'display_order' => 0,
        ]);

        $this->actingAs($this->user)
            ->post('/skills', [
                'type'  => 'technical',
                'name'  => 'PHP',
                'level' => 3,
            ])
            ->assertSessionHas('error_duplicate');
    }

    public function test_no_permite_nombre_con_numeros_largos(): void
    {
        $this->actingAs($this->user)
            ->post('/skills', [
                'type'  => 'technical',
                'name'  => 'Skill123456789',
                'level' => 1,
            ])
            ->assertSessionHasErrors('name');
    }

    // ─── ACTUALIZAR SKILL ─────────────────────────────────────────────────────

    public function test_actualizar_skill_exitosamente(): void
    {
        $skill = Skill::create([
            'user_id'       => $this->user->id,
            'type'          => 'technical',
            'name'          => 'Vue',
            'level'         => 1,
            'display_order' => 0,
        ]);

        $this->actingAs($this->user)
            ->put("/skills/{$skill->id}", [
                'name'  => 'Vue.js',
                'level' => 2,
            ])
            ->assertRedirect(route('skills.tecnicas'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'name' => 'Vue.js', 'level' => 2]);
    }

    public function test_no_puede_actualizar_skill_de_otro_usuario(): void
    {
        $otro = User::factory()->create();
        $skill = Skill::create([
            'user_id'       => $otro->id,
            'type'          => 'soft',
            'name'          => 'Empatia',
            'level'         => 1,
            'display_order' => 0,
        ]);

        $this->actingAs($this->user)
            ->put("/skills/{$skill->id}", ['name' => 'Hack', 'level' => 1])
            ->assertStatus(403);
    }

    // ─── ELIMINAR SKILL ───────────────────────────────────────────────────────

    public function test_eliminar_skill_exitosamente(): void
    {
        $skill = Skill::create([
            'user_id'       => $this->user->id,
            'type'          => 'soft',
            'name'          => 'Comunicación',
            'level'         => 1,
            'display_order' => 0,
        ]);

        $this->actingAs($this->user)
            ->delete("/skills/{$skill->id}")
            ->assertRedirect(route('skills.blandas'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }

    public function test_no_puede_eliminar_skill_de_otro_usuario(): void
    {
        $otro = User::factory()->create();
        $skill = Skill::create([
            'user_id'       => $otro->id,
            'type'          => 'soft',
            'name'          => 'Trabajo en equipo',
            'level'         => 1,
            'display_order' => 0,
        ]);

        $this->actingAs($this->user)
            ->delete("/skills/{$skill->id}")
            ->assertStatus(403);
    }

    // ─── VINCULAR / DESVINCULAR PROYECTO ──────────────────────────────────────

    public function test_vincular_proyecto_a_skill(): void
    {
        $skill = Skill::create([
            'user_id'       => $this->user->id,
            'type'          => 'technical',
            'name'          => 'React',
            'level'         => 2,
            'display_order' => 0,
        ]);

        $project = Project::create([
            'portfolio_id' => $this->portfolio->id,
            'name'         => 'Proyecto React',
            'description'  => 'Descripción',
            'is_visible'   => true,
        ]);

        $this->actingAs($this->user)
            ->postJson("/skills/{$skill->id}/projects", ['project_id' => $project->id])
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_desvincular_proyecto_de_skill(): void
    {
        $skill = Skill::create([
            'user_id'       => $this->user->id,
            'type'          => 'technical',
            'name'          => 'Angular',
            'level'         => 1,
            'display_order' => 0,
        ]);

        $project = Project::create([
            'portfolio_id' => $this->portfolio->id,
            'name'         => 'Proyecto Angular',
            'description'  => 'Descripción',
            'is_visible'   => true,
        ]);

        $skill->projects()->attach($project->id);

        $this->actingAs($this->user)
            ->deleteJson("/skills/{$skill->id}/projects/{$project->id}")
            ->assertOk()
            ->assertJson(['ok' => true]);
    }
}
