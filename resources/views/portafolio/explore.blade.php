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
        .explore-summary {
     max-width: 1100px;
     margin: 28px auto 18px;
     padding: 0 20px;
     font-size: 1rem;
     font-weight: 700;
     color: var(--dark);
     }

     .explore-summary span {
     color: var(--teal);
     font-weight: 800;
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
            padding: 16px 0 60px;
        }

        /* Contenedor central de filtros y resultados */
        .explore-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Barra de Filtros Dinámica */
        .filter-container {
            background: var(--white);
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            margin-bottom: 32px;
        }
        
       .search-bar-row {
      position: relative;
      margin-bottom: 16px;
      background: var(--white);
      border-radius: 10px;
      }

       /* ICONO LUPA */
       .search-bar-row i {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--gray-500);
      font-size: 0.95rem;
      pointer-events: none;
      z-index: 2;
     }

     .search-sticky-wrapper {
      position: sticky;
      top: 78px;
      z-index: 900;
      background: transparent;
      padding: 0;
      margin: 0;
      }

     .search-sticky-wrapper .search-bar-row {
      margin-bottom: 0;
     }

      /* INPUT */
     .search-bar-row input {
      width: 100%;
      padding: 14px 20px 14px 48px;
      border: 1.5px solid var(--gray-300);
      border-radius: 10px;
      font-size: 0.95rem;
      font-family: inherit;
      outline: none;
      transition: all 0.2s ease;
      background: var(--white);
     }

     /* FOCUS */
     .search-bar-row input:focus {
      border-color: var(--teal);
      box-shadow: 0 0 0 4px rgba(10,191,158,0.08);
      }
        .filters-row {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filters-row select {
            flex: 1;
            min-width: 180px;
            padding: 12px 16px;
            border: 1.5px solid var(--gray-300);
            border-radius: 10px;
            background: var(--white);
            font-size: 0.9rem;
            font-family: inherit;
            color: var(--dark);
            outline: none;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .filters-row select:focus {
            border-color: var(--teal);
        }
        .btn-clean {
            padding: 12px 24px;
            border: 1.5px solid var(--teal);
            background: transparent;
            color: var(--teal);
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-clean:hover {
            background: rgba(10,191,158,0.08);
        }

        /* Contador de resultados */
        .results-count {
            font-size: 0.95rem;
            color: var(--gray-600);
            margin-bottom: 20px;
            font-weight: 500;
        }
        .results-count span {
            font-weight: 700;
            color: var(--dark);
        }

        /* ── Grid: 3 columnas fijas en desktop, 1 en móvil ── */
        .explore-grid {
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
            margin-top: 40px;
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

        /* =========================================
   OCULTAR LINKS SUPERIORES
========================================= */

.nav-links .nav-link {
    display: none !important;
}

/* =========================================
   NAVBAR COMPACTO
========================================= */

.navbar.compact {
    padding: 8px 0;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

/* Ocultar botones al bajar */
.navbar.compact .btn-outline-nav,
.navbar.compact .btn-primary {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;

    transform: translateY(-10px);

    transition: all 0.25s ease;
}

/* Mantener logo visible */
.navbar.compact .logo {
    opacity: 1 !important;
    visibility: visible !important;
}

/* =========================================
   BUSCADOR STICKY
========================================= */

.search-sticky-wrapper {
    position: sticky;
    top: 14px;
    z-index: 999;

    margin-bottom: 18px;

    background: transparent;
}

/* Eliminar efectos raros */
.search-sticky-wrapper::before,
.search-sticky-wrapper::after {
    display: none;
}

.search-sticky-wrapper .search-bar-row {
    margin-bottom: 0;
    background: transparent;
    box-shadow: none;
}

/* =========================================
   BOTON VOLVER FLOTANTE
========================================= */

.btn-back {
    position: fixed;

    right: 24px;
    bottom: 24px;

    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 14px 22px;

    background: var(--white);
    color: var(--dark);

    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 999px;

    font-size: 0.95rem;
    font-weight: 700;

    text-decoration: none;

    box-shadow: 0 8px 24px rgba(0,0,0,0.12);

    z-index: 1200;

    transition: all 0.2s ease;
}

.btn-back:hover {
    transform: translateY(-2px);
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
                        <div class="nav-user-dropdown" id="userDropdown">
                            <button class="nav-user-trigger" onclick="toggleUserMenu()">
                                {{ Auth::user()->first_name ?? Auth::user()->name }}
                                <i class="fas fa-chevron-down" id="dropdownChevron"></i>
                            </button>
                            <div class="nav-user-menu" id="userMenu">
                                <a href="{{ route('profile.edit') }}">Configuración</a>
                                <a href="{{ route('cerrar.sesion') }}">Cerrar sesión</a>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"    class="btn-outline-nav">Iniciar sesión</a>
                        <a href="{{ route('register') }}" class="btn-primary">Registrarse</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    {{-- CUERPO PRINCIPAL --}}
    <div class="explore-body">
        <div class="explore-container">

            {{-- Estructura de Filtros Interactiva --}}
 {{-- Buscador sticky --}}
 <div class="search-sticky-wrapper">
    <div class="search-bar-row">
        <i class="fas fa-search"></i>

        <input
            type="text"
            id="searchRepo"
            maxlength="50"
            placeholder="Buscar por nombre, tecnología o rol..."
        >
    </div>
 </div>

 {{-- Filtros normales --}}
 <div class="filter-container">
    <div class="filters-row">
                    <select id="filterCategory">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <select id="filterSkills">
                        <option value="">Todas las tecnologías</option>
                        @foreach($skills as $skill)
                            <option value="{{ $skill->name }}">{{ $skill->name }}</option>
                        @endforeach
                    </select>

                    <select id="filterSort">
                        <option value="desc">Más recientes</option>
                        <option value="asc">Más antiguos</option>
                    </select>

                    <button id="btnClearFilters" class="btn-clean">Limpiar filtros</button>
                </div>
            </div>

            {{-- Contador de resultados dinámico --}}
            <div class="results-count">
                <span id="totalResults">{{ $portfolios->total() }}</span> resultados encontrados
            </div>

            {{-- Grilla contenedora dinámica para peticiones AJAX --}}
            <div id="portfoliosGrid" class="explore-grid">
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
                    @include('partials.portfolio_cards')

                    {{-- PAGINACIÓN --}}
                    @if($portfolios->hasPages())
                        <div class="pagination-wrapper">
                            @if($portfolios->onFirstPage())
                                <span class="page-link disabled"><i class="fas fa-chevron-left"></i></span>
                            @else
                                <a href="{{ $portfolios->previousPageUrl() }}" class="page-link"><i class="fas fa-chevron-left"></i></a>
                            @endif

                            @foreach($portfolios->getUrlRange(1, $portfolios->lastPage()) as $page => $url)
                                @if($page == $portfolios->currentPage())
                                    <span class="page-link active">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if($portfolios->hasMorePages())
                                <a href="{{ $portfolios->nextPageUrl() }}" class="page-link"><i class="fas fa-chevron-right"></i></a>
                            @else
                                <span class="page-link disabled"><i class="fas fa-chevron-right"></i></span>
                            @endif
                        </div>
                    @endif
                @endif
            </div>

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

    {{-- SCRIPTS INTERNOS Y AJAX --}}
    <script>
        // Menú hamburguesa navbar
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

    <script>
    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        const chevron = document.getElementById('dropdownChevron');

        if (!menu || !chevron) return;

        const isOpen = menu.classList.contains('open');
        menu.classList.toggle('open');
        chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('userDropdown');
        const menu = document.getElementById('userMenu');
        const chevron = document.getElementById('dropdownChevron');

        if (dropdown && menu && chevron && !dropdown.contains(e.target)) {
            menu.classList.remove('open');
            chevron.style.transform = 'rotate(0deg)';
        }
    });
 </script>
    
    <script src="{{ asset('js/welcome.js') }}"></script>

    <script>
 window.addEventListener('scroll', function () {

    const navbar = document.querySelector('.navbar');

    if (window.scrollY > 120) {
        navbar.classList.add('compact');
    } else {
        navbar.classList.remove('compact');
    }

 });
 </script>
 <a href="{{ route('home') }}" class="btn-back">
    <i class="fas fa-arrow-left"></i> Volver al inicio
 </a>

 </body>
 </html>