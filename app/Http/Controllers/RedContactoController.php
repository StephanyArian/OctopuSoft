<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RedProfesional;

class RedContactoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $redes = RedProfesional::where('user_id', $user->id)
            ->get()
            ->keyBy('platform_id');

        return view('redes-contacto', compact('redes'));
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

        $user = Auth::user();

        // CONFIGURACIÓN DE CAMPOS
        $campos = [
            1 => ['campo' => 'linkedin',      'is_primary' => 1],
            2 => ['campo' => 'github',       'is_primary' => 0],
            3 => ['campo' => 'whatsapp',     'is_primary' => 0],
            4 => ['campo' => 'email_contacto','is_primary' => 0],
            5 => ['campo' => 'otros',        'is_primary' => 0],
        ];

        foreach ($campos as $platformId => $config) {
            $valor = $request->input($config['campo']);

            if ($valor) {
                // CREA O ACTUALIZA
                RedProfesional::updateOrCreate(
                    [
                        'user_id'     => $user->id,
                        'platform_id' => $platformId
                    ],
                    [
                        'profile_url'   => $valor,
                        'is_visible'    => 1,
                        'is_primary'    => $config['is_primary'],
                        'display_order' => $platformId,
                    ]
                );
            } else {
                // ELIMINA SI ESTÁ VACÍO
                RedProfesional::where('user_id', $user->id)
                    ->where('platform_id', $platformId)
                    ->delete();
            }
        }

        return back()->with('success', 'Datos guardados correctamente.');
    }
}