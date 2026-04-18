<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordSecurityTest extends TestCase
{
    use RefreshDatabase;

    // Prueba 1: No deja entrar con contraseña incorrecta
    public function test_no_entra_con_contrasena_incorrecta(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'incorrecta',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // Prueba 2: Bloquea después de 5 intentos fallidos
    public function test_bloquea_despues_de_5_intentos(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'incorrecta',
            ]);
        }

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'incorrecta',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // Prueba 3: Entra correctamente con credenciales válidas
    public function test_entra_con_credenciales_correctas(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);

        $response->assertRedirect('/dashboard');
    }
}