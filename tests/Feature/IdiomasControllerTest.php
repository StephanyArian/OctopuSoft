<?php

namespace Tests\Feature;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests para IdiomasController
 * Cubre: listar idiomas, crear, actualizar y eliminar.
 */
class IdiomasControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ─── VISTA ────────────────────────────────────────────────────────────────

    public function test_vista_idiomas_requiere_auth(): void
    {
        $this->get('/idiomas-info')->assertRedirect('/login');
    }

    public function test_vista_idiomas_carga_correctamente(): void
    {
        $this->actingAs($this->user)
            ->get('/idiomas-info')
            ->assertOk()
            ->assertViewIs('idiomas');
    }

    // ─── CREAR ────────────────────────────────────────────────────────────────

    public function test_crear_idioma_exitosamente(): void
    {
        $this->actingAs($this->user)
            ->post('/idiomas-info', [
                'nombre' => 'Inglés',
                'nivel'  => 'B2',
            ])
            ->assertRedirect(route('idiomas.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('skills', [
            'user_id' => $this->user->id,
            'type'    => 'language',
            'name'    => 'Inglés',
            'level'   => 4, // B2 = 4
        ]);
    }

    public function test_crear_idioma_invalido_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/idiomas-info', [
                'nombre' => 'Klingon',
                'nivel'  => 'B1',
            ])
            ->assertSessionHasErrors('nombre');
    }

    public function test_crear_idioma_sin_nombre_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/idiomas-info', [
                'nivel' => 'A1',
            ])
            ->assertSessionHasErrors('nombre');
    }

    public function test_crear_idioma_nivel_invalido_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/idiomas-info', [
                'nombre' => 'Inglés',
                'nivel'  => 'Z9',
            ])
            ->assertSessionHasErrors('nivel');
    }

    public function test_no_permite_idioma_duplicado(): void
    {
        Skill::create([
            'user_id'       => $this->user->id,
            'type'          => 'language',
            'name'          => 'Inglés',
            'level'         => 4,
            'display_order' => 0,
        ]);

        $this->actingAs($this->user)
            ->post('/idiomas-info', [
                'nombre' => 'Inglés',
                'nivel'  => 'C1',
            ])
            ->assertSessionHasErrors('nombre');
    }

    // ─── ACTUALIZAR ───────────────────────────────────────────────────────────

    public function test_actualizar_idioma_exitosamente(): void
    {
        $idioma = Skill::create([
            'user_id'       => $this->user->id,
            'type'          => 'language',
            'name'          => 'Francés',
            'level'         => 3,
            'display_order' => 0,
        ]);

        $this->actingAs($this->user)
            ->put("/idiomas-info/{$idioma->id}", [
                'nombre' => 'Francés',
                'nivel'  => 'C1',
            ])
            ->assertRedirect(route('idiomas.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('skills', ['id' => $idioma->id, 'level' => 5]); // C1 = 5
    }

    // ─── ELIMINAR ─────────────────────────────────────────────────────────────

    public function test_eliminar_idioma_exitosamente(): void
    {
        $idioma = Skill::create([
            'user_id'       => $this->user->id,
            'type'          => 'language',
            'name'          => 'Alemán',
            'level'         => 2,
            'display_order' => 0,
        ]);

        $this->actingAs($this->user)
            ->delete("/idiomas-info/{$idioma->id}")
            ->assertRedirect(route('idiomas.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('skills', ['id' => $idioma->id]);
    }

    public function test_no_puede_eliminar_idioma_de_otro_usuario(): void
    {
        $otro = User::factory()->create();
        $idioma = Skill::create([
            'user_id'       => $otro->id,
            'type'          => 'language',
            'name'          => 'Español',
            'level'         => 7,
            'display_order' => 0,
        ]);

        $this->actingAs($this->user)
            ->delete("/idiomas-info/{$idioma->id}")
            ->assertStatus(404);
    }
}
