<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // Muestra el formulario de registro
    public function create()
    {
        return view('auth.register');
    }

    // Procesa el registro (T4, T5, T6, T7)
    public function store(Request $request)
    {
        $request->validate([

            // T4: campos obligatorios
            'name'     => ['required', 'string', 'max:255'],

            // T4 + T5: obligatorio, formato email, único en BD
            'email'    => ['required', 'email', 'unique:users,email'],

            // T4 + T7: obligatorio, mínimo 8 chars, mayúscula, número
            // T6: confirmed valida que coincida con password_confirmation
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',   // al menos una mayúscula
                'regex:/[0-9]/',   // al menos un número
            ],

        ], [
            // T11: mensajes de error 
            'name.required'       => 'El nombre es obligatorio.',
            'email.required'      => 'El correo electrónico es obligatorio.',
            'email.email'         => 'Ingresa un correo electrónico válido.',
            'email.unique'        => 'Este correo ya está registrado.',
            'password.required'   => 'La contraseña es obligatoria.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'  => 'Las contraseñas no coinciden.',
            'password.regex'      => 'La contraseña debe incluir al menos una mayúscula y un número.',
        ]);

        //T8: Implementar registro de usuario con formulario y controlador
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'user',
            'status'   => 'pending',
        ]);

        //  T10: Enviar correo de verificación 
        $user->sendEmailVerificationNotification();

        // T9: Iniciar sesión automáticamente con sesiones Laravel 
        Auth::login($user);
        $request->session()->regenerate();

        // Redirigir al dashboard con mensaje de éxito 
        return redirect()->route('dashboard')
                         ->with('success', '¡Bienvenido! Tu cuenta fue creada correctamente.');
    
    }
}
