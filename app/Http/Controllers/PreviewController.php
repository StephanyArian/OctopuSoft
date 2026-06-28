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
    /**
     * ==========================================
     * NUEVO: Verificar si el portafolio tiene contenido suficiente
     * ==========================================
     */
    private function portafolioTieneContenido($user)
    {
        // Contar items en cada sección
        $contenido = [
            'experiencias' => $user->experiences->where('type', 'work')->where('is_visible', true)->count(),
            'academicas'   => $user->experiences->where('type', 'education')->where('is_visible', true)->count(),
            'proyectos'    => $user->portfolio ? $user->portfolio->projects->where('is_visible', true)->count() : 0,
            'habilidades_tecnicas' => $user->skills->where('type', 'technical')->where('is_visible', true)->count(),
            'habilidades_blandas'  => $user->skills->where('type', 'soft')->where('is_visible', true)->count(),
            'idiomas'      => $user->skills->where('type', 'language')->where('is_visible', true)->count(),
            'biografia'    => !empty($user->biography),
            'profesion'    => !empty($user->profession_id),
        ];

        // Contar secciones completas
        $seccionesCompletas = 0;
        foreach ($contenido as $key => $value) {
            if (in_array($key, ['biografia', 'profesion'])) {
                if ($value) $seccionesCompletas++;
            } else {
                if ($value > 0) $seccionesCompletas++;
            }
        }

        // Total de items (excluyendo biografía y profesión)
        $totalItems = $contenido['experiencias'] + $contenido['academicas'] + 
                      $contenido['proyectos'] + $contenido['habilidades_tecnicas'] + 
                      $contenido['habilidades_blandas'] + $contenido['idiomas'];

        // 🔥 REGLA NUEVA: mínimo 2 secciones completas Y al menos 2 items totales
        return $seccionesCompletas >= 2 && $totalItems >= 2;
    }

    /**
     * ==========================================
     * NUEVO: Obtener campos faltantes para mostrar al usuario
     * ==========================================
     */
    private function obtenerCamposFaltantes($user)
    {
        $faltantes = [];
        
        if ($user->experiences->where('type', 'work')->where('is_visible', true)->count() === 0) {
            $faltantes[] = 'Agregar al menos una experiencia laboral';
        }
        if ($user->experiences->where('type', 'education')->where('is_visible', true)->count() === 0) {
            $faltantes[] = 'Agregar al menos una formación académica';
        }
        if ($user->portfolio && $user->portfolio->projects->where('is_visible', true)->count() === 0) {
            $faltantes[] = 'Agregar al menos un proyecto';
        }
        if ($user->skills->where('type', 'technical')->where('is_visible', true)->count() === 0) {
            $faltantes[] = 'Agregar habilidades técnicas';
        }
        if ($user->skills->where('type', 'soft')->where('is_visible', true)->count() === 0) {
            $faltantes[] = 'Agregar habilidades blandas';
        }
        if ($user->skills->where('type', 'language')->where('is_visible', true)->count() === 0) {
            $faltantes[] = 'Agregar idiomas';
        }
        if (empty($user->biography)) {
            $faltantes[] = 'Completar tu biografía';
        }
        if (empty($user->profession_id)) {
            $faltantes[] = 'Seleccionar tu profesión';
        }

        return $faltantes;
    }

    /**
     * ==========================================
     * NUEVO: PUBLICAR PORTAFOLIO
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
    'SEGIP',
    'UMSS',
    'JalaSoft',
    'Digital Harboard',
    'Apple',
    'Microsoft',
    'Banco Unión',
    'Google Bolivia',
    'ABC',
    'SIN',
    'Gobernación',
    'Alcaldía',
    'YPFB',
    'ENTEL',
    'Tigo',
    'Viva',
    'Banco Nacional de Bolivia',
    'Banco Mercantil Santa Cruz',
    'Banco Bisa',
    'Caja Nacional de Salud',
    'Aduana Nacional',
    'Impuestos Nacionales',
    'SENASAG',
    'ENDE',
    'BoA',
    'COTEL',
    'EMAPA',
    'Farmacorp',
    'Hipermaxi',
    'Univalle',
    'UPB',
    'Universidad Católica Boliviana',
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

    return $terms
        ->map(fn ($term) => trim($term))
        ->filter()
        ->unique()
        ->values();
};

// BÚSQUEDA POR TEXTO GENERAL
if ($request->filled('search')) {
    $terms = $expandSearchTerms($request->input('search'));

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
    $company = $normalizeText($request->input('company'));

    $query->whereHas('user.experiences', function ($e) use ($company, $normalizeColumn) {
        $e->where('type', 'work')
          ->where('is_visible', true)
          ->whereRaw($normalizeColumn('institution') . " LIKE ?", ["%{$company}%"]);
    });
}

// FILTRO POR CATEGORÍA / PROFESIÓN
if ($request->filled('category')) {
    $categoryTerms = $expandSearchTerms($request->input('category'));

    $query->where(function ($q) use ($categoryTerms, $normalizeColumn) {
        foreach ($categoryTerms as $category) {
            $like = "%{$category}%";

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

                'languages_count' => DB::table('skills')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('skills.user_id', 'portfolios.user_id')
                    ->where('skills.type', 'language')
                    ->where('skills.is_visible', true),

                'experiences_count' => DB::table('experiences')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('experiences.user_id', 'portfolios.user_id')
                    ->where('experiences.type', 'work')
                    ->where('experiences.is_visible', true),

                'education_count' => DB::table('experiences')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('experiences.user_id', 'portfolios.user_id')
                    ->where('experiences.type', 'education')
                    ->where('experiences.is_visible', true),

                'networks_count' => DB::table('professional_networks')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('professional_networks.user_id', 'portfolios.user_id')
                    ->where('professional_networks.is_visible', true),
            ])
            ->orderByDesc(DB::raw("
                (
                    CASE WHEN EXISTS (
                        SELECT 1 FROM users
                        WHERE users.id = portfolios.user_id
                        AND users.photo_base64 IS NOT NULL
                        AND users.photo_base64 != ''
                    ) THEN 1 ELSE 0 END
                    +
                    CASE WHEN EXISTS (
                        SELECT 1 FROM users
                        WHERE users.id = portfolios.user_id
                        AND users.biography IS NOT NULL
                        AND users.biography != ''
                    ) THEN 1 ELSE 0 END
                    +
                    CASE WHEN EXISTS (
                        SELECT 1 FROM users
                        WHERE users.id = portfolios.user_id
                        AND users.profession_id IS NOT NULL
                    ) THEN 1 ELSE 0 END
                    +
                    CASE WHEN EXISTS (
                        SELECT 1 FROM skills
                        WHERE skills.user_id = portfolios.user_id
                        AND skills.type = 'technical'
                        AND skills.is_visible = 1
                    ) THEN 1 ELSE 0 END
                    +
                    CASE WHEN EXISTS (
                        SELECT 1 FROM projects
                        WHERE projects.portfolio_id = portfolios.id
                        AND projects.is_visible = 1
                    ) THEN 1 ELSE 0 END
                    +
                    CASE WHEN EXISTS (
                        SELECT 1 FROM professional_networks
                        WHERE professional_networks.user_id = portfolios.user_id
                        AND professional_networks.is_visible = 1
                    ) THEN 1 ELSE 0 END
                    +
                    CASE WHEN EXISTS (
                        SELECT 1 FROM experiences
                        WHERE experiences.user_id = portfolios.user_id
                        AND experiences.type = 'work'
                        AND experiences.is_visible = 1
                    ) THEN 1 ELSE 0 END
                    +
                    CASE WHEN EXISTS (
                        SELECT 1 FROM experiences
                        WHERE experiences.user_id = portfolios.user_id
                        AND experiences.type = 'education'
                        AND experiences.is_visible = 1
                    ) THEN 1 ELSE 0 END
                    +
                    CASE WHEN EXISTS (
                        SELECT 1 FROM skills
                        WHERE skills.user_id = portfolios.user_id
                        AND skills.type = 'language'
                        AND skills.is_visible = 1
                    ) THEN 1 ELSE 0 END
                )
            "))
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
}