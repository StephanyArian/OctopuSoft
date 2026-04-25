<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Portfolio;
use App\Models\Technology;

class ProyectoController extends Controller
{
    // Obtiene o crea el portfolio del usuario autenticado
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

    // GET /proyectos
    public function index()
    {
        $portfolio = $this->obtenerPortfolio();

        $proyectos = Project::with('technologies', 'evidencias')
            ->where('portfolio_id', $portfolio->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => $this->formato($p));

        return response()->json($proyectos);
    }

    // POST /proyectos
    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'required|string|max:500',
            'fecha'        => 'nullable|date',
            'estado'       => 'nullable|string|max:50',
            'tecnologias'  => 'nullable|array',
            'tecnologias.*'=> 'string|max:100',
        ]);

        $portfolio = $this->obtenerPortfolio();

        $proyecto = Project::create([
            'portfolio_id' => $portfolio->id,
            'name'         => $request->input('nombre'),
            'summary'      => null,
            'description'  => $request->input('descripcion'),
            'start_date'   => $request->input('fecha') ?: null,
            'status'       => $request->input('estado', 'En curso'),
            'is_visible'   => true,
            'is_featured'  => false,
        ]);

        // Sincronizar tecnologías
        $this->sincronizarTecnologias($proyecto, $request->input('tecnologias', []));

        $proyecto->load('technologies', 'evidencias');

        return response()->json($this->formato($proyecto), 201);
    }

    // PUT /proyectos/{id}
    public function update(Request $request, int $id)
    {
        $portfolio = $this->obtenerPortfolio();

        $proyecto = Project::where('id', $id)
            ->where('portfolio_id', $portfolio->id)
            ->firstOrFail();

        $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'required|string|max:500',
            'fecha'        => 'nullable|date',
            'estado'       => 'nullable|string|max:50',
            'tecnologias'  => 'nullable|array',
            'tecnologias.*'=> 'string|max:100',
        ]);

        $proyecto->update([
            'name'        => $request->input('nombre'),
            'description' => $request->input('descripcion'),
            'start_date'  => $request->input('fecha') ?: null,
            'status'      => $request->input('estado', 'En curso'),
        ]);

        $this->sincronizarTecnologias($proyecto, $request->input('tecnologias', []));

        $proyecto->load('technologies', 'evidencias');

        return response()->json($this->formato($proyecto));
    }

    // DELETE /proyectos/{id}
    public function destroy(int $id)
    {
        $portfolio = $this->obtenerPortfolio();

        $proyecto = Project::where('id', $id)
            ->where('portfolio_id', $portfolio->id)
            ->firstOrFail();

        // Eliminar imágenes físicas de las evidencias
        foreach ($proyecto->evidencias as $ev) {
            if ($ev->imagen_path) {
                \Storage::disk('public')->delete($ev->imagen_path);
            }
        }

        $proyecto->delete();

        return response()->json(['ok' => true]);
    }

    // ── Sincroniza tecnologías: busca o crea en tabla technologies y adjunta ──
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

    // ── Formato de respuesta para el frontend ──
    private function formato(Project $p): array
    {
        return [
            'id'          => $p->id,
            'nombre'      => $p->name,
            'descripcion' => $p->description,
            'fecha'       => $p->start_date?->format('Y-m-d'),
            'estado'      => $p->status,
            'tecnologias' => $p->technologies->pluck('name')->toArray(),
            'evidencias'  => $p->evidencias->count(),
        ];
    }
}