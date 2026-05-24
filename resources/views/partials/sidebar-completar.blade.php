@php
    $user = Auth::user();

    $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));

    if ($fullName === '') {
        $fullName = $user->name ?? 'Usuario';
    }

    $initial = strtoupper(substr($fullName, 0, 1));
@endphp

<div class="sidebar">

    <div class="sidebar-profile">
        <div class="sidebar-profile-photo">
            @if($user->photo_base64)
                <img src="{{ $user->photo_base64 }}" alt="Foto de perfil">
            @else
                <span>{{ $initial }}</span>
            @endif
        </div>

        <div class="sidebar-profile-name" title="{{ $fullName }}">
            {{ $fullName }}
        </div>

        <div class="sidebar-profile-email" title="{{ $user->email }}">
            {{ $user->email }}
        </div>
    </div>

    <a href="{{ route('profile.create') }}" class="sidebar-item {{ request()->routeIs('profile.create') ? 'active' : '' }}">
        <i class="bi bi-person"></i>
        Personal
    </a>

    <a href="{{ route('experiencia.laboral') }}" class="sidebar-item {{ request()->routeIs('experiencia.laboral') ? 'active' : '' }}">
        <i class="bi bi-briefcase"></i>
        Experiencia laboral
    </a>

    <a href="{{ route('informacion.academica') }}" class="sidebar-item {{ request()->routeIs('informacion.academica') ? 'active' : '' }}">
        <i class="bi bi-mortarboard"></i>
        Información académica
    </a>

    <a href="{{ route('skills.tecnicas') }}" class="sidebar-item {{ request()->routeIs('skills.tecnicas') ? 'active' : '' }}">
        <i class="bi bi-stars"></i>
        Habilidades técnicas
    </a>

    <a href="{{ route('skills.blandas') }}" class="sidebar-item {{ request()->routeIs('skills.blandas') ? 'active' : '' }}">
        <i class="bi bi-heart"></i>
        Habilidades blandas
    </a>

    <a href="{{ route('proyectos') }}" class="sidebar-item {{ request()->routeIs('proyectos') ? 'active' : '' }}">
        <i class="bi bi-journal-code"></i>
        Proyectos
    </a>

    <a href="{{ route('idiomas.index') }}" class="sidebar-item {{ request()->routeIs('idiomas.index') ? 'active' : '' }}">
        <i class="bi bi-card-text"></i>
        Idiomas
    </a>

    <a href="{{ route('redes.index') }}" class="sidebar-item {{ request()->routeIs('redes.index') ? 'active' : '' }}">
        <i class="bi bi-globe2"></i>
        Redes profesionales y contacto
    </a>

    <div class="sidebar-footer-actions">
        <a href="{{ route('preview') }}" class="sidebar-preview-link">
            <i class="bi bi-eye"></i>
            Ver perfil
        </a>
    </div>

</div>