<x-app-layout>

<x-slot name="header">
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <h2 class="font-semibold text-xl leading-tight" style="color: var(--burg-deep);">
            {{ __('Formulario de Perfil Profesional') }}
        </h2>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-slot>

<link rel="stylesheet" href="{{ asset('css/informacion-academica.css') }}">
<link rel="stylesheet" href="{{ asset('css/redes.css') }}">

<div class="main-content">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

<div class="shell">
    <div class="header-bar">BIENVENIDO</div>

    <div class="navbar">
        <div class="nav-tab active">COMPLETAR</div>
        <div class="nav-tab muted">VER PERFIL</div>
    </div>

    <div class="body-row">

        {{-- SIDEBAR --}}
        <div class="sidebar">
            <a href="{{ route('profile.create') }}" class="sidebar-item">Personal</a>
            <div class="sidebar-item">Experiencia laboral</div>
            <a href="{{ route('informacion.academica') }}" class="sidebar-item">Información académica</a>
            <a href="{{ route('skills.tecnicas') }}" class="sidebar-item">Habilidades técnicas</a>
            <a href="{{ route('skills.blandas') }}" class="sidebar-item">Habilidades blandas</a>
            <div class="sidebar-item">Proyectos</div>
            <a href="{{ route('redes.index') }}" class="sidebar-item active">
                Redes profesionales y contacto
            </a>
        </div>

        {{-- CONTENIDO --}}
        <div class="main">

            <div class="page-title">Redes profesionales y contacto</div>

            <div class="section-card">

                <div class="section-subtitle">
                    Agrega tus enlaces profesionales y medios de contacto.
                </div>

                {{-- MENSAJES --}}
                @if(session('success'))
                    <div class="alert-skill success">
                        <div class="alert-skill-icon">✓</div>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert-skill error">
                        <div class="alert-skill-icon">!</div>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- FORMULARIO --}}
                <form method="POST" action="{{ route('redes.store') }}">
                    @csrf

                    <div class="form-group">
                        <label>LinkedIn</label>
                        <input type="url" name="linkedin"
                               class="form-input"
                               placeholder="Ej. linkedin.com/in/juan"
                               value="{{ old('linkedin') }}">
                    </div>

                    <div class="form-group">
                        <label>GitHub</label>
                        <input type="url" name="github"
                               class="form-input"
                               placeholder="Ej. github.com/usuario"
                               value="{{ old('github') }}">
                    </div>

                    <div class="form-group">
                        <label>WhatsApp</label>
                        <input type="text" name="whatsapp"
                               class="form-input"
                               placeholder="+591..."
                               value="{{ old('whatsapp') }}">
                    </div>

                    <div class="form-group">
                        <label>Correo</label>
                        <input type="email" name="email_contacto"
                               class="form-input"
                               value="{{ old('email_contacto') }}">
                    </div>

                    <div class="form-group">
                        <label>Otros</label>
                        <input type="text" name="otros"
                               class="form-input"
                               value="{{ old('otros') }}">
                    </div>

                    <div class="btn-row">
                        <button type="submit" class="btn primary">
                            Guardar cambios
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

</div>
</div>

</x-app-layout>
