<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\PasswordChangedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function update(Request $request)
    {
        // Validación - campo password_confirmation es el que usa Laravel
        $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 
                'string',
                'min:8',
                'max:20',
                'confirmed',  // ← Esto busca el campo "password_confirmation"
            ],
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'current_password.current_password' => 'La contraseña actual es incorrecta.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La nueva contraseña no puede tener más de 20 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Validación adicional de fortaleza
        $password = $request->password;
        if (!preg_match('/[A-Z]/', $password)) {
            return back()->withErrors([
                'password' => 'La contraseña debe incluir al menos una mayúscula.'
            ], 'updatePassword');
        }
        if (!preg_match('/[a-z]/', $password)) {
            return back()->withErrors([
                'password' => 'La contraseña debe incluir al menos una minúscula.'
            ], 'updatePassword');
        }
        if (!preg_match('/[0-9]/', $password)) {
            return back()->withErrors([
                'password' => 'La contraseña debe incluir al menos un número.'
            ], 'updatePassword');
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return back()->withErrors([
                'password' => 'La contraseña debe incluir al menos un carácter especial.'
            ], 'updatePassword');
        }

        // Verificar que la nueva contraseña no sea igual a la anterior
        if (Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors([
                'password' => 'La nueva contraseña no puede ser igual a la anterior.'
            ], 'updatePassword');
        }

        // Actualizar contraseña en la BD (columna "password")
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        // Enviar notificación por correo
        $request->user()->notify(new PasswordChangedNotification());

        return back()->with('status', 'password-updated');
    }
}