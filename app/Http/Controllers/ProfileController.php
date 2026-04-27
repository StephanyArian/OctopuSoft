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
        // Validar los datos del formulario
        $request->validate([
            'name' => ['required', 'string', 'max:30', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'title' => ['nullable', 'string', 'max:30', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'location' => 'nullable|string|max:30',
            'bio' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png|max:2048'
        ], [
            'name.regex' => 'El nombre solo debe contener letras y espacios.',
            'title.regex' => 'El título profesional solo debe contener letras y espacios.'
        ]);

        $user = $request->user();
        
        // Separar nombre completo en first_name y last_name
        $fullName = explode(' ', $request->name, 2);
        $user->first_name = $fullName[0];
        $user->last_name = $fullName[1] ?? '';
        
        // Guardar la biografía
        $user->biography = $request->bio;
        
        // Separar ubicación en ciudad y país
        if ($request->location) {
            $location = explode(',', $request->location, 2);
            $user->city = trim($location[0]);
            $user->country = trim($location[1] ?? '');
        }
        
        // Guardar el título profesional (profesión)
        if ($request->title) {
            // Buscar o crear la profesión
            $profession = \App\Models\Profession::firstOrCreate(
                ['name' => $request->title]
            );
            $user->profession_id = $profession->id;
        }
        
        // ✅ MODIFICADO: Guardar la foto como BASE64 en la base de datos
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $imageData = base64_encode(file_get_contents($image));
            $mimeType = $image->getMimeType();
            $user->photo_base64 = 'data:' . $mimeType . ';base64,' . $imageData;
        }
        
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Perfil guardado exitosamente.');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
    
        // Separar nombre en first_name y last_name
        $fullName = explode(' ', $request->name, 2);
        $user->first_name = $fullName[0];
        $user->last_name = $fullName[1] ?? $fullName[0];
    
        $user->email = $request->email;

        $user->save();


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

    /**
     * ✅ NUEVO: Eliminar la foto de perfil
     */
    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->photo_base64 = null;
        $user->save();
        
        return redirect()->route('dashboard')->with('success', 'Foto eliminada exitosamente');
    }
}