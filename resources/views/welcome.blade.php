<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Generador de Portafolios Digitales | Crea tu portafolio profesional</title>

    {{-- Fuentes --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    {{-- Estilos de la landing --}}
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
<div class="logo">
    <h2 style="color:#0abf9e; font-weight:800; font-size:1.5rem; font-family:'Figtree',sans-serif; margin:0;">DevFolio</h2>
</div>

            <div class="nav-links-center" id="navLinks">
<a href="#inicio" class="nav-link" style="display:flex;flex-direction:column;align-items:center;gap:4px;text-decoration:none;">
    <div style="width:44px;height:44px;background:linear-gradient(135deg,#0abf9e,#1de8c0,#00ff88);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 0 14px rgba(10,191,158,0.7),0 0 28px rgba(0,255,136,0.3);">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 16 16">
            <path fill="#2d0a1e" d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
            <path fill="#2d0a1e" d="M13 7.293l-5-5-5 5V14a1 1 0 0 0 1 1h3v-3h2v3h3a1 1 0 0 0 1-1z"/>
        </svg>
    </div>
    <span style="font-size:0.58rem;font-weight:800;background:linear-gradient(135deg,#0abf9e,#00ff88);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:0.5px;">Inicio</span>
</a>


<a href="#que-es" class="nav-link" style="display:flex;flex-direction:column;align-items:center;gap:4px;text-decoration:none;">
    <div style="width:44px;height:44px;background:linear-gradient(135deg,#0abf9e,#1de8c0,#00ff88);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 0 14px rgba(10,191,158,0.7),0 0 30px rgba(0,255,136,0.3);">
        <i class="bi bi-grid-fill" style="font-size:20px;color:#2d0a1e;"></i>
    </div>
    <span style="font-size:0.58rem;font-weight:800;background:linear-gradient(135deg,#0abf9e,#00ff88);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:0.5px;">Nuestro sistema</span>
</a>

<a href="#beneficios" class="nav-link" style="display:flex;flex-direction:column;align-items:center;gap:4px;text-decoration:none;">
    <div style="width:44px;height:44px;background:linear-gradient(135deg,#0abf9e,#1de8c0,#00ff88);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 0 14px rgba(10,191,158,0.7),0 0 30px rgba(0,255,136,0.3);">
        <i class="bi bi-star-fill" style="font-size:20px;color:#2d0a1e;"></i>
    </div>
    <span style="font-size:0.58rem;font-weight:800;background:linear-gradient(135deg,#0abf9e,#00ff88);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:0.5px;">Beneficios</span>
</a>

<a href="#como-funciona" class="nav-link" style="display:flex;flex-direction:column;align-items:center;gap:4px;text-decoration:none;">
    <div style="width:44px;height:44px;background:linear-gradient(135deg,#0abf9e,#1de8c0,#00ff88);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 0 14px rgba(10,191,158,0.7),0 0 30px rgba(0,255,136,0.3);">
        <i class="bi bi-lightning-fill" style="font-size:20px;color:#2d0a1e;"></i>
    </div>
    <span style="font-size:0.58rem;font-weight:800;background:linear-gradient(135deg,#0abf9e,#00ff88);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:0.5px;">Cómo funciona</span>
</a>
            </div>

            <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
                <i class="bi bi-list"></i>
            </button>

            <div class="nav-links-right">
                @if (Route::has('login'))
                    @auth
                        <div class="nav-user-dropdown" id="userDropdown">
                            <button class="nav-user-trigger" onclick="toggleUserMenu()" style="background:transparent;border:none;padding:0;cursor:pointer;display:flex;align-items:center;gap:8px;">
    <div style="position:relative;">
        @if(Auth::user()->photo_base64)
            <img src="{{ Auth::user()->photo_base64 }}" 
                 style="width:46px;height:46px;border-radius:50%;object-fit:cover;border:2.5px solid #0abf9e;box-shadow:0 0 12px rgba(10,191,158,0.6),0 0 24px rgba(0,255,136,0.25);">
        @else
            <div style="width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,#0abf9e,#1de8c0,#00ff88);display:flex;align-items:center;justify-content:center;border:2.5px solid #0abf9e;box-shadow:0 0 12px rgba(10,191,158,0.6),0 0 24px rgba(0,255,136,0.25);font-weight:800;font-size:1.1rem;color:#2d0a1e;">
                {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->name, 0, 1)) }}
            </div>
        @endif
        <div style="position:absolute;bottom:1px;right:1px;width:12px;height:12px;background:#00ff88;border-radius:50%;border:2px solid #2d0a1e;"></div>
    </div>
    <i class="bi bi-chevron-down" id="dropdownChevron" style="color:#0abf9e;font-size:12px;"></i>
</button>
                            <div class="nav-user-menu" id="userMenu" style="background:white; border-radius:16px; border:2px solid #a855f7; box-shadow:0 0 20px rgba(168,85,247,0.25); min-width:200px; padding:8px; display:flex; flex-direction:column; gap:6px;">

    <a href="{{ url('/') }}"
    style="display:flex;align-items:center;gap:12px;padding:10px 12px;text-decoration:none;color:#2d0a1e;font-size:14px;font-weight:500;border-radius:10px;border:1.5px solid transparent;background:linear-gradient(white,white) padding-box, linear-gradient(135deg,#a855f7,#7c3aed) border-box;box-shadow:0 2px 8px rgba(168,85,247,0.15);"
    onmouseover="this.style.boxShadow='0 4px 16px rgba(168,85,247,0.35)'"
    onmouseout="this.style.boxShadow='0 2px 8px rgba(168,85,247,0.15)'">
        <div style="width:30px;height:30px;border-radius:7px;background:linear-gradient(135deg,#0abf9e,#1de8c0);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-house-fill" style="color:#2d0a1e;"></i>
        </div>
        Inicio
    </a>

    <a href="{{ route('dashboard') }}"
    style="display:flex;align-items:center;gap:12px;padding:10px 12px;text-decoration:none;color:#2d0a1e;font-size:14px;font-weight:500;border-radius:10px;border:1.5px solid transparent;background:linear-gradient(white,white) padding-box, linear-gradient(135deg,#a855f7,#7c3aed) border-box;box-shadow:0 2px 8px rgba(168,85,247,0.15);"
    onmouseover="this.style.boxShadow='0 4px 16px rgba(168,85,247,0.35)'"
    onmouseout="this.style.boxShadow='0 2px 8px rgba(168,85,247,0.15)'">
        <div style="width:30px;height:30px;border-radius:7px;background:linear-gradient(135deg,#0abf9e,#1de8c0);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-display-fill" style="color:#2d0a1e;"></i>
        </div>
        Mi espacio
    </a>

    <a href="{{ route('informacion.academica') }}"
    style="display:flex;align-items:center;gap:12px;padding:10px 12px;text-decoration:none;color:#2d0a1e;font-size:14px;font-weight:500;border-radius:10px;border:1.5px solid transparent;background:linear-gradient(white,white) padding-box, linear-gradient(135deg,#a855f7,#7c3aed) border-box;box-shadow:0 2px 8px rgba(168,85,247,0.15);"
    onmouseover="this.style.boxShadow='0 4px 16px rgba(168,85,247,0.35)'"
    onmouseout="this.style.boxShadow='0 2px 8px rgba(168,85,247,0.15)'">
        <div style="width:30px;height:30px;border-radius:7px;background:linear-gradient(135deg,#0abf9e,#1de8c0);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-pencil-fill" style="color:#2d0a1e;"></i>
        </div>
        Completar
    </a>

    <a href="{{ route('preview') }}"
    style="display:flex;align-items:center;gap:12px;padding:10px 12px;text-decoration:none;color:#2d0a1e;font-size:14px;font-weight:500;border-radius:10px;border:1.5px solid transparent;background:linear-gradient(white,white) padding-box, linear-gradient(135deg,#a855f7,#7c3aed) border-box;box-shadow:0 2px 8px rgba(168,85,247,0.15);"
    onmouseover="this.style.boxShadow='0 4px 16px rgba(168,85,247,0.35)'"
    onmouseout="this.style.boxShadow='0 2px 8px rgba(168,85,247,0.15)'">
        <div style="width:30px;height:30px;border-radius:7px;background:linear-gradient(135deg,#0abf9e,#1de8c0);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-eye-fill" style="color:#2d0a1e;"></i>
        </div>
        Ver perfil
    </a>

    <a href="{{ route('profile.edit') }}"
    style="display:flex;align-items:center;gap:12px;padding:10px 12px;text-decoration:none;color:#2d0a1e;font-size:14px;font-weight:500;border-radius:10px;border:1.5px solid transparent;background:linear-gradient(white,white) padding-box, linear-gradient(135deg,#a855f7,#7c3aed) border-box;box-shadow:0 2px 8px rgba(168,85,247,0.15);"
    onmouseover="this.style.boxShadow='0 4px 16px rgba(168,85,247,0.35)'"
    onmouseout="this.style.boxShadow='0 2px 8px rgba(168,85,247,0.15)'">
        <div style="width:30px;height:30px;border-radius:7px;background:linear-gradient(135deg,#0abf9e,#1de8c0);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-gear-fill" style="color:#2d0a1e;"></i>
        </div>
        Configuración
    </a>

    <a href="{{ route('cerrar.sesion') }}"
    style="display:flex;align-items:center;gap:12px;padding:10px 12px;text-decoration:none;color:#e53e3e;font-size:14px;font-weight:500;border-radius:10px;border:1.5px solid transparent;background:linear-gradient(white,white) padding-box, linear-gradient(135deg,#f87171,#dc2626) border-box;box-shadow:0 2px 8px rgba(229,62,62,0.15);"
    onmouseover="this.style.boxShadow='0 4px 16px rgba(229,62,62,0.3)'"
    onmouseout="this.style.boxShadow='0 2px 8px rgba(229,62,62,0.15)'">
        <div style="width:30px;height:30px;border-radius:7px;background:linear-gradient(135deg,#f87171,#dc2626);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-box-arrow-right" style="color:white;"></i>
        </div>
        Cerrar sesión
    </a>

</div>

                        </div>
        @else
            <a href="{{ route('login') }}" class="nav-link" style="display:flex;flex-direction:column;align-items:center;gap:4px;text-decoration:none;">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#0abf9e,#1de8c0,#00ff88);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 0 14px rgba(10,191,158,0.7),0 0 30px rgba(0,255,136,0.3);">
                    <i class="bi bi-box-arrow-in-right" style="font-size:20px;color:#2d0a1e;"></i>
                </div>
                <span style="font-size:0.58rem;font-weight:800;background:linear-gradient(135deg,#0abf9e,#00ff88);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:0.5px;">Iniciar sesión</span>
            </a>

            <a href="{{ route('register') }}" class="nav-link" style="display:flex;flex-direction:column;align-items:center;gap:4px;text-decoration:none;">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#0abf9e,#1de8c0,#00ff88);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 0 14px rgba(10,191,158,0.7),0 0 30px rgba(0,255,136,0.3);">
                    <i class="bi bi-person-plus-fill" style="font-size:20px;color:#2d0a1e;"></i>
                </div>
                <span style="font-size:0.58rem;font-weight:800;background:linear-gradient(135deg,#0abf9e,#00ff88);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:0.5px;">Registrarse</span>
            </a>
        @endauth
                @endif
            </div>
        </div>
    </nav>
    {{-- HERO --}}
    <section id="inicio" class="hero">
        <div class="container hero-content">
            <h1>Crea tu Portafolio Digital y<br>Destaca tu Talento</h1>
            <p>Nuestra plataforma te permite construir un portafolio profesional de
               forma fácil, rápida y moderna para mostrar tus proyectos,
               habilidades y experiencia al mundo.</p>

        </div>
    </section>

    {{-- ¿QUÉ ES EL SISTEMA? --}}
    <section id="que-es" class="section section-white">
        <div class="container">
            <h2 class="section-title">Tu trabajo merece ser visto</h2>
            <div style="max-width: 760px; margin: 0 auto; text-align: center;">
                <p style="font-size: 1.1rem; color: var(--gray-700); margin-bottom: 20px; line-height: 1.7;">
                    En un mundo donde el primer contacto es digital, tu portafolio 
                    es tu mejor carta de presentación. Muestra a reclutadores, 
                    clientes y colaboradores lo que eres capaz de crear.
                </p>
                <p style="color: var(--gray-500); font-size: 0.95rem; line-height: 1.6;">
                    Ideal para desarrolladores, diseñadores, estudiantes y profesionales 
                    que desean destacar sus proyectos y habilidades de manera profesional
                </p>
            </div>
        </div>
    </section>

        {{-- PORTAFOLIOS DESTACADOS (CARRUSEL DINÁMICO) --}}
    <section class="carousel-section">
        <div class="container">

            <div class="section-header">
                <div class="header-left">
                    <div class="header-text">
                        <h2 class="section-title">Portafolios destacados</h2>
                        <p class="section-subtitle" style="margin-bottom: 0">
                            Explora el talento de nuestra comunidad
                        </p>
                    </div>
                </div>
                <div class="header-action">
                    <a href="{{ route('portafolio.explore') }}" class="btn-outline">
                        Ver todos los portafolios &nbsp;<i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            

            <div class="carousel-wrapper">
                <div class="carousel-track" id="carouselTrack">
                    @forelse($portfolios as $portfolio)
                    <div class="portfolio-card">
                        @php
                            $colors = ['color-1', 'color-2', 'color-3', 'color-4', 'color-5'];
                            $colorIndex = $loop->index % 5;
                        @endphp
                        @if($portfolio->user->photo_base64)
                            <div class="card-photo">
                                <img src="{{ $portfolio->user->photo_base64 }}" alt="{{ $portfolio->user->first_name }}">
                            </div>
                        @else
                            <div class="card-photo no-photo {{ $colors[$colorIndex] }}">
                                <div class="initials-circle">
                                    {{ strtoupper(substr($portfolio->user->first_name, 0, 1) . substr($portfolio->user->last_name, 0, 1)) }}
                                </div>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="card-name">{{ $portfolio->user->first_name }} {{ $portfolio->user->last_name }}</div>
                            <div class="card-professions">
                                <span class="card-profession-main">{{ $portfolio->user->profession->name ?? 'Profesional' }}</span>
                                @php
                                    $extraSkills = $portfolio->user->skills->where('type', 'technical')->count() - 1;
                                @endphp
                                @if($extraSkills > 0)
                                    <span class="card-profession-extra">+{{ $extraSkills }} más</span>
                                @endif
                            </div>
                            <p class="card-bio">{{ Str::limit(strip_tags($portfolio->user->biography ?? 'Sin biografía'), 80) }}</p>
                            <div class="card-tags">
                                @foreach($portfolio->user->skills->where('type', 'technical')->take(3) as $skill)
                                    <span class="card-tag">{{ $skill->name }}</span>
                                @endforeach
                            </div>
                            <div class="card-footer-row">
                                <a href="{{ route('portafolio.public', $portfolio->slug) }}" class="card-link">
                                    Ver portafolio <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                        <div style="text-align: center; padding: 40px;">No hay portafolios públicos disponibles</div>
                    @endforelse
                </div>
            </div>

            <div class="carousel-controls">
                <button class="carousel-btn" id="carouselPrev" aria-label="Anterior">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="carousel-dots" id="carouselDots"></div>
                <button class="carousel-btn" id="carouselNext" aria-label="Siguiente">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>

    {{-- BENEFICIOS --}}
    <section id="beneficios" class="section section-white">
        <div class="container">
            <h2 class="section-title">Beneficios</h2>
            <p class="section-subtitle">Todo lo que necesitas para destacar en el mundo digital</p>

            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                       <i class="fas fa-layer-group"></i>
                    </div>
                    <h3>Diseños Profesionales</h3>
                    <p>Plantillas modernas y personalizables para todos los estilos.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Fácil de Usar</h3>
                    <p>Crea tu portafolio en minutos, sin complicaciones técnicas.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-mobile-screen-button"></i>
                    </div>
                    <h3>Acceso desde cualquier dispositivo</h3>
                    <p>Tu portafolio se ve bien en todos lados.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                       <i class="fas fa-share-nodes"></i>
                    </div>
                    <h3>Comparte tu Trabajo</h3>
                    <p>Comparte tu portafolio con un enlace único y profesional.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CÓMO FUNCIONA --}}
    <section id="como-funciona" class="section section-gray">
        <div class="container">
            <h2 class="section-title">¿Cómo funciona?</h2>
            <p class="section-subtitle">En 4 sencillos pasos ya tienes tu portafolio en línea</p>

            <div class="steps-grid">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Regístrate</h3>
                    <p>Crea tu cuenta en pocos pasos.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Personaliza</h3>
                    <p>Completa tu perfil y agrega tus proyectos.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Publica</h3>
                    <p>Genera tu portafolio y hazlo visible al mundo.</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Comparte</h3>
                    <p>Comparte tu enlace y destaca tu talento.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
<footer style="min-height: 80px; display: flex; align-items: center;">
    <div class="container">
        <div class="footer-bottom">
            <p>© 2026 Todos los derechos reservados OctopuSoft SRL. Cochabamba-Bolivia</p>
        </div>
    </div>
</footer>

    <script src="{{ asset('js/welcome.js') }}"></script>
    <script>
        function toggleUserMenu() {
            const menu     = document.getElementById('userMenu');
            const chevron  = document.getElementById('dropdownChevron');
            const isOpen   = menu.classList.contains('open');
            menu.classList.toggle('open');
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        // Cerrar al hacer clic fuera
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            const menu     = document.getElementById('userMenu');
            if (dropdown && !dropdown.contains(e.target)) {
                menu.classList.remove('open');
                document.getElementById('dropdownChevron').style.transform = 'rotate(0deg)';
            }
        });
    </script>
</body>
</html>