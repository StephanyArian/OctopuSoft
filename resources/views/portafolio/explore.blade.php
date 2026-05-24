<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portafolios | DevFolio</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/explore.css') }}">
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
            
            <a href="{{ route('home') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver al inicio
            </a>
        </div>
    </nav>

    {{-- CUERPO PRINCIPAL --}}
    <div class="explore-body">
        <div class="explore-container">

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

            {{-- Filtros --}}
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

            {{-- Contador de resultados --}}
            <div class="results-count">
                <span id="totalResults">{{ $portfolios->total() }}</span> resultados encontrados
            </div>

            {{-- Grilla --}}
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

                    {{-- Paginación --}}
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
            
            <div class="footer-bottom">
                <p>© 2026 Todos los derechos reservados OctopuSoft SRL. Cochabamba-Bolivia</p>
            </div>
        </div>
    </footer>


    <script src="{{ asset('js/welcome.js') }}"></script>
    <script src="{{ asset('js/explore.js') }}"></script>
</body>
</html>