<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>{{ $user->first_name ?? 'Portafolio' }} {{ $user->last_name ?? '' }} | Portafolio Profesional</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/preview.css') }}">
    <style>
        .btn-volver-flotante {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 999;
            background: #0abf9e;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 40px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.2s;
            font-family: inherit;
            text-decoration: none;
        }
        .btn-volver-flotante:hover {
            transform: translateY(-2px);
            background: #07866e;
        }
        
        @media (max-width: 768px) {
            .btn-volver-flotante {
                top: 10px;
                left: 10px;
                padding: 8px 16px;
                font-size: 12px;
            }
        }

        /* Botón ver todos los proyectos - ABAJO */
        .btn-ver-todos {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .btn-ver-todos button {
            background: #0abf9e;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-ver-todos button:hover {
            background: #07866e;
            transform: translateY(-2px);
        }

        /* Modal de todos los proyectos */
        .modal-todos-proyectos {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(3px);
        }
        .modal-todos-content {
            background: white;
            border-radius: 20px;
            max-width: 950px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            position: relative;
            animation: modalFadeIn 0.2s ease;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .modal-todos-header {
            padding: 18px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }
        .modal-todos-header h2 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .modal-todos-header h2 i {
            color: #0abf9e;
            margin-right: 8px;
        }
        .close-todos-modal {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: #94a3b8;
            transition: all 0.2s;
        }
        .close-todos-modal:hover {
            color: #ef4444;
        }
        .todos-proyectos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 24px;
        }
        .proyecto-card-modal {
            background: #ffffff;
            border-radius: 16px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .proyecto-card-modal:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            border-color: #0abf9e;
        }
        .proyecto-card-modal h4 {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            color: #1abc9c;
        }
        .proyecto-card-modal .proyecto-tech {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }
        .proyecto-card-modal .proyecto-tech span {
            background: #eef2ff;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            color: #0abf9e;
        }
        .proyecto-card-modal .proyecto-fecha {
            font-size: 0.7rem;
            color: #94a3b8;
            margin: 8px 0;
        }
        .proyecto-card-modal .estado-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }
        .estado-completado { background: #d1fae5; color: #065f46; }
        .estado-curso { background: #fef3c7; color: #92400e; }
        .estado-default { background: #f1f5f9; color: #64748b; }

        /* ⭐ EFECTO HOVER PROFESIONAL PARA TARJETAS DE PROYECTOS ⭐ */
        .card {
            transition: all 0.25s ease;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            background: white;
            position: relative;
        }
        .card:hover {
            border-color: #0abf9e;
            box-shadow: 0 12px 28px -10px rgba(10, 191, 158, 0.25);
            transform: translateY(-4px);
        }
        .card:hover h3 {
            color: #0abf9e !important;
            transform: translateX(3px);
        }
        .card h3 {
            transition: all 0.2s;
            display: inline-block;
        }
       

        @media (max-width: 768px) {
            .todos-proyectos-grid {
                grid-template-columns: 1fr;
                padding: 16px;
            }
        }
    </style>
</head>
<body>

<button class="btn-volver-flotante" onclick="window.history.back()">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    Volver
</button>

@php
    $allowedHtmlTags = '<p><br><strong><b><em><i><u><s><strike><del><sup><sub><ul><ol><li><a><span><h1><h2><h3><blockquote><pre><div>';
    
    // Separar proyectos: los 2 más recientes para mostrar, el resto para el modal
    $proyectosRecientes = $proyectos->take(2);
    $proyectosRestantes = $proyectos->skip(2);
@endphp

<div class="preview-container">
    
    <!-- CABECERA CON DATOS PERSONALES -->
    <div class="profile-header">
        <div class="profile-info">
            <h1>{{ $user->first_name ?? 'Usuario' }} {{ $user->last_name ?? '' }}</h1>
            <div class="title">{{ $user->profession->name ?? 'Profesional' }}</div>

            <div class="profile-contact-list">
                @if($user->city || $user->country)
                <div class="contact-row">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $user->city ?? '' }}{{ $user->country ? ', ' . $user->country : '' }}</span>
                </div>
                @endif

                @if($redes['correo'])
                <div class="contact-row">
                    <i class="fas fa-envelope"></i>
                    <span>{{ $redes['correo'] }}</span>
                </div>
                @endif

                @if($redes['whatsapp'])
                <div class="contact-row">
                    <i class="fab fa-whatsapp"></i>
                    <span>{{ $redes['whatsapp'] }}</span>
                </div>
                @endif

                @if($user->biography)
                <div class="contact-row" style="align-items: flex-start;">
                    <i class="fas fa-quote-left" style="margin-top: 4px;"></i>
                    <div class="ql-snow" style="width: 100%;">
                        <div class="ql-editor" style="padding: 0; min-height: 0; font-family: inherit;">{!! strip_tags($user->biography, $allowedHtmlTags) !!}</div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Iconos de redes sociales -->
            <div class="profile-social-icons">
                @if($redes['linkedin'])
                    <a href="{{ $redes['linkedin'] }}" target="_blank" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                @endif
                @if($redes['github'])
                    <a href="{{ $redes['github'] }}" target="_blank" title="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                @endif
                @if($redes['maps_url'])
                    <a href="{{ $redes['maps_url'] }}" target="_blank" title="Ubicación en mapa">
                        <i class="fas fa-map-marker-alt"></i>
                    </a>
                @endif
                @if($redes['whatsapp'])
                    @php $wpNum = preg_replace('/[^0-9]/', '', $redes['whatsapp']); @endphp
                    <a href="https://wa.me/{{ $wpNum }}" target="_blank" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                @endif
                @if($user->portfolio && $user->portfolio->is_public)
                    <a href="javascript:void(0)" onclick="abrirModalCompartir()" title="Compartir Portafolio">
                        <i class="fas fa-share-nodes"></i>
                    </a>
                @endif
                @if($redes['correo'])
                    <a href="mailto:{{ $redes['correo'] }}" title="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                @endif
                @if($redes['otros'])
                    <a href="{{ $redes['otros'] }}" target="_blank" title="Otro">
                        <i class="fas fa-globe"></i>
                    </a>
                @endif
            </div>
        </div>

        <!-- Lado derecho: foto -->
        <div class="profile-avatar-side">
            @if($user->photo_base64)
                <img src="{{ $user->photo_base64 }}" alt="Foto de perfil">
            @else
                <div class="avatar-placeholder">
                    <i class="fas fa-user-circle"></i>
                </div>
            @endif
        </div>
    </div>
    
    <!-- EXPERIENCIA LABORAL -->
    <div class="section">
        <h2><i class="fas fa-briefcase"></i> Experiencia laboral</h2>
        <div class="cards-grid">
            @forelse($experiencias as $exp)
            <div class="card">
                <h3>{{ $exp->empresa }}</h3>
                <div class="subtitle">
                    @if($exp->ubicacion)
                        {{ $exp->ubicacion }}
                    @endif
                </div>
                
                @if(str_contains($exp->cargo, ' / '))
                    <ul class="roles-list">
                        @foreach(explode(' / ', $exp->cargo) as $rol)
                            <li>{{ $rol }}</li>
                        @endforeach
                    </ul>
                @else
                    <div class="role-single">{{ $exp->cargo }}</div>
                @endif
                
                <div class="date">
                    {{ \Carbon\Carbon::parse($exp->fecha_inicio)->format('d F Y') }}
                    @if($exp->fecha_fin)
                        — {{ \Carbon\Carbon::parse($exp->fecha_fin)->format('d F Y') }}
                    @elseif($exp->trabajo_actual)
                        — Actualidad
                    @endif
                </div>

                @if($exp->descripcion)
                <div class="description-wrapper">
                    <div class="description collapsed" id="desc-exp-{{ $loop->index }}">
                        <div class="ql-snow"><div class="ql-editor">{!! strip_tags($exp->descripcion, $allowedHtmlTags) !!}</div></div>
                    </div>
                    @if(mb_strlen(trim(strip_tags($exp->descripcion))) > 150)
                        <button class="ver-mas-btn" onclick="toggleDesc('desc-exp-{{ $loop->index }}', this)">Ver más</button>
                    @endif
                </div>
                @endif
            </div>
            @empty
                <div class="empty-message" style="grid-column: 1 / -1;">No hay experiencias laborales registradas</div>
            @endforelse
        </div>
    </div>

    <!-- INFORMACIÓN ACADÉMICA -->
    <div class="section">
        <h2><i class="fas fa-graduation-cap"></i> Información académica</h2>
        <div class="cards-grid">
            @forelse($academicas as $aca)
                <div class="card">
                    <h3>{{ $aca->institucion }}</h3>
                    <div class="subtitle">{{ $aca->titulo }}</div>
                    
                    @if(isset($aca->specialty) && $aca->specialty)
                        <div class="specialty-badge">
                            <i class="fas fa-tag"></i> {{ $aca->specialty }}
                        </div>
                    @endif
                    
                    <div class="date">
                        {{ \Carbon\Carbon::parse($aca->fecha_inicio)->format('F Y') }}
                        @if($aca->fecha_fin)
                            — {{ \Carbon\Carbon::parse($aca->fecha_fin)->format('F Y') }}
                        @elseif($aca->estudio_actual)
                            — Actualidad
                        @endif
                    </div>
                    
                    @if($aca->descripcion)
                    <div class="description-wrapper">
                        <div class="description collapsed" id="desc-aca-{{ $loop->index }}">
                            <div class="ql-snow"><div class="ql-editor">{!! strip_tags($aca->descripcion, $allowedHtmlTags) !!}</div></div>
                        </div>
                        @if(mb_strlen(trim(strip_tags($aca->descripcion))) > 150)
                            <button class="ver-mas-btn" onclick="toggleDesc('desc-aca-{{ $loop->index }}', this)">Ver más</button>
                        @endif
                    </div>
                    @endif
                    
                    <!-- MOSTRAR EVIDENCIAS ACADÉMICAS -->
                    @if(isset($aca->evidence_url) && $aca->evidence_url)
                        @php 
                            $evidencias = is_array($aca->evidence_url) ? $aca->evidence_url : (json_decode($aca->evidence_url, true) ?? []);
                        @endphp
                        @if(!empty($evidencias))
                            <div class="academic-evidences">
                                <div class="evidences-title">
                                    <i class="fas fa-paperclip"></i> Evidencias
                                </div>
                                <div class="evidences-grid-preview">
                                    @foreach($evidencias as $evidencia)
                                        @if(pathinfo($evidencia, PATHINFO_EXTENSION) === 'pdf')
                                            <a href="{{ asset('storage/' . $evidencia) }}" target="_blank" class="pdf-card-preview" title="{{ basename($evidencia) }}">
                                                <div class="pdf-icon-preview">
                                                    <i class="fas fa-file-pdf"></i>
                                                    <span>PDF</span>
                                                </div>
                                                <span class="pdf-name">{{ \Illuminate\Support\Str::limit(basename($evidencia), 20) }}</span>
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/' . $evidencia) }}" target="_blank" class="img-card-preview">
                                                <img src="{{ asset('storage/' . $evidencia) }}" alt="Evidencia">
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            @empty
                <div class="empty-message" style="grid-column: 1 / -1;">No hay información académica registrada</div>
            @endforelse
        </div>
    </div>

    <!-- HABILIDADES TÉCNICAS -->
    <div class="section">
        <h2><i class="fas fa-code"></i> Habilidades técnicas</h2>
        @if(($habilidadesTecnicasFrontend ?? collect())->count() === 0 && ($habilidadesTecnicasBackend ?? collect())->count() === 0)
            <div class="empty-message" style="margin:0 40px;">No hay habilidades técnicas registradas</div>
        @else
            @if(($habilidadesTecnicasFrontend ?? collect())->count() > 0)
                <div class="tech-category-title">Frontend</div>
                <div class="tech-skills-grid">
                    @foreach($habilidadesTecnicasFrontend as $skill)
                        @php
                            $nivel = $skill->nivel ?? 'Intermedio';
                            if ($nivel == 'Avanzado') $claseNivel = 'advanced';
                            elseif ($nivel == 'Intermedio') $claseNivel = 'intermediate';
                            else $claseNivel = 'basic';
                            $hasProjects = isset($skill->proyectos) && count($skill->proyectos) > 0;
                        @endphp
                        <div class="tech-skill-item">
                            <div class="tech-skill-header">
                                <span class="tech-skill-name">{{ $skill->nombre }}</span>
                                <span class="tech-skill-level">{{ $nivel }}</span>
                            </div>
                            <div class="tech-skill-bar-bg">
                                <div class="tech-skill-bar-fill {{ $claseNivel }}"></div>
                            </div>
                            @if($hasProjects)
                                <div class="tech-skill-projects">
                                    @foreach($skill->proyectos as $p)
                                        <a href="javascript:void(0)"
                                           class="tech-skill-project-chip"
                                           onclick="abrirModalPorId({{ $p->id }});"
                                           title="Ver proyecto: {{ $p->nombre }}">
                                            {{ $p->nombre }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if(($habilidadesTecnicasBackend ?? collect())->count() > 0)
                <div class="tech-category-title">Backend</div>
                <div class="tech-skills-grid">
                    @foreach($habilidadesTecnicasBackend as $skill)
                        @php
                            $nivel = $skill->nivel ?? 'Intermedio';
                            if ($nivel == 'Avanzado') $claseNivel = 'advanced';
                            elseif ($nivel == 'Intermedio') $claseNivel = 'intermediate';
                            else $claseNivel = 'basic';
                            $hasProjects = isset($skill->proyectos) && count($skill->proyectos) > 0;
                        @endphp
                        <div class="tech-skill-item">
                            <div class="tech-skill-header">
                                <span class="tech-skill-name">{{ $skill->nombre }}</span>
                                <span class="tech-skill-level">{{ $nivel }}</span>
                            </div>
                            <div class="tech-skill-bar-bg">
                                <div class="tech-skill-bar-fill {{ $claseNivel }}"></div>
                            </div>
                            @if($hasProjects)
                                <div class="tech-skill-projects">
                                    @foreach($skill->proyectos as $p)
                                        <a href="javascript:void(0)"
                                           class="tech-skill-project-chip"
                                           onclick="abrirModalPorId({{ $p->id }});"
                                           title="Ver proyecto: {{ $p->nombre }}">
                                            {{ $p->nombre }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    <!-- IDIOMAS -->
    <div class="section">
        <h2><i class="fas fa-language"></i> Idiomas</h2>
        @if($idiomas->isEmpty())
            <div class="empty-message">No hay idiomas registrados</div>
        @else
            <div class="idiomas-preview-grid">
                @foreach($idiomas as $idioma)
                <div class="idioma-preview-card">
                    <div class="idioma-preview-header">
                        <div class="idioma-preview-left">
                            <span class="idioma-bandera">{{ $idioma->bandera }}</span>
                            <div class="idioma-preview-info">
                                <span class="idioma-preview-nombre">{{ $idioma->nombre }}</span>
                                <span class="idioma-preview-nivel">{{ $idioma->nivel_label }} — {{ $idioma->nivel_nombre }}</span>
                            </div>
                        </div>
                        @if($idioma->certificado)
                        <a href="{{ asset('storage/' . $idioma->certificado) }}"
                        target="_blank" class="idioma-cert-link">
                            <i class="fas fa-certificate"></i> Cert.
                        </a>
                        @endif
                    </div>
                    <div class="idioma-barra-wrap">
                        <div class="idioma-barra-fill" style="width: {{ $idioma->porcentaje }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- HABILIDADES BLANDAS -->
    <div class="section">
        <h2><i class="fas fa-heart"></i> Habilidades blandas</h2>
        <div class="skills-container">
            @forelse($habilidadesBlandas as $skill)
                <span class="soft-skill-tag"><i class="fas fa-star" style="color:#0abf9e;"></i> {{ $skill->nombre }}</span>
            @empty
                <div class="empty-message">No hay habilidades blandas registradas</div>
            @endforelse
        </div>
    </div>

    <!-- PROYECTOS - SOLO 2 PROYECTOS + BOTÓN VER TODOS ABAJO -->
    <div class="section">
        <h2><i class="fas fa-project-diagram"></i> Proyectos</h2>
        
        @if($proyectos->isEmpty())
            <div class="empty-message">No hay proyectos registrados</div>
        @else
            <div class="cards-grid">
                @foreach($proyectosRecientes as $proyecto)
                    @php
                        $modalProyectoPayload = [
                            'nombre' => $proyecto->nombre,
                            'descripcion' => strip_tags($proyecto->descripcion ?? '', $allowedHtmlTags),
                            'fecha_inicio' => optional($proyecto->fecha_inicio)->format('d/m/Y'),
                            'fecha_fin' => optional($proyecto->fecha_fin)->format('d/m/Y'),
                            'estado' => $proyecto->estado,
                            'rol' => $proyecto->rol,
                            'cliente' => $proyecto->cliente,
                            'tecnologias' => $proyecto->tecnologias,
                            'evidencias' => $proyecto->evidencias,
                        ];
                    @endphp
                    <!-- ⭐ TARJETA COMPLETA CLICKEABLE ⭐ -->
                    <div class="card" id="project-card-{{ $proyecto->id }}" onclick='abrirModal(@json($modalProyectoPayload))'>
                        <h3 style="color:#1abc9c; font-size:1rem; margin-bottom: 8px;">{{ $proyecto->nombre }}</h3>
                        
                        @if($proyecto->descripcion)
                        <div class="description-wrapper">
                            <div class="description collapsed proyecto-desc-wrap" id="desc-proy-{{ $loop->index }}">
                                <div class="ql-snow"><div class="ql-editor">{!! strip_tags($proyecto->descripcion, $allowedHtmlTags) !!}</div></div>
                            </div>
                            @if(mb_strlen(trim(strip_tags($proyecto->descripcion))) > 150)
                                <button type="button" class="ver-mas-btn" onclick="event.stopPropagation(); toggleDesc('desc-proy-{{ $loop->index }}', this)">Ver más</button>
                            @endif
                        </div>
                        @endif

                        <div class="date" style="font-size:0.7rem;">
                            {{ \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') }}
                            @if($proyecto->fecha_fin)
                                — {{ \Carbon\Carbon::parse($proyecto->fecha_fin)->format('d/m/Y') }}
                            @endif
                            | {{ $proyecto->estado ?? 'En progreso' }}
                        </div>
                        <div class="description" style="font-size:0.75rem;">
                            Rol: {{ $proyecto->rol ?? '' }}
                            @if($proyecto->cliente) | Cliente: {{ $proyecto->cliente }} @endif
                        </div>
                        @if(!empty($proyecto->tecnologias))
                            <div class="proyecto-tecnologias">
                                @foreach(array_slice($proyecto->tecnologias, 0, 3) as $tec)
                                    <span class="tec-badge">{{ $tec }}</span>
                                @endforeach
                                @if(count($proyecto->tecnologias) > 3)
                                    <span class="tec-badge">+{{ count($proyecto->tecnologias) - 3 }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Botón "Ver todos los proyectos" solo si hay más de 2 proyectos - COLOCADO ABAJO -->
            @if($proyectosRestantes->count() > 0)
                <div class="btn-ver-todos">
                    <button onclick="abrirModalTodosProyectos()">
                        <i class="fas fa-th-large"></i> Ver todos los proyectos ({{ $proyectos->count() }})
                    </button>
                </div>
            @endif
        @endif
    </div>

    <!-- MODAL "TODOS LOS PROYECTOS" -->
    <div id="modal-todos-proyectos" class="modal-todos-proyectos">
        <div class="modal-todos-content">
            <div class="modal-todos-header">
                <h2><i class="fas fa-project-diagram"></i> Todos los proyectos ({{ $proyectos->count() }})</h2>
                <button class="close-todos-modal" onclick="cerrarModalTodosProyectos()">✕</button>
            </div>
            <div class="todos-proyectos-grid">
                @foreach($proyectos as $proyecto)
                    @php
                        $estadoClass = '';
                        if ($proyecto->estado == 'Completado') $estadoClass = 'estado-completado';
                        elseif ($proyecto->estado == 'En curso') $estadoClass = 'estado-curso';
                        else $estadoClass = 'estado-default';
                        
                        $modalProyectoPayload = [
                            'nombre' => $proyecto->nombre,
                            'descripcion' => strip_tags($proyecto->descripcion ?? '', $allowedHtmlTags),
                            'fecha_inicio' => optional($proyecto->fecha_inicio)->format('d/m/Y'),
                            'fecha_fin' => optional($proyecto->fecha_fin)->format('d/m/Y'),
                            'estado' => $proyecto->estado,
                            'rol' => $proyecto->rol,
                            'cliente' => $proyecto->cliente,
                            'tecnologias' => $proyecto->tecnologias,
                            'evidencias' => $proyecto->evidencias,
                        ];
                    @endphp
                    <div class="proyecto-card-modal" onclick='abrirModal(@json($modalProyectoPayload)); cerrarModalTodosProyectos();'>
                        <h4>{{ $proyecto->nombre }}</h4>
                        <div class="proyecto-fecha">
                            <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') }}
                            @if($proyecto->fecha_fin) → {{ \Carbon\Carbon::parse($proyecto->fecha_fin)->format('d/m/Y') }} @endif
                        </div>
                        <div class="description" style="font-size:0.75rem; margin: 8px 0; color:#475569;">
                            Rol: {{ $proyecto->rol ?? '' }}
                            @if($proyecto->cliente) | Cliente: {{ $proyecto->cliente }} @endif
                        </div>
                        <span class="estado-badge {{ $estadoClass }}">{{ $proyecto->estado ?? 'En progreso' }}</span>
                        @if(!empty($proyecto->tecnologias))
                            <div class="proyecto-tech">
                                @foreach(array_slice($proyecto->tecnologias, 0, 3) as $tec)
                                    <span>{{ $tec }}</span>
                                @endforeach
                                @if(count($proyecto->tecnologias) > 3)
                                    <span>+{{ count($proyecto->tecnologias) - 3 }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- MODAL COMPARTIR -->
    <div id="modal-compartir" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(2px);">
        <div style="background:#fff; border-radius:12px; width:450px; max-width:90%; position:relative; padding:24px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h2 style="margin:0; font-size:20px; font-weight:600; color:#333;">Compartir</h2>
                <button onclick="cerrarModalCompartir()" style="background:none; border:none; font-size:20px; cursor:pointer; color:#888; transition:color 0.2s;">✕</button>
            </div>
            
            <div style="display:flex; justify-content:space-between; margin-bottom:24px; text-align:center;">
                <a href="javascript:void(0)" style="text-decoration:none; color:#333; transition:transform 0.2s;" onclick="shareTo('whatsapp')" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <div style="width:55px; height:55px; border-radius:50%; background:#25D366; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; color:white; font-size:26px;">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <span style="font-size:12px; font-weight:500;">WhatsApp</span>
                </a>
                <a href="javascript:void(0)" style="text-decoration:none; color:#333; transition:transform 0.2s;" onclick="shareTo('facebook')" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <div style="width:55px; height:55px; border-radius:50%; background:#1877F2; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; color:white; font-size:26px;">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <span style="font-size:12px; font-weight:500;">Facebook</span>
                </a>
                <a href="javascript:void(0)" style="text-decoration:none; color:#333; transition:transform 0.2s;" onclick="shareTo('twitter')" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <div style="width:55px; height:55px; border-radius:50%; background:#000000; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; color:white; font-size:26px;">
                        <i class="fab fa-x-twitter"></i>
                    </div>
                    <span style="font-size:12px; font-weight:500;">X</span>
                </a>
                <a href="javascript:void(0)" style="text-decoration:none; color:#333; transition:transform 0.2s;" onclick="shareTo('email')" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <div style="width:55px; height:55px; border-radius:50%; background:#7f8c8d; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; color:white; font-size:26px;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <span style="font-size:12px; font-weight:500;">Correo</span>
                </a>
            </div>

            <div style="display:flex; border:1px solid #e0e0e0; border-radius:8px; padding:6px; background:#f9f9f9; align-items:center; transition:border-color 0.2s;">
                <input type="text" id="share-link-input" readonly value="{{ $user->portfolio ? url('/portafolio/' . $user->portfolio->slug) : '' }}" style="flex:1; border:none; background:transparent; padding:8px 12px; outline:none; color:#555; font-size:14px; text-overflow:ellipsis; white-space:nowrap; overflow:hidden;">
                <button onclick="copiarLinkPortafolio()" id="btn-copiar-link" style="background:white; border:1px solid #e0e0e0; border-radius:20px; padding:6px 18px; cursor:pointer; font-weight:600; font-size:14px; color:#333; transition:all 0.2s;" onmouseover="this.style.backgroundColor='#f0f0f0'" onmouseout="this.style.backgroundColor='white'">Copiar</button>
            </div>
        </div>
    </div>

    <!-- MODAL PROYECTO (detalle) -->
    <div id="modal-proyecto" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:12px; max-width:680px; width:90%; max-height:88vh; overflow-y:auto; position:relative;">
            <div style="padding:24px 28px; border-bottom:1px solid #f0f0f0; display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <h2 id="modal-nombre" style="color:#1a0a2e; font-size:22px; margin:0 0 10px;"></h2>
                    <div id="modal-badges" style="display:flex; flex-wrap:wrap; gap:6px;"></div>
                </div>
                <button onclick="cerrarModal()" style="background:none; border:none; font-size:20px; cursor:pointer; color:#888;">✕</button>
            </div>
            <div id="modal-fechas" style="padding:12px 28px; background:#f9f9f9; border-bottom:1px solid #f0f0f0; font-size:13px; color:#666; display:flex; gap:20px;"></div>
            <div style="padding:24px 28px;">
                <div style="margin-bottom:20px;">
                    <div style="font-size:11px; font-weight:600; color:#888; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:8px;">Descripción</div>
                    <div class="ql-snow" style="border:none;padding:0;margin:0;">
                        <div id="modal-descripcion" class="ql-editor" style="padding:0;min-height:0;color:#444;font-size:14px;line-height:1.6;"></div>
                    </div>
                </div>
                <div id="modal-tec-section" style="margin-bottom:20px;">
                    <div style="font-size:11px; font-weight:600; color:#888; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:8px;">Stack Tecnológico</div>
                    <div id="modal-tecnologias" style="display:flex; flex-wrap:wrap; gap:6px;"></div>
                </div>
                <div id="modal-evidencias" style="display:none;">
                    <div style="font-size:11px; font-weight:600; color:#888; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:12px; padding-top:16px; border-top:1px solid #f0f0f0;">
                        Evidencias <span id="modal-ev-count" style="color:#1abc9c;"></span>
                    </div>
                    <div id="modal-evidencias-lista" style="display:flex; flex-direction:column; gap:10px;"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function toggleDesc(id, btn) {
        const el = document.getElementById(id);
        if (el.classList.contains('collapsed')) {
            el.classList.remove('collapsed');
            el.classList.add('expanded');
            btn.textContent = 'Ver menos';
        } else {
            el.classList.remove('expanded');
            el.classList.add('collapsed');
            btn.textContent = 'Ver más';
        }
    }

    window.previewProjectsById = {!! json_encode(
        collect($proyectos)->keyBy('id')->map(function($p) use ($allowedHtmlTags) {
            return [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'descripcion' => strip_tags($p->descripcion ?? '', $allowedHtmlTags),
                'fecha_inicio' => optional($p->fecha_inicio)->format('d/m/Y'),
                'fecha_fin' => optional($p->fecha_fin)->format('d/m/Y'),
                'estado' => $p->estado,
                'rol' => $p->rol,
                'cliente' => $p->cliente,
                'tecnologias' => $p->tecnologias,
                'evidencias' => $p->evidencias,
            ];
        })
    ) !!};

    function abrirModalPorId(projectId) {
        const data = (window.previewProjectsById || {})[projectId];
        if (!data) return;
        const target = document.getElementById('project-card-' + projectId);
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        abrirModal(data);
    }

    function previewEscapeHtml(s) {
        if (s === null || s === undefined) return '';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function abrirModal(data) {
        document.getElementById('modal-nombre').textContent = data.nombre;

        let badgesDiv = document.getElementById('modal-badges');
        badgesDiv.innerHTML = '';
        const badge = (texto, bg, color) => `<span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:${bg};color:${color};">${texto}</span>`;
        const badgeIcon = (iconClass, texto, bg, color) => `<span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:${bg};color:${color};"><i class="${iconClass}" style="font-size:10px;opacity:.9;"></i>${texto}</span>`;

        if (data.estado) {
            let bg = data.estado === 'Completado' ? '#d1fae5' : data.estado === 'En curso' ? '#fef3c7' : '#f1f5f9';
            let color = data.estado === 'Completado' ? '#065f46' : data.estado === 'En curso' ? '#92400e' : '#64748b';
            badgesDiv.innerHTML += badge(previewEscapeHtml(data.estado), bg, color);
        }
        if (data.rol) badgesDiv.innerHTML += badgeIcon('fas fa-user-check', previewEscapeHtml(data.rol), '#ede9fe', '#5b21b6');
        if (data.cliente) badgesDiv.innerHTML += badgeIcon('fas fa-building', previewEscapeHtml(data.cliente), '#f1f5f9', '#475569');

        let fechasDiv = document.getElementById('modal-fechas');
        fechasDiv.innerHTML = '';
        if (data.fecha_inicio) fechasDiv.innerHTML += `<span><i class="far fa-calendar-alt" style="color:#94a3b8;margin-right:4px;"></i>Inicio: <strong>${previewEscapeHtml(data.fecha_inicio)}</strong></span>`;
        if (data.fecha_fin) fechasDiv.innerHTML += `<span><i class="far fa-calendar-alt" style="color:#94a3b8;margin-right:4px;"></i>Fin: <strong>${previewEscapeHtml(data.fecha_fin)}</strong></span>`;

        document.getElementById('modal-descripcion').innerHTML = data.descripcion ?? '';
        let tecDiv = document.getElementById('modal-tecnologias');
        let tecSection = document.getElementById('modal-tec-section');
        tecDiv.innerHTML = '';
        if (data.tecnologias && data.tecnologias.length) {
            tecSection.style.display = 'block';
            data.tecnologias.forEach(t => {
                tecDiv.innerHTML += `<span class="tec-badge">${previewEscapeHtml(t)}</span>`;
            });
        } else {
            tecSection.style.display = 'none';
        }

        let evDiv = document.getElementById('modal-evidencias-lista');
        let evSec = document.getElementById('modal-evidencias');
        evDiv.innerHTML = '';
        if (data.evidencias && data.evidencias.length) {
            evSec.style.display = 'block';
            document.getElementById('modal-ev-count').textContent = '(' + data.evidencias.length + ')';
            data.evidencias.forEach(ev => {
                let item = document.createElement('div');
                item.style.cssText = 'padding:12px;border:1px solid #eee;border-radius:8px;background:#fafafa;';
                if (ev.tipo === 'imagen' && ev.imagen) {
                    item.innerHTML = `<p style="font-size:12px;color:#888;margin:0 0 8px;">🖼️ ${ev.titulo}</p><img src="${ev.imagen}" style="width:100%;border-radius:6px;max-height:220px;object-fit:cover;">`;
                } else if (ev.tipo === 'enlace') {
                    item.innerHTML = `<p style="font-size:12px;color:#888;margin:0 0 4px;">🔗 ${ev.titulo}</p><a href="${ev.url}" target="_blank" style="color:#1abc9c;font-size:13px;word-break:break-all;">${ev.url}</a>`;
                } else if (ev.tipo === 'repositorio') {
                    item.innerHTML = `<p style="font-size:12px;color:#888;margin:0 0 4px;">📦 ${ev.titulo}</p><a href="${ev.url}" target="_blank" style="color:#1abc9c;font-size:13px;word-break:break-all;">${ev.url}</a>`;
                }
                evDiv.appendChild(item);
            });
        } else {
            evSec.style.display = 'none';
        }

        document.getElementById('modal-proyecto').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function cerrarModal() {
        document.getElementById('modal-proyecto').style.display = 'none';
        document.body.style.overflow = '';
        // Si veníamos del modal de todos los proyectos, lo volvemos a mostrar
        if (window.desdeModalTodos) {
            document.getElementById('modal-todos-proyectos').style.display = 'flex';
            window.desdeModalTodos = false;
        }
    }

    document.getElementById('modal-proyecto').addEventListener('click', function(e) {
        if (e.target === this) cerrarModal();
    });

    function abrirModalCompartir() {
        document.getElementById('modal-compartir').style.display = 'flex';
    }

    function cerrarModalCompartir() {
        document.getElementById('modal-compartir').style.display = 'none';
        document.getElementById('btn-copiar-link').textContent = 'Copiar';
    }

    document.getElementById('modal-compartir').addEventListener('click', function(e) {
        if (e.target === this) cerrarModalCompartir();
    });

    function copiarLinkPortafolio() {
        const input = document.getElementById('share-link-input');
        input.select();
        input.setSelectionRange(0, 99999); 
        navigator.clipboard.writeText(input.value).then(() => {
            const btn = document.getElementById('btn-copiar-link');
            btn.textContent = '¡Copiado!';
            setTimeout(() => { btn.textContent = 'Copiar'; }, 2000);
        });
    }

    function shareTo(platform) {
        const link = encodeURIComponent(document.getElementById('share-link-input').value);
        const text = encodeURIComponent('¡Mira mi portafolio profesional en DevFolio!');
        let url = '';
        
        switch(platform) {
            case 'whatsapp':
                url = `https://api.whatsapp.com/send?text=${text} ${link}`;
                break;
            case 'facebook':
                url = `https://www.facebook.com/sharer/sharer.php?u=${link}`;
                break;
            case 'twitter':
                url = `https://twitter.com/intent/tweet?text=${text}&url=${link}`;
                break;
            case 'email':
                url = `mailto:?subject=${text}&body=Puedes ver mi portafolio aquí: ${link}`;
                break;
        }
        
        if(url) window.open(url, '_blank', 'width=600,height=400');
    }

    function abrirModalTodosProyectos() {
        document.getElementById('modal-todos-proyectos').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        window.desdeModalTodos = true;
    }

    function cerrarModalTodosProyectos() {
        document.getElementById('modal-todos-proyectos').style.display = 'none';
        document.body.style.overflow = '';
        window.desdeModalTodos = false;
    }

    document.getElementById('modal-todos-proyectos')?.addEventListener('click', function(e) {
        if (e.target === this) cerrarModalTodosProyectos();
    });
</script>

</body>
</html>