<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Experience;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

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

    // Patrón de caracteres permitidos por campo
    private const LETRAS       = 'a-zA-ZáéíóúÁÉÍÓÚàèìòùÀÈÌÒÙäëïöüÄËÏÖÜñÑ';
    private const VOCALES      = 'aeiouáéíóúàèìòùäëïöüAEIOUÁÉÍÓÚÀÈÌÒÙÄËÏÖÜ';

    private function validarCampoTexto(string $valor, string $campo): ?string
    {
        $letras  = self::LETRAS;
        $vocales = self::VOCALES;

        $patrones = [
            'empresa'     => "/^[{$letras}0-9\s\.\,\-\&]+$/",
            'cargo'       => "/^[{$letras}0-9\s\.\,\-\/]+$/",
            'location'    => "/^[{$letras}0-9\s\.\,\-\/\#\@\(\)°º\'\"]+$/",
            'descripcion' => "/^[{$letras}0-9\s\.\,\;\:\!\?\-\(\)\"\'\n\r]+$/",
        ];

        $mensajesCaracteres = [
            'empresa'     => 'El nombre de la empresa solo puede contener letras, números y los caracteres: . , - &',
            'cargo'       => 'El cargo solo puede contener letras, números y los caracteres: . , - /',
            'location'    => 'La ubicación solo puede contener letras, números y los caracteres: . , - / # @ ( ) ° º \' "',
            'descripcion' => 'La descripción solo puede contener letras, números y puntuación común ( . , ; : ! ? - ( ) " \' )',
        ];

        // Validar caracteres permitidos
        if (!preg_match($patrones[$campo], $valor)) {
            return $mensajesCaracteres[$campo];
        }

        // Validar que tenga al menos una vocal (evita texto sin sentido)
        if (!preg_match("/[{$vocales}]/", $valor)) {
            $nombres = [
                'empresa'     => 'El nombre de la empresa',
                'cargo'       => 'El cargo',
                'location'    => 'La ubicación',
                'descripcion' => 'La descripción',
            ];
            return $nombres[$campo] . ' no parece ser un texto válido. Asegúrate de escribir palabras reales.';
        }

        return null;
    }

    private function validarRequest(Request $request): array
    {
        $errores = [];

        // Empresa
        if (empty(trim($request->empresa))) {
            $errores['empresa'] = 'El nombre de la empresa es obligatorio.';
        } elseif (strlen($request->empresa) < 2) {
            $errores['empresa'] = 'El nombre de la empresa debe tener al menos 2 caracteres.';
        } elseif (strlen($request->empresa) > 100) {
            $errores['empresa'] = 'El nombre de la empresa no puede superar los 100 caracteres.';
        } else {
            $error = $this->validarCampoTexto($request->empresa, 'empresa');
            if ($error) $errores['empresa'] = $error;
        }

        // Cargo
        if (empty(trim($request->cargo))) {
            $errores['cargo'] = 'El cargo es obligatorio.';
        } elseif (strlen($request->cargo) < 2) {
            $errores['cargo'] = 'El cargo debe tener al menos 2 caracteres.';
        } elseif (strlen($request->cargo) > 100) {
            $errores['cargo'] = 'El cargo no puede superar los 100 caracteres.';
        } else {
            $error = $this->validarCampoTexto($request->cargo, 'cargo');
            if ($error) $errores['cargo'] = $error;
        }

        // Ubicación (opcional)
        if (!empty($request->location)) {
            if (strlen($request->location) < 2) {
                $errores['location'] = 'La ubicación debe tener al menos 2 caracteres.';
            } elseif (strlen($request->location) > 150) {
                $errores['location'] = 'La ubicación no puede superar los 150 caracteres.';
            } else {
                $error = $this->validarCampoTexto($request->location, 'location');
                if ($error) $errores['location'] = $error;
            }
        }

        // Descripción (opcional)
        if (!empty($request->descripcion)) {
            if (strlen($request->descripcion) < 10) {
                $errores['descripcion'] = 'La descripción debe tener al menos 10 caracteres.';
            } elseif (strlen($request->descripcion) > 1000) {
                $errores['descripcion'] = 'La descripción no puede superar los 1000 caracteres.';
            } else {
                $error = $this->validarCampoTexto($request->descripcion, 'descripcion');
                if ($error) $errores['descripcion'] = $error;
            }
        }

        // Fecha de inicio
        if (empty($request->fecha_inicio_dia)) {
            $errores['fecha_inicio_dia'] = 'El día de inicio es obligatorio.';
        }
        if (empty($request->fecha_inicio_mes)) {
            $errores['fecha_inicio_mes'] = 'El mes de inicio es obligatorio.';
        }
        if (empty($request->fecha_inicio_anio)) {
            $errores['fecha_inicio_anio'] = 'El año de inicio es obligatorio.';
        } elseif ($request->fecha_inicio_anio < 1950 || $request->fecha_inicio_anio > date('Y')) {
            $errores['fecha_inicio_anio'] = 'El año de inicio debe estar entre 1950 y ' . date('Y') . '.';
        }

        // Fecha de fin (solo si no es trabajo actual)
        if (!$request->trabajo_actual) {
            if (!empty($request->fecha_fin_anio)) {
                if ($request->fecha_fin_anio < 1950 || $request->fecha_fin_anio > date('Y')) {
                    $errores['fecha_fin_anio'] = 'El año de fin debe estar entre 1950 y ' . date('Y') . '.';
                }
            }
        }

        return $errores;
    }

    public function store(Request $request)
    {
        $errores = $this->validarRequest($request);
        if (!empty($errores)) {
            return back()->withErrors($errores)->withInput();
        }

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

        $errores = $this->validarRequest($request);
        if (!empty($errores)) {
            return response()->json(['errors' => $errores], 422);
        }

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