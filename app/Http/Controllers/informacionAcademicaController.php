<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Experience;

class InformacionAcademicaController extends Controller
{
    public function index()
    {
        $formaciones = Experience::education()
            ->where('user_id', auth()->id())
            ->orderBy('start_date', 'desc')
            ->get();

        return view('informacion-academica', compact('formaciones'));
    }

    public function store(Request $request)
{
    $request->validate([
        'institucion'     => 'required|string|max:255',
        'titulo_obtenido' => 'required|string|max:255',
        'fecha_inicio'    => 'required',
        'fecha_fin'       => 'nullable',
        'descripcion'     => 'nullable|string|max:1000',
        'estudio_actual'  => 'nullable',
    ]);

    Experience::create([
        'user_id'     => auth()->id(),
        'type'        => 'education',
        'institution' => $request->institucion,
        'title'       => $request->titulo_obtenido,
        'description' => $request->descripcion,
        'start_date'  => $request->fecha_inicio . '-01',
        'end_date'    => $request->estudio_actual ? null : ($request->fecha_fin ? $request->fecha_fin . '-01' : null),
        'is_current'  => $request->has('estudio_actual'),
        'is_visible'  => true,
    ]);

    return redirect()->route('informacion.academica')
        ->with('success', 'Formación académica guardada correctamente');
}
}