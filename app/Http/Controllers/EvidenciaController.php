<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectEvidencia;

class EvidenciaController extends Controller
{
    // GET /proyectos/{id}/evidencias
    public function index($proyectoId)
    {
        $evidencias = ProjectEvidencia::where('project_id', $proyectoId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($e) => $this->formato($e));

        return response()->json($evidencias);
    }

    // POST /proyectos/{id}/evidencias
    public function store(Request $request, $proyectoId)
    {
        $tipo = $request->input('tipo');

        // ── IMAGEN ───────────────────────────────────────────
        if ($tipo === 'imagen') {
            $request->validate([
                'imagenes'   => 'required|array|min:1',
                'imagenes.*' => 'image|mimes:jpg,jpeg,png|max:5120',
                'nombre'     => 'required|string|max:200',
            ]);

            $guardadas = [];
            foreach ($request->file('imagenes') as $file) {
                $path = $file->store('evidencias', 'public');
                $ev = ProjectEvidencia::create([
                    'project_id'     => $proyectoId,
                    'tipo'           => 'imagen',
                    'titulo'         => $request->input('nombre'),
                    'imagen_path'    => $path,
                    'url'            => null,
                ]);
                $guardadas[] = $this->formato($ev);
            }
            return response()->json(['evidencias' => $guardadas], 201);
        }

        // ── ENLACE ───────────────────────────────────────────
        if ($tipo === 'enlace') {
            $request->validate([
                'etiqueta'    => 'required|string|max:150',
                'url'         => 'required|url|max:500',
                'descripcion' => 'nullable|string|max:200',
            ]);

            $ev = ProjectEvidencia::create([
                'project_id' => $proyectoId,
                'tipo'       => 'enlace',
                'titulo'     => $request->input('etiqueta'),
                'url'        => $request->input('url'),
                'imagen_path'=> null,
            ]);
            return response()->json(['evidencia' => $this->formato($ev)], 201);
        }

        // ── REPOSITORIO ──────────────────────────────────────
        if ($tipo === 'repositorio') {
            $request->validate([
                'etiqueta'    => 'required|string|max:150',
                'url'         => 'required|url|max:500',
                'plataforma'  => 'nullable|string|max:50',
                'descripcion' => 'nullable|string|max:200',
            ]);

            $ev = ProjectEvidencia::create([
                'project_id' => $proyectoId,
                'tipo'       => 'repositorio',
                'titulo'     => $request->input('etiqueta'),
                'url'        => $request->input('url'),
                'imagen_path'=> null,
            ]);
            return response()->json(['evidencia' => $this->formato($ev)], 201);
        }

        return response()->json(['message' => 'Tipo no válido'], 422);
    }

    // DELETE /evidencias/{id}
    public function destroy($id)
    {
        $ev = ProjectEvidencia::findOrFail($id);

        // Eliminar archivo físico si es imagen
        if ($ev->imagen_path) {
            \Storage::disk('public')->delete($ev->imagen_path);
        }

        $ev->delete();
        return response()->json(['ok' => true]);
    }

    // ── Formato de respuesta compatible con evidencia.blade.php ──
    private function formato(ProjectEvidencia $ev): array
    {
        return [
            'id'             => $ev->id,
            'tipo'           => $ev->tipo,
            'etiqueta'       => $ev->titulo,
            'archivo_nombre' => $ev->imagen_path ? basename($ev->imagen_path) : null,
            'url_publica'    => $ev->imagen_path
                                    ? asset('storage/' . $ev->imagen_path)
                                    : $ev->url,
            'descripcion'    => null,
            'plataforma'     => null,
            'created_at'     => $ev->created_at,
        ];
    }
}