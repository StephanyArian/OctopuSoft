<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    private const MAX_SKILLS = 20;

    private function validateSkillName(string $name, string $type): ?string
    {
        $trimmed = trim($name);

        if (preg_match('/\d{6,}/', $trimmed)) {
            return 'El nombre no puede contener secuencias numéricas largas.';
        }

        $digits  = preg_replace('/[^0-9]/', '', $trimmed);
        $letters = preg_replace('/[^a-zA-Z\pL]/u', '', $trimmed);
        if (strlen($letters) > 0 && strlen($digits) / mb_strlen($trimmed) > 0.4) {
            return 'El nombre contiene demasiados números para ser una habilidad válida.';
        }

        if (preg_match('/(.)\1{4}/u', $trimmed)) {
            return 'El nombre contiene caracteres repetidos en exceso.';
        }

        //Grupos de consonantes antinaturales (4 o más seguidas)
        // Lista para tecnologías conocidas con grupos inusuales
        $allowedExceptions = [
            'postgresql', 'django', 'symfony', 'pthreads',
            'strength', 'zxing', 'rxjava', 'rxswift', 'struts',
        ];
        $nameLower   = mb_strtolower($trimmed);
        $isException = in_array($nameLower, $allowedExceptions);

        if (!$isException) {
            $onlyAlpha       = preg_replace('/[^a-zA-Z]/u', '', $trimmed);
            $consonantGroups = preg_match_all(
                '/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]{4,}/',
                $onlyAlpha
            );
            if ($consonantGroups > 0) {
                return $type === 'technical'
                    ? 'El nombre no parece una tecnología válida (grupos de consonantes inusuales).'
                    : 'El nombre no parece una habilidad válida (grupos de consonantes inusuales).';
            }
        }

        // Proporción mínima de vocales (aplica si tiene más de 3 letras)
        // Permite siglas cortas válidas como CSS, PHP, SQL, CI/CD
        $onlyLetters = preg_replace('/[^a-zA-Z\pL]/u', '', $trimmed);
        $letterCount = mb_strlen($onlyLetters);

        if ($letterCount > 3) {
            $vowelCount = preg_match_all('/[aeiouáéíóúAEIOUÁÉÍÓÚ]/u', $onlyLetters);
            $vowelRatio = $vowelCount / $letterCount;

            if ($vowelRatio < 0.18) {
                return $type === 'technical'
                    ? 'El nombre no parece una tecnología válida (muy pocas vocales).'
                    : 'El nombre no parece una habilidad válida (muy pocas vocales).';
            }
        }


        if ($type === 'technical') {
            if (!preg_match('/^[\pL\pN\s\.\+\#\/\-\_]+$/u', $trimmed)) {
                return 'Solo se permiten letras, números y los símbolos: . + # / - _';
            }
        } else {
            if (!preg_match('/^[\pL\s\-\.]+$/u', $trimmed)) {
                return 'Solo se permiten letras, espacios, guiones y puntos.';
            }
        }

        return null; 
    }

    //  Vistas

    public function tecnicas()
    {
        $skills = Auth::user()->skills()
            ->where('type', 'technical')
            ->orderBy('display_order')
            ->get();

        return view('habilidades-tecnicas', compact('skills'));
    }

    public function blandas()
    {
        $skills = Auth::user()->skills()
            ->where('type', 'soft')
            ->orderBy('display_order')
            ->get();

        return view('habilidades-blandas', compact('skills'));
    }


    public function store(Request $request)
    {
        $type = $request->input('type');

        $rules = [
           'type' => 'required|in:technical,soft',
           'name' => 'required|string|min:2|max:50',
        ];

        if ($type === 'technical') {
            $rules['level'] = 'required|integer|min:1|max:3';
        }

        $request->validate($rules, [
            'name.required' => 'El nombre de la habilidad es obligatorio.',
            'name.min'      => 'El nombre debe tener al menos :min caracteres.',
            'name.max'      => 'El nombre no puede superar los :max caracteres.',
        ]);

        $error = $this->validateSkillName($request->name, $type);
        if ($error) {
            return back()
                ->withErrors(['name' => $error])
                ->withInput();
        }

        $user = Auth::user();

        $count = $user->skills()->where('type', $type)->count();
        if ($count >= self::MAX_SKILLS) {
            return back()
                ->with('error_limit', 'Has alcanzado el límite máximo de 20 habilidades ' . ($type === 'technical' ? 'técnicas' : 'blandas') . '.')
                ->withInput();
        }

        // Duplicado
        $exists = $user->skills()
            ->where('type', $type)
            ->whereRaw('LOWER(name) = ?', [strtolower($request->name)])
            ->exists();

        if ($exists) {
            return back()
                ->with('error_duplicate', 'Ya tienes esta habilidad registrada.')
                ->withInput();
        }

        $user->skills()->create([
            'type'          => $type,
            'name'          => $request->name,
            'level'         => $type === 'technical' ? $request->level : 1,
            'display_order' => $user->skills()->where('type', $type)->count(),
        ]);

        $route = $type === 'technical' ? 'skills.tecnicas' : 'skills.blandas';
        return redirect()->route($route)->with('success', 'Habilidad guardada correctamente.');
    }


    public function update(Request $request, Skill $skill)
    {
        $this->authorize('update', $skill);

        $type = $skill->type;
        $rules = [
            'name' => 'required|string|min:2|max:50',
        ];

        if ($type === 'technical') {
            $rules['level'] = 'required|integer|min:1|max:3';
        }

        $request->validate($rules, [
            'name.required' => 'El nombre de la habilidad es obligatorio.',
            'name.min'      => 'El nombre debe tener al menos :min caracteres.',
            'name.max'      => 'El nombre no puede superar los :max caracteres.',
        ]);

        $exists = Auth::user()->skills()
            ->where('type', $type)
            ->whereRaw('LOWER(name) = ?', [strtolower($request->name)])
            ->where('id', '!=', $skill->id)
            ->exists();

        if ($exists) {
            return back()
                ->with('error_duplicate', 'Ya tienes esta habilidad registrada.')
                ->withInput();
        }

        $skill->update([
            'name'  => $request->name,
            'level' => $type === 'technical' ? $request->level : 1,
        ]);

        $route = $type === 'technical' ? 'skills.tecnicas' : 'skills.blandas';
        return redirect()->route($route)->with('success', 'Habilidad actualizada correctamente.');
    }


    public function destroy(Skill $skill)
    {
        $this->authorize('delete', $skill);

        $type = $skill->type;
        $skill->delete();

        $route = $type === 'technical' ? 'skills.tecnicas' : 'skills.blandas';
        return redirect()->route($route)->with('success', 'Habilidad eliminada correctamente.');
    }
}