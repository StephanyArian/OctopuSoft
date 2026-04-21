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
        $redes = RedProfesional::where('user_id', $user->id)->get()->keyBy('platform_id');
        return view('redes-contacto', compact('redes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'linkedin'       => 'nullable|url|max:500',
            'github'         => 'nullable|url|max:500',
            'whatsapp'       => 'nullable|max:30',
            'email_contacto' => 'nullable|email|max:150',
            'otros'          => 'nullable|max:500',
            'sitio_web'      => 'nullable|url|max:500',
        ]);

        $user = Auth::user();

        $campos = [
            1 => ['campo' => 'linkedin',       'is_primary' => 1],
            2 => ['campo' => 'github',          'is_primary' => 0],
            3 => ['campo' => 'whatsapp',        'is_primary' => 0],
            4 => ['campo' => 'email_contacto',  'is_primary' => 0],
            5 => ['campo' => 'otros',           'is_primary' => 0],
        ];

        foreach ($campos as $platformId => $config) {
            $valor = $request->input($config['campo']);

            // updateOrCreate evita borrar y reinsertar innecesariamente
            if ($valor) {
                RedProfesional::updateOrCreate(
                    ['user_id' => $user->id, 'platform_id' => $platformId],
                    [
                        'profile_url'    => $valor,
                        'is_visible'     => 1,
                        'is_primary'     => $config['is_primary'],
                        'display_order'  => $platformId,
                    ]
                );
            } else {
                // Si el campo viene vacío, eliminar ese registro si existía
                RedProfesional::where('user_id', $user->id)
                    ->where('platform_id', $platformId)
                    ->delete();
            }
        }

        return back()->with('success', 'Datos guardados correctamente.');
    }
}