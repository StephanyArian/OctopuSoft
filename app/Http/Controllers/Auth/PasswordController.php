<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\PasswordChangedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // T7 — Evitar reutilización de contraseña anterior
        if (Hash::check($validated['password'], $request->user()->password)) {
            return back()->withErrors([
                'password' => 'La nueva contraseña no puede ser igual a la anterior.'
            ], 'updatePassword');
        }

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // T11 — Enviar correo de notificación de cambio de contraseña
        $request->user()->notify(new PasswordChangedNotification());

        return back()->with('status', 'password.reset.store');
    }
}