<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portafolios | DevFolio</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    {{-- Reutiliza las variables CSS del welcome.css --}}
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

    <style>
        /* ── Hero ── */
        .explore-hero {
            background: var(--burg-deep);
            padding: 40px 0 32px;
            border-bottom: 1px solid var(--burg-mid);
        }
        .explore-hero-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .explore-hero-text h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--white);
            margin-bottom: 4px;
        }
        .explore-hero-text p {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.5);
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 8px 16px;
            border: 1.5px solid rgba(255,255,255,0.2);
            border-radius: 40px;
            transition: all 0.2s ease;
        }
        .btn-back:hover {
            color: var(--white);
            border-color: var(--teal);
            background: rgba(10,191,158,0.1);
        }

        /* ── Body ── */
        .explore-body {
            background: var(--off);
            min-height: calc(100vh - 200px);
            padding: 48px 0 60px;
        }

        /* ── Grid: 3 columnas fijas en desktop, 1 en móvil ── */
        .explore-grid {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        @media (max-width: 900px) {
            .explore-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 580px) {
            .explore-grid { grid-template-columns: 1fr; }
    }

        /* ── Tarjeta uniforme ── */
        .explore-grid .portfolio-card {
            flex: none;
            width: 100%;
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            overflow: hidden;
            background: var(--white);
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .explore-grid .portfolio-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        /* ── Foto: altura fija para todas ── */
        .explore-grid .card-photo {
            width: 100%;
            height: 220px;
            overflow: hidden;
            flex-shrink: 0;
        }
        .explore-grid .card-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 45%;
            display: block;
        }

        /* ── Iniciales: misma altura que la foto ── */
        .explore-grid .card-photo.no-photo {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .explore-grid .card-photo .initials-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--white);
        }

        /* ── Body de la tarjeta crece para igualar alturas ── */
        .explore-grid .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }
        .explore-grid .card-footer-row {
            margin-top: auto;
            padding-top: 16px;
        }

        /* ── Paginación ── */
        .pagination-wrapper {
            max-width: 1100px;
            margin: 40px auto 0;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .pagination-wrapper .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border-radius: 8px;
            border: 1.5px solid var(--gray-300);
            background: var(--white);
            color: var(--dark);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .pagination-wrapper .page-link:hover {
            border-color: var(--teal);
            color: var(--teal);
        }
        .pagination-wrapper .page-link.active {
            background: var(--teal);
            border-color: var(--teal);
            color: var(--white);
        }
        .pagination-wrapper .page-link.disabled {
            opacity: 0.4;
            pointer-events: none;
        }

        /* ── Estado vacío ── */
        .empty-state {
            max-width: 400px;
            margin: 60px auto;
            text-align: center;
            padding: 0 20px;
        }
        .empty-state i {
            font-size: 3rem;
            color: var(--gray-300);
            margin-bottom: 16px;
        }
        .empty-state h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gray-700);
            margin-bottom: 8px;
        }
        .empty-state p {
            font-size: 0.9rem;
            color: var(--gray-500);
            line-height: 1.6;
        }
    </style>
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <a href="{{ route('home') }}" style="text-decoration:none">
                    <h2>DevFolio</h2>
                </a>
            </div>

            <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
                <i class="fas fa-bars"></i>
            </button>

            <div class="nav-links" id="navLinks">
                <a href="{{ route('home') }}#inicio"        class="nav-link">Inicio</a>
                <a href="{{ route('home') }}#que-es"        class="nav-link">Nuestro sistema</a>
                <a href="{{ route('home') }}#beneficios"    class="nav-link">Beneficios</a>
                <a href="{{ route('home') }}#como-funciona" class="nav-link">Cómo funciona</a>

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

    {{-- HEADER DE LA PÁGINA --}}
    <div class="explore-hero">
        <div class="explore-hero-inner">
            <div class="explore-hero-text">
                <h1>Portafolios de la comunidad</h1>
                <p>{{ $portfolios->total() }} portafolios publicados</p>
            </div>
            <a href="{{ route('home') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver al inicio
            </a>
        </div>
    </div>

    {{-- GRID DE PORTAFOLIOS --}}
    <div class="explore-body">

        @if($portfolios->isEmpty())
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>No hay portafolios aún</h3>
                <p>Sé el primero en crear y publicar tu portafolio profesional.</p>
                <br>
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary">Crear mi portafolio</a>
                @endif
            </div>

        @else
            <div class="explore-grid">
                @foreach($portfolios as $portfolio)
                <div class="portfolio-card">

                    {{-- Foto o color + iniciales --}}
                    @php
                        $colorList = ['color-1','color-2','color-3','color-4','color-5'];
                        $colorClass = $colorList[$loop->index % 5];
                    @endphp

                    @if($portfolio->user->photo_base64)
                        <div class="card-photo">
                            <img src="{{ $portfolio->user->photo_base64 }}"
                                 alt="{{ $portfolio->user->first_name }}">
                        </div>
                    @else
                        <div class="card-photo no-photo {{ $colorClass }}">
                            <div class="initials-circle">
                                {{ strtoupper(
                                    substr($portfolio->user->first_name ?? '?', 0, 1) .
                                    substr($portfolio->user->last_name  ?? '', 0, 1)
                                ) }}
                            </div>
                        </div>
                    @endif
                    

                    <div class="card-body">
                        <div class="card-name">
                            {{ $portfolio->user->first_name }} {{ $portfolio->user->last_name }}
                        </div>

                        {{-- Profesión principal + badge extras --}}
                        <div class="card-professions">
                            <span class="card-profession-main">
                                {{ $portfolio->user->profession->name ?? 'Profesional' }}
                            </span>
                            @php
                                $extraSkills = $portfolio->user->skills
                                    ->where('type', 'technical')->count() - 1;
                            @endphp
                            @if($extraSkills > 0)
                                <span class="card-profession-extra">+{{ $extraSkills }} más</span>
                            @endif
                        </div>

                        <p class="card-bio">
                            {{ Str::limit($portfolio->user->biography ?? 'Sin biografía', 80) }}
                        </p>

                        {{-- Tags de habilidades --}}
                        <div class="card-tags">
                            @foreach($portfolio->user->skills->where('type','technical')->take(3) as $skill)
                                <span class="card-tag">{{ $skill->name }}</span>
                            @endforeach
                        </div>

                        {{-- Botón ver portafolio --}}
                        <div class="card-footer-row">
                            <a href="{{ route('portafolio.public', $portfolio->slug) }}"
                               class="card-link">
                                Ver portafolio <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- PAGINACIÓN --}}
            @if($portfolios->hasPages())
                <div class="pagination-wrapper">
                    {{-- Anterior --}}
                    @if($portfolios->onFirstPage())
                        <span class="page-link disabled">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $portfolios->previousPageUrl() }}" class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Números de página --}}
                    @foreach($portfolios->getUrlRange(1, $portfolios->lastPage()) as $page => $url)
                        @if($page == $portfolios->currentPage())
                            <span class="page-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Siguiente --}}
                    @if($portfolios->hasMorePages())
                        <a href="{{ $portfolios->nextPageUrl() }}" class="page-link">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="page-link disabled">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            @endif

        @endif
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
                    <a href="{{ route('home') }}#inicio">Inicio</a>
                    <a href="{{ route('home') }}#que-es">Nuestro sistema</a>
                    <a href="{{ route('home') }}#beneficios">Beneficios</a>
                    <a href="{{ route('home') }}#como-funciona">Cómo funciona</a>
                </nav>
            </div>
            <div class="footer-bottom">
                <p>Todos los derechos están reservados por la empresa de software OctopuSoft.</p>
            </div>
        </div>
    </footer>

    <script>
        // Menú hamburguesa (mismo que welcome.js)
        const menuToggle = document.getElementById('menuToggle');
        const navLinks   = document.getElementById('navLinks');
        if (menuToggle && navLinks) {
            menuToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
                const icon = menuToggle.querySelector('i');
                const isOpen = navLinks.classList.contains('active');
                icon.classList.toggle('fa-bars', !isOpen);
                icon.classList.toggle('fa-times', isOpen);
            });
        }
    </script>

</body>
</html>