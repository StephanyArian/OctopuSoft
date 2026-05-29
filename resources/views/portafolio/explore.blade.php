<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portafolios | DevFolio</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/explore.css') }}?v={{ time() }}">
</head>

<body>

    {{-- NAVBAR --}}
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <a href="{{ route('home') }}" style="text-decoration: none;">
                    <h2>DevFolio</h2>
                </a>
            </div>

            <a href="{{ route('home') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Volver al inicio
            </a>
        </div>
    </nav>

    {{-- CUERPO PRINCIPAL --}}
    <div class="explore-body">
        <div class="explore-container">

            {{-- BUSCADOR + LIMPIAR FILTROS --}}
            <div class="search-sticky-wrapper">
                <div class="search-actions-row">
                    <div class="search-container">
                        <i class="fas fa-search"></i>

                        <input
                            type="text"
                            id="searchRepo"
                            value="{{ request('search') }}"
                            placeholder="Buscar por nombre, tecnología o rol..."
                            autocomplete="off"
                            maxlength="50"
                        >
                    </div>

                    <button type="button" id="btnClearFilters" class="btn-clear-inline">
                        Limpiar filtros
                    </button>
                </div>
            </div>

            {{-- FILTROS --}}
            <div class="filter-container">
                    <button type="button" class="mobile-filter-toggle" id="mobileFilterToggle">
        <span>Filtrar portafolios</span>
        <i class="fas fa-chevron-down"></i>
    </button>

                <div class="filters-row" id="filtersRow">

                    {{-- Categorías --}}
<div class="custom-dropdown" id="categoryDropdown">
    <button type="button" class="custom-dropdown-btn" id="categoryDropdownBtn">
        <span id="categoryDropdownText">Categorías</span>
        <i class="fas fa-chevron-down"></i>
    </button>

    <div class="custom-dropdown-menu" id="categoryDropdownMenu">
        <label class="custom-option">
            <input
                type="radio"
                name="category"
                value=""
                {{ request('category') ? '' : 'checked' }}
            >
            <span>Categorías</span>
        </label>

        @foreach($categories as $cat)
            <label class="custom-option">
                <input
                    type="radio"
                    name="category"
                    value="{{ $cat->id }}"
                    {{ request('category') == $cat->id ? 'checked' : '' }}
                >
                <span>{{ $cat->name }}</span>
            </label>
        @endforeach
    </div>
</div>

{{-- Tecnologías múltiples --}}
<div class="custom-dropdown" id="skillsDropdown">
    <button type="button" class="custom-dropdown-btn" id="skillsDropdownBtn">
        <span id="skillsDropdownText">Tecnologías</span>
        <i class="fas fa-chevron-down"></i>
    </button>

    <div class="custom-dropdown-menu" id="skillsDropdownMenu">

        <label class="custom-option custom-option-reset" id="clearSkillsOption">
            <input type="button" value="">
            <span>Tecnologías</span>
        </label>

        @foreach($skills as $skill)
            <label class="custom-option">
                <input
                    type="checkbox"
                    name="skills[]"
                    value="{{ $skill->name }}"
                    {{ in_array($skill->name, request('skills', [])) ? 'checked' : '' }}
                >
                <span>{{ $skill->name }}</span>
            </label>
        @endforeach
    </div>
</div>

{{-- Experiencia --}}
<div class="custom-dropdown" id="experienceDropdown">
    <button type="button" class="custom-dropdown-btn" id="experienceDropdownBtn">
        <span id="experienceDropdownText">Experiencia</span>
        <i class="fas fa-chevron-down"></i>
    </button>

    <div class="custom-dropdown-menu" id="experienceDropdownMenu">
        <label class="custom-option">
            <input
                type="radio"
                name="min_experience"
                value=""
                {{ request('min_experience') ? '' : 'checked' }}
            >
            <span>Experiencia</span>
        </label>

        <label class="custom-option">
            <input
                type="radio"
                name="min_experience"
                value="1"
                {{ request('min_experience') == '1' ? 'checked' : '' }}
            >
            <span>1 o más años</span>
        </label>

        <label class="custom-option">
            <input
                type="radio"
                name="min_experience"
                value="5"
                {{ request('min_experience') == '5' ? 'checked' : '' }}
            >
            <span>5 o más años</span>
        </label>

        <label class="custom-option">
            <input
                type="radio"
                name="min_experience"
                value="10"
                {{ request('min_experience') == '10' ? 'checked' : '' }}
            >
            <span>10 o más años</span>
        </label>
    </div>
</div>

{{-- Idiomas --}}
<div class="custom-dropdown" id="languageDropdown">
    <button type="button" class="custom-dropdown-btn" id="languageDropdownBtn">
        <span id="languageDropdownText">Idiomas</span>
        <i class="fas fa-chevron-down"></i>
    </button>

    <div class="custom-dropdown-menu" id="languageDropdownMenu">
    <label class="custom-option">
        <input type="radio" name="language" value="" {{ request('language') ? '' : 'checked' }}>
        <span>Idiomas</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Español" {{ request('language') == 'Español' ? 'checked' : '' }}>
        <span>Español</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Inglés" {{ request('language') == 'Inglés' ? 'checked' : '' }}>
        <span>Inglés</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Portugués" {{ request('language') == 'Portugués' ? 'checked' : '' }}>
        <span>Portugués</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Francés" {{ request('language') == 'Francés' ? 'checked' : '' }}>
        <span>Francés</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Alemán" {{ request('language') == 'Alemán' ? 'checked' : '' }}>
        <span>Alemán</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Italiano" {{ request('language') == 'Italiano' ? 'checked' : '' }}>
        <span>Italiano</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Chino" {{ request('language') == 'Chino' ? 'checked' : '' }}>
        <span>Chino</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Japonés" {{ request('language') == 'Japonés' ? 'checked' : '' }}>
        <span>Japonés</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Ruso" {{ request('language') == 'Ruso' ? 'checked' : '' }}>
        <span>Ruso</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Arabe" {{ request('language') == 'Arabe' ? 'checked' : '' }}>
        <span>Arabe</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Hindi" {{ request('language') == 'Hindi' ? 'checked' : '' }}>
        <span>Hindi</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Turco" {{ request('language') == 'Turco' ? 'checked' : '' }}>
        <span>Turco</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Catalán" {{ request('language') == 'Catalán' ? 'checked' : '' }}>
        <span>Catalán</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Griego" {{ request('language') == 'Griego' ? 'checked' : '' }}>
        <span>Griego</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Ucraniano" {{ request('language') == 'Ucraniano' ? 'checked' : '' }}>
        <span>Ucraniano</span>
    </label>

    <label class="custom-option">
        <input type="radio" name="language" value="Rumano" {{ request('language') == 'Rumano' ? 'checked' : '' }}>
        <span>Rumano</span>
    </label>

</div>
</div>

{{-- Ordenamiento --}}
<div class="custom-dropdown" id="sortDropdown">
    <button type="button" class="custom-dropdown-btn" id="sortDropdownBtn">
        <span id="sortDropdownText">Más recientes</span>
        <i class="fas fa-chevron-down"></i>
    </button>

    <div class="custom-dropdown-menu" id="sortDropdownMenu">
        <label class="custom-option">
            <input
                type="radio"
                name="sort"
                value="desc"
                {{ request('sort', 'desc') == 'desc' ? 'checked' : '' }}
            >
            <span>Más recientes</span>
        </label>

        <label class="custom-option">
            <input
                type="radio"
                name="sort"
                value="asc"
                {{ request('sort') == 'asc' ? 'checked' : '' }}
            >
            <span>Más antiguos</span>
        </label>

        <label class="custom-option">
            <input
                type="radio"
                name="sort"
                value="complete"
                {{ request('sort') == 'complete' ? 'checked' : '' }}
            >
            <span>Más completos</span>
        </label>

        <label class="custom-option">
            <input
                type="radio"
                name="sort"
                value="projects"
                {{ request('sort') == 'projects' ? 'checked' : '' }}
            >
            <span>Más proyectos</span>
        </label>

        <label class="custom-option">
            <input
                type="radio"
                name="sort"
                value="skills"
                {{ request('sort') == 'skills' ? 'checked' : '' }}
            >
            <span>Más tecnologías</span>
        </label>

        <label class="custom-option">
            <input
                type="radio"
                name="sort"
                value="az"
                {{ request('sort') == 'az' ? 'checked' : '' }}
            >
            <span>A-Z</span>
        </label>

        <label class="custom-option">
            <input
                type="radio"
                name="sort"
                value="za"
                {{ request('sort') == 'za' ? 'checked' : '' }}
            >
            <span>Z-A</span>
        </label>
    </div>
</div>

                </div>
            </div>

            {{-- CONTADOR DE RESULTADOS --}}
            <div class="results-count">
                <span id="totalResults">{{ $portfolios->total() }}</span>
                resultados encontrados
            </div>

            {{-- GRILLA DE PORTAFOLIOS --}}
            <div id="portfoliosGrid" class="explore-grid">
    @if($portfolios->isEmpty())
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>

            @if(request()->hasAny(['search', 'category', 'skills', 'min_experience', 'language', 'sort']))
                <h3>No se encontraron resultados</h3>
                <p>Intenta cambiar los criterios o filtros de búsqueda.</p>
            @else
                <h3>No hay portafolios aún</h3>
                <p>Aún no existen portafolios públicos disponibles para explorar.</p>
            @endif
        </div>
    @else
        @include('partials.portfolio_cards')
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

    <script src="{{ asset('js/explore.js') }}"></script>
</body>
</html>