<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'token' => ['required'],
        'email' => ['required', 'email'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    \Log::info('Reset password attempt', [
        'email' => $request->email,
        'token' => $request->token,
        'has_token' => !empty($request->token)
    ]);

    // T7 — Evitar reutilización de contraseña anterior
    $user = User::where('email', $request->email)->first();
    if ($user && Hash::check($request->password, $user->password)) {
        \Log::info('password reset callback' ,['email' => $user->email]);
        return back()->withInput($request->only('email'))
            ->withErrors(['password' => 'La nueva contraseña no puede ser igual a la anterior.']);
    }

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user) use ($request) {
            \Log::info('Password reset calback execute', ['email' => $user->email]);
            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }
    );

    \Log::info('pasword reset status' , ['status'=>$status]);

    return $status == Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
}
}
