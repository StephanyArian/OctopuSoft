<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Portfolio;
use App\Models\Profession; // Suponiendo el nombre del modelo de categorías
use App\Models\Skill;      // Para las tecnologías

class PreviewController extends Controller
{
    /**
     * Vista previa del portafolio para el usuario logueado (con edición)
     */
    public function preview()
    {
        // Obtener usuario logueado
        $user = Auth::user();
        
        // Cargar relaciones SEGÚN TU ESTRUCTURA DE BD
        $user->load([
            'profession',                           // profesión del usuario
            'skills.projects',                      // habilidades del usuario + proyectos vinculados
            'experiences',                          // experiencias del usuario
            'portfolio.projects',                   // portafolio y sus proyectos
            'professionalNetworks.platform',         // redes profesionales con plataforma
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
            // Convertir nivel numérico (1-5) a texto
            $nivel = '';
            if ($skill->level ==3) $nivel = 'Avanzado';
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

        // Separar por categoría (Frontend / Backend)
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
        // EXPERIENCIAS LABORALES (type = 'work')
        // ==========================================
        $experiencias = $user->experiences
        ->where('type', 'work')
        ->where('is_visible', true)
        ->groupBy('institution')  // Agrupar por empresa
        ->map(function($grupo) {

            $primera = $grupo->first();

           
            
            // Listar todos los roles de esta empresa
            $roles = $grupo->map(function($exp) {
                return $exp->title;
            })->implode(' / ');
            
            // Obtener fechas (tomar la más temprana y más reciente)
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
        })
        ->values();
        
        // ==========================================
        // INFORMACIÓN ACADÉMICA (type = 'education')
        // ==========================================
        $academicas = $user->experiences->where('type', 'education')->map(function($edu) {
            return (object) [
                'institucion' => $edu->institution,
                'titulo' => $edu->title,
                'specialty'      => $edu->specialty,
                'fecha_inicio' => $edu->start_date,
                'fecha_fin' => $edu->end_date,
                'estudio_actual' => $edu->is_current,
                'descripcion' => $edu->description,
                'evidence_url'   => $edu->evidence_url,
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
        
        // Cargar redes desde professional_networks
        foreach ($user->professionalNetworks as $network) {
            if (!$network->is_visible) {
                continue;  
            }
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

        // Limpiar URLs
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
            'redes'
        ));
    }

    /**
     * Muestra el portafolio público de cualquier usuario (HU-16)
     * No requiere autenticación - usa vista separada sin layout
     */
    public function publicShow($slug)
    {
        // Buscar portafolio por slug y que sea público
        $portfolio = \App\Models\Portfolio::where('slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();
        
        $user = $portfolio->user;
        
        // Cargar relaciones (solo datos visibles públicamente)
        $user->load([
            'profession',
            'skills' => fn($q) => $q->where('is_visible', true),
            'experiences' => fn($q) => $q->where('is_visible', true),
            'portfolio.projects' => fn($q) => $q->where('is_visible', true),
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
        // INFORMACIÓN ACADÉMICA
        // ==========================================
        $academicas = $user->experiences->where('type', 'education')->map(fn($edu) => (object) [
            'institucion' => $edu->institution,
            'titulo' => $edu->title,
            'fecha_inicio' => $edu->start_date,
            'fecha_fin' => $edu->end_date,
            'estudio_actual' => $edu->is_current,
            'descripcion' => $edu->description
        ]);
        
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
        
        // Limpiar URLs
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
        
        // Retornar la vista pública (separada, sin layout)
        return view('portafolio.publico', compact(
            'user',
            'habilidadesTecnicasFrontend',
            'habilidadesTecnicasBackend',
            'habilidadesBlandas',
            'experiencias',
            'academicas',
            'proyectos',
            'redes',
            'portfolio'
        ));
    }

    //Página de exploración — todos los portafolios públicos
 
    /**
     * Página de exploración unificada — Soporta carga normal y filtrado AJAX
     */
    public function explore(Request $request)
    {
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

    $query = Portfolio::where('is_public', true)
        ->with(['user.profession', 'user.skills', 'projects.technologies']);

    if ($request->filled('search')) {
        $search = trim($request->input('search'));

        $query->where(function ($q) use ($search) {
            $q->whereHas('user', function ($u) use ($search) {
                $u->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                  ->orWhere('biography', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('user.profession', function ($p) use ($search) {
                $p->where('name', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('user.skills', function ($s) use ($search) {
                $s->where('name', 'LIKE', "%{$search}%");
            })

            ->orWhereHas('projects.technologies', function ($t) use ($search) {
                $t->where('name', 'LIKE', "%{$search}%");
            })

            ->orWhereHas('projects', function ($p) use ($search) {
            $p->where('name', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('role', 'LIKE', "%{$search}%")
              ->orWhere('company', 'LIKE', "%{$search}%");
           });
        });
    }

    if ($request->filled('category')) {
    $query->whereHas('user.profession', function ($q) use ($request) {
        $q->where('name', $request->category);
    });
}

    if ($request->filled('skills')) {
    $skillsArray = array_filter((array) $request->input('skills'));

    foreach ($skillsArray as $skillName) {
        $query->where(function ($q) use ($skillName) {

            // 1. Busca en tecnologías/habilidades que domina el usuario
            $q->whereHas('user.skills', function ($s) use ($skillName) {
                $s->where('name', $skillName)
                  ->where('type', 'technical');
            })

            // 2. Busca también en tecnologías usadas dentro de proyectos
            ->orWhereHas('projects.technologies', function ($t) use ($skillName) {
                $t->where('name', $skillName);
            });
        });
    }
}
    $sort = $request->input('sort', 'desc');
    $sort = in_array($sort, ['asc', 'desc']) ? $sort : 'desc';

    $query->orderBy('created_at', $sort);

    if ($request->ajax()) {
        $portfolios = $query->get();

        return response()->json([
            'html' => view('partials.portfolio_cards', [
                'portfolios' => $portfolios,
                'ajax' => true
            ])->render(),
            'count' => $portfolios->count()
        ]);
    }

    $portfolios = $query->paginate(12);

    return view('portafolio.explore', compact('portfolios', 'categories', 'skills'));
   }
}