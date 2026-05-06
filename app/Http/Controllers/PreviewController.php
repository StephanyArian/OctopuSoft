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
            'skills',                               // habilidades del usuario
            'experiences',                          // experiencias del usuario
            'portfolio.projects',                   // portafolio y sus proyectos
            'professionalNetworks.platform',         // redes profesionales con plataforma
            'location'
        ]);
        
        // ==========================================
        // HABILIDADES TÉCNICAS (type = 'technical')
        // ==========================================
        $habilidadesTecnicas = $user->skills->where('type', 'technical')->map(function($skill) {
            // Convertir nivel numérico (1-5) a texto
            $nivel = '';
            if ($skill->level ==3) $nivel = 'Avanzado';
            elseif ($skill->level == 2) $nivel = 'Intermedio';
            else $nivel = 'Básico';
            
            return (object) [
                'nombre' => $skill->name,
                'nivel' => $nivel
            ];
        });
        
        // ==========================================
        // HABILIDADES BLANDAS (type = 'soft')
        // ==========================================
        $habilidadesBlandas = $user->skills->where('type', 'soft')->map(function($skill) {
            return (object) ['nombre' => $skill->name];
        });
        
        // ==========================================
        // EXPERIENCIAS LABORALES (type = 'work')
        // ==========================================
        $experiencias = $user->experiences->where('type', 'work')->map(function($exp) {
            return (object) [
                'empresa' => $exp->institution,
                'cargo' => $exp->title,
                'ubicacion' => $exp->location,
                'fecha_inicio' => $exp->start_date,
                'fecha_fin' => $exp->end_date,
                'trabajo_actual' => $exp->is_current,
                'descripcion' => $exp->description
            ];
        });
        
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
            $proyectos = $user->portfolio->projects->map(function($project) {
                return (object) [
                    'nombre' => $project->name,
                    'descripcion' => $project->description,
                    'fecha_inicio' => $project->start_date,
                    'fecha_fin' => $project->end_date,
                    'estado' => $project->status,
                    'rol' => $project->role,
                    'cliente' => null  // Tu tabla no tiene campo cliente
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
            'habilidadesBlandas',
            'experiencias',
            'academicas',
            'proyectos',
            'redes'
        ));
    }

        
}