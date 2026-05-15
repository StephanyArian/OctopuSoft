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

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    {{-- Estilos de la landing --}}
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="navbar">
        <div class="nav-container">
            
            <div class="logo">
                
                <h2>DevFolio</h2>
            </div>

            <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
                <i class="fas fa-bars"></i>
            </button>

            <div class="nav-links" id="navLinks">
                <a href="#inicio"        class="nav-link">Inicio</a>
                <a href="#que-es"        class="nav-link">Nuestro sistema</a>
                <a href="#beneficios"    class="nav-link">Beneficios</a>
                <a href="#como-funciona" class="nav-link">Cómo funciona</a>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"    class="btn-outline-nav">Iniciar sesión</a>
                        <a href="{{ route('register') }}" class="btn-primary">Registrarse</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    {{-- -----HERO —-----   --}}
    <section id="inicio" class="hero">
        <div class="container hero-content">
            <h1>Crea tu Portafolio Digital y<br>Destaca tu Talento</h1>
            <p>Nuestra plataforma te permite construir un portafolio profesional de
               forma fácil, rápida y moderna para mostrar tus proyectos,
               habilidades y experiencia al mundo.</p>

            <div class="hero-buttons">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary">Registrarse</a>
                @endif
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-outline">Iniciar sesión</a>
                @endif
            </div>
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

    {{-- BENEFICIOS --}}
    <section id="beneficios" class="section section-gray">
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
    <section id="como-funciona" class="section section-white">
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

    
    <section class="carousel-section">
        <div class="container">

            <div class="section-header">
                <h2 class="section-title">Portafolios destacados</h2>
                <p class="section-subtitle" style="margin-bottom: 0">
                    Explora el talento de nuestra comunidad
                </p>
            </div>

            <div class="carousel-wrapper">
                <div class="carousel-track" id="carouselTrack">

                    {{--
                        DATOS REALES — descomentar cuando el controlador esté listo.
                        IMPORTANTE: al descomentar, eliminar las tarjetas de ejemplo de abajo
                        y cambiar este bloque a sintaxis Blade (quitar los signos de comentario HTML).

                        @foreach ($portfolios as $portfolio)
                        <div class="portfolio-card">

                            <div class="card-photo [avatar ? '' : 'no-photo ' . avatar_color]">
                                [si tiene avatar]
                                    <img src="[asset storage/avatar]" alt="Foto de [name]">
                                [si no]
                                    <div class="initials-circle">[initials]</div>
                                [fin si]
                            </div>

                            <div class="card-body">
                                <div class="card-name">[name]</div>

                                <div class="card-professions">
                                    <span class="card-profession-main">
                                        [professions->first()->name ?? 'Profesional']
                                    </span>
                                    [si extra_professions > 0]
                                        <span class="card-profession-extra">
                                            +[extra_professions] más
                                        </span>
                                    [fin si]
                                </div>

                                [si bio]
                                    <p class="card-bio">[bio]</p>
                                [fin si]

                                <div class="card-tags">
                                    [foreach skills as skill]
                                        <span class="card-tag">[skill]</span>
                                    [endforeach]
                                </div>

                                <div class="card-footer-row">
                                    <a href="[route portfolio.public slug]" class="card-link">
                                        Ver portafolio <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                       @endforeach
                    --}}

                    {{-- ── TARJETAS DE EJEMPLO (reemplazar con @foreach arriba) ── --}}

                    {{-- Tarjeta 1: CON foto de perfil, 1 sola profesión --}}
                    <div class="portfolio-card">
                        <div class="card-photo">
                            <img src="{{ asset('imagenes/demo/avatar1.jpg') }}" alt="Andrea Morales">
                        </div>
                        <div class="card-body">
                            <div class="card-name">Andrea Morales</div>
                            <div class="card-professions">
                                <span class="card-profession-main">Diseñadora UX/UI</span>
                                {{-- Sin badge porque solo tiene 1 profesión --}}
                            </div>
                            <p class="card-bio">Creando experiencias digitales centradas en el usuario para productos modernos.</p>
                            <div class="card-tags">
                                <span class="card-tag">Figma</span>
                                <span class="card-tag">Prototyping</span>
                                <span class="card-tag">Research</span>
                            </div>
                            <div class="card-footer-row">
                                <a href="#" class="card-link">
                                    Ver portafolio <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta 2: CON foto, múltiples profesiones → muestra badge --}}
                    <div class="portfolio-card">
                        <div class="card-photo">
                            <img src="{{ asset('imagenes/demo/avatar2.jpg') }}" alt="Carlos Ríos">
                        </div>
                        <div class="card-body">
                            <div class="card-name">Carlos Ríos</div>
                            <div class="card-professions">
                                <span class="card-profession-main">Full Stack Developer</span>
                                {{-- Tiene 2 profesiones más: badge "+2 más" --}}
                                <span class="card-profession-extra">+2 más</span>
                            </div>
                            <p class="card-bio">Apasionado por construir aplicaciones web escalables con tecnologías modernas.</p>
                            <div class="card-tags">
                                <span class="card-tag">React</span>
                                <span class="card-tag">Node.js</span>
                                <span class="card-tag">PostgreSQL</span>
                            </div>
                            <div class="card-footer-row">
                                <a href="#" class="card-link">
                                    Ver portafolio <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta 3: SIN foto → color-3 + iniciales --}}
                    <div class="portfolio-card">
                        <div class="card-photo no-photo color-3">
                            <div class="initials-circle">LV</div>
                        </div>
                        <div class="card-body">
                            <div class="card-name">Lucía Vega</div>
                            <div class="card-professions">
                                <span class="card-profession-main">Data Scientist</span>
                                {{-- Tiene 1 profesión adicional --}}
                                <span class="card-profession-extra">+1 más</span>
                            </div>
                            <p class="card-bio">Transformando datos crudos en insights accionables mediante ML y analítica avanzada.</p>
                            <div class="card-tags">
                                <span class="card-tag">Python</span>
                                <span class="card-tag">ML</span>
                                <span class="card-tag">Tableau</span>
                            </div>
                            <div class="card-footer-row">
                                <a href="#" class="card-link">
                                    Ver portafolio <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta 4: SIN foto → color-4 + iniciales, 1 profesión --}}
                    <div class="portfolio-card">
                        <div class="card-photo no-photo color-4">
                            <div class="initials-circle">JP</div>
                        </div>
                        <div class="card-body">
                            <div class="card-name">Jorge Palacios</div>
                            <div class="card-professions">
                                <span class="card-profession-main">Ing. de Software</span>
                            </div>
                            <p class="card-bio">Especializado en arquitecturas backend robustas y servicios en la nube con AWS.</p>
                            <div class="card-tags">
                                <span class="card-tag">Java</span>
                                <span class="card-tag">Spring</span>
                                <span class="card-tag">AWS</span>
                            </div>
                            <div class="card-footer-row">
                                <a href="#" class="card-link">
                                    Ver portafolio <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta 5: CON foto, múltiples profesiones --}}
                    <div class="portfolio-card">
                        <div class="card-photo">
                            <img src="{{ asset('imagenes/demo/avatar3.jpg') }}" alt="María Paredes">
                        </div>
                        <div class="card-body">
                            <div class="card-name">María Paredes</div>
                            <div class="card-professions">
                                <span class="card-profession-main">Diseñadora Gráfica</span>
                                <span class="card-profession-extra">+1 más</span>
                            </div>
                            <p class="card-bio">Especialista en identidad visual y branding para marcas que quieren destacar.</p>
                            <div class="card-tags">
                                <span class="card-tag">Illustrator</span>
                                <span class="card-tag">Branding</span>
                                <span class="card-tag">Photoshop</span>
                            </div>
                            <div class="card-footer-row">
                                <a href="#" class="card-link">
                                    Ver portafolio <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>{{-- /.carousel-track --}}
            </div>{{-- /.carousel-wrapper --}}

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

    {{--CTA FINAL --}}
    <div class="container">
        <div class="cta-section">
            <div class="cta-text">
                <h2>¿Listo para crear tu portafolio?</h2>
                <p>Únete a miles de profesionales que ya destacan su talento</p>
            </div>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-primary">Regístrate Gratis</a>
            @endif
        </div>
    </div>

    {{-- FOOTER --}}
    <footer>
        <div class="container">
            <div class="footer-main">
                <div class="footer-brand">
                    <div class="footer-logo-text">DevFolio</div>
                    <div class="footer-tagline">Crea. Comparte. Impacta.</div>
                </div>
                <nav class="footer-nav" aria-label="Links del footer">
                    <a href="#inicio">Inicio</a>
                    <a href="#que-es">¿Qué es el sistema?</a>
                    <a href="#beneficios">Beneficios</a>
                    <a href="#como-funciona">Cómo funciona</a>
                </nav>
            </div>
            <div class="footer-bottom">
                <p>Todos los derechos estan reservados por la empresa de software OctopuSoft.</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/welcome.js') }}"></script>

</body>
</html>