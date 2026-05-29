<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:30', 'regex:/^[\pL\s\-]+$/u'],
            'last_name'  => ['required', 'string', 'max:30', 'regex:/^[\pL\s\-]+$/u'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:60', 'unique:'.User::class],
            'password'   => ['required', 'confirmed', Rules\Password::defaults(),'min:8', 'max:72'],
        ]);

        $user = User::create([
        'first_name' => $request->first_name,   
        'last_name'  => $request->last_name,    
        'email'      => $request->email,
        'password'   => Hash::make($request->password),
    ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}