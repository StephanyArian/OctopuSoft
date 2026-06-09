<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests para InformacionAcademicaController
 * Cubre: vista, crear formación académica, actualizar y eliminar.
 */
class InformacionAcademicaControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ─── VISTA ────────────────────────────────────────────────────────────────

    public function test_vista_info_academica_requiere_auth(): void
    {
        $this->get('/informacion-academica')->assertRedirect('/login');
    }

    public function test_vista_info_academica_carga_correctamente(): void
    {
        $this->actingAs($this->user)
            ->get('/informacion-academica')
            ->assertOk()
            ->assertViewIs('informacion-academica');
    }

    // ─── CREAR ────────────────────────────────────────────────────────────────

    public function test_crear_formacion_academica_exitosamente(): void
    {
        $this->actingAs($this->user)
            ->post('/informacion-academica', [
                'institucion'     => 'UMSS',
                'titulo_obtenido' => 'Ingeniería',
                'tipo_formacion'  => 'Universitaria',
                'fecha_inicio'    => '2020-01-01',
                'estudio_actual'  => '1',
            ])
            ->assertRedirect(route('informacion.academica'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('experiences', [
            'user_id'     => $this->user->id,
            'type'        => 'education',
            'institution' => 'UMSS',
        ]);
    }

    public function test_crear_sin_institucion_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/informacion-academica', [
                'titulo_obtenido' => 'Ingeniería',
                'tipo_formacion'  => 'Universitaria',
                'fecha_inicio'    => '2020-01-01',
                'estudio_actual'  => '1',
            ])
            ->assertSessionHasErrors('institucion');
    }

    public function test_crear_sin_tipo_formacion_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/informacion-academica', [
                'institucion'    => 'UMSS',
                'fecha_inicio'   => '2020-01-01',
                'estudio_actual' => '1',
            ])
            ->assertSessionHasErrors('tipo_formacion');
    }

    public function test_crear_sin_fecha_inicio_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/informacion-academica', [
                'institucion'    => 'UMSS',
                'tipo_formacion' => 'Universitaria',
                'estudio_actual' => '1',
            ])
            ->assertSessionHasErrors('fecha_inicio');
    }

    public function test_crear_sin_fecha_fin_ni_actual_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/informacion-academica', [
                'institucion'    => 'UMSS',
                'tipo_formacion' => 'Universitaria',
                'fecha_inicio'   => '2020-01-01',
                // sin estudio_actual ni fecha_fin
            ])
            ->assertSessionHasErrors('fecha_fin');
    }

    // ─── ELIMINAR ─────────────────────────────────────────────────────────────

    public function test_eliminar_formacion_academica_exitosamente(): void
    {
        $formacion = Experience::create([
            'user_id'     => $this->user->id,
            'type'        => 'education',
            'institution' => 'UAGRM',
            'title'       => 'Ingeniería de Sistemas',
            'start_date'  => '2019-01-01',
            'is_current'  => true,
            'is_visible'  => true,
        ]);

        $this->actingAs($this->user)
            ->delete("/informacion-academica/{$formacion->id}")
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('experiences', ['id' => $formacion->id]);
    }

    public function test_no_puede_eliminar_formacion_de_otro_usuario(): void
    {
        $otro = User::factory()->create();
        $formacion = Experience::create([
            'user_id'     => $otro->id,
            'type'        => 'education',
            'institution' => 'UAGRM',
            'title'       => 'Sistemas',
            'start_date'  => '2019-01-01',
            'is_current'  => true,
            'is_visible'  => true,
        ]);

        $this->actingAs($this->user)
            ->delete("/informacion-academica/{$formacion->id}")
            ->assertStatus(404);
    }
}
