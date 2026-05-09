<?php
// app/Http/Controllers/ProyectoController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Portfolio;
use App\Models\Technology;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProyectoController extends Controller
{
    private function obtenerPortfolio(): Portfolio
    {
        return Portfolio::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'slug'      => 'portfolio-' . Auth::id(),
                'title'     => 'Mi Portafolio',
                'is_public' => true,
                'show_email'=> false,
                'show_phone'=> false,
            ]
        );
    }

    public function index()
    {
        try {
            $portfolio = $this->obtenerPortfolio();
            $proyectos = Project::with('technologies', 'evidencias')
                ->where('portfolio_id', $portfolio->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($p) => $this->formato($p));
            return response()->json($proyectos);
        } catch(\Exception $e) {
            Log::error('Error al cargar proyectos: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar proyectos'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre'       => 'required|string|max:255',
                'descripcion'  => 'required|string|max:5000',
                'fecha'        => 'nullable|date',
                'fecha_fin'    => 'nullable|date',
                'estado'       => 'nullable|string|max:50',
                'rol'          => 'nullable|string|max:150',
                'cliente'      => 'nullable|string|max:255',
                'visibilidad'  => 'nullable|in:publico,privado',
                'tecnologias'  => 'nullable|array',
                'tecnologias.*'=> 'string|max:100',
            ]);

            $portfolio = $this->obtenerPortfolio();

            $proyecto = Project::create([
                'portfolio_id' => $portfolio->id,
                'name'         => $request->input('nombre'),
                'description'  => $request->input('descripcion'),
                'start_date'   => $request->input('fecha') ?: null,
                'end_date'     => $request->input('fecha_fin') ?: null,
                'status'       => $request->input('estado', 'En curso'),
                'role'         => $request->input('rol'),
                'company'      => $request->input('cliente'),
                'is_visible'   => $request->input('visibilidad') === 'publico',
            ]);

            $this->sincronizarTecnologias($proyecto, $request->input('tecnologias', []));
            $proyecto->load('technologies', 'evidencias');

            return response()->json($this->formato($proyecto), 201);
        } catch (\Exception $e) {
            Log::error('Error al crear proyecto: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear proyecto: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $portfolio = $this->obtenerPortfolio();
            $proyecto = Project::where('id', $id)
                ->where('portfolio_id', $portfolio->id)
                ->firstOrFail();

            $request->validate([
                'nombre'       => 'required|string|max:255',
                'descripcion'  => 'required|string|max:5000',
                'fecha'        => 'nullable|date',
                'fecha_fin'    => 'nullable|date',
                'estado'       => 'nullable|string|max:50',
                'rol'          => 'nullable|string|max:150',
                'cliente'      => 'nullable|string|max:255',
                'visibilidad'  => 'nullable|in:publico,privado',
                'tecnologias'  => 'nullable|array',
                'tecnologias.*'=> 'string|max:100',
            ]);

            $proyecto->update([
                'name'        => $request->input('nombre'),
                'description' => $request->input('descripcion'),
                'start_date'  => $request->input('fecha') ?: null,
                'end_date'    => $request->input('fecha_fin') ?: null,
                'status'      => $request->input('estado', 'En curso'),
                'role'        => $request->input('rol'),
                'company'     => $request->input('cliente'),
                'is_visible'  => $request->input('visibilidad') === 'publico',
            ]);

            $this->sincronizarTecnologias($proyecto, $request->input('tecnologias', []));
            $proyecto->load('technologies', 'evidencias');

            return response()->json($this->formato($proyecto));
        } catch (\Exception $e) {
            Log::error('Error al actualizar proyecto: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar proyecto'], 500);
        }
    }

    /**
     * Toggle rápido de visibilidad (público/privado) sin necesidad de editar.
     */
    public function toggleVisibilidad(Request $request, int $id)
    {
        try {
            $portfolio = $this->obtenerPortfolio();
            $proyecto = Project::where('id', $id)
                ->where('portfolio_id', $portfolio->id)
                ->firstOrFail();

            $request->validate([
                'is_visible' => 'required|boolean',
            ]);

            $proyecto->update([
                'is_visible' => $request->input('is_visible'),
            ]);

            return response()->json([
                'ok'         => true,
                'is_visible' => $proyecto->is_visible,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cambiar visibilidad: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cambiar visibilidad'], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $portfolio = $this->obtenerPortfolio();
            $proyecto = Project::where('id', $id)
                ->where('portfolio_id', $portfolio->id)
                ->firstOrFail();

            foreach ($proyecto->evidencias as $ev) {
                if ($ev->imagen_path) {
                    Storage::disk('public')->delete($ev->imagen_path);
                }
            }
            $proyecto->delete();
            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar proyecto: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar proyecto'], 500);
        }
    }

    private function sincronizarTecnologias(Project $proyecto, array $nombres): void
    {
        $ids = [];
        foreach ($nombres as $nombre) {
            $nombre = trim($nombre);
            if ($nombre === '') continue;
            $tec = Technology::firstOrCreate(['name' => $nombre]);
            $ids[] = $tec->id;
        }
        $proyecto->technologies()->sync($ids);
    }

    private function formato(Project $p): array
    {
        return [
            'id'          => $p->id,
            'nombre'      => $p->name,
            'descripcion' => $p->description,
            'fecha'       => $p->start_date?->format('Y-m-d'),
            'fecha_fin'   => $p->end_date?->format('Y-m-d'),
            'estado'      => $p->status,
            'rol'         => $p->role,
            'cliente'     => $p->company,
            'is_visible'  => $p->is_visible,
            'tecnologias' => $p->technologies->pluck('name')->toArray(),
            'evidencias'  => $p->evidencias->count(),
        ];
    }
}