<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Experience;
use Illuminate\Support\Facades\DB;

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

    // ── Patrones de caracteres permitidos ──────────────────────────────────
    private const LETRAS  = 'a-zA-ZáéíóúÁÉÍÓÚàèìòùÀÈÌÒÙäëïöüÄËÏÖÜñÑ';
    private const VOCALES = 'aeiouáéíóúàèìòùäëïöüAEIOUÁÉÍÓÚÀÈÌÒÙÄËÏÖÜ';

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

        if (!preg_match($patrones[$campo], $valor)) {
            return $mensajesCaracteres[$campo];
        }

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

    // ── Validación de campos comunes ───────────────────────────────────────
    private function validarCamposComunes(Request $request): array
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

        // Ubicación (opcional)
        if (!empty($request->location)) {
            if (strlen($request->location) < 2) {
                $errores['location'] = 'La ubicación debe tener al menos 2 caracteres.';
            } elseif (strlen($request->location) > 100) {
                $errores['location'] = 'La ubicación no puede superar los 100 caracteres.';
            } else {
                $error = $this->validarCampoTexto($request->location, 'location');
                if ($error) $errores['location'] = $error;
            }
        }

        // Descripción (opcional)
        if (!empty($request->descripcion)) {
            if (strlen($request->descripcion) < 10) {
                $errores['descripcion'] = 'La descripción debe tener al menos 10 caracteres.';
            } elseif (strlen($request->descripcion) > 500) {
                $errores['descripcion'] = 'La descripción no puede superar los 500 caracteres.';
            } else {
                $error = $this->validarCampoTexto($request->descripcion, 'descripcion');
                if ($error) $errores['descripcion'] = $error;
            }
        }

        // ── Fecha de inicio ───────────────────────────────────────────────
        // Acepta hiddens separados (dia/mes/anio) O campo completo YYYY-MM-DD
        $tieneParciales = !empty($request->fecha_inicio_dia)
                       && !empty($request->fecha_inicio_mes)
                       && !empty($request->fecha_inicio_anio);
        $tieneCompleto  = !empty($request->fecha_inicio);

        if (!$tieneParciales && !$tieneCompleto) {
            $errores['fecha_inicio_dia'] = 'La fecha de inicio es obligatoria.';
        } else {
            $anioInicio = $tieneParciales
                ? (int) $request->fecha_inicio_anio
                : (int) substr($request->fecha_inicio, 0, 4);

            if ($anioInicio < 1950 || $anioInicio > (int) date('Y')) {
                $errores['fecha_inicio_anio'] = 'El año de inicio debe estar entre 1950 y ' . date('Y') . '.';
            }
        }

        // ── Fecha de fin ──────────────────────────────────────────────────
        // Obligatoria si no es trabajo actual
        if (!$request->trabajo_actual) {
            $tieneFinParcial  = !empty($request->fecha_fin_dia)
                             && !empty($request->fecha_fin_mes)
                             && !empty($request->fecha_fin_anio);
            $tieneFinCompleto = !empty($request->fecha_fin);

            if (!$tieneFinParcial && !$tieneFinCompleto) {
                $errores['fecha_fin_dia'] = 'Selecciona la fecha de fin o marca "Trabajo actual".';
            } elseif ($tieneFinParcial) {
                $anioFin = (int) $request->fecha_fin_anio;
                if ($anioFin < 1950 || $anioFin > (int) date('Y')) {
                    $errores['fecha_fin_anio'] = 'El año de fin debe estar entre 1950 y ' . date('Y') . '.';
                }
            } elseif ($tieneFinCompleto) {
                $anioFin = (int) substr($request->fecha_fin, 0, 4);
                if ($anioFin < 1950 || $anioFin > (int) date('Y')) {
                    $errores['fecha_fin_anio'] = 'El año de fin debe estar entre 1950 y ' . date('Y') . '.';
                }
            }
        }

        return $errores;
    }

    // ── Validación de la lista de cargos (HU-12) ──────────────────────────
    private function validarCargos(Request $request): array
    {
        $errores = [];
        $cargos  = $request->input('cargos', []);

        if (empty($cargos)) {
            $errores['cargos'] = 'Debe registrar al menos un cargo.';
            return $errores;
        }

        if (count($cargos) > 5) {
            $errores['cargos'] = 'No se pueden registrar más de 5 cargos por experiencia.';
            return $errores;
        }

        $vistos = [];
        foreach ($cargos as $i => $cargo) {
            $cargo = trim($cargo ?? '');

            if ($cargo === '') {
                $errores["cargos.{$i}"] = 'El cargo #' . ($i + 1) . ' no puede estar vacío.';
                continue;
            }
            if (strlen($cargo) < 2) {
                $errores["cargos.{$i}"] = 'El cargo #' . ($i + 1) . ' debe tener al menos 2 caracteres.';
                continue;
            }
            if (strlen($cargo) > 100) {
                $errores["cargos.{$i}"] = 'El cargo #' . ($i + 1) . ' no puede superar los 100 caracteres.';
                continue;
            }

            $textoError = $this->validarCampoTexto($cargo, 'cargo');
            if ($textoError) {
                $errores["cargos.{$i}"] = 'Cargo #' . ($i + 1) . ': ' . $textoError;
                continue;
            }

            $lower = mb_strtolower($cargo);
            if (in_array($lower, $vistos, true)) {
                $errores["cargos.{$i}"] = 'El cargo "' . $cargo . '" está duplicado.';
                continue;
            }
            $vistos[] = $lower;
        }

        return $errores;
    }

    /**
     * Construye las fechas aceptando dos formatos:
     *  A) Campos separados: fecha_inicio_dia / mes / anio  (hiddens blade)
     *  B) Campo completo:   fecha_inicio = 'YYYY-MM-DD'    (fallback date picker)
     */
    private function construirFechas(Request $request): array
    {
        // Fecha inicio
        if (!empty($request->fecha_inicio_anio) && !empty($request->fecha_inicio_mes) && !empty($request->fecha_inicio_dia)) {
            $fechaInicio = $request->fecha_inicio_anio . '-'
                         . str_pad($request->fecha_inicio_mes, 2, '0', STR_PAD_LEFT) . '-'
                         . str_pad($request->fecha_inicio_dia, 2, '0', STR_PAD_LEFT);
        } elseif (!empty($request->fecha_inicio)) {
            $fechaInicio = $request->fecha_inicio;
        } else {
            $fechaInicio = null;
        }

        // Fecha fin
        $fechaFin = null;
        if (!$request->trabajo_actual) {
            if (!empty($request->fecha_fin_dia) && !empty($request->fecha_fin_mes) && !empty($request->fecha_fin_anio)) {
                $fechaFin = $request->fecha_fin_anio . '-'
                          . str_pad($request->fecha_fin_mes, 2, '0', STR_PAD_LEFT) . '-'
                          . str_pad($request->fecha_fin_dia, 2, '0', STR_PAD_LEFT);
            } elseif (!empty($request->fecha_fin)) {
                $fechaFin = $request->fecha_fin;
            }
        }

        return [$fechaInicio, $fechaFin];
    }

    // ══════════════════════════════════════════════════════════════════════
    //  STORE  —  guarda UNA fila por cargo en transacción (HU-12)
    //  Acepta form POST (blade) y JSON (edición React)
    // ══════════════════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $isJson = $request->expectsJson() || $request->isJson();

        $errores       = $this->validarCamposComunes($request);
        $erroresCargos = $this->validarCargos($request);
        $errores       = array_merge($errores, $erroresCargos);

        if (!empty($errores)) {
            return $isJson
                ? response()->json(['errors' => $errores], 422)
                : back()->withErrors($errores)->withInput();
        }

        [$fechaInicio, $fechaFin] = $this->construirFechas($request);

        if ($fechaFin && strtotime($fechaFin) < strtotime($fechaInicio)) {
            $msg = ['fecha_fin_dia' => 'La fecha de fin no puede ser anterior a la fecha de inicio.'];
            return $isJson
                ? response()->json(['errors' => $msg], 422)
                : back()->withErrors($msg)->withInput();
        }

        $cargos  = array_filter(array_map('trim', $request->input('cargos', [])));
        $creados = [];

        DB::transaction(function () use ($request, $cargos, $fechaInicio, $fechaFin, &$creados) {
            foreach ($cargos as $cargo) {
                $creados[] = Experience::create([
                    'user_id'       => auth()->id(),
                    'type'          => 'work',
                    'institution'   => $request->empresa,
                    'title'         => $cargo,
                    'location'      => $request->location,
                    'description'   => $request->descripcion,
                    'start_date'    => $fechaInicio,
                    'end_date'      => $fechaFin,
                    'is_current'    => $request->trabajo_actual ? true : false,
                    'is_visible'    => true,
                    'display_order' => 0,
                ]);
            }
        });

        return $isJson
            ? response()->json($creados, 201)
            : redirect()->route('experiencia.laboral')->with('success', 'Experiencia laboral guardada correctamente.');
    }

    // ══════════════════════════════════════════════════════════════════════
    //  UPDATE  —  edita un registro individual
    // ══════════════════════════════════════════════════════════════════════
    public function update(Request $request, $id)
    {
        $experiencia = Experience::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('type', 'work')
            ->firstOrFail();

        if (!$request->has('cargos')) {
            $request->merge(['cargos' => [$request->input('cargo', '')]]);
        }

        $errores       = $this->validarCamposComunes($request);
        $erroresCargos = $this->validarCargos($request);
        $errores       = array_merge($errores, $erroresCargos);

        if (!empty($errores)) {
            return response()->json(['errors' => $errores], 422);
        }

        [$fechaInicio, $fechaFin] = $this->construirFechas($request);

        if ($fechaFin && strtotime($fechaFin) < strtotime($fechaInicio)) {
            return response()->json([
                'error' => 'La fecha de fin no puede ser anterior a la fecha de inicio.'
            ], 422);
        }

        $cargo = trim($request->input('cargos.0') ?? $request->input('cargo', ''));

        $experiencia->update([
            'institution' => $request->empresa,
            'title'       => $cargo,
            'location'    => $request->location,
            'description' => $request->descripcion,
            'start_date'  => $fechaInicio,
            'end_date'    => $fechaFin,
            'is_current'  => $request->trabajo_actual ? true : false,
        ]);

        return response()->json($experiencia->fresh());
    }

    // ══════════════════════════════════════════════════════════════════════
    //  DESTROY
    //  ?grupo=1  → elimina todo el grupo (botón "Eliminar experiencia")
    //  sin param → elimina solo ese cargo  (edición individual)
    // ══════════════════════════════════════════════════════════════════════
    public function destroy($id, Request $request)
    {
        $experiencia = Experience::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('type', 'work')
            ->firstOrFail();

        if ($request->query('grupo') === '1') {
            Experience::where('user_id', auth()->id())
                ->where('type', 'work')
                ->where('institution', $experiencia->institution)
                ->where('start_date',  $experiencia->start_date)
                ->delete();
            return response()->json(['message' => 'Experiencia y todos sus cargos eliminados correctamente.']);
        }

        $experiencia->delete();
        return response()->json(['message' => 'Cargo eliminado correctamente.']);
    }
}