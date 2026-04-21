<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function create()
    {
        return redirect()->route('dashboard');
    }   

    public function store(Request $request): RedirectResponse
    {
    $request->validate([
        'first_name' => 'required|string|max:255',
        'profession_id' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'biography' => 'nullable|string|max:500',
        'photo_url' => 'nullable|image|mimes:jpeg,png|max:2048',
    ]);

    $user = $request->user();
    $user->name = $request->name;
    $user->save();

    return redirect()->route('dashboard')->with('success', 'Perfil guardado exitosamente.');
    }   

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
