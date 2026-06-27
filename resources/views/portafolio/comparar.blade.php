<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comparar perfiles | DevFolio</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/explore.css') }}?v={{ time() }}">
</head>

<body>

    @php
        $backParams = request()->except(['ids']);

        if (request()->has('ids')) {
            $backParams = request()->except(['ids']);
        }

        $backUrl = route('portafolio.explore', $backParams);
    @endphp
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <a href="{{ url('/') }}" style="text-decoration: none;">
                    <h2>DevFolio</h2>
                </a>
            </div>

            <a href="{{ $backUrl }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Volver a portafolios
            </a>
        </div>
    </nav>

    <main class="compare-page">
        <div class="compare-container">

            <div class="compare-header">
                <h1>Comparación de perfiles</h1>
                <p>
                    Revisa rápidamente cuál perfil se ajusta mejor a los criterios de búsqueda.
                </p>
            </div>

            @php
                $bestPortfolio = $portfolios->firstWhere('id', $bestPortfolioId);
            @endphp

            @if($bestPortfolio)
                <div class="compare-recommendation">
                    <i class="fas fa-award"></i>

                    <div>
                        <strong>Perfil recomendado:</strong>
                        {{ $bestPortfolio->user->first_name }} {{ $bestPortfolio->user->last_name }}

                        <span>
                            con {{ $scores[$bestPortfolio->id]['score'] ?? 0 }}% de coincidencia.
                        </span>
                    </div>
                </div>
            @endif

            <div class="compare-table-wrapper">
                <table class="compare-table">
                    <thead>
                        <tr>
                            <th>Criterio</th>

                            @foreach($portfolios as $portfolio)
                                <th>
                                    <div class="compare-user-title">
                                        {{ $portfolio->user->first_name }} {{ $portfolio->user->last_name }}
                                    </div>

                                    <div class="compare-score">
                                        {{ $scores[$portfolio->id]['score'] ?? 0 }}% match
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>Profesión</td>

                            @foreach($portfolios as $portfolio)
                                <td>
                                    {{ optional($portfolio->user->profession)->name ?? 'No registrada' }}
                                </td>
                            @endforeach
                        </tr>

                        <tr>
                            <td>Ubicación</td>

                            @foreach($portfolios as $portfolio)
                                <td>
                                    @php
                                        $city = $portfolio->user->city ?? null;
                                        $country = $portfolio->user->country ?? null;
                                        $address = optional($portfolio->user->location)->address ?? null;
                                    @endphp

                                    @if($city || $country)
                                        {{ $city ?? '' }}{{ $city && $country ? ', ' : '' }}{{ $country ?? '' }}
                                    @elseif($address)
                                        {{ $address }}
                                    @else
                                        No registrada
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        <tr>
                            <td>Empresas / instituciones</td>

                            @foreach($portfolios as $portfolio)
                                @php
                                    $empresas = collect($portfolio->user->experiences)
                                        ->where('type', 'work')
                                        ->filter(fn ($exp) => (bool) $exp->is_visible)
                                        ->pluck('institution')
                                        ->filter()
                                        ->unique()
                                        ->values();
                                @endphp

                                <td>
                                    {{ $empresas->count() ? $empresas->join(', ') : 'No registrado' }}
                                </td>
                            @endforeach
                        </tr>

                        <tr>
                            <td>Cargos</td>

                            @foreach($portfolios as $portfolio)
                                @php
                                    $cargos = collect($portfolio->user->experiences)
                                        ->where('type', 'work')
                                        ->filter(fn ($exp) => (bool) $exp->is_visible)
                                        ->pluck('title')
                                        ->filter()
                                        ->unique()
                                        ->values();
                                @endphp

                                <td>
                                    {{ $cargos->count() ? $cargos->join(', ') : 'No registrado' }}
                                </td>
                            @endforeach
                        </tr>

                        <tr>
                            <td>Años de experiencia</td>

                            @foreach($portfolios as $portfolio)
                                <td>
                                    {{ $scores[$portfolio->id]['years'] ?? 0 }} año(s)
                                </td>
                            @endforeach
                        </tr>

                        <tr>
                            <td>Habilidades técnicas</td>

                            @foreach($portfolios as $portfolio)
                                @php
                                    $skills = collect($portfolio->user->skills)
                                        ->where('type', 'technical')
                                        ->filter(fn ($skill) => (bool) $skill->is_visible)
                                        ->pluck('name')
                                        ->filter()
                                        ->take(8)
                                        ->values();
                                @endphp

                                <td>
                                    {{ $skills->count() ? $skills->join(', ') : 'No registrado' }}
                                </td>
                            @endforeach
                        </tr>

                        <tr>
                            <td>Idiomas</td>

                            @foreach($portfolios as $portfolio)
                                @php
                                    $idiomas = collect($portfolio->user->skills)
                                        ->where('type', 'language')
                                        ->filter(fn ($skill) => (bool) $skill->is_visible)
                                        ->pluck('name')
                                        ->filter()
                                        ->values();
                                @endphp

                                <td>
                                    {{ $idiomas->count() ? $idiomas->join(', ') : 'No registrado' }}
                                </td>
                            @endforeach
                        </tr>

                        <tr>
                            <td>Proyectos visibles</td>

                            @foreach($portfolios as $portfolio)
                                @php
                                    $proyectos = collect($portfolio->projects)
                                        ->filter(fn ($project) => (bool) $project->is_visible);
                                @endphp

                                <td>
                                    {{ $proyectos->count() }} proyecto(s)
                                </td>
                            @endforeach
                        </tr>

                        <tr>
                            <td>Redes profesionales</td>

                            @foreach($portfolios as $portfolio)
                                @php
                                    $redes = collect($portfolio->user->professionalNetworks)
                                        ->filter(fn ($network) => (bool) $network->is_visible);
                                @endphp

                                <td>
                                    {{ $redes->count() }} red(es) visible(s)
                                </td>
                            @endforeach
                        </tr>

                        <tr>
                            <td>Razones de coincidencia</td>

                            @foreach($portfolios as $portfolio)
                                <td>
                                    @if(!empty($scores[$portfolio->id]['reasons']))
                                        <ul class="compare-reasons">
                                            @foreach($scores[$portfolio->id]['reasons'] as $reason)
                                                <li>{{ $reason }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        No se encontraron coincidencias específicas.
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        <tr>
                            <td>Acción</td>

                            @foreach($portfolios as $portfolio)
                                <td>
                                    <a href="{{ route('portafolio.public', $portfolio->slug) }}" class="compare-profile-link">
                                        Ver portafolio
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</body>
</html>