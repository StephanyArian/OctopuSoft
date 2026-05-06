<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RedProfesional;
use App\Models\PlataformaRed;
use App\Models\UserLocation;

class RedContactoController extends Controller
{
    public function index()
{
    $user = Auth::user();

    $redes = RedProfesional::where('user_id', $user->id)
        ->get()
        ->keyBy('platform_id');

    $platforms = \App\Models\PlataformaRed::pluck('id', 'name');
    
    $location = UserLocation::firstOrNew(['user_id' => $user->id]);

    return view('redes-contacto', compact('redes', 'platforms', 'location'));
}

    public function store(Request $request)
    {
        // VALIDACIONES
        $request->validate(
            [
                'linkedin' => [
                    'nullable',
                    'url',
                    'max:255',
                    'regex:/^https?:\/\/(www\.)?linkedin\.com\/.+$/'
                ],

                'github' => [
                    'nullable',
                    'url',
                    'max:255',
                    'regex:/^https?:\/\/(www\.)?github\.com\/.+$/'
                ],

                'whatsapp' => [
                    'nullable',
                    'regex:/^\+?[0-9]{8,15}$/'
                ],

                'email_contacto' => [
                    'nullable',
                    'email',
                    'max:100',
                    'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'
                ],

                'otros' => [
                    'nullable',
                    'string',
                    'max:50'
                ],
            ],
            [
                // MENSAJES PERSONALIZADOS
                'linkedin.regex' => 'El enlace debe ser de LinkedIn válido',
                'github.regex' => 'El enlace debe ser de GitHub válido',

                'whatsapp.regex' => 'El número debe tener solo números (8 a 15 dígitos, sin letras)',

                'email_contacto.email' => 'Debe ser un correo válido',
                'email_contacto.regex' => 'Solo se permiten correos @gmail.com',
            ]
        );
        $request->validate([
        'address'       => ['nullable', 'string', 'max:300'],
        'show_location' => ['nullable', 'boolean'],
        ]);
        
        $user = Auth::user();

        // Crear plataformas si no existen
        $defaultPlatforms = [
            ['name' => 'LinkedIn', 'base_url' => 'https://linkedin.com/in/'],
            ['name' => 'GitHub', 'base_url' => 'https://github.com/'],
            ['name' => 'WhatsApp', 'base_url' => 'https://wa.me/'],
            ['name' => 'Email', 'base_url' => 'mailto:'],
            ['name' => 'Otros', 'base_url' => null],
            ];
            
            foreach ($defaultPlatforms as $p) {
                PlataformaRed::firstOrCreate(['name' => $p['name']], $p);
            }
            
            // Obtener IDs reales
            $platforms = PlataformaRed::pluck('id', 'name')->toArray();
            
            // Configuración SIN IDs fijos
            $campos = [
                $platforms['LinkedIn'] => ['campo' => 'linkedin', 'is_primary' => 1],
                $platforms['GitHub'] => ['campo' => 'github', 'is_primary' => 0],
                $platforms['WhatsApp'] => ['campo' => 'whatsapp', 'is_primary' => 0],
                $platforms['Email'] => ['campo' => 'email_contacto', 'is_primary' => 0],
                $platforms['Otros'] => ['campo' => 'otros', 'is_primary' => 0],
            ];

            

            $user = Auth::user();

            // ACTUALIZAR TAMBIÉN EL CAMPO PHONE EN LA TABLA USERS
            if ($request->has('whatsapp') && $request->whatsapp) {
                $user->phone = $request->whatsapp;
                $user->save();
            } elseif ($request->whatsapp === null || $request->whatsapp === '') {
                // Opcional: Si envían vacío, limpiar el campo
                $user->phone = null;
                $user->save();
            }

            $visibleValues = [];
            foreach ($campos as $platformId => $config) {

                $campo = $config['campo'];

                $switchName = match($config['campo']) {
                    'linkedin' => 'visible_linkedin',
                    'github' => 'visible_github',
                    'whatsapp' => 'visible_whatsapp',
                    'email_contacto' => 'visible_email',
                    'otros' => 'visible_otros',
                    default => 'visible_' . $config['campo']
                };
               
            

             $visibleValues[$platformId] = $request->input($switchName, 0) ? 1 : 0;

            }

            foreach ($campos as $platformId => $config) {
            $valor = $request->input($config['campo']);
            $isVisible = $visibleValues[$platformId] ?? 0; 
            if ($valor) {
                $existente = RedProfesional::where('user_id', $user->id)
                    ->where('platform_id', $platformId)
                    ->first();
            
                RedProfesional::updateOrCreate(
                    ['user_id' => $user->id, 'platform_id' => $platformId],
                    [
                        'profile_url'   => $valor,
                        'is_visible'    => $isVisible,
                        'is_primary'    => $config['is_primary'],
                        'display_order' => $platformId,
                    ]
                );
            
                // Reemplaza las líneas de session()->push('warnings'...) por esto:
                if ($existente && $existente->profile_url !== $valor) {
                    session()->put('warning_' . $config['campo'], 'El enlace fue actualizado.');
                } elseif ($existente && $existente->profile_url === $valor) {
                    session()->put('warning_' . $config['campo'], 'Este enlace ya estaba registrado.');
                }
            } else {
                RedProfesional::where('user_id', $user->id)
                    ->where('platform_id', $platformId)
                    ->delete();
            }
            }
         UserLocation::updateOrCreate(
        ['user_id' => $user->id],
        [
            'address'       => $request->address,
            'show_location' => $request->boolean('show_location'),
            // latitude/longitude las llenas en T7 con geocodificación
        ]
    );   
    
    
    return back()->with('success', 'Datos guardados correctamente.');
    }
}