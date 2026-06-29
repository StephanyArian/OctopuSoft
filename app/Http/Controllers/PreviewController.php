<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Portfolio;
use App\Models\Profession;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class PreviewController extends Controller
{
    
 
 // VERIFICAR SI EL PORTAFOLIO TIENE CONTENIDO SUFICIENTE
 //REGLA: Necesita 3 de 5 secciones principales
 
 
    private function portafolioTieneContenido($user)
    {
        $userId = $user->id;

        // ==========================================
        // CAMPOS OBLIGATORIOS (SIEMPRE, sin excepción)
        // ==========================================
        $tieneNombre = DB::table('users')
            ->where('id', $userId)
            ->whereNotNull('first_name')
            ->where('first_name', '!=', '')
            ->exists();

        $tieneTitulo = DB::table('users')
            ->where('id', $userId)
            ->whereNotNull('profession_id')
            ->exists();

        $tieneBiografia = DB::table('users')
            ->where('id', $userId)
            ->whereNotNull('biography')
            ->where('biography', '!=', '')
            ->exists();

        $tieneCorreo = DB::table('users')
            ->where('id', $userId)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->exists();

        // Si falta CUALQUIERA de los 4 obligatorios, no se puede publicar
        if (!$tieneNombre || !$tieneTitulo || !$tieneBiografia || !$tieneCorreo) {
            return false;
        }

        // ==========================================
        // 4 SECCIONES ADICIONALES (necesita 3 de 4)
        // ==========================================
        $tieneExperiencia = DB::table('experiences')
            ->where('user_id', $userId)
            ->where('type', 'work')
            ->where('is_visible', true)
            ->exists();

        $tieneHabilidadTecnica = DB::table('skills')
            ->where('user_id', $userId)
            ->where('type', 'technical')
            ->where('is_visible', true)
            ->exists();

        $tieneProyecto = DB::table('projects')
            ->join('portfolios', 'projects.portfolio_id', '=', 'portfolios.id')
            ->where('portfolios.user_id', $userId)
            ->where('projects.is_visible', true)
            ->exists();

        $tieneAcademica = DB::table('experiences')
            ->where('user_id', $userId)
            ->where('type', 'education')
            ->where('is_visible', true)
            ->exists();

        $seccionesCompletas = 0;
        if ($tieneExperiencia) $seccionesCompletas++;
        if ($tieneHabilidadTecnica) $seccionesCompletas++;
        if ($tieneProyecto) $seccionesCompletas++;
        if ($tieneAcademica) $seccionesCompletas++;

        // 🔥 REGLA: obligatorios completos + 3 de las 4 secciones adicionales
        return $seccionesCompletas >= 3;
    }

    /**
     * ==========================================
     *  Obtener campos faltantes para mostrar al usuario
     * ==========================================
     */
       private function obtenerCamposFaltantes($user)
        {
            $userId = $user->id;
            $faltantes = [];

            // ==========================================
            // OBLIGATORIOS PRIMERO (siempre se muestran si faltan)
            // ==========================================
            $tieneNombre = DB::table('users')
                ->where('id', $userId)
                ->whereNotNull('first_name')
                ->where('first_name', '!=', '')
                ->exists();
            if (!$tieneNombre) {
                $faltantes[] = '🔒 Completar tu nombre (obligatorio)';
            }

            $tieneTitulo = DB::table('users')
                ->where('id', $userId)
                ->whereNotNull('profession_id')
                ->exists();
            if (!$tieneTitulo) {
                $faltantes[] = '🔒 Elegir tu título/profesión (obligatorio)';
            }

            $tieneBiografia = DB::table('users')
                ->where('id', $userId)
                ->whereNotNull('biography')
                ->where('biography', '!=', '')
                ->exists();
            if (!$tieneBiografia) {
                $faltantes[] = '🔒 Completar tu biografía (obligatorio)';
            }

            $tieneCorreo = DB::table('users')
                ->where('id', $userId)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->exists();
            if (!$tieneCorreo) {
                $faltantes[] = '🔒 Agregar tu correo electrónico (obligatorio)';
            }

            // ==========================================
            // 4 SECCIONES ADICIONALES (necesita 3 de 4)
            // ==========================================
            $completas = 0;

            $tieneExperiencia = DB::table('experiences')
                ->where('user_id', $userId)
                ->where('type', 'work')
                ->where('is_visible', true)
                ->exists();
            if ($tieneExperiencia) {
                $completas++;
            } else {
                $faltantes[] = 'Agregar al menos una experiencia laboral';
            }

            $tieneHabilidadTecnica = DB::table('skills')
                ->where('user_id', $userId)
                ->where('type', 'technical')
                ->where('is_visible', true)
                ->exists();
            if ($tieneHabilidadTecnica) {
                $completas++;
            } else {
                $faltantes[] = 'Agregar al menos una habilidad técnica';
            }

            $tieneProyecto = DB::table('projects')
                ->join('portfolios', 'projects.portfolio_id', '=', 'portfolios.id')
                ->where('portfolios.user_id', $userId)
                ->where('projects.is_visible', true)
                ->exists();
            if ($tieneProyecto) {
                $completas++;
            } else {
                $faltantes[] = 'Agregar al menos un proyecto';
            }

            $tieneAcademica = DB::table('experiences')
                ->where('user_id', $userId)
                ->where('type', 'education')
                ->where('is_visible', true)
                ->exists();
            if ($tieneAcademica) {
                $completas++;
            } else {
                $faltantes[] = 'Agregar formación académica';
            }

            $necesita = 3 - $completas;
            if ($necesita > 0) {
                $faltantes[] = "📊 Necesitas {$necesita} sección(es) más de estas 4 (tienes {$completas} de 3)";
            }

            return $faltantes;
        }

    /**
     * ==========================================
     *  PUBLICAR PORTAFOLIO
     * ==========================================
     */
    public function publicar(Request $request)
    {
        $user = Auth::user();
        
        // 🔍 Verificar si hay contenido
        if (!$this->portafolioTieneContenido($user)) {
            $faltantes = $this->obtenerCamposFaltantes($user);
            
            // Guardar en sesión para mostrar en la vista
            session()->flash('publish_error', true);
            session()->flash('publish_missing', $faltantes);
            
            return redirect()->back()->withErrors([
                'publish' => 'Tu portafolio está incompleto. Completa la información faltante antes de publicar.'
            ]);
        }

        // ✅ Si tiene contenido, publicar
        try {
            $portfolio = $user->portfolio;
            if ($portfolio) {
                $portfolio->is_public = true;
               //$portfolio->published_at = now();
                $portfolio->save();
            }

            session()->flash('success', '¡Tu portafolio ha sido publicado exitosamente!');
            
            return redirect()->back()->with('success', '¡Portafolio publicado!');
            
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'publish' => 'Ocurrió un error al publicar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ==========================================
     * NUEVO: CAMBIAR TEMA (Vibe)
     * ==========================================
     */
    public function updateTheme(Request $request)
    {
        $user = Auth::user();
        if ($user->portfolio) {
            $user->portfolio->color_theme = $request->theme;
            $user->portfolio->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Portafolio no encontrado']);
    }

    /**
     * Vista previa del portafolio para el usuario logueado (con edición)
     */
    public function preview()
    {
        // Obtener usuario logueado
        $user = Auth::user();


        $this->verificarYDespublicarSiEsNecesario($user);
         $tieneContenido = $this->portafolioTieneContenido($user);
        $camposFaltantes = $this->obtenerCamposFaltantes($user);
        
        // Cargar relaciones SEGÚN TU ESTRUCTURA DE BD
        $user->load([
            'profession',
            'skills.projects',
            'experiences',
            'portfolio.projects',
            'professionalNetworks.platform',
            'location'
        ]);

      
        
        // ==========================================
        // HABILIDADES TÉCNICAS (type = 'technical')
        // ==========================================
        $habilidadesTecnicas = $user->skills
            ->where('type', 'technical')
            ->where('is_visible', true)
            ->sortBy(fn ($s) => $s->display_order ?? 0)
            ->map(function($skill) {
                $nivel = '';
                if ($skill->level == 3) $nivel = 'Avanzado';
                elseif ($skill->level == 2) $nivel = 'Intermedio';
                else $nivel = 'Básico';
                
                return (object) [
                    'nombre' => $skill->name,
                    'nivel' => $nivel,
                    'categoria' => $skill->category ?: null,
                    'proyectos' => ($skill->projects ?? collect())
                        ->where('is_visible', true)
                        ->map(fn ($p) => (object) ['id' => $p->id, 'nombre' => $p->name])
                        ->values(),
                ];
            });

        $habilidadesTecnicasFrontend = $habilidadesTecnicas->filter(function ($s) {
            return ($s->categoria ?? '') === 'frontend';
        })->values();

        $habilidadesTecnicasBackend = $habilidadesTecnicas->filter(function ($s) {
            return ($s->categoria ?? '') === 'backend';
        })->values();
        
        // ==========================================
        // HABILIDADES BLANDAS (type = 'soft')
        // ==========================================
        $habilidadesBlandas = $user->skills->where('type', 'soft')->map(function($skill) {
            return (object) ['nombre' => $skill->name];
        });

        // ==========================================
        // IDIOMAS (type = 'language')
        // ==========================================
        $idiomas = $user->skills
            ->where('type', 'language')
            ->where('is_visible', true)
            ->sortBy(fn($s) => $s->display_order ?? 0)
            ->map(function($skill) {
                $nivelesMap = [1=>'A1',2=>'A2',3=>'B1',4=>'B2',5=>'C1',6=>'C2',7=>'Nativo'];
                $nivelesNombre = ['A1'=>'Principiante','A2'=>'Básico','B1'=>'Intermedio','B2'=>'Intermedio alto','C1'=>'Avanzado','C2'=>'Maestría','Nativo'=>'Nativo'];
                $porcentaje = ['A1'=>15,'A2'=>30,'B1'=>50,'B2'=>65,'C1'=>80,'C2'=>95,'Nativo'=>100];
                $banderas = ['inglés'=>'🇬🇧','español'=>'🇧🇴','portugués'=>'🇧🇷','francés'=>'🇫🇷','alemán'=>'🇩🇪','italiano'=>'🇮🇹','chino'=>'🇨🇳','japonés'=>'🇯🇵'];

                $nivelLabel  = $nivelesMap[$skill->level] ?? 'A1';
                $nivelNombre = $nivelesNombre[$nivelLabel] ?? '';
                $pct         = $porcentaje[$nivelLabel] ?? 50;
                $bandera     = $banderas[strtolower($skill->name)] ?? '🌐';

                return (object) [
                    'nombre'       => $skill->name,
                    'nivel_label'  => $nivelLabel,
                    'nivel_nombre' => $nivelNombre,
                    'porcentaje'   => $pct,
                    'bandera'      => $bandera,
                    'certificado'  => $skill->evidence_url,
                ];
            })->values();
        
        // ==========================================
        // EXPERIENCIAS LABORALES
        // ==========================================
        $experiencias = $user->experiences
            ->where('type', 'work')
            ->where('is_visible', true)
            ->groupBy('institution')
            ->map(function($grupo) {
                $primera = $grupo->first();
                $roles = $grupo->map(function($exp) {
                    return $exp->title;
                })->implode(' / ');
                
                $fecha_inicio = $grupo->min('start_date');
                $fecha_fin = $grupo->contains('is_current', true) ? null : $grupo->max('end_date');
                $trabajo_actual = $grupo->contains('is_current', true);
                $descripcion = $primera->description;
                
                return (object) [
                    'empresa' => $primera->institution,
                    'cargo' => $roles,
                    'ubicacion' => $primera->location,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin,
                    'trabajo_actual' => $trabajo_actual,
                    'descripcion' => $descripcion
                ];
            })->values();
        
       // ==========================================
        // INFORMACIÓN ACADÉMICA (CON EVIDENCIAS PROCESADAS - IGUAL QUE PÚBLICO)
        // ==========================================
        $academicas = $user->experiences->where('type', 'education')->map(function($edu) {
            // Procesar evidence_url - convertir string JSON a ARRAY
            $evidenceUrl = $edu->evidence_url;
            if (is_string($evidenceUrl) && !empty($evidenceUrl)) {
                $decoded = json_decode($evidenceUrl, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $evidenceUrl = $decoded;
                } else {
                    $evidenceUrl = [$evidenceUrl];
                }
            } elseif (empty($evidenceUrl)) {
                $evidenceUrl = null;
            }
            
            return (object) [
                'institucion' => $edu->institution,
                'titulo' => $edu->title,
                'specialty' => $edu->specialty ?? null,
                'fecha_inicio' => $edu->start_date,
                'fecha_fin' => $edu->end_date,
                'estudio_actual' => $edu->is_current,
                'descripcion' => $edu->description,
                'evidence_url' => $evidenceUrl,  // ✅ Ahora es ARRAY
            ];
        });
        
        // ==========================================
        // PROYECTOS
        // ==========================================
        $proyectos = collect();
        if ($user->portfolio) {
            $proyectos = $user->portfolio->projects->where('is_visible', true)->map(function($project) {
                return (object) [
                    'id' => $project->id,
                    'nombre' => $project->name,
                    'descripcion' => $project->description,
                    'fecha_inicio' => $project->start_date,
                    'fecha_fin' => $project->end_date,
                    'estado' => $project->status,
                    'rol' => $project->role,
                    'cliente' => $project->company ?? null,
                    'tecnologias' => $project->technologies->pluck('name')->toArray(),
                    'evidencias' => $project->evidencias->map(function($ev) {
                        return (object) [
                            'tipo'   => $ev->tipo,
                            'titulo' => $ev->titulo ?? '',
                            'url'    => $ev->url ?? null,
                            'imagen' => $ev->imagen_path ? asset('storage/' . $ev->imagen_path) : null,
                        ];
                    })->toArray(),
                ];
            });
        }
        
        // ==========================================
        // REDES SOCIALES
        // ==========================================
        $redes = [
            'linkedin' => null,
            'github' => null,
            'whatsapp' => null,
            'correo' => null,
            'otros' => null,
            'ubicacion' => null,
            'maps_url' => null
        ];
        
        foreach ($user->professionalNetworks as $network) {
            if (!$network->is_visible) continue;
            $platformName = $network->platform->name ?? '';
            $profileUrl = $network->profile_url;
            
            if (stripos($platformName, 'linkedin') !== false) {
                $redes['linkedin'] = $profileUrl;
            } elseif (stripos($platformName, 'github') !== false) {
                $redes['github'] = $profileUrl;
            } elseif (stripos($platformName, 'whatsapp') !== false) {
                $redes['whatsapp'] = $profileUrl;
            } elseif (stripos($platformName, 'email') !== false) {
                $redes['correo'] = $profileUrl;
            } else {
                $redes['otros'] = $profileUrl;
            }
        }

        $redes['maps_url'] = null;
        if ($user->location && $user->location->show_location) {
            if ($user->location->address) {
                $redes['ubicacion'] = $user->location->address;
            }
            if ($user->location->latitude && $user->location->longitude) {
                $redes['maps_url'] = "https://www.google.com/maps/search/?api=1&query={$user->location->latitude},{$user->location->longitude}";
            } elseif ($user->location->address) {
                $redes['maps_url'] = "https://www.google.com/maps/search/?api=1&query=" . urlencode($user->location->address);
            }
        }

        foreach ($redes as $key => $url) {
            if ($url && !in_array($key, ['correo', 'whatsapp', 'ubicacion'])) {
                if (preg_match('/^https?:\/\//', $url)) continue;
                if (filter_var('https://' . $url, FILTER_VALIDATE_URL)) {
                    $redes[$key] = 'https://' . $url;
                } else {
                    $redes[$key] = null;
                }
            }
        }

        return view('Preview', compact(
            'user',
            'habilidadesTecnicasFrontend',
            'habilidadesTecnicasBackend',
            'habilidadesBlandas',
            'idiomas',
            'experiencias',
            'academicas',
            'proyectos',
            'redes',
            'tieneContenido',        //  NUEVO
            'camposFaltantes'        // NUEVO

        ));
    }

    /**
     * Muestra el portafolio público de cualquier usuario (HU-16)
     * No requiere autenticación - usa vista separada sin layout
     */
    public function publicShow($slug)
    {
        // Buscar portafolio por slug y que sea público
        $portfolio = Portfolio::where('slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();
        
        $user = $portfolio->user;
        
        // Cargar relaciones (solo datos visibles públicamente)
        $user->load([
            'profession',
            'skills' => fn($q) => $q->where('is_visible', true),
            'experiences' => fn($q) => $q->where('is_visible', true),
            'portfolio.projects' => fn($q) => $q->where('is_visible', true),
            'portfolio.projects.technologies',
            'portfolio.projects.evidencias',
            'professionalNetworks' => fn($q) => $q->where('is_visible', true),
            'professionalNetworks.platform',
            'location' => fn($q) => $q->where('show_location', true)
        ]);
        
        // ==========================================
        // HABILIDADES TÉCNICAS
        // ==========================================
        $habilidadesTecnicas = $user->skills
            ->where('type', 'technical')
            ->sortBy(fn ($s) => $s->display_order ?? 0)
            ->map(function($skill) {
                $nivel = '';
                if ($skill->level == 3) $nivel = 'Avanzado';
                elseif ($skill->level == 2) $nivel = 'Intermedio';
                else $nivel = 'Básico';
                
                return (object) [
                    'nombre' => $skill->name,
                    'nivel' => $nivel,
                    'categoria' => $skill->category ?: null,
                    'proyectos' => ($skill->projects ?? collect())
                        ->where('is_visible', true)
                        ->map(fn ($p) => (object) ['id' => $p->id, 'nombre' => $p->name])
                        ->values(),
                ];
            });
        
        $habilidadesTecnicasFrontend = $habilidadesTecnicas->filter(fn($s) => ($s->categoria ?? '') === 'frontend')->values();
        $habilidadesTecnicasBackend = $habilidadesTecnicas->filter(fn($s) => ($s->categoria ?? '') === 'backend')->values();
        
        // ==========================================
        // HABILIDADES BLANDAS
        // ==========================================
        $habilidadesBlandas = $user->skills->where('type', 'soft')->map(fn($skill) => (object) ['nombre' => $skill->name]);
        
        // ==========================================
        // IDIOMAS (type = 'language')
        // ==========================================
        $idiomas = $user->skills
            ->where('type', 'language')
            ->where('is_visible', true)
            ->sortBy(fn($s) => $s->display_order ?? 0)
            ->map(function($skill) {
                $nivelesMap = [1=>'A1',2=>'A2',3=>'B1',4=>'B2',5=>'C1',6=>'C2',7=>'Nativo'];
                $nivelesNombre = ['A1'=>'Principiante','A2'=>'Básico','B1'=>'Intermedio','B2'=>'Intermedio alto','C1'=>'Avanzado','C2'=>'Maestría','Nativo'=>'Nativo'];
                $porcentaje = ['A1'=>15,'A2'=>30,'B1'=>50,'B2'=>65,'C1'=>80,'C2'=>95,'Nativo'=>100];
                $banderas = ['inglés'=>'🇬🇧','español'=>'🇧🇴','portugués'=>'🇧🇷','francés'=>'🇫🇷','alemán'=>'🇩🇪','italiano'=>'🇮🇹','chino'=>'🇨🇳','japonés'=>'🇯🇵'];

                $nivelLabel  = $nivelesMap[$skill->level] ?? 'A1';
                $nivelNombre = $nivelesNombre[$nivelLabel] ?? '';
                $pct         = $porcentaje[$nivelLabel] ?? 50;
                $bandera     = $banderas[strtolower($skill->name)] ?? '🌐';

                return (object) [
                    'nombre'       => $skill->name,
                    'nivel_label'  => $nivelLabel,
                    'nivel_nombre' => $nivelNombre,
                    'porcentaje'   => $pct,
                    'bandera'      => $bandera,
                    'certificado'  => $skill->evidence_url,
                ];
            })->values();
        
        // ==========================================
        // EXPERIENCIAS LABORALES
        // ==========================================
        $experiencias = $user->experiences
            ->where('type', 'work')
            ->groupBy('institution')
            ->map(function($grupo) {
                $primera = $grupo->first();
                $roles = $grupo->map(fn($exp) => $exp->title)->implode(' / ');
                $fecha_inicio = $grupo->min('start_date');
                $fecha_fin = $grupo->contains('is_current', true) ? null : $grupo->max('end_date');
                $trabajo_actual = $grupo->contains('is_current', true);
                $descripcion = $primera->description;
                
                return (object) [
                    'empresa' => $primera->institution,
                    'cargo' => $roles,
                    'ubicacion' => $primera->location,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin,
                    'trabajo_actual' => $trabajo_actual,
                    'descripcion' => $descripcion
                ];
            })->values();
        
        // ==========================================
        // INFORMACIÓN ACADÉMICA (CON EVIDENCIAS PROCESADAS)
        // ⭐ ESTA VERSIÓN ES PARA LA VISTA PÚBLICA (CONVIERTE A ARRAY) ⭐
        // ==========================================
        $academicas = $user->experiences->where('type', 'education')->map(function($edu) {
            // Procesar evidence_url para la vista pública
            $evidenceUrl = $edu->evidence_url;
            if (is_string($evidenceUrl) && !empty($evidenceUrl)) {
                $decoded = json_decode($evidenceUrl, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $evidenceUrl = $decoded;
                } else {
                    $evidenceUrl = [$evidenceUrl];
                }
            } elseif (empty($evidenceUrl)) {
                $evidenceUrl = null;
            }
            
            return (object) [
                'institucion' => $edu->institution,
                'titulo' => $edu->title,
                'specialty' => $edu->specialty ?? null,
                'fecha_inicio' => $edu->start_date,
                'fecha_fin' => $edu->end_date,
                'estudio_actual' => $edu->is_current,
                'descripcion' => $edu->description,
                'evidence_url' => $evidenceUrl,  // ⭐ CONVERTIDO A ARRAY
            ];
        })->values();
        
        // ==========================================
        // PROYECTOS
        // ==========================================
        $proyectos = collect();
        if ($user->portfolio) {
            $proyectos = $user->portfolio->projects->where('is_visible', true)->map(function($project) {
                return (object) [
                    'id' => $project->id,
                    'nombre' => $project->name,
                    'descripcion' => $project->description,
                    'fecha_inicio' => $project->start_date,
                    'fecha_fin' => $project->end_date,
                    'estado' => $project->status,
                    'rol' => $project->role,
                    'cliente' => $project->company ?? null,
                    'tecnologias' => $project->technologies->pluck('name')->toArray(),
                    'evidencias' => $project->evidencias->map(function($ev) {
                        return (object) [
                            'tipo'   => $ev->tipo,
                            'titulo' => $ev->titulo ?? '',
                            'url'    => $ev->url ?? null,
                            'imagen' => $ev->imagen_path ? asset('storage/' . $ev->imagen_path) : null,
                        ];
                    })->toArray(),
                ];
            });
        }
        
        // ==========================================
        // REDES SOCIALES
        // ==========================================
        $redes = [
            'linkedin' => null,
            'github' => null,
            'whatsapp' => null,
            'correo' => $portfolio->show_email ? $user->email : null,
            'otros' => null,
            'ubicacion' => null,
            'maps_url' => null
        ];
        
        foreach ($user->professionalNetworks as $network) {
            $platformName = $network->platform->name ?? '';
            $profileUrl = $network->profile_url;
            
            if (stripos($platformName, 'linkedin') !== false) {
                $redes['linkedin'] = $profileUrl;
            } elseif (stripos($platformName, 'github') !== false) {
                $redes['github'] = $profileUrl;
            } elseif (stripos($platformName, 'whatsapp') !== false) {
                $redes['whatsapp'] = $profileUrl;
            } else {
                $redes['otros'] = $profileUrl;
            }
        }
        
        if ($user->location && $user->location->show_location) {
            if ($user->location->address) {
                $redes['ubicacion'] = $user->location->address;
            }
            if ($user->location->latitude && $user->location->longitude) {
                $redes['maps_url'] = "https://www.google.com/maps/search/?api=1&query={$user->location->latitude},{$user->location->longitude}";
            } elseif ($user->location->address) {
                $redes['maps_url'] = "https://www.google.com/maps/search/?api=1&query=" . urlencode($user->location->address);
            }
        }
        
        foreach ($redes as $key => $url) {
            if ($url && !in_array($key, ['correo', 'whatsapp', 'ubicacion'])) {
                if (preg_match('/^https?:\/\//', $url)) continue;
                if (filter_var('https://' . $url, FILTER_VALIDATE_URL)) {
                    $redes[$key] = 'https://' . $url;
                } else {
                    $redes[$key] = null;
                }
            }
        }
        
        return view('portafolio.publico', compact(
            'user',
            'habilidadesTecnicasFrontend',
            'habilidadesTecnicasBackend',
            'habilidadesBlandas',
            'idiomas',
            'experiencias',
            'academicas',
            'proyectos',
            'redes',
            'portfolio'
        ));
    }

    /**
     * Página de exploración unificada
     */
    public function explore(Request $request)
    {
        // ==========================================
        // CATEGORÍAS (Profesiones)
        // ==========================================
        $categories = collect([
            'Frontend Developer',
            'Backend Developer',
            'Full Stack Developer',
            'UI/UX Designer',
            'DevOps Engineer',
            'Mobile Developer',
            'Project Manager',
            'QA Tester',
            'Database Administrator',
            'Technical Leader',
            'Data Analyst',
            'Scrum Master',
            'Product Owner',
            'Business Analyst',
            'Security Engineer',
            'Data Engineer',
            'Cloud Engineer',
            'AI Engineer',
            'Systems Analyst',
        ])->map(function ($name, $index) {
            return (object) [
                'id' => $name,
                'name' => $name,
            ];
        });

        // ==========================================
        // SKILLS (Tecnologías)
        // ==========================================
        $skills = collect([
            'Angular', 'AWS', 'Azure', 'Bootstrap', 'C#', 'Cassandra', 'Django',
            'Docker', 'Express.js', 'Figma', 'Firebase', 'Flutter', 'Git', 'Go',
            'GraphQL', 'Java', 'JavaScript', 'Jenkins', 'Kotlin', 'Kubernetes',
            'Laravel', 'Linux', 'MongoDB', 'MySQL', 'Next.js', 'Node.js', 'PHP',
            'PostgreSQL', 'Python', 'React', 'Redis', 'Redux', 'Ruby on Rails',
            'Rust', 'Sass', 'Spring Boot', 'Supabase', 'Svelte', 'Swift',
            'Tailwind CSS', 'TypeScript', 'Unity', 'Vue.js', 'Webpack', 'WordPress'
        ])->map(function ($name) {
            return (object) [
                'name' => $name,
            ];
        });

// ==========================================
// EMPRESAS / INSTITUCIONES
// ==========================================
$companies = collect([
    // Empresas tecnológicas y software
    'JalaSoft',
    'AssureSoft',
    'Mojix',
    'Truextend',
    'Digital Harbor',
    'Ultracasas',
    'TuGerente',
    'Síntesis',
    'GeoPagos',
    'Tigo Business',
    'Microsoft',
    'Google',
    'Apple',
    'Amazon Web Services',
    'IBM',

    // Telecomunicaciones
    'ENTEL',
    'Tigo',
    'Viva',
    'AXS Bolivia',
    'COTEL',
    'Comteco',

    // Bancos y entidades financieras
    'Banco Unión',
    'Banco Nacional de Bolivia',
    'Banco Mercantil Santa Cruz',
    'Banco Bisa',
    'Banco Económico',
    'Banco Ganadero',
    'BancoSol',
    'FIE',
    'Prodem',
    'BCP Bolivia',

    // Instituciones públicas
    'SEGIP',
    'SIN',
    'Aduana Nacional',
    'Gobernación',
    'Alcaldía',
    'YPFB',
    'ENDE',
    'SENASAG',
    'ABC',
    'BoA',
    'Caja Nacional de Salud',
    'Impuestos Nacionales',

    // Universidades e instituciones académicas
    'UMSS',
    'Universidad Mayor de San Simón',
    'Universidad Católica Boliviana',
    'UPB',
    'Univalle',
    'Universidad Privada Domingo Savio',
    'Universidad Franz Tamayo',
    'Universidad Técnica de Oruro',
    'Universidad Gabriel René Moreno',

    // Empresas comerciales y servicios
    'Farmacorp',
    'Hipermaxi',
    'EMAPA',
    'Soboce',
    'Coca Cola Bolivia',
    'Pil Andina',
    'Sofía',
    'PedidosYa Bolivia',
])->map(function ($name) {
    return (object) [
        'name' => $name,
    ];
});

        // ==========================================
        // QUERY PRINCIPAL
        // ==========================================
$query = Portfolio::where('is_public', true)
    ->with([
        'user.profession',
        'user.skills',
        'user.experiences',
        'user.location',
        'user.professionalNetworks',
        'projects.technologies'
    ]);

$normalizeText = function ($text) {
    $text = trim($text ?? '');

    $text = str_replace(
        ['á','é','í','ó','ú','Á','É','Í','Ó','Ú','ñ','Ñ'],
        ['a','e','i','o','u','a','e','i','o','u','n','n'],
        $text
    );

    return strtolower($text);
};

$normalizeColumn = function ($column) {
    return "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE($column,
        'á','a'),'é','e'),'í','i'),'ó','o'),'ú','u'),'ñ','n'))";
};

$expandSearchTerms = function ($text) use ($normalizeText) {
    $search = $normalizeText($text);

    $terms = collect([$search]);

    $synonyms = [
        'desarrollador' => ['developer', 'develop', 'development', 'dev'],
        'desarrolladora' => ['developer', 'develop', 'development', 'dev'],
        'desarrollo' => ['developer', 'develop', 'development', 'dev'],
        'developer' => ['desarrollador', 'desarrolladora', 'desarrollo', 'dev'],
        'develop' => ['developer', 'desarrollador', 'desarrolladora', 'desarrollo'],
        'development' => ['desarrollo', 'developer', 'desarrollador'],

        'frontend' => ['front end', 'front-end'],
        'front end' => ['frontend', 'front-end'],
        'backend' => ['back end', 'back-end'],
        'back end' => ['backend', 'back-end'],

        'full stack' => ['fullstack', 'full-stack'],
        'fullstack' => ['full stack', 'full-stack'],
        'full-stack' => ['full stack', 'fullstack'],

        'ingeniero' => ['engineer'],
        'ingeniera' => ['engineer'],
        'engineer' => ['ingeniero', 'ingeniera'],

        'sistemas' => ['systems'],
        'systems' => ['sistemas'],

        'diseñador' => ['designer'],
        'diseñadora' => ['designer'],
        'designer' => ['diseñador', 'diseñadora'],

        'datos' => ['data'],
        'data' => ['datos'],
    ];

    foreach ($synonyms as $word => $equivalents) {
        if (str_contains($search, $word)) {
            foreach ($equivalents as $equivalent) {
                $terms->push(str_replace($word, $equivalent, $search));
                $terms->push($equivalent);
            }
        }
    }

    // Si la búsqueda contiene un rol específico, no permitir términos demasiado generales.
// Ejemplo: "full stack development" no debe convertirse en solo "developer".
$hasFullStack = str_contains($search, 'full stack')
    || str_contains($search, 'fullstack')
    || str_contains($search, 'full-stack');

$hasFrontend = str_contains($search, 'frontend')
    || str_contains($search, 'front end')
    || str_contains($search, 'front-end');

$hasBackend = str_contains($search, 'backend')
    || str_contains($search, 'back end')
    || str_contains($search, 'back-end');

if ($hasFullStack) {
    $terms = $terms->merge([
        'full stack',
        'fullstack',
        'full-stack',
        'full stack developer',
        'fullstack developer',
        'full-stack developer',
        'desarrollador full stack',
        'desarrolladora full stack',
        'desarrollo full stack',
    ]);
}

if ($hasFrontend) {
    $terms = $terms->merge([
        'frontend',
        'front end',
        'front-end',
        'frontend developer',
        'front end developer',
        'front-end developer',
        'desarrollador frontend',
        'desarrolladora frontend',
        'desarrollo frontend',
    ]);
}

if ($hasBackend) {
    $terms = $terms->merge([
        'backend',
        'back end',
        'back-end',
        'backend developer',
        'back end developer',
        'back-end developer',
        'desarrollador backend',
        'desarrolladora backend',
        'desarrollo backend',
    ]);
}

if ($hasFullStack || $hasFrontend || $hasBackend) {
    $blockedGenericTerms = [
        'developer',
        'develop',
        'development',
        'dev',
        'desarrollador',
        'desarrolladora',
        'desarrollo',
    ];

    $terms = $terms->reject(function ($term) use ($blockedGenericTerms) {
        return in_array($term, $blockedGenericTerms, true);
    });
}

    return $terms
        ->map(fn ($term) => trim($term))
        ->filter()
        ->unique()
        ->values();
};

$expandCompanyTerms = function ($text) use ($normalizeText) {
    $company = $normalizeText($text);

    $aliases = [
        'google bolivia' => [
            'google',
            'google bolivia',
        ],
        'google' => [
            'google',
            'google bolivia',
        ],

        'umss' => [
            'umss',
            'universidad mayor de san simon',
            'universidad mayor de san simón',
        ],
        'universidad mayor de san simon' => [
            'umss',
            'universidad mayor de san simon',
            'universidad mayor de san simón',
        ],
        'universidad mayor de san simón' => [
            'umss',
            'universidad mayor de san simon',
            'universidad mayor de san simón',
        ],

        'ucb' => [
            'ucb',
            'universidad catolica boliviana',
            'universidad católica boliviana',
        ],
        'universidad catolica boliviana' => [
            'ucb',
            'universidad catolica boliviana',
            'universidad católica boliviana',
        ],
        'universidad católica boliviana' => [
            'ucb',
            'universidad catolica boliviana',
            'universidad católica boliviana',
        ],

        'upb' => [
            'upb',
            'universidad privada boliviana',
        ],
        'universidad privada boliviana' => [
            'upb',
            'universidad privada boliviana',
        ],

        'banco union' => [
            'banco union',
            'banco unión',
        ],
        'banco unión' => [
            'banco union',
            'banco unión',
        ],

        'banco nacional de bolivia' => [
            'banco nacional de bolivia',
            'bnb',
        ],
        'bnb' => [
            'banco nacional de bolivia',
            'bnb',
        ],

        'banco mercantil santa cruz' => [
            'banco mercantil santa cruz',
            'banco mercantil',
            'mercantil santa cruz',
            'bmsc',
        ],
        'bmsc' => [
            'banco mercantil santa cruz',
            'banco mercantil',
            'mercantil santa cruz',
            'bmsc',
        ],

        'jalasoft' => [
            'jalasoft',
            'jala soft',
            'jala',
        ],
        'jala soft' => [
            'jalasoft',
            'jala soft',
            'jala',
        ],
    ];

    return collect($aliases[$company] ?? [$company])
        ->map(fn ($term) => $normalizeText($term))
        ->filter()
        ->unique()
        ->values();
};

// BÚSQUEDA POR TEXTO GENERAL
if ($request->filled('search')) {
    $terms = $expandSearchTerms($request->input('search'));
    
    $terms = $terms
        ->merge($expandCompanyTerms($request->input('search')))
        ->unique()
        ->values();

    $query->where(function ($q) use ($terms, $normalizeColumn) {
        foreach ($terms as $term) {
            $like = "%{$term}%";

            $q->orWhereHas('user', function ($u) use ($like, $normalizeColumn) {
                $u->whereRaw($normalizeColumn('first_name') . " LIKE ?", [$like])
                  ->orWhereRaw($normalizeColumn('last_name') . " LIKE ?", [$like])
                  ->orWhereRaw($normalizeColumn("CONCAT(first_name, ' ', last_name)") . " LIKE ?", [$like])
                  ->orWhereRaw($normalizeColumn('biography') . " LIKE ?", [$like])
                  ->orWhereRaw($normalizeColumn('city') . " LIKE ?", [$like])
                  ->orWhereRaw($normalizeColumn('country') . " LIKE ?", [$like]);
            })
            ->orWhereHas('user.location', function ($location) use ($like, $normalizeColumn) {
                $location->whereRaw($normalizeColumn('address') . " LIKE ?", [$like]);
            })
            ->orWhereHas('user.profession', function ($p) use ($like, $normalizeColumn) {
                $p->whereRaw($normalizeColumn('name') . " LIKE ?", [$like]);
            })
            ->orWhereHas('user.skills', function ($s) use ($like, $normalizeColumn) {
                $s->where('is_visible', true)
                  ->whereRaw($normalizeColumn('name') . " LIKE ?", [$like]);
            })
            ->orWhereHas('user.experiences', function ($e) use ($like, $normalizeColumn) {
                $e->where('is_visible', true)
                  ->where(function ($exp) use ($like, $normalizeColumn) {
                      $exp->whereRaw($normalizeColumn('title') . " LIKE ?", [$like])
                          ->orWhereRaw($normalizeColumn('institution') . " LIKE ?", [$like])
                          ->orWhereRaw($normalizeColumn('specialty') . " LIKE ?", [$like])
                          ->orWhereRaw($normalizeColumn('description') . " LIKE ?", [$like]);
                  });
            })
            ->orWhereHas('projects', function ($p) use ($like, $normalizeColumn) {
                $p->where('is_visible', true)
                  ->where(function ($projectQuery) use ($like, $normalizeColumn) {
                      $projectQuery->whereRaw($normalizeColumn('name') . " LIKE ?", [$like])
                          ->orWhereRaw($normalizeColumn('description') . " LIKE ?", [$like])
                          ->orWhereRaw($normalizeColumn('role') . " LIKE ?", [$like])
                          ->orWhereRaw($normalizeColumn('company') . " LIKE ?", [$like]);
                  });
            })
            ->orWhereHas('projects.technologies', function ($t) use ($like, $normalizeColumn) {
                $t->whereRaw($normalizeColumn('name') . " LIKE ?", [$like]);
            });
        }
    });
}

// FILTRO POR EMPRESA / INSTITUCIÓN LABORAL
if ($request->filled('company')) {
    $companyTerms = $expandCompanyTerms($request->input('company'));

    $query->where(function ($companyQuery) use ($companyTerms, $normalizeColumn) {
        foreach ($companyTerms as $companyTerm) {
            $like = "%{$companyTerm}%";

            $companyQuery
                ->orWhereHas('user.experiences', function ($e) use ($like, $normalizeColumn) {
                    $e->where('type', 'work')
                      ->where('is_visible', true)
                      ->where(function ($exp) use ($like, $normalizeColumn) {
                          $exp->whereRaw($normalizeColumn('institution') . " LIKE ?", [$like])
                              ->orWhereRaw($normalizeColumn('location') . " LIKE ?", [$like]);
                      });
                })
                ->orWhereHas('projects', function ($p) use ($like, $normalizeColumn) {
                    $p->where('is_visible', true)
                      ->whereRaw($normalizeColumn('company') . " LIKE ?", [$like]);
                });
        }
    });
}

// FILTRO POR CATEGORÍA / PROFESIÓN
if ($request->filled('category')) {
    $category = $normalizeText($request->input('category'));

    $categoryMap = [
        'frontend developer' => [
            'frontend',
            'front end',
            'front-end',
            'frontend developer',
            'front end developer',
            'front-end developer',
            'desarrollador frontend',
            'desarrolladora frontend',
        ],

        'backend developer' => [
            'backend',
            'back end',
            'back-end',
            'backend developer',
            'back end developer',
            'back-end developer',
            'desarrollador backend',
            'desarrolladora backend',
        ],

        'full stack developer' => [
            'full stack',
            'fullstack',
            'full-stack',
            'full stack developer',
            'fullstack developer',
            'full-stack developer',
            'desarrollador full stack',
            'desarrolladora full stack',
        ],

        'mobile developer' => [
            'mobile',
            'movil',
            'móvil',
            'android',
            'ios',
            'mobile developer',
            'desarrollador mobile',
            'desarrollador movil',
            'desarrolladora mobile',
        ],

        'ui/ux designer' => [
            'ui/ux',
            'ux/ui',
            'ui ux',
            'ux',
            'interfaz',
            'experiencia de usuario',
            'designer',
            'diseñador ui',
            'diseñadora ui',
            'diseñador ux',
            'diseñadora ux',
        ],

        'qa tester' => [
            'qa',
            'tester',
            'testing',
            'quality assurance',
            'control de calidad',
            'aseguramiento de calidad',
        ],

        'devops engineer' => [
            'devops',
            'devops engineer',
            'ingeniero devops',
            'ingeniera devops',
        ],

        'data analyst' => [
            'data analyst',
            'analista de datos',
            'datos',
            'data',
        ],

        'ai engineer' => [
            'ai engineer',
            'ia engineer',
            'inteligencia artificial',
            'machine learning',
            'artificial intelligence',
        ],
    ];

    $categoryTerms = collect($categoryMap[$category] ?? [$category])
        ->map(fn ($term) => $normalizeText($term))
        ->filter()
        ->unique()
        ->values();

    $query->where(function ($q) use ($categoryTerms, $normalizeColumn) {
        foreach ($categoryTerms as $categoryTerm) {
            $like = "%{$categoryTerm}%";

            $q->orWhereHas('user.profession', function ($p) use ($like, $normalizeColumn) {
                $p->whereRaw($normalizeColumn('name') . " LIKE ?", [$like]);
            })
            ->orWhereHas('user.experiences', function ($e) use ($like, $normalizeColumn) {
                $e->where('type', 'work')
                  ->where('is_visible', true)
                  ->whereRaw($normalizeColumn('title') . " LIKE ?", [$like]);
            })
            ->orWhereHas('projects', function ($p) use ($like, $normalizeColumn) {
                $p->where('is_visible', true)
                  ->whereRaw($normalizeColumn('role') . " LIKE ?", [$like]);
            });
        }
    });
}

        // FILTRO POR SKILLS (TECNOLOGÍAS)
        if ($request->filled('skills')) {
            $skillsArray = array_filter((array) $request->input('skills'));
            foreach ($skillsArray as $skillName) {
                $query->where(function ($q) use ($skillName) {
                    $q->whereHas('user.skills', function ($s) use ($skillName) {
                        $s->where('name', $skillName)
                          ->where('type', 'technical');
                    })
                    ->orWhereHas('projects', function ($p) use ($skillName) {
                        $p->where('is_visible', true)
                          ->whereHas('technologies', function ($t) use ($skillName) {
                              $t->where('name', $skillName);
                          });
                    });
                });
            }
        }

        // FILTRO POR AÑOS MÍNIMOS DE EXPERIENCIA LABORAL
$minExperience = null;

if ($request->filled('min_experience')) {
    $minExperience = (int) $request->input('min_experience');

    if (!in_array($minExperience, [1, 5, 10])) {
        $minExperience = null;
    }
}

// FILTRO POR IDIOMA
if ($request->filled('language')) {
    $language = $normalizeText($request->input('language'));

    $languageMap = [
        'ingles' => ['ingles', 'english'],
        'english' => ['ingles', 'english'],

        'espanol' => ['espanol', 'spanish', 'castellano'],
        'spanish' => ['espanol', 'spanish', 'castellano'],
        'castellano' => ['espanol', 'spanish', 'castellano'],

        'portugues' => ['portugues', 'portuguese'],
        'portuguese' => ['portugues', 'portuguese'],

        'frances' => ['frances', 'french'],
        'french' => ['frances', 'french'],

        'aleman' => ['aleman', 'german'],
        'german' => ['aleman', 'german'],

        'italiano' => ['italiano', 'italian'],
        'italian' => ['italiano', 'italian'],
    ];

    $languageTerms = $languageMap[$language] ?? [$language];

    $query->whereHas('user.skills', function ($s) use ($languageTerms, $normalizeColumn) {
        $s->where('type', 'language')
          ->where('is_visible', true)
          ->where(function ($langQuery) use ($languageTerms, $normalizeColumn) {
              foreach ($languageTerms as $term) {
                  $langQuery->orWhereRaw($normalizeColumn('name') . " LIKE ?", ["%{$term}%"]);
              }
          });
    });
}

// ORDENAMIENTO
$sort = $request->input('sort', 'complete');

switch ($sort) {
    case 'asc':
        $query->orderBy('portfolios.created_at', 'asc');
        break;

case 'complete':
    /*
    |--------------------------------------------------------------------------
    | Puntaje de completitud
    |--------------------------------------------------------------------------
    | Mide qué tan completo está el portafolio.
    | Ya no solo pregunta si existe una sección, también toma en cuenta
    | cuánta información tiene en habilidades, proyectos, experiencias, etc.
    */
    $completenessScore = "
        (
            CASE WHEN EXISTS (
                SELECT 1 FROM users u
                WHERE u.id = portfolios.user_id
                AND u.photo_base64 IS NOT NULL
                AND u.photo_base64 != ''
            ) THEN 8 ELSE 0 END

            +

            CASE WHEN EXISTS (
                SELECT 1 FROM users u
                WHERE u.id = portfolios.user_id
                AND u.biography IS NOT NULL
                AND u.biography != ''
            ) THEN 12 ELSE 0 END

            +

            CASE WHEN EXISTS (
                SELECT 1 FROM users u
                WHERE u.id = portfolios.user_id
                AND u.profession_id IS NOT NULL
            ) THEN 10 ELSE 0 END

            +

            LEAST((
                SELECT COUNT(*)
                FROM skills s
                WHERE s.user_id = portfolios.user_id
                AND s.type = 'technical'
                AND s.is_visible = 1
            ), 5) * 4

            +

            LEAST((
                SELECT COUNT(*)
                FROM projects p
                WHERE p.portfolio_id = portfolios.id
                AND p.is_visible = 1
            ), 4) * 5

            +

            LEAST((
                SELECT COUNT(*)
                FROM experiences e
                WHERE e.user_id = portfolios.user_id
                AND e.type = 'work'
                AND e.is_visible = 1
            ), 3) * 4

            +

            LEAST((
                SELECT COUNT(*)
                FROM experiences e
                WHERE e.user_id = portfolios.user_id
                AND e.type = 'education'
                AND e.is_visible = 1
            ), 2) * 4

            +

            LEAST((
                SELECT COUNT(*)
                FROM skills s
                WHERE s.user_id = portfolios.user_id
                AND s.type = 'language'
                AND s.is_visible = 1
            ), 3) * 2

            +

            LEAST((
                SELECT COUNT(*)
                FROM professional_networks pn
                WHERE pn.user_id = portfolios.user_id
                AND pn.is_visible = 1
            ), 2) * 2
        )
    ";

    /*
    |--------------------------------------------------------------------------
    | Puntaje de compatibilidad
    |--------------------------------------------------------------------------
    | Mide qué tanto coincide el portafolio con búsqueda y filtros.
    | No reemplaza los filtros. Solo ordena mejor los resultados encontrados.
    */
    $compatibilityParts = ['0'];
    $compatibilityBindings = [];

    $addExistsScore = function ($sql, $score, $bindings = []) use (&$compatibilityParts, &$compatibilityBindings) {
        $compatibilityParts[] = "CASE WHEN EXISTS ($sql) THEN {$score} ELSE 0 END";

        foreach ($bindings as $binding) {
            $compatibilityBindings[] = $binding;
        }
    };

    $addCountScore = function ($sql, $limit, $weight, $bindings = []) use (&$compatibilityParts, &$compatibilityBindings) {
        $compatibilityParts[] = "(LEAST(($sql), {$limit}) * {$weight})";

        foreach ($bindings as $binding) {
            $compatibilityBindings[] = $binding;
        }
    };

    /*
    |--------------------------------------------------------------------------
    | Coincidencia con búsqueda general
    |--------------------------------------------------------------------------
    */
    if ($request->filled('search')) {
        $searchTerms = $expandSearchTerms($request->input('search'));

        foreach ($searchTerms as $term) {
            $like = "%{$term}%";

            // Coincidencia con profesión principal
            $addExistsScore("
                SELECT 1
                FROM users u
                INNER JOIN professions pr ON pr.id = u.profession_id
                WHERE u.id = portfolios.user_id
                AND " . $normalizeColumn('pr.name') . " LIKE ?
            ", 24, [$like]);

            // Coincidencia con habilidades técnicas visibles
            $addCountScore("
                SELECT COUNT(*)
                FROM skills s
                WHERE s.user_id = portfolios.user_id
                AND s.type = 'technical'
                AND s.is_visible = 1
                AND " . $normalizeColumn('s.name') . " LIKE ?
            ", 5, 5, [$like]);

            // Coincidencia con proyectos y tecnologías usadas
            $addCountScore("
                SELECT COUNT(DISTINCT p.id)
                FROM projects p
                LEFT JOIN project_technology pt ON pt.project_id = p.id
                LEFT JOIN technologies t ON t.id = pt.technology_id
                WHERE p.portfolio_id = portfolios.id
                AND p.is_visible = 1
                AND (
                    " . $normalizeColumn('p.name') . " LIKE ?
                    OR " . $normalizeColumn('p.description') . " LIKE ?
                    OR " . $normalizeColumn('p.role') . " LIKE ?
                    OR " . $normalizeColumn('p.company') . " LIKE ?
                    OR " . $normalizeColumn('t.name') . " LIKE ?
                )
            ", 4, 4, [$like, $like, $like, $like, $like]);

            // Coincidencia con experiencia laboral o académica
            $addCountScore("
                SELECT COUNT(*)
                FROM experiences e
                WHERE e.user_id = portfolios.user_id
                AND e.is_visible = 1
                AND (
                    " . $normalizeColumn('e.title') . " LIKE ?
                    OR " . $normalizeColumn('e.institution') . " LIKE ?
                    OR " . $normalizeColumn('e.specialty') . " LIKE ?
                    OR " . $normalizeColumn('e.description') . " LIKE ?
                )
            ", 3, 4, [$like, $like, $like, $like]);

            // Coincidencia con nombre, biografía, ciudad, país o ubicación
            $addExistsScore("
                SELECT 1
                FROM users u
                LEFT JOIN user_locations ul ON ul.user_id = u.id
                WHERE u.id = portfolios.user_id
                AND (
                    " . $normalizeColumn('u.first_name') . " LIKE ?
                    OR " . $normalizeColumn('u.last_name') . " LIKE ?
                    OR " . $normalizeColumn("CONCAT(u.first_name, ' ', u.last_name)") . " LIKE ?
                    OR " . $normalizeColumn('u.biography') . " LIKE ?
                    OR " . $normalizeColumn('u.city') . " LIKE ?
                    OR " . $normalizeColumn('u.country') . " LIKE ?
                    OR " . $normalizeColumn('ul.address') . " LIKE ?
                )
            ", 8, [$like, $like, $like, $like, $like, $like, $like]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Coincidencia con categoría / rol
    |--------------------------------------------------------------------------
    */
    if ($request->filled('category')) {
        $categoryTerms = $expandSearchTerms($request->input('category'));

        foreach ($categoryTerms as $categoryTerm) {
            $like = "%{$categoryTerm}%";

            // Coincidencia fuerte con profesión
            $addExistsScore("
                SELECT 1
                FROM users u
                LEFT JOIN professions pr ON pr.id = u.profession_id
                WHERE u.id = portfolios.user_id
                AND " . $normalizeColumn('pr.name') . " LIKE ?
            ", 30, [$like]);

            // Coincidencia con cargo laboral
            $addCountScore("
                SELECT COUNT(*)
                FROM experiences e
                WHERE e.user_id = portfolios.user_id
                AND e.type = 'work'
                AND e.is_visible = 1
                AND " . $normalizeColumn('e.title') . " LIKE ?
            ", 3, 6, [$like]);

            // Coincidencia con rol en proyectos
            $addCountScore("
                SELECT COUNT(*)
                FROM projects p
                WHERE p.portfolio_id = portfolios.id
                AND p.is_visible = 1
                AND " . $normalizeColumn('p.role') . " LIKE ?
            ", 3, 6, [$like]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Coincidencia con empresa / institución
    |--------------------------------------------------------------------------
    */
    if ($request->filled('company')) {
    $companyTerms = $expandCompanyTerms($request->input('company'));

    $experienceConditions = [];
    $experienceBindings = [];

    $projectConditions = [];
    $projectBindings = [];

    foreach ($companyTerms as $companyTerm) {
        $like = "%{$companyTerm}%";

        $experienceConditions[] = "(
            " . $normalizeColumn('e.institution') . " LIKE ?
            OR " . $normalizeColumn('e.location') . " LIKE ?
        )";

        $experienceBindings[] = $like;
        $experienceBindings[] = $like;

        $projectConditions[] = $normalizeColumn('p.company') . " LIKE ?";
        $projectBindings[] = $like;
    }

    // Empresa laboral: cuenta como coincidencia una sola vez, no por cantidad de registros.
    $addExistsScore("
        SELECT 1
        FROM experiences e
        WHERE e.user_id = portfolios.user_id
        AND e.type = 'work'
        AND e.is_visible = 1
        AND (" . implode(' OR ', $experienceConditions) . ")
    ", 25, $experienceBindings);

    // Empresa/cliente en proyectos: también cuenta una sola vez.
    $addExistsScore("
        SELECT 1
        FROM projects p
        WHERE p.portfolio_id = portfolios.id
        AND p.is_visible = 1
        AND (" . implode(' OR ', $projectConditions) . ")
    ", 10, $projectBindings);
}

    /*
    |--------------------------------------------------------------------------
    | Coincidencia con idioma
    |--------------------------------------------------------------------------
    */
    if ($request->filled('language')) {
        $language = $normalizeText($request->input('language'));

        $languageMap = [
            'ingles' => ['ingles', 'english'],
            'english' => ['ingles', 'english'],

            'espanol' => ['espanol', 'spanish', 'castellano'],
            'spanish' => ['espanol', 'spanish', 'castellano'],
            'castellano' => ['espanol', 'spanish', 'castellano'],

            'portugues' => ['portugues', 'portuguese'],
            'portuguese' => ['portugues', 'portuguese'],

            'frances' => ['frances', 'french'],
            'french' => ['frances', 'french'],

            'aleman' => ['aleman', 'german'],
            'german' => ['aleman', 'german'],

            'italiano' => ['italiano', 'italian'],
            'italian' => ['italiano', 'italian'],
        ];

        $languageTerms = $languageMap[$language] ?? [$language];

        $languageConditions = [];
        $languageBindings = [];

        foreach ($languageTerms as $term) {
            $languageConditions[] = $normalizeColumn('s.name') . " LIKE ?";
            $languageBindings[] = "%{$term}%";
        }

        $addExistsScore("
            SELECT 1
            FROM skills s
            WHERE s.user_id = portfolios.user_id
            AND s.type = 'language'
            AND s.is_visible = 1
            AND (" . implode(' OR ', $languageConditions) . ")
        ", 25, $languageBindings);
    }

    /*
    |--------------------------------------------------------------------------
    | Coincidencia con tecnologías seleccionadas
    |--------------------------------------------------------------------------
    */
    if ($request->filled('skills')) {
        $skillsArray = array_filter((array) $request->input('skills'));

        foreach ($skillsArray as $skillName) {
            $skill = $normalizeText($skillName);
            $like = "%{$skill}%";

            $compatibilityParts[] = "
                CASE WHEN (
                    EXISTS (
                        SELECT 1
                        FROM skills s
                        WHERE s.user_id = portfolios.user_id
                        AND s.type = 'technical'
                        AND s.is_visible = 1
                        AND " . $normalizeColumn('s.name') . " LIKE ?
                    )
                    OR EXISTS (
                        SELECT 1
                        FROM projects p
                        INNER JOIN project_technology pt ON pt.project_id = p.id
                        INNER JOIN technologies t ON t.id = pt.technology_id
                        WHERE p.portfolio_id = portfolios.id
                        AND p.is_visible = 1
                        AND " . $normalizeColumn('t.name') . " LIKE ?
                    )
                ) THEN 16 ELSE 0 END
            ";

            $compatibilityBindings[] = $like;
            $compatibilityBindings[] = $like;
        }
    }

    $compatibilityScore = implode(' + ', $compatibilityParts);

    $query
        ->addSelect([
            'visible_projects_count' => DB::table('projects')
                ->selectRaw('COUNT(*)')
                ->whereColumn('projects.portfolio_id', 'portfolios.id')
                ->where('projects.is_visible', true),

            'technical_skills_count' => DB::table('skills')
                ->selectRaw('COUNT(*)')
                ->whereColumn('skills.user_id', 'portfolios.user_id')
                ->where('skills.type', 'technical')
                ->where('skills.is_visible', true),
        ])
        ->selectRaw("$completenessScore AS completeness_score")
        ->selectRaw("($compatibilityScore) AS compatibility_score", $compatibilityBindings)
        ->orderByDesc('compatibility_score')
        ->orderByDesc('completeness_score')
        ->orderByDesc('visible_projects_count')
        ->orderByDesc('technical_skills_count')
        ->orderBy('portfolios.created_at', 'desc');

    break;

    case 'projects':
        $query
            ->addSelect([
                'visible_projects_count' => DB::table('projects')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('projects.portfolio_id', 'portfolios.id')
                    ->where('projects.is_visible', true),
            ])
            ->orderByDesc('visible_projects_count')
            ->orderBy('portfolios.created_at', 'desc');
        break;

    case 'skills':
        $query
            ->addSelect([
                'technical_skills_count' => DB::table('skills')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('skills.user_id', 'portfolios.user_id')
                    ->where('skills.type', 'technical')
                    ->where('skills.is_visible', true),
            ])
            ->orderByDesc('technical_skills_count')
            ->orderBy('portfolios.created_at', 'desc');
        break;

    case 'az':
        $query
            ->join('users', 'users.id', '=', 'portfolios.user_id')
            ->orderBy('users.first_name', 'asc')
            ->orderBy('users.last_name', 'asc')
            ->select('portfolios.*');
        break;

    case 'za':
        $query
            ->join('users', 'users.id', '=', 'portfolios.user_id')
            ->orderBy('users.first_name', 'desc')
            ->orderBy('users.last_name', 'desc')
            ->select('portfolios.*');
        break;

    case 'desc':
    default:
        $query->orderBy('portfolios.created_at', 'desc');
        break;
}

// PAGINACIÓN
$query->with([
    'user.experiences' => function ($experienceQuery) {
        $experienceQuery
            ->where('type', 'work')
            ->where('is_visible', true)
            ->whereNotNull('start_date');
    }
]);

if ($minExperience !== null) {
    $allPortfolios = $query->get();

    $filteredPortfolios = $allPortfolios->filter(function ($portfolio) use ($minExperience) {
        $years = $this->calculateRealExperienceYears($portfolio->user->experiences ?? collect());

        return $years >= $minExperience;
    })->values();

    $page = request()->get('page', 1);
    $perPage = 12;

    $portfolios = new LengthAwarePaginator(
        $filteredPortfolios->forPage($page, $perPage),
        $filteredPortfolios->count(),
        $perPage,
        $page,
        [
            'path' => request()->url(),
            'query' => request()->query(),
        ]
    );
} else {
    $portfolios = $query->paginate(12)->appends($request->query());
}
// RESPUESTA AJAX: solo cuando el filtro JS pide JSON explícitamente
if (
    $request->ajax() &&
    $request->wantsJson() &&
    $request->query('_ajax') === '1'
) {
    return response()->json([
        'html' => view('partials.portfolio_cards', [
            'portfolios' => $portfolios,
            'ajax' => true
        ])->render(),
        'count' => $portfolios->total()
    ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
      ->header('Pragma', 'no-cache')
      ->header('Vary', 'Accept, X-Requested-With');
}

// RESPUESTA NORMAL: cuando entras o vuelves desde un portafolio
return view('portafolio.explore', compact('portfolios', 'categories', 'skills', 'companies'));
    }

    private function calculateRealExperienceYears($experiences): int
    {
        $today = Carbon::today();

        $intervals = [];

        foreach ($experiences as $experience) {
            if (!$experience->start_date) {
                continue;
            }

            $start = Carbon::parse($experience->start_date)->startOfDay();

            if ($experience->is_current || !$experience->end_date) {
                $end = $today->copy();
            } else {
                $end = Carbon::parse($experience->end_date)->startOfDay();
            }

            if ($start->greaterThan($today)) {
                continue;
            }

            if ($end->greaterThan($today)) {
                $end = $today->copy();
            }

            if ($end->lessThan($start)) {
                continue;
            }

            $intervals[] = [
                'start' => $start,
                'end' => $end,
            ];
        }

        if (empty($intervals)) {
            return 0;
        }

        usort($intervals, function ($a, $b) {
            return $a['start']->timestamp <=> $b['start']->timestamp;
        });

        $merged = [];

        foreach ($intervals as $interval) {
            if (empty($merged)) {
                $merged[] = $interval;
                continue;
            }

            $lastIndex = count($merged) - 1;
            $last = $merged[$lastIndex];

            if ($interval['start']->lessThanOrEqualTo($last['end'])) {
                if ($interval['end']->greaterThan($last['end'])) {
                    $merged[$lastIndex]['end'] = $interval['end'];
                }
            } else {
                $merged[] = $interval;
            }
        }

        $totalDays = 0;

        foreach ($merged as $interval) {
            $totalDays += $interval['start']->diffInDays($interval['end']);
        }

        return (int) floor($totalDays / 365);
    }

    /**
     * ==========================================
     * VERIFICAR SI EL PORTAFOLIO DEBE DESPUBLICARSE
     * ==========================================
     */
    private function verificarYDespublicarSiEsNecesario($user)
    {
        // Si no está publicado, no hacer nada
        if (!$user->portfolio || !$user->portfolio->is_public) {
            return;
        }
        
        // Verificar si aún cumple con los requisitos
        if (!$this->portafolioTieneContenido($user)) {
            // Despublicar automáticamente
            $user->portfolio->is_public = false;
           // $user->portfolio->published_at = null;
            $user->portfolio->save();
            
            // Guardar en sesión para mostrar mensaje
            session()->flash('auto_unpublished', true);
        }
    }
}