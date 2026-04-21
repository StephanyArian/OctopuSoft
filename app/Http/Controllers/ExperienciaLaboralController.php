<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Experience;

class ExperienciaLaboralController extends Controller
{
    public function index()
    {
        $experiencias = Experience::where('user_id', auth()->id())
            ->where('type', 'work')
            ->orderBy('start_date', 'desc')
            ->get();

        return view('experiencia-laboral', compact('experiencias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empresa'            => 'required|string|max:255',
            'cargo'              => 'required|string|max:255',
            'location'           => 'nullable|string|max:255',
            'fecha_inicio_dia'   => 'required|string',
            'fecha_inicio_mes'   => 'required|string',
            'fecha_inicio_anio'  => 'required|integer|min:1950|max:' . date('Y'),
            'fecha_fin_dia'      => 'nullable|string',
            'fecha_fin_mes'      => 'nullable|string',
            'fecha_fin_anio'     => 'nullable|integer|min:1950|max:' . date('Y'),
            'descripcion'        => 'nullable|string',
        ]);

        $fechaInicio = $request->fecha_inicio_anio . '-'
                     . $request->fecha_inicio_mes  . '-'
                     . $request->fecha_inicio_dia;

        $fechaFin = null;
        if (!$request->trabajo_actual
            && $request->fecha_fin_dia
            && $request->fecha_fin_mes
            && $request->fecha_fin_anio
        ) {
            $fechaFin = $request->fecha_fin_anio . '-'
                      . $request->fecha_fin_mes   . '-'
                      . $request->fecha_fin_dia;

            if (strtotime($fechaFin) < strtotime($fechaInicio)) {
                return back()
                    ->withErrors(['fecha_fin_dia' => 'La fecha de fin no puede ser anterior a la fecha de inicio.'])
                    ->withInput();
            }
        }

        Experience::create([
            'user_id'       => auth()->id(),
            'type'          => 'work',
            'institution'   => $request->empresa,
            'title'         => $request->cargo,
            'location'      => $request->location,
            'description'   => $request->descripcion,
            'start_date'    => $fechaInicio,
            'end_date'      => $fechaFin,
            'is_current'    => $request->trabajo_actual ? true : false,
            'is_visible'    => true,
            'display_order' => 0,
        ]);

        return redirect()->route('experiencia.laboral')
            ->with('success', 'Experiencia laboral guardada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $experiencia = Experience::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('type', 'work')
            ->firstOrFail();

        $fechaInicio = $request->fecha_inicio_anio . '-'
                     . $request->fecha_inicio_mes  . '-'
                     . $request->fecha_inicio_dia;

        $fechaFin = null;
        if (!$request->trabajo_actual
            && $request->fecha_fin_dia
            && $request->fecha_fin_mes
            && $request->fecha_fin_anio
        ) {
            $fechaFin = $request->fecha_fin_anio . '-'
                      . $request->fecha_fin_mes   . '-'
                      . $request->fecha_fin_dia;

            if (strtotime($fechaFin) < strtotime($fechaInicio)) {
                return response()->json([
                    'error' => 'La fecha de fin no puede ser anterior a la fecha de inicio.'
                ], 422);
            }
        }

        $experiencia->update([
            'institution' => $request->empresa,
            'title'       => $request->cargo,
            'location'    => $request->location,
            'description' => $request->descripcion,
            'start_date'  => $fechaInicio,
            'end_date'    => $fechaFin,
            'is_current'  => $request->trabajo_actual ? true : false,
        ]);

        return response()->json($experiencia->fresh());
    }

    public function destroy($id)
    {
        $experiencia = Experience::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('type', 'work')
            ->firstOrFail();

        $experiencia->delete();

        return response()->json(['message' => 'Experiencia eliminada correctamente.']);
    }
}