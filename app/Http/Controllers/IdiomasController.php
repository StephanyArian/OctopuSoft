<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\Skill;
 
class IdiomasController extends Controller
{
    public function index()
    {
        $idiomas = Skill::where('user_id', auth()->id())
            ->where('type', 'language')
            ->orderBy('display_order')
            ->get();
 
        return view('idiomas', compact('idiomas'));
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|min:3|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'nivel'     => 'required|string|in:A1,A2,B1,B2,C1,C2,Nativo',
            'evidencia' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'nombre.required' => 'El nombre del idioma es obligatorio',
            'nombre.regex'    => 'El nombre solo debe contener letras',
            'nombre.max'      => 'El nombre no puede tener más de 100 caracteres',
            'nivel.required'  => 'El nivel es obligatorio',
            'nivel.in'        => 'El nivel no es válido',
            'evidencia.mimes' => 'Solo se permiten archivos JPG, PNG o PDF',
            'evidencia.max'   => 'El archivo no puede superar los 2MB',
        ]);

        $idiomasValidos = [
            'español', 'inglés', 'ingles', 'francés', 'frances', 'portugués', 'portugues',
            'alemán', 'aleman', 'italiano', 'chino', 'japonés', 'japones', 'coreano',
            'árabe', 'arabe', 'ruso', 'hindi', 'turco', 'holandés', 'holandes',
            'sueco', 'noruego', 'danés', 'danes', 'finlandés', 'finlandes', 'polaco',
            'checo', 'húngaro', 'hungaro', 'rumano', 'griego', 'hebreo', 'tailandés',
            'tailandes', 'vietnamita', 'indonesio', 'malayo', 'swahili', 'catalán',
            'catalan', 'euskera', 'gallego', 'ucraniano', 'bengalí', 'bengali',
            'urdu', 'persa', 'farsi', 'punjabi', 'tamil', 'telugu', 'marathi',
            'quechua', 'aymara', 'guaraní', 'guarani', 'latin', 'latín',
            'mandarín', 'mandarin', 'cantonés', 'cantones', 'shanghainés',
        ];
        
        $nombreNorm = strtolower(trim($request->nombre));
        
        if (!in_array($nombreNorm, $idiomasValidos)) {
            return back()
                ->withErrors(['nombre' => 'Ingresa un idioma válido (ej. Inglés, Francés, Alemán...)'])
                ->withInput();
        }

        

 
        // Verificar duplicado
        $existe = Skill::where('user_id', auth()->id())
            ->where('type', 'language')
            ->whereRaw('LOWER(name) = ?', [strtolower($request->nombre)])
            ->exists();
 
        if ($existe) {
            return back()
                ->withErrors(['nombre' => 'Ya tienes este idioma registrado'])
                ->withInput();
        }
 
        // Subir evidencia
        $evidenciaUrl = null;
        if ($request->hasFile('evidencia')) {
            $evidenciaUrl = $request->file('evidencia')->store('idiomas', 'public');
        }
 
        // Nivel a número para display_order
        $niveles = ['A1' => 1, 'A2' => 2, 'B1' => 3, 'B2' => 4, 'C1' => 5, 'C2' => 6, 'Nativo' => 7];
 
        Skill::create([
            'user_id'       => auth()->id(),
            'type'          => 'language',
            'name'          => $request->nombre,
            'level'         => $niveles[$request->nivel] ?? 1,
            'evidence_url'  => $evidenciaUrl,
            'is_visible'    => true,
            'display_order' => 0,
        ]);
 
        return redirect()->route('idiomas.index')
            ->with('success', 'Idioma guardado correctamente');
    }
 
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|min:3|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'nivel'     => 'required|string|in:A1,A2,B1,B2,C1,C2,Nativo',
            'evidencia' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'nombre.required' => 'El nombre del idioma es obligatorio',
            'nombre.regex'    => 'El nombre solo debe contener letras',
            'nivel.required'  => 'El nivel es obligatorio',
            'evidencia.mimes' => 'Solo se permiten archivos JPG, PNG o PDF',
            'evidencia.max'   => 'El archivo no puede superar los 2MB',
        ]);

        $idiomasValidos = [
            'español', 'inglés', 'ingles', 'francés', 'frances', 'portugués', 'portugues',
            'alemán', 'aleman', 'italiano', 'chino', 'japonés', 'japones', 'coreano',
            'árabe', 'arabe', 'ruso', 'hindi', 'turco', 'holandés', 'holandes',
            'sueco', 'noruego', 'danés', 'danes', 'finlandés', 'finlandes', 'polaco',
            'checo', 'húngaro', 'hungaro', 'rumano', 'griego', 'hebreo', 'tailandés',
            'tailandes', 'vietnamita', 'indonesio', 'malayo', 'swahili', 'catalán',
            'catalan', 'euskera', 'gallego', 'ucraniano', 'bengalí', 'bengali',
            'urdu', 'persa', 'farsi', 'punjabi', 'tamil', 'telugu', 'marathi',
            'quechua', 'aymara', 'guaraní', 'guarani', 'latin', 'latín',
            'mandarín', 'mandarin', 'cantonés', 'cantones', 'shanghainés',
        ];
        
        $nombreNorm = strtolower(trim($request->nombre));
        
        if (!in_array($nombreNorm, $idiomasValidos)) {
            return back()
                ->withErrors(['nombre' => 'Ingresa un idioma válido (ej. Inglés, Francés, Alemán...)'])
                ->withInput();
        }
                
        $idioma = Skill::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('type', 'language')
            ->firstOrFail();
 
        $niveles = ['A1' => 1, 'A2' => 2, 'B1' => 3, 'B2' => 4, 'C1' => 5, 'C2' => 6, 'Nativo' => 7];
 
        // Manejar evidencia
        $evidenciaUrl = $idioma->evidence_url;
 
        // Eliminar evidencia si se pidió
        if ($request->eliminar_evidencia == '1' && $evidenciaUrl) {
            \Storage::disk('public')->delete($evidenciaUrl);
            $evidenciaUrl = null;
        }
 
        // Subir nueva evidencia
        if ($request->hasFile('evidencia')) {
            if ($evidenciaUrl) {
                \Storage::disk('public')->delete($evidenciaUrl);
            }
            $evidenciaUrl = $request->file('evidencia')->store('idiomas', 'public');
        }
 
        $idioma->update([
            'name'         => $request->nombre,
            'level'        => $niveles[$request->nivel] ?? 1,
            'evidence_url' => $evidenciaUrl,
        ]);
 
        return redirect()->route('idiomas.index')
            ->with('success', 'Idioma actualizado correctamente');
    }
 
    public function destroy($id)
    {
        $idioma = Skill::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('type', 'language')
            ->firstOrFail();
 
        if ($idioma->evidence_url) {
            \Storage::disk('public')->delete($idioma->evidence_url);
        }
 
        $idioma->delete();
 
        return redirect()->route('idiomas.index')
            ->with('success', 'Idioma eliminado correctamente');
    }
}