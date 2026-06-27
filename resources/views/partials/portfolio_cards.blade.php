@forelse($portfolios as $portfolio)
    @php
        $cardTheme = $portfolio->color_theme ?? 'default';
        $cardThemeClass = $cardTheme !== 'default' ? 'card-theme-' . $cardTheme : '';

        $completedSections = collect([
            filled($portfolio->user->photo_base64),
            filled($portfolio->user->biography),
            filled($portfolio->user->profession_id),
            $portfolio->user->skills->where('type', 'technical')->where('is_visible', true)->count() > 0,
            $portfolio->projects->where('is_visible', true)->count() > 0,
            $portfolio->user->professionalNetworks->where('is_visible', true)->count() > 0,
            $portfolio->user->experiences->where('type', 'work')->where('is_visible', true)->count() > 0,
            $portfolio->user->experiences->where('type', 'education')->where('is_visible', true)->count() > 0,
            $portfolio->user->skills->where('type', 'language')->where('is_visible', true)->count() > 0,
        ])->filter()->count();

        $portfolioStars = max(1, min(5, (int) ceil(($completedSections / 9) * 5)));
    @endphp
    <div class="portfolio-card {{ $cardThemeClass }}">
        {{-- Foto o color + iniciales --}}
        @php
            $colorList = ['color-1','color-2','color-3','color-4','color-5'];
            $colorClass = $colorList[$loop->index % 5];
        @endphp

        @if($portfolio->user->photo_base64)
            <div class="card-photo">
                <img src="{{ $portfolio->user->photo_base64 }}" alt="{{ $portfolio->user->first_name }}">
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
                    $extraSkills = $portfolio->user->skills->where('type', 'technical')->count() - 1;
                @endphp
                @if($extraSkills > 0)
                    <span class="card-profession-extra">+{{ $extraSkills }} más</span>
                @endif
            </div>

            <p class="card-bio">
                {{ Str::limit(strip_tags($portfolio->user->biography ?? 'Sin biografía'), 80) }}
            </p>

            {{-- Tags de habilidades --}}
            <div class="card-tags">
                @foreach($portfolio->user->skills->where('type','technical')->take(3) as $skill)
                    <span class="card-tag">{{ $skill->name }}</span>
                @endforeach
            </div>

            {{-- Estrellas + botón ver portafolio --}}
<div class="card-footer-row">
    <div class="portfolio-rating" title="Portafolio {{ $completedSections }}/9 secciones completas">
        <span class="rating-stars">
            @for($i = 1; $i <= 5; $i++)
                <i class="{{ $i <= $portfolioStars ? 'fas' : 'far' }} fa-star"></i>
            @endfor
        </span>

        <span class="rating-text">
            {{ $portfolioStars }}/5 completo
        </span>
    </div>

    <a href="{{ route('portafolio.public', $portfolio->slug) }}" class="card-link">
        Ver portafolio <i class="fas fa-arrow-right"></i>
    </a>
</div>
        </div>
    </div>
@empty
    <div class="empty-state empty-results">
        <i class="fas fa-folder-open"></i>
        <h3>No se encontraron resultados</h3>
        <p>Intenta cambiar los criterios o filtros de búsqueda.</p>
    </div>
@endforelse

{{-- Paginación colocada fuera del bucle pero renderizada dinámicamente --}}
@if(empty($ajax) && method_exists($portfolios, 'hasPages') && $portfolios->hasPages())
    <div class="pagination-wrapper" style="grid-column: 1 / -1; width: 100%;">
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