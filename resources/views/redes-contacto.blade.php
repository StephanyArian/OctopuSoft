<x-app-layout>



    {{-- ESTILOS --}}
    <link rel="stylesheet" href="{{ asset('css/informacion-academica.css') }}">
    <link rel="stylesheet" href="{{ asset('css/redes-contacto.css') }}">

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
                        <a href="{{ route('experiencia.laboral') }}" class="sidebar-item">Experiencia laboral</a>
                        <a href="{{ route('informacion.academica') }}" class="sidebar-item">Información académica</a>
                        <a href="{{ route('skills.tecnicas') }}" class="sidebar-item">Habilidades técnicas</a>
                        <a href="{{ route('skills.blandas') }}" class="sidebar-item">Habilidades blandas</a>
                        <a href="{{ route('proyectos') }}" class="sidebar-item" >Proyectos</a>  
                        <a href="{{ route('redes.index') }}" class="sidebar-item active">Redes profesionales y contacto</a>
                    </div>

                    {{-- CONTENIDO PRINCIPAL --}}
                    <div class="main">

                        <div class="page-title">
                            Redes profesionales y contacto
                        </div>

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

                                {{-- LinkedIn --}}
                                <div class="form-group">
                                    <label>LinkedIn</label>
                                    <input 
                                        type="url" 
                                        name="linkedin"
                                        class="form-input"
                                        placeholder="Ej. https://linkedin.com/in/juan"
                                        pattern="https://(www\.)?linkedin\.com/.*"
                                        value="{{ old('linkedin', $redes[1]->profile_url ?? '') }}"
                                    >
                                </div>

                                {{-- GitHub --}}
                                <div class="form-group">
                                    <label>GitHub</label>
                                    <input 
                                        type="url" 
                                        name="github"
                                        class="form-input"
                                        placeholder="Ej. https://github.com/usuario"
                                        pattern="https://(www\.)?github\.com/.*"
                                        value="{{ old('github', $redes[2]->profile_url ?? '') }}"
                                    >
                                </div>

                                {{-- WhatsApp --}}
                                <div class="form-group">
                                    <label>WhatsApp</label>
                                    <input 
                                        type="text" 
                                        name="whatsapp"
                                        class="form-input"
                                        placeholder="+591..."
                                        pattern="^\+?[0-9]{8,15}$"
                                        title="Solo números, entre 8 y 15 dígitos"
                                        value="{{ old('whatsapp', isset($redes[3]) 
                                            ? preg_replace('/https:\/\/wa\.me\//', '', $redes[3]->profile_url) 
                                            : '') }}"
                                    >
                                </div>

                                {{-- Correo --}}
                                <div class="form-group">
                                    <label>Correo</label>
                                    <input 
                                        type="email" 
                                        name="email_contacto"
                                        class="form-input"
                                        pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$"
                                        title="Solo correos Gmail"
                                        value="{{ old('email_contacto', $redes[4]->profile_url ?? '') }}"
                                    >
                                </div>

                                {{-- Otros --}}
                                <div class="form-group">
                                    <label>Otros</label>
                                    <input 
                                        type="text" 
                                        name="otros"
                                        class="form-input"
                                        maxlength="50"
                                        value="{{ old('otros', $redes[5]->profile_url ?? '') }}"
                                    >
                                </div>

                                {{-- BOTÓN --}}
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
