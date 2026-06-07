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
            'location' => [
                'nullable',
                'string',
                'max:30',
                function ($attribute, $value, $fail) {
                    $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($value) . '&limit=1';
                    
                    $opts = [
                        'http' => [
                            'method' => 'GET',
                            'header' => "User-Agent: OctopuSoft-Portfolio-App\r\n",
                            'timeout' => 2 // 2 segundos de timeout
                        ]
                    ];
                    $context = stream_context_create($opts);
                    $response = @file_get_contents($url, false, $context);
                    
                    if ($response) {
                        $data = json_decode($response, true);
                        if (empty($data)) {
                            $fail('La ubicación ingresada no corresponde a un lugar geográfico real.');
                        }
                    }
                }
            ],
            'bio' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $plainText = html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8');
                    $plainText = rtrim($plainText, "\n\r");
                    $cleanText = preg_replace('/[\s\x{00A0}\x{1680}\x{2000}-\x{200B}\x{202F}\x{205F}\x{3000}\x{FEFF}]/u', '', $plainText);

                    if (mb_strlen($cleanText, 'UTF-8') > 500) {
                        $fail('La biografía no puede exceder los 500 caracteres (sin contar espacios).');
                    }
                }
            ],
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

    public function publish(Request $request): RedirectResponse
{
    $user = $request->user();
    
    // Verificar si el usuario tiene un portafolio
    if ($user->portfolio) {
        $user->portfolio->update(['is_public' => true]);
        
        return redirect()
            ->route('preview')
            ->with('success', '✅ ¡Perfil publicado exitosamente! Ahora es visible para todos.');
    }
    
    // Si no tiene portafolio, crear uno
    $portfolio = $user->portfolio()->create([
        'slug' => \Illuminate\Support\Str::slug($user->first_name . '-' . $user->last_name . '-' . $user->id),
        'title' => 'Portafolio de ' . $user->first_name . ' ' . $user->last_name,
        'description' => $user->biography ?? '',
        'is_public' => true
    ]);
    
    return redirect()
        ->route('preview')
        ->with('success', '✅ ¡Perfil publicado exitosamente!');
}

    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|string|in:default,sunset,emerald,midnight,ocean,sakura'
        ]);

        $user = $request->user();
        
        if (!$user->portfolio) {
            $user->portfolio()->create([
                'slug' => \Illuminate\Support\Str::slug($user->first_name . '-' . $user->last_name . '-' . $user->id),
                'title' => 'Portafolio de ' . $user->first_name . ' ' . $user->last_name,
                'description' => $user->biography ?? '',
                'is_public' => false,
                'color_theme' => $request->theme
            ]);
        } else {
            $user->portfolio->update(['color_theme' => $request->theme]);
        }

        return response()->json(['success' => true, 'message' => '¡Vibra de color actualizada con éxito!']);
    }

}