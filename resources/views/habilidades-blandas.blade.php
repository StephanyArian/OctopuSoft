{{-- resources/views/habilidades-blandas.blade.php --}}
<x-app-layout>
    
    <link rel="stylesheet" href="{{ asset('css/informacion-academica.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skills.css') }}">

    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="shell">
                <div class="header-bar" style="display: none;"></div>
                <div class="navbar">
                    <div class="nav-tab active">COMPLETAR</div>
                    <a href="{{ route('preview') }}" class="nav-tab muted">VER PERFIL</a>
                </div>

                <div class="body-row">
                    {{-- Sidebar --}}
                    <div class="sidebar">
                        <a href="{{ route('profile.create') }}" class="sidebar-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg>
        Personal
    </a>

    <a href="{{ route('experiencia.laboral') }}" class="sidebar-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
        </svg>
        Experiencia laboral
    </a>

    <a href="{{ route('informacion.academica') }}" class="sidebar-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
        </svg>
        Información académica
    </a>

    <a href="{{ route('skills.tecnicas') }}" class="sidebar-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/>
        </svg>
        Habilidades técnicas
    </a>

    <a href="{{ route('skills.blandas') }}" class="sidebar-item active">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        Habilidades blandas
    </a>

    <a href="{{ route('proyectos') }}" class="sidebar-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
        </svg>
        Proyectos
    </a>

    <a href="{{ route('idiomas.index') }}" class="sidebar-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h4M15 9h2M9 9h2"/><path d="M2 9h20"/>
        </svg>
        Idiomas
    </a>

    <a href="{{ route('redes.index') }}" class="sidebar-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        </svg>
        Redes profesionales y contacto
    </a>
                    </div>

                    {{-- Contenido --}}
                    <div class="main">
                        <div class="page-title">Habilidades blandas</div>

                        <div class="section-card">
                            <div class="section-subtitle">
                                Agrega tus habilidades interpersonales y de comunicación para enriquecer tu perfil.
                            </div>

                            {{-- Alertas --}}
                            @if(session('success'))
                                <div class="alert-skill success">
                                    <div class="alert-skill-icon">✓</div>
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif

                            @if(session('error_limit'))
                                <div class="alert-skill warning">
                                    <div class="alert-skill-icon">!</div>
                                    <span>{{ session('error_limit') }}</span>
                                </div>
                            @endif

                            @if(session('error_duplicate'))
                                <div class="alert-skill error">
                                    <div class="alert-skill-icon">!</div>
                                    <span>{{ session('error_duplicate') }}</span>
                                </div>
                            @endif

                            @php
                                $total     = $skills->count();
                                $atLimit   = $total >= 20;
                                $editSkill = null;
                                if (request('edit')) {
                                    $editSkill = $skills->firstWhere('id', request('edit'));
                                }
                                $showForm = old('_action') === 'store'
                                         || $errors->any()
                                         || session('error_duplicate')
                                         || session('error_limit')
                                         || $editSkill !== null
                                         || request()->has('add');
                            @endphp

                            {{-- Botón agregar / formulario --}}
                            @if(!$atLimit || $editSkill)
                                @if(!$showForm)
                                    <button class="btn-agregar" onclick="toggleForm(true)">
                                        + Agregar habilidad blanda
                                    </button>
                                @endif

                                <div id="skill-form-wrap" style="{{ $showForm ? '' : 'display:none;' }}margin-top:20px;">
                                    <div class="skill-form-box {{ $errors->any() ? 'has-error' : '' }}" style="margin-top:20px;">
                                        @if($editSkill)
                                            <form action="{{ route('skills.update', $editSkill->id) }}" method="POST">
                                                @csrf @method('PUT')
                                        @else
                                            <form action="{{ route('skills.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="_action" value="store">
                                        @endif
                                            <input type="hidden" name="type" value="soft">

                                            <div class="form-group">
                                                <label class="form-label">
                                                    Nombre de la habilidad <span class="required">*</span>
                                                </label>
                                                <input type="text" name="name" class="form-input"
                                                    placeholder="Ej. Trabajo en equipo, Liderazgo..."
                                                    value="{{ old('name', $editSkill?->name) }}"
                                                    maxlength="60"
                                                    autocomplete="off">
                                                @error('name')
                                                    <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="btn-row">
                                                <button type="submit" class="btn primary">
                                                    {{ $editSkill ? 'Guardar cambios' : 'Guardar' }}
                                                </button>
                                                <button type="button" class="btn" onclick="cancelForm()">
                                                    Cancelar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div class="alert-skill warning">
                                    <div class="alert-skill-icon">!</div>
                                    <span>Has alcanzado el límite máximo de 20 habilidades blandas.</span>
                                </div>
                            @endif

                            {{-- Pills / lista --}}
                            @if($total > 0)
                                <div class="soft-pills-wrap" style="margin-top:28px;">
                                    @foreach($skills as $skill)
                                        <div class="soft-pill">
                                            <span>{{ $skill->name }}</span>
                                            <div class="soft-pill-actions">
                                                <a href="{{ route('skills.blandas') }}?edit={{ $skill->id }}"
                                                   class="soft-pill-btn" title="Editar">
                                                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                                                        <path d="M9.5 1.5l2 2-7 7H2.5v-2l7-7z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                                                    </svg>
                                                </a>
                                                <button class="soft-pill-btn danger"
                                                    onclick="openDeleteModal('{{ $skill->id }}', '{{ addslashes($skill->name) }}')"
                                                    title="Eliminar">×</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="skills-empty" style="margin-top:28px;">
                                    <div class="skills-empty-icon">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <circle cx="10" cy="10" r="8" stroke="#c8cdd8" stroke-width="1.5"/>
                                            <path d="M10 7v4M10 13v.5" stroke="#c8cdd8" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                    Aún no has agregado habilidades blandas.
                                </div>
                            @endif

                        </div>{{-- /section-card --}}
                    </div>{{-- /main --}}
                </div>{{-- /body-row --}}
            </div>{{-- /shell --}}
        </div>
    </div>

    {{-- Modal eliminar --}}
    <div class="modal-backdrop" id="delete-modal">
        <div class="modal-box">
            <h3>Eliminar habilidad</h3>
            <p>¿Estás seguro de que deseas eliminar <strong id="modal-skill-name"></strong>?<br>Esta acción no se puede deshacer.</p>
            <form id="delete-form" method="POST">
                @csrf @method('DELETE')
                <div style="display:flex;gap:12px;justify-content:center;">
                    <button type="submit" class="btn-danger">Sí, eliminar</button>
                    <button type="button" class="btn" onclick="closeDeleteModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/habilidades-blandas.js') }}"></script>

</x-app-layout>
