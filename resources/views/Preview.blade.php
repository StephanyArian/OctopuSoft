<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista Previa - Mi Portafolio</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Estilos propios -->
    <link rel="stylesheet" href="{{ asset('css/preview.css') }}">
</head>
<body>
    <div class="preview-container">
        
        <!-- CABECERA CON DATOS PERSONALES -->
        <div class="profile-header">
            <div class="profile-avatar">
                @if($user->photo_base64)
                    <img src="{{ $user->photo_base64 }}" alt="Foto perfil" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                @else
                    <i class="fas fa-user-circle"></i>
                @endif
            </div>
            <h1>{{ $user->first_name ?? 'Usuario' }} {{ $user->last_name ?? '' }}</h1>
            <div class="title">{{ $user->profession->name ?? 'Profesional' }}</div>
            <div class="location">
                <i class="fas fa-map-marker-alt"></i> 
                {{ $user->city ?? '' }}{{ $user->country ? ', ' . $user->country : '' }}
            </div>
            <div class="bio">{{ $user->biography ?? 'Sin biografía' }}</div>
        </div>

        <!-- EXPERIENCIA LABORAL -->
        <div class="section">
            <h2><i class="fas fa-briefcase"></i> Experiencia laboral</h2>
            @forelse($experiencias as $exp)
                <div class="card">
                    <h3>{{ $exp->empresa }}</h3>
                    <div class="subtitle">{{ $exp->cargo }} {{ $exp->ubicacion ? '| ' . $exp->ubicacion : '' }}</div>
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

        <!-- HABILIDADES TÉCNICAS -->
        <div class="section">
            <h2><i class="fas fa-code"></i> Habilidades técnicas</h2>
            <div class="skills-container">
                @forelse($habilidadesTecnicas as $skill)
                    <span class="skill-tag 
                        @if($skill->nivel == 'Avanzado') advanced
                        @elseif($skill->nivel == 'Intermedio') intermediate
                        @else basic
                        @endif">
                        {{ $skill->nombre }} - {{ $skill->nivel }}
                    </span>
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
                    <span class="soft-skill-tag">{{ $skill->nombre }}</span>
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
                    <h3>{{ $proyecto->nombre }}</h3>
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
                    <div class="contact-item"><i class="fab fa-linkedin"></i> <span>{{ $redes['linkedin'] }}</span></div>
                @endif
                @if($redes['github'])
                    <div class="contact-item"><i class="fab fa-github"></i> <span>{{ $redes['github'] }}</span></div>
                @endif
                @if($redes['whatsapp'])
                    <div class="contact-item"><i class="fab fa-whatsapp"></i> <span>{{ $redes['whatsapp'] }}</span></div>
                @endif
                @if($redes['correo'])
                    <div class="contact-item"><i class="fas fa-envelope"></i> <span>{{ $redes['correo'] }}</span></div>
                @endif
                @if($redes['otros'])
                    <div class="contact-item"><i class="fas fa-link"></i> <span>{{ $redes['otros'] }}</span></div>
                @endif
                @if(empty(array_filter($redes)))
                    <div class="empty-message">No hay redes de contacto registradas</div>
                @endif
            </div>
        </div>

        <!-- BOTONES -->
        <div class="buttons-container">
            <a href="{{ url('/dashboard') }}" class="btn btn-editar">
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
    </script>
</body>
</html>