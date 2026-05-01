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
            'professionalNetworks.platform'         // redes profesionales con plataforma
        ]);
        
        // ==========================================
        // HABILIDADES TÉCNICAS (type = 'technical')
        // ==========================================
        $habilidadesTecnicas = $user->skills->where('type', 'technical')->map(function($skill) {
            // Convertir nivel numérico (1-5) a texto
            $nivel = '';
            if ($skill->level >= 4) $nivel = 'Avanzado';
            elseif ($skill->level >= 2) $nivel = 'Intermedio';
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
            'whatsapp' => $user->phone,
            'correo' => $user->email,
            'otros' => null
        ];
        
        // Cargar redes desde professional_networks
        foreach ($user->professionalNetworks as $network) {
            $platformName = $network->platform->name ?? '';
            $profileUrl = $network->profile_url;
            
            if (stripos($platformName, 'linkedin') !== false) {
                $redes['linkedin'] = $profileUrl;
            } elseif (stripos($platformName, 'github') !== false) {
                $redes['github'] = $profileUrl;
            } else {
                $redes['otros'] = $profileUrl;
            }
        }
        
        // ==========================================
        // UBICACIÓN (opcional)
        // ==========================================
        // Si tienes user_locations:
        // $ubicacion = $user->location;
        
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