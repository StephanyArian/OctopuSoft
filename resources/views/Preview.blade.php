@extends('layouts.app-classic')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/preview.css') }}">

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
                    @if($redes['whatsapp'])
                        @php $wpNum = preg_replace('/[^0-9]/', '', $redes['whatsapp']); @endphp
                        <a href="https://wa.me/{{ $wpNum }}" target="_blank" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
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
                <div class="description">{{ $exp->descripcion }}</div>
            </div>
            @empty
                <div class="empty-message">No hay experiencias laborales registradas</div>
            @endforelse
        </div>

        <!-- INFORMACIÓN ACADÉMICA -->
        <div class="section">
            <h2><i class="fas fa-graduation-cap"></i> Información académica</h2>
            @forelse($academicas as $aca)
                <div class="card">
                    <h3>{{ $aca->institucion }}</h3>
                    <div class="subtitle">{{ $aca->titulo }}</div>
                    <div class="date">
                        {{ \Carbon\Carbon::parse($aca->fecha_inicio)->format('F Y') }}
                        @if($aca->fecha_fin)
                            — {{ \Carbon\Carbon::parse($aca->fecha_fin)->format('F Y') }}
                        @elseif($aca->estudio_actual)
                            — Actualidad
                        @endif
                    </div>
                    <div class="description">{{ $aca->descripcion }}</div>
                </div>
            @empty
                <div class="empty-message">No hay información académica registrada</div>
            @endforelse
        </div>

        <!-- HABILIDADES TÉCNICAS CON BARRAS CORTAS AMARILLAS -->
        <div class="section">
            <h2><i class="fas fa-code"></i> Habilidades técnicas</h2>
            <div class="tech-skills-grid">
                @forelse($habilidadesTecnicas as $skill)
                    @php
                        $nivel = $skill->nivel ?? 'Intermedio';
                        if ($nivel == 'Avanzado') {
                            $claseNivel = 'advanced';
                        } elseif ($nivel == 'Intermedio') {
                            $claseNivel = 'intermediate';
                        } else {
                            $claseNivel = 'basic';
                        }
                    @endphp
                    <div class="tech-skill-item">
                        <div class="tech-skill-header">
                            <span class="tech-skill-name">{{ $skill->nombre }}</span>
                            <span class="tech-skill-level">{{ $nivel }}</span>
                        </div>
                        <div class="tech-skill-bar-bg">
                            <div class="tech-skill-bar-fill {{ $claseNivel }}"></div>
                        </div>
                    </div>
                @empty
                    <div class="empty-message">No hay habilidades técnicas registradas</div>
                @endforelse
            </div>
        </div>

        <!-- HABILIDADES BLANDAS -->
        <div class="section">
            <h2><i class="fas fa-heart"></i> Habilidades blandas</h2>
            <div class="skills-container">
                @forelse($habilidadesBlandas as $skill)
                    <span class="soft-skill-tag">⭐{{ $skill->nombre }}</span>
                @empty
                    <div class="empty-message">No hay habilidades blandas registradas</div>
                @endforelse
            </div>
        </div>

        <!-- PROYECTOS -->
        <div class="section">
            <h2><i class="fas fa-project-diagram"></i> Proyectos</h2>
            @forelse($proyectos as $proyecto)
                <div class="card">
                    <h3 style="cursor:pointer; color:#1abc9c;" onclick="abrirModal({{ json_encode([
                        'nombre'      => $proyecto->nombre,
                        'descripcion' => $proyecto->descripcion,
                        'fecha_inicio'=> optional($proyecto->fecha_inicio)->format('d/m/Y'),
                        'fecha_fin'   => optional($proyecto->fecha_fin)->format('d/m/Y'),
                        'estado'      => $proyecto->estado,
                        'rol'         => $proyecto->rol,
                        'cliente'     => $proyecto->cliente,
                        'tecnologias' => $proyecto->tecnologias,
                        'evidencias'  => $proyecto->evidencias,
                    ]) }})">{{ $proyecto->nombre }}</h3>
                    <div class="description">{{ $proyecto->descripcion }}</div>
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
            @empty
                <div class="empty-message">No hay proyectos registrados</div>
            @endforelse
        </div>

        <!-- REDES Y CONTACTO -->
        <div class="section">
            <h2><i class="fas fa-share-alt"></i> Redes y contacto</h2>
            <div class="contact-grid">
                @if($redes['linkedin'])
                    <div class="contact-item">
                        <i class="fab fa-linkedin"></i>
                        <a href="{{ $redes['linkedin'] }}" target="_blank" rel="noopener noreferrer">
                            LinkedIn
                        </a>
                    </div>
                @endif
                
                @if($redes['github'])
                    <div class="contact-item">
                        <i class="fab fa-github"></i>
                        <a href="{{ $redes['github'] }}" target="_blank" rel="noopener noreferrer">
                            GitHub
                        </a>
                    </div>
                @endif
                
                @if($redes['whatsapp'])
                    <div class="contact-item">
                        <i class="fab fa-whatsapp"></i>
                        @php
                            $whatsappNumber = preg_replace('/[^0-9]/', '', $redes['whatsapp']);
                            $whatsappUrl = 'https://wa.me/' . $whatsappNumber;
                        @endphp
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">
                            WhatsApp: {{ $redes['whatsapp'] }}
                        </a>
                    </div>
                @endif
                
                @if($redes['correo'])
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:{{ $redes['correo'] }}">
                            {{ $redes['correo'] }}
                        </a>
                    </div>
                @endif
                
                @if($redes['otros'])
                    <div class="contact-item">
                        <i class="fas fa-link"></i>
                        <a href="{{ $redes['otros'] }}" target="_blank" rel="noopener noreferrer">
                            Otra red profesional
                        </a>
                    </div>
                @endif

                @if($redes['ubicacion'])
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($redes['ubicacion']) }}" 
                        target="_blank" 
                        rel="noopener noreferrer">
                             {{ $redes['ubicacion'] }}
                        </a>
                    </div>
                @endif
                
                @if(empty(array_filter($redes)))
                    <div class="empty-message">No hay redes de contacto registradas</div>
                @endif
            </div>
        </div>

        <!-- MODAL PROYECTO -->
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
                        <p id="modal-descripcion" style="color:#444; font-size:14px; line-height:1.6; margin:0;"></p>
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

        <!-- BOTONES -->
        <div class="buttons-container">
            <a href="javascript:history.back()" class="btn btn-editar">
                <i class="fas fa-edit"></i> Continuar editando
            </a>
            <form action="{{ route('perfil.publicar') }}" method="POST" style="margin: 0;" id="formPublicar">
                @csrf
                <button type="submit" class="btn btn-publicar">
                    <i class="fas fa-globe"></i> Publicar perfil
                </button>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('formPublicar')?.addEventListener('submit', function(e) {
        if(!confirm('¿Estás segura de que quieres publicar tu perfil? Una vez publicado, será visible para todos.')) {
            e.preventDefault();
        }
    });

    function abrirModal(data) {
        document.getElementById('modal-nombre').textContent = data.nombre;

        let badgesDiv = document.getElementById('modal-badges');
        badgesDiv.innerHTML = '';
        const badge = (texto, bg, color) => `<span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:${bg};color:${color};">${texto}</span>`;

        if (data.estado) {
            let bg = data.estado === 'Completado' ? '#d1fae5' : data.estado === 'En curso' ? '#fef3c7' : '#f1f5f9';
            let color = data.estado === 'Completado' ? '#065f46' : data.estado === 'En curso' ? '#92400e' : '#64748b';
            badgesDiv.innerHTML += badge(data.estado, bg, color);
        }
        if (data.rol) badgesDiv.innerHTML += badge('👤 ' + data.rol, '#ede9fe', '#5b21b6');
        if (data.cliente) badgesDiv.innerHTML += badge('🏢 ' + data.cliente, '#f1f5f9', '#475569');

        let fechasDiv = document.getElementById('modal-fechas');
        fechasDiv.innerHTML = '';
        if (data.fecha_inicio) fechasDiv.innerHTML += `<span>📅 Inicio: <strong>${data.fecha_inicio}</strong></span>`;
        if (data.fecha_fin) fechasDiv.innerHTML += `<span>📅 Fin: <strong>${data.fecha_fin}</strong></span>`;

        document.getElementById('modal-descripcion').textContent = data.descripcion ?? '';

        let tecDiv = document.getElementById('modal-tecnologias');
        let tecSection = document.getElementById('modal-tec-section');
        tecDiv.innerHTML = '';
        if (data.tecnologias && data.tecnologias.length) {
            tecSection.style.display = 'block';
            data.tecnologias.forEach(t => {
                tecDiv.innerHTML += `<span class="tec-badge">${t}</span>`;
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
    }

    function cerrarModal() {
        document.getElementById('modal-proyecto').style.display = 'none';
    }

    document.getElementById('modal-proyecto').addEventListener('click', function(e) {
        if (e.target === this) cerrarModal();
    });
    </script>
@endsection