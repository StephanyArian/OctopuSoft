<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests para ExperienciaLaboralController
 * Cubre: vista, crear experiencia, actualizar y eliminar.
 */
class ExperienciaLaboralControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ─── VISTAS ───────────────────────────────────────────────────────────────

    public function test_vista_experiencia_laboral_requiere_auth(): void
    {
        $this->get('/experiencia-laboral')->assertRedirect('/login');
    }

    public function test_vista_experiencia_laboral_carga_correctamente(): void
    {
        $this->actingAs($this->user)
            ->get('/experiencia-laboral')
            ->assertOk()
            ->assertViewIs('experiencia-laboral');
    }

    // ─── CREAR ────────────────────────────────────────────────────────────────

    public function test_crear_experiencia_laboral_exitosamente(): void
    {
        $this->actingAs($this->user)
            ->post('/experiencia-laboral', [
                'empresa'         => 'Google Bolivia',
                'cargos'          => ['Desarrollador Backend'],
                'fecha_inicio'    => '2022-01-15',
                'trabajo_actual'  => '1',
            ])
            ->assertRedirect(route('experiencia.laboral'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('experiences', [
            'user_id'     => $this->user->id,
            'type'        => 'work',
            'institution' => 'Google Bolivia',
        ]);
    }

    public function test_crear_sin_empresa_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/experiencia-laboral', [
                'cargos'         => ['Desarrollador'],
                'fecha_inicio'   => '2022-01-01',
                'trabajo_actual' => '1',
            ])
            ->assertSessionHasErrors('empresa');
    }

    public function test_crear_sin_cargos_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/experiencia-laboral', [
                'empresa'        => 'Empresa SA',
                'fecha_inicio'   => '2022-01-01',
                'trabajo_actual' => '1',
            ])
            ->assertSessionHasErrors('cargos');
    }

    public function test_crear_sin_fecha_inicio_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/experiencia-laboral', [
                'empresa'        => 'Empresa SA',
                'cargos'         => ['Desarrollador'],
                'trabajo_actual' => '1',
            ])
            ->assertSessionHasErrors();
    }

    public function test_crear_con_fecha_fin_menor_a_inicio_falla(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/experiencia-laboral', [
                'empresa'      => 'Empresa SA',
                'cargos'       => ['Analista'],
                'fecha_inicio' => '2023-01-01',
                'fecha_fin'    => '2022-01-01',
            ]);

        // Debe haber algún error de sesión
        $response->assertSessionHasErrors();
    }

    // ─── ACTUALIZAR ───────────────────────────────────────────────────────────

    public function test_actualizar_experiencia_exitosamente(): void
    {
        $exp = Experience::create([
            'user_id'     => $this->user->id,
            'type'        => 'work',
            'institution' => 'Empresa Vieja',
            'title'       => 'Programador',
            'start_date'  => '2020-01-01',
            'is_current'  => true,
            'is_visible'  => true,
        ]);

        $this->actingAs($this->user)
            ->putJson("/experiencia-laboral/{$exp->id}", [
                'empresa'        => 'Empresa Nueva',
                'cargos'         => ['Senior Developer'],
                'fecha_inicio'   => '2020-01-01',
                'trabajo_actual' => '1',
            ])
            ->assertOk();

        $this->assertDatabaseHas('experiences', [
            'id'          => $exp->id,
            'institution' => 'Empresa Nueva',
        ]);
    }

    // ─── ELIMINAR ─────────────────────────────────────────────────────────────

    public function test_eliminar_experiencia_exitosamente(): void
    {
        $exp = Experience::create([
            'user_id'     => $this->user->id,
            'type'        => 'work',
            'institution' => 'Empresa Borrar',
            'title'       => 'Analista',
            'start_date'  => '2021-01-01',
            'is_current'  => true,
            'is_visible'  => true,
        ]);

        $this->actingAs($this->user)
            ->deleteJson("/experiencia-laboral/{$exp->id}")
            ->assertOk()
            ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('experiences', ['id' => $exp->id]);
    }

    public function test_no_puede_eliminar_experiencia_de_otro_usuario(): void
    {
        $otro = User::factory()->create();
        $exp = Experience::create([
            'user_id'     => $otro->id,
            'type'        => 'work',
            'institution' => 'Empresa Ajena',
            'title'       => 'Programador',
            'start_date'  => '2020-01-01',
            'is_current'  => true,
            'is_visible'  => true,
        ]);

        $this->actingAs($this->user)
            ->deleteJson("/experiencia-laboral/{$exp->id}")
            ->assertStatus(404);
    }
}
