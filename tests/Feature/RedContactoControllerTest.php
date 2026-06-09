<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests para RedContactoController
 * Cubre: vista y guardado de redes de contacto.
 */
class RedContactoControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ─── VISTA ────────────────────────────────────────────────────────────────

    public function test_vista_redes_contacto_requiere_auth(): void
    {
        $this->get('/redes-contacto')->assertRedirect('/login');
    }

    public function test_vista_redes_contacto_carga_correctamente(): void
    {
        $this->actingAs($this->user)
            ->get('/redes-contacto')
            ->assertOk()
            ->assertViewIs('redes-contacto');
    }

    // ─── GUARDAR REDES ────────────────────────────────────────────────────────

    public function test_guardar_linkedin_valido(): void
    {
        $this->actingAs($this->user)
            ->post('/redes-contacto', [
                'linkedin' => 'https://linkedin.com/in/usuario-prueba',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    public function test_linkedin_invalido_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/redes-contacto', [
                'linkedin' => 'https://facebook.com/usuario',
            ])
            ->assertSessionHasErrors('linkedin');
    }

    public function test_guardar_github_valido(): void
    {
        $this->actingAs($this->user)
            ->post('/redes-contacto', [
                'github' => 'https://github.com/mi-usuario',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    public function test_github_invalido_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/redes-contacto', [
                'github' => 'https://gitlab.com/usuario',
            ])
            ->assertSessionHasErrors('github');
    }

    public function test_guardar_whatsapp_valido(): void
    {
        $this->actingAs($this->user)
            ->post('/redes-contacto', [
                'whatsapp' => '+59179123456',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    public function test_whatsapp_invalido_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/redes-contacto', [
                'whatsapp' => 'no-es-un-numero',
            ])
            ->assertSessionHasErrors('whatsapp');
    }

    public function test_email_contacto_valido(): void
    {
        $this->actingAs($this->user)
            ->post('/redes-contacto', [
                'email_contacto' => 'usuario@gmail.com',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    public function test_email_contacto_no_gmail_falla(): void
    {
        $this->actingAs($this->user)
            ->post('/redes-contacto', [
                'email_contacto' => 'usuario@hotmail.com',
            ])
            ->assertSessionHasErrors('email_contacto');
    }

    public function test_guardar_redes_vacias_es_valido(): void
    {
        // Guardar sin ningún dato es válido (todos son opcionales)
        $this->actingAs($this->user)
            ->post('/redes-contacto', [])
            ->assertRedirect()
            ->assertSessionHas('success');
    }
}
