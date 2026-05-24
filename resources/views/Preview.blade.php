@extends('layouts.app-classic')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/preview.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@php
    $allowedHtmlTags = '<p><br><strong><b><em><i><u><s><strike><del><sup><sub><ul><ol><li><a><span><h1><h2><h3><blockquote><pre><div>';
    
    $proyectosRecientes = $proyectos->take(2);
    $proyectosRestantes = $proyectos->skip(2);
    
    $limiteMostrar = 2;
    
    $experienciasRecientes = $experiencias->take($limiteMostrar);
    $experienciasRestantes = $experiencias->skip($limiteMostrar);
    
    $academicasRecientes = $academicas->take($limiteMostrar);
    $academicasRestantes = $academicas->skip($limiteMostrar);
    
    $habilidadesFrontendRecientes = ($habilidadesTecnicasFrontend ?? collect())->take($limiteMostrar);
    $habilidadesFrontendRestantes = ($habilidadesTecnicasFrontend ?? collect())->skip($limiteMostrar);
    $habilidadesBackendRecientes = ($habilidadesTecnicasBackend ?? collect())->take($limiteMostrar);
    $habilidadesBackendRestantes = ($habilidadesTecnicasBackend ?? collect())->skip($limiteMostrar);
    
    $habilidadesBlandasRecientes = $habilidadesBlandas->take($limiteMostrar);
    $habilidadesBlandasRestantes = $habilidadesBlandas->skip($limiteMostrar);
    
    $idiomasRecientes = $idiomas->take($limiteMostrar);
    $idiomasRestantes = $idiomas->skip($limiteMostrar);
    
    $banderas = [
        'inglés'=>'🇬🇧','ingles'=>'🇬🇧','español'=>'🇧🇴','espanol'=>'🇧🇴',
        'portugués'=>'🇧🇷','portugues'=>'🇧🇷','francés'=>'🇫🇷','frances'=>'🇫🇷',
        'alemán'=>'🇩🇪','aleman'=>'🇩🇪','italiano'=>'🇮🇹','chino'=>'🇨🇳',
        'japonés'=>'🇯🇵','japones'=>'🇯🇵','coreano'=>'🇰🇷','árabe'=>'🇸🇦','arabe'=>'🇸🇦',
        'ruso'=>'🇷🇺','hindi'=>'🇮🇳','hindú'=>'🇮🇳','indu'=>'🇮🇳',
        'holandés'=>'🇳🇱','holandes'=>'🇳🇱','sueco'=>'🇸🇪','noruego'=>'🇳🇴',
        'danés'=>'🇩🇰','danes'=>'🇩🇰','polaco'=>'🇵🇱','turco'=>'🇹🇷',
        'griego'=>'🇬🇷','hebreo'=>'🇮🇱','tailandés'=>'🇹🇭','tailandes'=>'🇹🇭',
        'vietnamita'=>'🇻🇳','indonesio'=>'🇮🇩','catalán'=>'🏳️','catalan'=>'🏳️',
        'mandarin'=>'🇨🇳','mandarín'=>'🇨🇳',
    ];
    $codigos = [
        'inglés'=>'EN','ingles'=>'EN','español'=>'ES','espanol'=>'ES',
        'francés'=>'FR','frances'=>'FR','alemán'=>'DE','aleman'=>'DE',
        'portugués'=>'PT','portugues'=>'PT','italiano'=>'IT','chino'=>'ZH',
        'japonés'=>'JP','japones'=>'JP','coreano'=>'KO','árabe'=>'AR','arabe'=>'AR',
        'ruso'=>'RU','hindi'=>'HI','indu'=>'HI','mandarin'=>'ZH','mandarín'=>'ZH',
    ];
    $colorFondos = ['#0abf9e','#3b82f6','#a855f7','#f59e0b','#f43f5e','#22d3ee','#4ade80','#fb923c'];
    $coloresBarra = [
        'linear-gradient(90deg,#07866e,#0abf9e)',
        'linear-gradient(90deg,#1d4ed8,#3b82f6)',
        'linear-gradient(90deg,#7c3aed,#a855f7)',
        'linear-gradient(90deg,#b45309,#f59e0b)',
        'linear-gradient(90deg,#be123c,#f43f5e)',
        'linear-gradient(90deg,#0e7490,#22d3ee)',
        'linear-gradient(90deg,#15803d,#4ade80)',
        'linear-gradient(90deg,#9a3412,#fb923c)',
    ];
@endphp

<a href="javascript:history.back()" class="btn-flotante">
    <i class="fas fa-edit"></i> Continuar editando
</a>

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
                <div class="contact-row">
                    <i class="fas fa-quote-left"></i>
                    <span>{{ $user->biography }}</span>
                </div>
                @endif
            </div>

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
            @forelse($experienciasRecientes as $exp)
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
        
        @if($experienciasRestantes->count() > 0)
            <div class="btn-ver-todos">
                <button onclick="abrirModalExperiencias()">
                    <i class="fas fa-briefcase"></i> Ver todas las experiencias ({{ $experiencias->count() }})
                </button>
            </div>
        @endif
    </div>

    <!-- INFORMACIÓN ACADÉMICA -->
    <div class="section">
        <h2><i class="fas fa-graduation-cap"></i> Información académica</h2>
        <div class="cards-grid">
            @forelse($academicasRecientes as $aca)
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
                    
                    @if(isset($aca->evidence_url) && $aca->evidence_url)
                        @php $evidencias = json_decode($aca->evidence_url, true); @endphp
                        @if(!empty($evidencias))
                            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px;">
                                @foreach($evidencias as $evidencia)
                                    @if(pathinfo($evidencia, PATHINFO_EXTENSION) === 'pdf')
                                        <a href="{{ asset('storage/' . $evidencia) }}" target="_blank" 
                                           style="display: inline-flex; align-items: center; gap: 6px; color: #0abf9e; font-size: 12px; font-weight: 600; text-decoration: none; padding: 6px 14px; border-radius: 20px; background: #f0fdf9; border: 1px solid #d1fae5; transition: all 0.2s;"
                                           onmouseover="this.style.background='#d1fae5';"
                                           onmouseout="this.style.background='#f0fdf9';">
                                            <i class="fas fa-file-pdf"></i> Ver PDF
                                        </a>
                                    @else
                                        <a href="javascript:void(0)" 
                                           onclick="abrirLightbox('{{ asset('storage/' . $evidencia) }}')" 
                                           style="display: inline-flex; align-items: center; gap: 6px; color: #0abf9e; font-size: 12px; font-weight: 600; text-decoration: none; padding: 6px 14px; border-radius: 20px; background: #f0fdf9; border: 1px solid #d1fae5; transition: all 0.2s;"
                                           onmouseover="this.style.background='#d1fae5';"
                                           onmouseout="this.style.background='#f0fdf9';">
                                            <i class="fas fa-certificate"></i> Ver certificado
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            @empty
                <div class="empty-message" style="grid-column: 1 / -1;">No hay información académica registrada</div>
            @endforelse
        </div>
        
        @if($academicasRestantes->count() > 0)
            <div class="btn-ver-todos">
                <button onclick="abrirModalAcademicas()">
                    <i class="fas fa-graduation-cap"></i> Ver toda la formación académica ({{ $academicas->count() }})
                </button>
            </div>
        @endif
    </div>

    <!-- HABILIDADES TÉCNICAS -->
    <div class="section">
        <h2><i class="fas fa-code"></i> Habilidades técnicas</h2>
        @if(($habilidadesTecnicasFrontend ?? collect())->count() === 0 && ($habilidadesTecnicasBackend ?? collect())->count() === 0)
            <div class="empty-message" style="margin:0 40px;">No hay habilidades técnicas registradas</div>
        @else
            @if(($habilidadesFrontendRecientes ?? collect())->count() > 0)
                <div class="tech-category-title">Frontend</div>
                <div class="tech-skills-grid">
                    @foreach($habilidadesFrontendRecientes as $skill)
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

            @if(($habilidadesBackendRecientes ?? collect())->count() > 0)
                <div class="tech-category-title">Backend</div>
                <div class="tech-skills-grid">
                    @foreach($habilidadesBackendRecientes as $skill)
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
            
            @if(($habilidadesFrontendRestantes->count() > 0) || ($habilidadesBackendRestantes->count() > 0))
                <div class="btn-ver-todos">
                    <button onclick="abrirModalTecnicas()">
                        <i class="fas fa-code"></i> Ver todas las habilidades técnicas
                    </button>
                </div>
            @endif
        @endif
    </div>

    <!-- HABILIDADES BLANDAS -->
    <div class="section">
        <h2><i class="fas fa-heart"></i> Habilidades blandas</h2>
        <div class="skills-container">
            @forelse($habilidadesBlandasRecientes as $skill)
                <span class="soft-skill-tag"><i class="fas fa-star" style="color:#0abf9e;"></i> {{ $skill->nombre }}</span>
            @empty
                <div class="empty-message">No hay habilidades blandas registradas</div>
            @endforelse
        </div>
        
        @if($habilidadesBlandasRestantes->count() > 0)
            <div class="btn-ver-todos">
                <button onclick="abrirModalBlandas()">
                    <i class="fas fa-heart"></i> Ver todas las habilidades blandas ({{ $habilidadesBlandas->count() }})
                </button>
            </div>
        @endif
    </div>

    <!-- IDIOMAS -->
    <div class="section">
        <h2><i class="fas fa-language"></i> Idiomas</h2>
        @if($idiomas->isEmpty())
            <div class="empty-message">No hay idiomas registrados</div>
        @else
            <div class="idiomas-preview-grid">
                @foreach($idiomasRecientes as $index => $idioma)
                @php
                    $banderaEmoji = $banderas[strtolower($idioma->nombre)] ?? null;
                    $codigo = $codigos[strtolower($idioma->nombre)] ?? strtoupper(substr($idioma->nombre, 0, 2));
                    $colorFondo = $colorFondos[$index % count($colorFondos)];
                    $colorBarra = $coloresBarra[$index % count($coloresBarra)];
                @endphp
                <div class="idioma-preview-card">
                    <div class="idioma-preview-header">
                        <div class="idioma-preview-left">
                            @if($banderaEmoji)
                                <span class="idioma-bandera">{{ $banderaEmoji }}</span>
                            @else
                                <span class="idioma-flag-code-preview" style="background: {{ $colorFondo }}; width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: white; margin-right: 10px;">
                                    {{ $codigo }}
                                </span>
                            @endif
                            <div class="idioma-preview-info">
                                <span class="idioma-preview-nombre">{{ $idioma->nombre }}</span>
                                <span class="idioma-preview-nivel">{{ $idioma->nivel_label }} — {{ $idioma->nivel_nombre }}</span>
                            </div>
                        </div>
                        @if($idioma->certificado)
                            <a href="javascript:void(0)" onclick="abrirLightbox('{{ asset('storage/' . $idioma->certificado) }}')" class="idioma-cert-link">
                                <i class="fas fa-certificate"></i> Cert.
                            </a>
                        @endif
                    </div>
                    <div class="idioma-barra-wrap">
                        <div class="idioma-barra-fill-custom" style="width: {{ $idioma->porcentaje }}%; background: {{ $colorBarra }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if($idiomasRestantes->count() > 0)
                <div class="btn-ver-todos">
                    <button onclick="abrirModalIdiomas()">
                        <i class="fas fa-language"></i> Ver todos los idiomas ({{ $idiomas->count() }})
                    </button>
                </div>
            @endif
        @endif
    </div>

    <!-- PROYECTOS -->
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
                    <div class="card" id="project-card-{{ $proyecto->id }}" onclick='abrirModal(@json($modalProyectoPayload))' style="cursor: pointer;">
                        <h3 style="color:#1abc9c;">{{ $proyecto->nombre }}</h3>
                        
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

                        <div class="date">
                            {{ \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') }}
                            @if($proyecto->fecha_fin)
                                — {{ \Carbon\Carbon::parse($proyecto->fecha_fin)->format('d/m/Y') }}
                            @endif
                            | {{ $proyecto->estado ?? 'En progreso' }}
                        </div>
                        <div class="description">
                            Rol: {{ $proyecto->rol ?? '' }}
                            @if($proyecto->cliente) | Cliente: {{ $proyecto->cliente }} @endif
                        </div>
                        @if(!empty($proyecto->tecnologias))
                            <div class="proyecto-tecnologias">
                                @foreach($proyecto->tecnologias as $tec)
                                    <span class="tec-badge">{{ $tec }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($proyectosRestantes->count() > 0)
                <div class="btn-ver-todos">
                    <button onclick="abrirModalTodosProyectos()">
                        <i class="fas fa-th-large"></i> Ver todos los proyectos ({{ $proyectos->count() }})
                    </button>
                </div>
            @endif
        @endif
    </div>

    <!-- ==================== MODALES ==================== -->

    <!-- MODAL TODOS LOS PROYECTOS -->
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
                    <div class="proyecto-card-modal" onclick='cerrarModalTodosProyectos(); abrirModal(@json($modalProyectoPayload));'>
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

    <!-- MODAL TODAS LAS EXPERIENCIAS -->
    <div id="modal-todos-experiencias" class="modal-todos-proyectos">
        <div class="modal-todos-content">
            <div class="modal-todos-header">
                <h2><i class="fas fa-briefcase"></i> Todas las experiencias ({{ $experiencias->count() }})</h2>
                <button class="close-todos-modal" onclick="cerrarModalExperiencias()">✕</button>
            </div>
            <div class="todos-proyectos-grid">
                @foreach($experiencias as $exp)
                    <div class="proyecto-card-modal">
                        <h4>{{ $exp->empresa }}</h4>
                        <div class="proyecto-fecha">
                            <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($exp->fecha_inicio)->format('d/m/Y') }}
                            @if($exp->fecha_fin) → {{ \Carbon\Carbon::parse($exp->fecha_fin)->format('d/m/Y') }} 
                            @elseif($exp->trabajo_actual) → Actualidad @endif
                        </div>
                        <div class="description" style="font-size:0.75rem; margin: 8px 0; color:#475569;">
                            {{ $exp->cargo }}
                            @if($exp->ubicacion) | {{ $exp->ubicacion }} @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- MODAL TODAS LAS ACADÉMICAS -->
    <div id="modal-todos-academicas" class="modal-todos-proyectos">
        <div class="modal-todos-content">
            <div class="modal-todos-header">
                <h2><i class="fas fa-graduation-cap"></i> Toda la formación académica ({{ $academicas->count() }})</h2>
                <button class="close-todos-modal" onclick="cerrarModalAcademicas()">✕</button>
            </div>
            <div class="todos-proyectos-grid">
                @foreach($academicas as $aca)
                    <div class="proyecto-card-modal">
                        <h4>{{ $aca->institucion }}</h4>
                        <div class="proyecto-fecha">
                            <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($aca->fecha_inicio)->format('d/m/Y') }}
                            @if($aca->fecha_fin) → {{ \Carbon\Carbon::parse($aca->fecha_fin)->format('d/m/Y') }}
                            @elseif($aca->estudio_actual) → Actualidad @endif
                        </div>
                        <div class="description" style="font-size:0.75rem; margin: 8px 0; color:#475569;">
                            {{ $aca->titulo }}
                            @if($aca->specialty) | {{ $aca->specialty }} @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- MODAL TODAS LAS HABILIDADES TÉCNICAS -->
    <div id="modal-todos-tecnicas" class="modal-todos-proyectos">
        <div class="modal-todos-content">
            <div class="modal-todos-header">
                <h2><i class="fas fa-code"></i> Todas las habilidades técnicas</h2>
                <button class="close-todos-modal" onclick="cerrarModalTecnicas()">✕</button>
            </div>
            <div class="todos-proyectos-grid">
                @foreach($habilidadesTecnicasFrontend ?? [] as $skill)
                    <div class="proyecto-card-modal">
                        <h4>🎨 Frontend - {{ $skill->nombre }}</h4>
                        <div class="proyecto-fecha">Nivel: {{ $skill->nivel ?? 'Intermedio' }}</div>
                        @if(isset($skill->proyectos) && count($skill->proyectos) > 0)
                            <div class="proyecto-tech" style="margin-top: 8px;">
                                <span>📁 {{ count($skill->proyectos) }} proyecto(s)</span>
                            </div>
                        @endif
                    </div>
                @endforeach
                @foreach($habilidadesTecnicasBackend ?? [] as $skill)
                    <div class="proyecto-card-modal">
                        <h4>⚙️ Backend - {{ $skill->nombre }}</h4>
                        <div class="proyecto-fecha">Nivel: {{ $skill->nivel ?? 'Intermedio' }}</div>
                        @if(isset($skill->proyectos) && count($skill->proyectos) > 0)
                            <div class="proyecto-tech" style="margin-top: 8px;">
                                <span>📁 {{ count($skill->proyectos) }} proyecto(s)</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- MODAL TODAS LAS HABILIDADES BLANDAS -->
    <div id="modal-todos-blandas" class="modal-todos-proyectos">
        <div class="modal-todos-content">
            <div class="modal-todos-header">
                <h2><i class="fas fa-heart"></i> Todas las habilidades blandas ({{ $habilidadesBlandas->count() }})</h2>
                <button class="close-todos-modal" onclick="cerrarModalBlandas()">✕</button>
            </div>
            <div class="todos-proyectos-grid">
                @foreach($habilidadesBlandas as $skill)
                    <div class="proyecto-card-modal">
                        <h4><i class="fas fa-star" style="color:#0abf9e;"></i> {{ $skill->nombre }}</h4>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- MODAL TODOS LOS IDIOMAS -->
    <div id="modal-todos-idiomas" class="modal-todos-proyectos">
        <div class="modal-todos-content">
            <div class="modal-todos-header">
                <h2><i class="fas fa-language"></i> Todos los idiomas ({{ $idiomas->count() }})</h2>
                <button class="close-todos-modal" onclick="cerrarModalIdiomas()">✕</button>
            </div>
            <div class="todos-proyectos-grid">
                @foreach($idiomas as $index => $idioma)
                @php
                    $banderaEmoji = $banderas[strtolower($idioma->nombre)] ?? null;
                    $codigo = $codigos[strtolower($idioma->nombre)] ?? strtoupper(substr($idioma->nombre, 0, 2));
                    $colorFondo = $colorFondos[$index % count($colorFondos)];
                    $colorBarra = $coloresBarra[$index % count($coloresBarra)];
                @endphp
                <div class="proyecto-card-modal">
                    <h4>
                        @if($banderaEmoji)
                            {{ $banderaEmoji }} {{ $idioma->nombre }}
                        @else
                            <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;background:{{ $colorFondo }};color:white;font-size:11px;font-weight:700;margin-right:8px;">
                                {{ $codigo }}
                            </span>
                            {{ $idioma->nombre }}
                        @endif
                    </h4>
                    <div class="proyecto-fecha" style="margin-top: 4px;">{{ $idioma->nivel_label }} — {{ $idioma->nivel_nombre }}</div>
                    <div class="idioma-barra-wrap" style="margin-top: 10px;">
                        <div style="height: 8px; background: #edf0f4; border-radius: 10px; overflow: hidden;">
                            <div style="width: {{ $idioma->porcentaje }}%; height: 100%; background: {{ $colorBarra }}; border-radius: 10px; transition: width 0.8s ease;"></div>
                        </div>
                    </div>
                    @if($idioma->certificado)
                        <a href="javascript:void(0)" 
                           onclick="event.stopPropagation(); abrirLightbox('{{ asset('storage/' . $idioma->certificado) }}')" 
                           style="margin-top: 10px; display: inline-flex; align-items: center; gap: 6px; color: #0abf9e; font-size: 12px; font-weight: 600; text-decoration: none; padding: 6px 14px; border-radius: 20px; background: #f0fdf9; border: 1px solid #d1fae5; transition: all 0.2s;"
                           onmouseover="this.style.background='#d1fae5';"
                           onmouseout="this.style.background='#f0fdf9';">
                            <i class="fas fa-certificate"></i> Ver certificado
                        </a>
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
                <button onclick="cerrarModalCompartir()" style="background:none; border:none; font-size:20px; cursor:pointer; color:#888;">✕</button>
            </div>
            
            <div style="display:flex; justify-content:space-between; margin-bottom:24px; text-align:center;">
                <a href="javascript:void(0)" style="text-decoration:none; color:#333;" onclick="shareTo('whatsapp')">
                    <div style="width:55px; height:55px; border-radius:50%; background:#25D366; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; color:white; font-size:26px;">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <span style="font-size:12px;">WhatsApp</span>
                </a>
                <a href="javascript:void(0)" style="text-decoration:none; color:#333;" onclick="shareTo('facebook')">
                    <div style="width:55px; height:55px; border-radius:50%; background:#1877F2; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; color:white; font-size:26px;">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <span style="font-size:12px;">Facebook</span>
                </a>
                <a href="javascript:void(0)" style="text-decoration:none; color:#333;" onclick="shareTo('twitter')">
                    <div style="width:55px; height:55px; border-radius:50%; background:#000000; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; color:white; font-size:26px;">
                        <i class="fab fa-x-twitter"></i>
                    </div>
                    <span style="font-size:12px;">X</span>
                </a>
                <a href="javascript:void(0)" style="text-decoration:none; color:#333;" onclick="shareTo('email')">
                    <div style="width:55px; height:55px; border-radius:50%; background:#7f8c8d; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; color:white; font-size:26px;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <span style="font-size:12px;">Correo</span>
                </a>
            </div>

            <div style="display:flex; border:1px solid #e0e0e0; border-radius:8px; padding:6px; background:#f9f9f9; align-items:center;">
                <input type="text" id="share-link-input" readonly value="{{ $user->portfolio ? url('/portafolio/' . $user->portfolio->slug) : '' }}" style="flex:1; border:none; background:transparent; padding:8px 12px; outline:none; color:#555; font-size:14px;">
                <button onclick="copiarLinkPortafolio()" id="btn-copiar-link" style="background:white; border:1px solid #e0e0e0; border-radius:20px; padding:6px 18px; cursor:pointer;">Copiar</button>
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
                    <div style="font-size:11px; font-weight:600; color:#888; text-transform:uppercase; margin-bottom:8px;">Descripción</div>
                    <div class="ql-snow" style="border:none;padding:0;margin:0;">
                        <div id="modal-descripcion" class="ql-editor" style="padding:0;"></div>
                    </div>
                </div>
                <div id="modal-tec-section" style="margin-bottom:20px;">
                    <div style="font-size:11px; font-weight:600; color:#888; text-transform:uppercase; margin-bottom:8px;">Stack Tecnológico</div>
                    <div id="modal-tecnologias" style="display:flex; flex-wrap:wrap; gap:6px;"></div>
                </div>
                <div id="modal-evidencias" style="display:none;">
                    <div style="font-size:11px; font-weight:600; color:#888; text-transform:uppercase; margin-bottom:12px; padding-top:16px; border-top:1px solid #f0f0f0;">
                        Evidencias <span id="modal-ev-count"></span>
                    </div>
                    <div id="modal-evidencias-lista"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTONES -->
    <div class="buttons-container">
        <form action="{{ route('perfil.publicar') }}" method="POST" style="margin: 0;" id="formPublicar">
            @csrf
            <button type="submit" class="btn btn-publicar">
                <i class="fas fa-globe"></i> Publicar perfil
            </button>
        </form>
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

function abrirLightbox(imagenSrc) {
    let lightbox = document.getElementById('lightbox-modal');
    if (!lightbox) {
        lightbox = document.createElement('div');
        lightbox.id = 'lightbox-modal';
        lightbox.style.cssText = 'display:none; position:fixed; inset:0; background:rgba(0,0,0,0.9); z-index:20000; align-items:center; justify-content:center; cursor:pointer;';
        lightbox.innerHTML = `
            <div style="position:relative; max-width:90vw; max-height:90vh;">
                <img id="lightbox-img" style="max-width:100%; max-height:90vh; object-fit:contain; border-radius:8px;">
                <button id="lightbox-close" style="position:absolute; top:-40px; right:0; background:none; border:none; color:white; font-size:28px; cursor:pointer; width:36px; height:36px; display:flex; align-items:center; justify-content:center; border-radius:50%; background:rgba(0,0,0,0.5);">✕</button>
            </div>
        `;
        document.body.appendChild(lightbox);
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox || e.target.id === 'lightbox-close') {
                lightbox.style.display = 'none';
            }
        });
    }
    const img = document.getElementById('lightbox-img');
    if (img) {
        img.src = imagenSrc;
        lightbox.style.display = 'flex';
    }
}

document.getElementById('formPublicar')?.addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
        title: '¿Publicar portafolio?',
        text: 'Tu perfil será visible para todos los usuarios.',
        showCancelButton: true,
        confirmButtonColor: '#0abf9e',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Publicar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Publicando...', showConfirmButton: false, allowOutsideClick: false });
            document.getElementById('formPublicar').submit();
        }
    });
});

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
        data.tecnologias.forEach(t => { tecDiv.innerHTML += `<span class="tec-badge">${previewEscapeHtml(t)}</span>`; });
    } else { tecSection.style.display = 'none'; }
    
    let evDiv = document.getElementById('modal-evidencias-lista');
    let evSec = document.getElementById('modal-evidencias');
    evDiv.innerHTML = '';
    if (data.evidencias && data.evidencias.length) {
        evSec.style.display = 'block';
        document.getElementById('modal-ev-count').textContent = '(' + data.evidencias.length + ')';
        data.evidencias.forEach(ev => {
            let item = document.createElement('div');
            
            if (ev.tipo === 'imagen' && ev.imagen) {
                let nombreImagen = ev.titulo || 'Imagen del proyecto';
                if (nombreImagen.match(/\.(jpg|jpeg|png|gif|webp)$/i) || nombreImagen.length > 30) {
                    nombreImagen = 'Imagen del proyecto';
                }
                item.style.cssText = 'margin-bottom:12px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);';
                item.innerHTML = `
                    <div style="display:flex;align-items:center;gap:12px; padding: 14px 18px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <div style="width: 36px; height: 36px; background: #0abf9e15; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image" style="color: #0abf9e; font-size: 18px;"></i>
                        </div>
                        <div>
                            <strong style="font-size: 14px; color: #0f172a;">${previewEscapeHtml(nombreImagen)}</strong>
                        </div>
                    </div>
                    <div style="padding: 16px; background: white; position: relative;">
                        <div style="position: relative; display: inline-block; width: 100%; border-radius: 16px; overflow: hidden;">
                            <img src="${ev.imagen}" style="width:100%; max-height:280px; object-fit:cover; border-radius: 16px; cursor: pointer; transition: transform 0.2s;" 
                                 onclick="abrirLightbox('${ev.imagen}')"
                                 onmouseover="this.style.transform='scale(1.01)'"
                                 onmouseout="this.style.transform='scale(1)'"
                                 onerror="this.style.display='none'; this.parentElement.innerHTML+='<p style=\'color:#ef4444;font-size:12px;padding:16px;text-align:center;\'>❌ No se pudo cargar la imagen</p>'">
                            <div onclick="abrirLightbox('${ev.imagen}')" style="position: absolute; bottom: 16px; right: 16px; background: rgba(0,0,0,0.65); backdrop-filter: blur(8px); border-radius: 40px; padding: 8px 16px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <span style="color: white; font-size: 12px; font-weight: 500;">Ver imagen</span>
                            </div>
                        </div>
                    </div>
                `;
            }
            else if (ev.tipo === 'enlace' && ev.url) {
                item.style.cssText = 'margin-bottom:12px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);';
                item.innerHTML = `
                    <div style="display:flex;align-items:center;gap:12px; padding: 14px 18px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <div style="width: 36px; height: 36px; background: #0abf9e15; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-link" style="color: #0abf9e; font-size: 18px;"></i>
                        </div>
                        <div>
                            <strong style="font-size: 14px; color: #0f172a;">${previewEscapeHtml(ev.titulo || 'Enlace')}</strong>
                            ${ev.descripcion ? `<p style="font-size: 11px; color: #64748b; margin-top: 2px;">${previewEscapeHtml(ev.descripcion)}</p>` : ''}
                        </div>
                    </div>
                    <div style="padding: 16px; background: white;">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #64748b; word-break: break-all; max-width: 65%; background: #f8fafc; padding: 8px 12px; border-radius: 12px;">
                                <i class="fas fa-globe" style="font-size: 12px; color: #0abf9e;"></i>
                                <a href="${ev.url}" target="_blank" style="color: #0abf9e; text-decoration: none;">${previewEscapeHtml(ev.url)}</a>
                            </div>
                            <a href="${ev.url}" target="_blank" style="background: #0abf9e; color: white; padding: 8px 18px; border-radius: 40px; font-size: 12px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;">
                                Abrir enlace <i class="fas fa-external-link-alt" style="font-size: 10px;"></i>
                            </a>
                        </div>
                    </div>
                `;
            }
            else if (ev.tipo === 'repositorio' && ev.url) {
                let icon = 'fa-github';
                if (ev.plataforma === 'GitLab') icon = 'fa-gitlab';
                else if (ev.plataforma === 'Bitbucket') icon = 'fa-bitbucket';
                item.style.cssText = 'margin-bottom:12px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);';
                item.innerHTML = `
                    <div style="display:flex;align-items:center;gap:12px; padding: 14px 18px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <div style="width: 36px; height: 36px; background: #0abf9e15; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fab ${icon}" style="color: #0abf9e; font-size: 18px;"></i>
                        </div>
                        <div>
                            <strong style="font-size: 14px; color: #0f172a;">${previewEscapeHtml(ev.titulo || 'Repositorio')}</strong>
                            ${ev.descripcion ? `<p style="font-size: 11px; color: #64748b; margin-top: 2px;">${previewEscapeHtml(ev.descripcion)}</p>` : ''}
                        </div>
                    </div>
                    <div style="padding: 16px; background: white;">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #64748b; word-break: break-all; max-width: 65%; background: #f8fafc; padding: 8px 12px; border-radius: 12px;">
                                <i class="fab ${icon}" style="font-size: 12px;"></i>
                                <a href="${ev.url}" target="_blank" style="color: #0abf9e; text-decoration: none;">${previewEscapeHtml(ev.url)}</a>
                            </div>
                            <a href="${ev.url}" target="_blank" style="background: #0abf9e; color: white; padding: 8px 18px; border-radius: 40px; font-size: 12px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;">
                                Ver repositorio <i class="fas fa-external-link-alt" style="font-size: 10px;"></i>
                            </a>
                        </div>
                    </div>
                `;
            }
            evDiv.appendChild(item);
        });
    } else { evSec.style.display = 'none'; }
    
    document.getElementById('modal-proyecto').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarModal() {
    document.getElementById('modal-proyecto').style.display = 'none';
    document.body.style.overflow = '';
}

function abrirModalTodosProyectos() {
    document.getElementById('modal-todos-proyectos').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarModalTodosProyectos() {
    document.getElementById('modal-todos-proyectos').style.display = 'none';
    document.body.style.overflow = '';
}

function abrirModalExperiencias() {
    document.getElementById('modal-todos-experiencias').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarModalExperiencias() {
    document.getElementById('modal-todos-experiencias').style.display = 'none';
    document.body.style.overflow = '';
}

function abrirModalAcademicas() {
    document.getElementById('modal-todos-academicas').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarModalAcademicas() {
    document.getElementById('modal-todos-academicas').style.display = 'none';
    document.body.style.overflow = '';
}

function abrirModalTecnicas() {
    document.getElementById('modal-todos-tecnicas').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarModalTecnicas() {
    document.getElementById('modal-todos-tecnicas').style.display = 'none';
    document.body.style.overflow = '';
}

function abrirModalBlandas() {
    document.getElementById('modal-todos-blandas').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarModalBlandas() {
    document.getElementById('modal-todos-blandas').style.display = 'none';
    document.body.style.overflow = '';
}

function abrirModalIdiomas() {
    document.getElementById('modal-todos-idiomas').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarModalIdiomas() {
    document.getElementById('modal-todos-idiomas').style.display = 'none';
    document.body.style.overflow = '';
}

function abrirModalCompartir() {
    document.getElementById('modal-compartir').style.display = 'flex';
}
function cerrarModalCompartir() {
    document.getElementById('modal-compartir').style.display = 'none';
}
function copiarLinkPortafolio() {
    const input = document.getElementById('share-link-input');
    input.select();
    navigator.clipboard.writeText(input.value).then(() => {
        const btn = document.getElementById('btn-copiar-link');
        btn.textContent = '¡Copiado!';
        setTimeout(() => { btn.textContent = 'Copiar'; }, 2000);
    });
}
function shareTo(platform) {
    const link = encodeURIComponent(document.getElementById('share-link-input').value);
    const text = encodeURIComponent('¡Mira mi portafolio profesional!');
    let url = '';
    switch(platform) {
        case 'whatsapp': url = `https://api.whatsapp.com/send?text=${text} ${link}`; break;
        case 'facebook': url = `https://www.facebook.com/sharer/sharer.php?u=${link}`; break;
        case 'twitter': url = `https://twitter.com/intent/tweet?text=${text}&url=${link}`; break;
        case 'email': url = `mailto:?subject=${text}&body=${link}`; break;
    }
    if(url) window.open(url, '_blank');
}

document.getElementById('modal-proyecto').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
document.getElementById('modal-compartir').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalCompartir();
});
document.getElementById('modal-todos-proyectos')?.addEventListener('click', function(e) {
    if (e.target === this) cerrarModalTodosProyectos();
});
document.getElementById('modal-todos-experiencias')?.addEventListener('click', function(e) {
    if (e.target === this) cerrarModalExperiencias();
});
document.getElementById('modal-todos-academicas')?.addEventListener('click', function(e) {
    if (e.target === this) cerrarModalAcademicas();
});
document.getElementById('modal-todos-tecnicas')?.addEventListener('click', function(e) {
    if (e.target === this) cerrarModalTecnicas();
});
document.getElementById('modal-todos-blandas')?.addEventListener('click', function(e) {
    if (e.target === this) cerrarModalBlandas();
});
document.getElementById('modal-todos-idiomas')?.addEventListener('click', function(e) {
    if (e.target === this) cerrarModalIdiomas();
});
</script>

@endsection