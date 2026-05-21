<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Experience;
use Carbon\Carbon;

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
            'institucion' => 'required|string|max:60|regex:/^(?!.*[^aeiouáéíóúAEIOUÁÉÍÓÚ]{6,})[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'titulo_obtenido' => 'nullable|string|max:30|regex:/^(?!.*[^aeiouáéíóúAEIOUÁÉÍÓÚ]{6,})[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'fecha_inicio'    => 'required|date_format:Y-m-d|before_or_equal:today',
            'fecha_fin'       => 'nullable|date_format:Y-m-d|after:fecha_inicio',
            'descripcion'     => 'nullable|string|max:500|regex:/^(?!.*[^aeiouáéíóúAEIOUÁÉÍÓÚ]{6,}).+$/u',
            'estudio_actual'  => 'nullable',
            'especialidad'    => 'nullable|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'tipo_formacion' => 'required|string|max:50',
            'evidencias.*'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

         ], [
            'institucion.required'     => 'La institución es obligatoria',
            'institucion.max'          => 'La institución no puede tener más de 60 caracteres',
            'institucion.regex'        => 'La institución no parece ser un nombre válido',
            'titulo_obtenido.required' => 'El título es obligatorio',
            'titulo_obtenido.max'      => 'El título no puede tener más de 30 caracteres',
            'titulo_obtenido.regex'    => 'El título no parece ser un nombre valido',
            'fecha_inicio.required'    => 'La fecha de inicio es obligatoria',
            'fecha_inicio.date_format' => 'La fecha de inicio debe tener formato Año-Mes (ej: 2024-01)',
            'fecha_inicio.before_or_equal' => 'La fecha de inicio no puede ser futura',
            'fecha_fin.after'          => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'fecha_fin.date_format'    => 'La fecha de fin debe tener formato Año-Mes (ej: 2024-12)',
            'especialidad.max'         => 'La especialidad no puede tener más de 30 caracteres',
            'especialidad.regex'       => 'La especialidad solo debe contener letras',
            'evidencias.*.mimes'           => 'Solo se permiten archivos JPG, PNG o PDF',
            'evidencias.*.max'             => 'Cada archivo no puede superar los 2MB',
         ]);

         if (!$request->has('estudio_actual') && empty($request->fecha_fin)) {
            return back()
                ->withErrors(['fecha_fin' => 'Debes indicar una fecha de fin o marcar "Estudio actual"'])
                ->withInput();
        }

        // Normalizar fechas
        $startDate = Carbon::createFromFormat('Y-m-d', $request->fecha_inicio)->startOfMonth();
        
        $endDate = null;
        if (!$request->has('estudio_actual') && $request->fecha_fin) {
            $endDate = Carbon::createFromFormat('Y-m-d', $request->fecha_fin)->endOfMonth();
        }

        $evidenciasUrls = [];
        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $archivo) {
                $path = $archivo->store('evidencias', 'public');
                $evidenciasUrls[] = $path;
            }
        }

        Experience::create([
            'user_id'     => auth()->id(),
            'type'        => 'education',
            'institution' => $request->institucion,
            'title'       => $request->titulo_obtenido,
            'specialty'    => $request->especialidad,  
            'formation_type' => $request->tipo_formacion,
            'description' => $request->descripcion,
            'evidence_url' => !empty($evidenciasUrls) ? json_encode($evidenciasUrls) : null,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'is_current'  => $request->has('estudio_actual'),
            'is_visible'  => true,
        ]);

        return redirect()->route('informacion.academica')
            ->with('success', 'Formación académica guardada correctamente');
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'institucion' => 'required|string|max:60|regex:/^(?!.*[^aeiouáéíóúAEIOUÁÉÍÓÚ]{6,})[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'titulo_obtenido' => 'required|string|max:30|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'especialidad' => 'nullable|string|max:50', 
            'tipo_formacion' => 'required|string|max:50',

            'fecha_inicio'    => 'required|date_format:Y-m-d|before_or_equal:today',
            'fecha_fin'       => 'nullable|date_format:Y-m-d|after:fecha_inicio',
            'descripcion'     => 'nullable|string|max:500',
            'estudio_actual'  => 'nullable',
            'evidencias.*'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'evidencias_eliminar' => 'nullable|string',
            'evidencias.*.mimes' => 'Solo se permiten archivos JPG, PNG o PDF',
            'evidencias.*.max'   => 'Cada archivo no puede superar los 2MB',
        ]);
        $formacion = Experience::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

            $startDate = Carbon::createFromFormat('Y-m-d', $request->fecha_inicio);
        
            $endDate = null;
            if (!$request->has('estudio_actual') && $request->fecha_fin) {
            $endDate = Carbon::parse($request->fecha_fin);   
            }   

            $evidenciasActuales = $formacion->evidence_url
            ? json_decode($formacion->evidence_url, true)
            : [];
 
        // Eliminar las que se marcaron para borrar
        if ($request->evidencias_eliminar) {
            $aEliminar = explode(',', $request->evidencias_eliminar);
            foreach ($aEliminar as $url) {
                \Storage::disk('public')->delete(trim($url));
                $evidenciasActuales = array_filter($evidenciasActuales, fn($e) => $e !== trim($url));
            }
        }
 
        // Agregar nuevas evidencias
        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $archivo) {
                $path = $archivo->store('evidencias', 'public');
                $evidenciasActuales[] = $path;
            }
        }
 
        $evidenciasActuales = array_values($evidenciasActuales);

        $formacion->update([
            'institution' => $request->institucion,
            'title'       => $request->titulo_obtenido,
            'specialty'    => $request->especialidad,  
            'formation_type' => $request->tipo_formacion,
            'description' => $request->descripcion,
            'evidence_url' => !empty($evidenciasActuales) ? json_encode($evidenciasActuales) : null,
            'start_date'  => $startDate,
            'end_date'    => $endDate, 
            'is_current'  => $request->estudio_actual ? true : false,
        ]);

        return response()->json($formacion->fresh());
    }

    public function destroy($id)
    {
        $formacion = Experience::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

            if ($formacion->evidence_url) {
                $urls = json_decode($formacion->evidence_url, true);
                foreach ($urls as $url) {
                    \Storage::disk('public')->delete($url);
                }
            }
     

        $formacion->delete();

        return response()->json(['success' => true]);
    }
}