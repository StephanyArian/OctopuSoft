<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests para ProyectoController
 * Cubre: listar, crear, actualizar, toggle visibilidad y eliminar proyectos.
 */
class ProyectoControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ─── LISTAR ───────────────────────────────────────────────────────────────

    public function test_listar_proyectos_requiere_auth(): void
    {
        // Las rutas JSON retornan 401 cuando no está autenticado (no redirect)
        $this->getJson('/proyectos')->assertUnauthorized();
    }

    public function test_listar_proyectos_retorna_json(): void
    {
        $this->actingAs($this->user)
            ->getJson('/proyectos')
            ->assertOk()
            ->assertJsonStructure([]);
    }

    // ─── CREAR ────────────────────────────────────────────────────────────────

    public function test_crear_proyecto_exitosamente(): void
    {
        $this->actingAs($this->user)
            ->postJson('/proyectos', [
                'nombre'      => 'Mi Proyecto',
                'descripcion' => 'Descripción detallada del proyecto',
                'estado'      => 'En curso',
                'visibilidad' => 'publico',
            ])
            ->assertStatus(201)
            ->assertJsonStructure(['id', 'nombre', 'descripcion']);

        $this->assertDatabaseHas('projects', ['name' => 'Mi Proyecto']);
    }

    public function test_crear_proyecto_sin_nombre_falla(): void
    {
        $this->actingAs($this->user)
            ->postJson('/proyectos', [
                'descripcion' => 'Sin nombre',
            ])
            ->assertStatus(422);
    }

    public function test_crear_proyecto_sin_descripcion_falla(): void
    {
        $this->actingAs($this->user)
            ->postJson('/proyectos', [
                'nombre' => 'Sin descripción',
            ])
            ->assertStatus(422);
    }

    public function test_crear_proyecto_con_tecnologias(): void
    {
        $this->actingAs($this->user)
            ->postJson('/proyectos', [
                'nombre'      => 'Proyecto Tech',
                'descripcion' => 'Descripción',
                'tecnologias' => ['Laravel', 'Vue.js', 'MySQL'],
            ])
            ->assertStatus(201)
            ->assertJsonPath('tecnologias', ['Laravel', 'Vue.js', 'MySQL']);
    }

    // ─── ACTUALIZAR ───────────────────────────────────────────────────────────

    public function test_actualizar_proyecto_exitosamente(): void
    {
        $portfolio = $this->crearPortfolio();
        $proyecto = Project::create([
            'portfolio_id' => $portfolio->id,
            'name'         => 'Proyecto Original',
            'description'  => 'Descripción original',
            'is_visible'   => true,
        ]);

        $this->actingAs($this->user)
            ->putJson("/proyectos/{$proyecto->id}", [
                'nombre'      => 'Proyecto Actualizado',
                'descripcion' => 'Nueva descripción',
                'estado'      => 'Completado',
            ])
            ->assertOk()
            ->assertJsonPath('nombre', 'Proyecto Actualizado');
    }

    public function test_no_puede_actualizar_proyecto_de_otro_usuario(): void
    {
        $otro = User::factory()->create();
        $otroPortfolio = Portfolio::create([
            'user_id'   => $otro->id,
            'slug'      => 'portfolio-otro',
            'title'     => 'Portafolio otro',
            'is_public' => false,
            'show_email'=> false,
            'show_phone'=> false,
        ]);
        $proyecto = Project::create([
            'portfolio_id' => $otroPortfolio->id,
            'name'         => 'Proyecto ajeno',
            'description'  => 'No mío',
            'is_visible'   => true,
        ]);

        $this->actingAs($this->user)
            ->putJson("/proyectos/{$proyecto->id}", [
                'nombre'      => 'Hack',
                'descripcion' => 'Intento de hackeo',
            ])
            ->assertStatus(500); // 404 del firstOrFail dentro del try-catch → 500
    }

    // ─── TOGGLE VISIBILIDAD ───────────────────────────────────────────────────

    public function test_toggle_visibilidad_proyecto(): void
    {
        $portfolio = $this->crearPortfolio();
        $proyecto = Project::create([
            'portfolio_id' => $portfolio->id,
            'name'         => 'Proyecto Visible',
            'description'  => 'Descripción',
            'is_visible'   => true,
        ]);

        $this->actingAs($this->user)
            ->patchJson("/proyectos/{$proyecto->id}/visibilidad", ['is_visible' => false])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('is_visible', false);
    }

    // ─── ELIMINAR ─────────────────────────────────────────────────────────────

    public function test_eliminar_proyecto_exitosamente(): void
    {
        $portfolio = $this->crearPortfolio();
        $proyecto = Project::create([
            'portfolio_id' => $portfolio->id,
            'name'         => 'Proyecto para borrar',
            'description'  => 'Descripción',
            'is_visible'   => true,
        ]);

        $this->actingAs($this->user)
            ->deleteJson("/proyectos/{$proyecto->id}")
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseMissing('projects', ['id' => $proyecto->id]);
    }

    // ─── HELPER ───────────────────────────────────────────────────────────────

    private function crearPortfolio(): Portfolio
    {
        return Portfolio::firstOrCreate(
            ['user_id' => $this->user->id],
            [
                'slug'      => 'portfolio-' . $this->user->id,
                'title'     => 'Mi Portafolio',
                'is_public' => true,
                'show_email'=> false,
                'show_phone'=> false,
            ]
        );
    }
}
