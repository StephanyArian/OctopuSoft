<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreviewController extends Controller
{
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
            
            // Combinar descripciones
            $descripcion = $grupo->map(function($exp) {
                return $exp->description;
            })->filter()->implode("\n\n");
            
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
                'fecha_inicio' => $edu->start_date,
                'fecha_fin' => $edu->end_date,
                'estudio_actual' => $edu->is_current,
                'descripcion' => $edu->description
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
            'ubicacion' => null 
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

        
        if ($user->location && $user->location->address && $user->location->show_location) {
            $redes['ubicacion'] = $user->location->address;
        }


        // ==========================================
// LIMPIAR Y VALIDAR URLs
// ==========================================

        // 1. Limpiar 'otros' si no es una URL válida
        if (isset($redes['otros']) && $redes['otros'] && !filter_var($redes['otros'], FILTER_VALIDATE_URL)) {
            $redes['otros'] = null;
        }

        // 2. Limpiar ubicación si parece una URL
        if (isset($redes['ubicacion']) && $redes['ubicacion'] && preg_match('/^https?:\/\//', $redes['ubicacion'])) {
            $redes['ubicacion'] = null;
        }

        // 3. Agregar protocolo solo a URLs que sean válidas
        foreach ($redes as $key => $url) {
            if ($url && !in_array($key, ['correo', 'whatsapp', 'ubicacion'])) {
                // Si ya tiene protocolo, está bien
                if (preg_match('/^https?:\/\//', $url)) {
                    continue;
                }
                // Si no tiene protocolo pero parece una URL válida, agregar https://
                if (filter_var('https://' . $url, FILTER_VALIDATE_URL)) {
                    $redes[$key] = 'https://' . $url;
                } else {
                    // No es una URL válida, poner null
                    $redes[$key] = null;
                }
            }
        }

         
    
    

        return view('Preview', compact(
            'user',
            'habilidadesTecnicas',
            'habilidadesTecnicasFrontend',
            'habilidadesTecnicasBackend',
            'habilidadesBlandas',
            'experiencias',
            'academicas',
            'proyectos',
            'redes'
        ));
    }

        
}