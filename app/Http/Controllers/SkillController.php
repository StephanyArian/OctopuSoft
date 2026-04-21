<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    private const MAX_SKILLS = 20;

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
        $type = $request->input('type'); // 'technical' o 'soft'

        // Validación base
        $rules = [
            'type'  => 'required|in:technical,soft',
            'name'  => 'required|string|max:150',
        ];

        if ($type === 'technical') {
            $rules['level'] = 'required|integer|min:1|max:3';
        }

        $request->validate($rules, [
            'name.required' => 'El nombre de la habilidad es obligatorio.',
        ]);

        $user = Auth::user();

        // Límite máximo
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
            'name' => 'required|string|max:150',
        ];

        if ($type === 'technical') {
            $rules['level'] = 'required|integer|min:1|max:3';
        }

        $request->validate($rules, [
            'name.required' => 'El nombre de la habilidad es obligatorio.',
        ]);

        // Duplicado excluyendo la misma habilidad
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