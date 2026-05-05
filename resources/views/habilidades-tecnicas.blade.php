{{-- resources/views/habilidades-tecnicas.blade.php --}}
<x-app-layout>
   

    <link rel="stylesheet" href="{{ asset('css/informacion-academica.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skills.css') }}">

    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="shell">
                <div class="header-bar" style="display: none;"></div>
                <div class="navbar">
                    <div class="nav-tab active">COMPLETAR</div>
                    <div class="nav-tab muted">VER PERFIL</div>
                </div>

                <div class="body-row">
                    {{-- Sidebar --}}
                    <div class="sidebar">
                        <a href="{{ route('profile.create') }}"  class="sidebar-item">Personal</a>
                        <a href="{{ route('experiencia.laboral') }}" class="sidebar-item">Experiencia laboral</a>
                        <a href="{{ route('informacion.academica') }}" class="sidebar-item">Información académica</a>
                        <a href="{{ route('skills.tecnicas') }}"  class="sidebar-item active">Habilidades técnicas</a>
                        <a href="{{ route('skills.blandas') }}"  class="sidebar-item">Habilidades blandas</a>
                        <a href="{{ route('proyectos') }}" class="sidebar-item">Proyectos</a> 
                        <a href="{{ route('redes.index') }}" class="sidebar-item">Redes profesionales y contacto  </a>
                    </div>

                    {{-- Contenido --}}
                    <div class="main">
                        <div class="page-title">Habilidades técnicas</div>

                        <div class="section-card">
                            <div class="section-subtitle">
                                Registra tus tecnologías, herramientas y lenguajes con su nivel de dominio.
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
                                        + Agregar habilidad técnica
                                    </button>
                                @endif

                                <div id="skill-form-wrap" style="{{ $showForm ? '' : 'display:none;' }}margin-top:20px;">
                                    <div class="skill-form-box {{ $errors->any() ? 'has-error' : '' }}" style="margin-top:20px;">
 
                                        @if($editSkill)
                                            {{-- ── FORMULARIO EDICIÓN ── --}}
                                            <form action="{{ route('skills.update', $editSkill->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="type" value="technical">
 
                                                <div class="form-row">
                                                    <div class="form-group">
                                                        <label class="form-label">
                                                            Nombre de la habilidad <span class="required">*</span>
                                                        </label>
                                                        <input type="text" name="name" class="form-input"
                                                            placeholder="Ej. Python, Figma, React..."
                                                            value="{{ old('name', $editSkill->name) }}"
                                                            maxlength="50" autocomplete="off">
                                                        @error('name')
                                                            <span class="error-message">{{ $message }}</span>
                                                        @enderror
                                                    </div>
 
                                                    <div class="form-group">
                                                        <label class="form-label">
                                                            Nivel de dominio <span class="required">*</span>
                                                        </label>
                                                        <select name="level" class="form-input">
                                                            <option value="">Seleccionar nivel</option>
                                                            <option value="1" {{ old('level', $editSkill->level) == 1 ? 'selected' : '' }}>Básico</option>
                                                            <option value="2" {{ old('level', $editSkill->level) == 2 ? 'selected' : '' }}>Intermedio</option>
                                                            <option value="3" {{ old('level', $editSkill->level) == 3 ? 'selected' : '' }}>Avanzado</option>
                                                        </select>
                                                        @error('level')
                                                            <span class="error-message">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
 
                                                {{-- Sección evidencia con proyectos (edición) --}}
                                                <div class="form-group" style="margin-top:12px;">
                                                    <label class="form-label">Evidencia con proyectos</label>
                                                    <div class="skill-projects-inline" id="edit-projects-{{ $editSkill->id }}">
                                                        @foreach($editSkill->projects as $project)
                                                            <span class="skill-project-chip"
                                                                  data-skill="{{ $editSkill->id }}"
                                                                  data-project="{{ $project->id }}">
                                                                {{ $project->name }}
                                                                <button type="button" class="chip-remove"
                                                                        onclick="confirmDetach({{ $editSkill->id }}, {{ $project->id }}, this)"
                                                                        title="Quitar proyecto">×</button>
                                                            </span>
                                                        @endforeach
                                                        <button type="button" class="chip-add"
                                                                onclick="openProjectSelectorForEdit({{ $editSkill->id }})">
                                                            + Agregar proyecto
                                                        </button>
                                                    </div>
                                                    <p style="font-size:11px;color:#aaa;margin:4px 0 0;">
                                                        Los cambios de proyectos se guardan al instante. Haz clic en × para desvincular.
                                                    </p>
                                                </div>
 
                                                <div class="btn-row">
                                                    <button type="submit" class="btn primary">Guardar cambios</button>
                                                    <button type="button" class="btn" onclick="cancelForm()">Cancelar</button>
                                                </div>
                                            </form>
 
                                        @else
                                            {{-- ── FORMULARIO CREAR ── --}}
                                            <form action="{{ route('skills.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="_action" value="store">
                                                <input type="hidden" name="type" value="technical">
 
                                                <div class="form-row">
                                                    <div class="form-group">
                                                        <label class="form-label">
                                                            Nombre de la habilidad <span class="required">*</span>
                                                        </label>
                                                        <input type="text" name="name" class="form-input"
                                                            placeholder="Ej. Python, Figma, React..."
                                                            value="{{ old('name') }}"
                                                            maxlength="50" autocomplete="off">
                                                        @error('name')
                                                            <span class="error-message">{{ $message }}</span>
                                                        @enderror
                                                    </div>
 
                                                    <div class="form-group">
                                                        <label class="form-label">
                                                            Nivel de dominio <span class="required">*</span>
                                                        </label>
                                                        <select name="level" class="form-input">
                                                            <option value="">Seleccionar nivel</option>
                                                            <option value="1" {{ old('level') == 1 ? 'selected' : '' }}>Básico</option>
                                                            <option value="2" {{ old('level') == 2 ? 'selected' : '' }}>Intermedio</option>
                                                            <option value="3" {{ old('level') == 3 ? 'selected' : '' }}>Avanzado</option>
                                                        </select>
                                                        @error('level')
                                                            <span class="error-message">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
 
                                                {{-- Sección evidencia con proyectos (crear) --}}
                                                
                                                    <div class="form-group" style="margin-top:12px;">
                                                        <label class="form-label">Evidencia con proyectos <span style="font-weight:400;color:#aaa;">(opcional)</span></label>
                                                        {{-- Chips pendientes + inputs ocultos se inyectan aquí desde JS --}}
                                                        <div class="skill-projects-inline" id="pending-projects-container">
                                                            <button type="button" class="chip-add"
                                                                    onclick="openProjectSelectorNew()">
                                                                + Agregar proyecto
                                                            </button>
                                                        </div>
                                                        @if($userProjects->count() > 0)
                                                    
                                                            <p style="font-size:11px;color:#aaa;margin:4px 0 0;">
                                                            Puedes vincular proyectos ahora o hacerlo después desde el historial.
                                                            </p>
                                                        @else
                                                            <p style="font-size:11px;color:#aaa;margin:4px 0 0;">
                                                               Aun no tienes proyectos registrados.Agrega un proyecto para poder vincularlo
                                                            </p>
                                                         @endif
                                                    </div>
                                               
 
                                                <div class="btn-row">
                                                    <button type="submit" class="btn primary">Guardar habilidad</button>
                                                    <button type="button" class="btn" onclick="cancelForm()">Cancelar</button>
                                                </div>
                                            </form>
                                        @endif
 
                                    </div>
                                </div>
                            @else
                            
                                <div class="alert-skill warning">
                                    <div class="alert-skill-icon">!</div>
                                    <span>Has alcanzado el límite máximo de 20 habilidades técnicas.</span>
                                </div>
                            @endif

                            {{-- Contador + historial --}}
                            <div style="margin-top:28px;">
                                @if($total > 0)
                                    <div class="skills-counter">
                                        <div class="historial-divider" style="flex:1;margin:0;">
                                            <span>Historial</span>
                                        </div>
                                        <span class="skills-counter-badge {{ $atLimit ? 'full' : 'ok' }}" style="margin-left:14px;">
                                            {{ $total }} / 20
                                        </span>
                                    </div>
                                @else
                                    <div class="historial-divider"><span>Historial</span></div>
                                @endif
                            </div>

                            {{-- Lista --}}
                            @forelse($skills as $index => $skill)
                                <div class="skill-card" id="skill-card-{{ $skill->id }}">
                                    <span class="skill-card-index">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>

                                    <div class="skill-card-info">
                                        <div class="skill-card-name">{{ $skill->name }}</div>
                                        <div class="skill-progress">
                                            <div class="skill-progress-fill {{ $skill->levelBadgeClass() }}"></div>
                                        </div>

                                        {{-- Proyectos vinculados (HU-24) --}}
                                       <div class="skill-projects" id="skill-projects-{{ $skill->id }}">
                                           @foreach($skill->projects as $project)
                                               <a href="{{ route('proyectos') }}?preview={{ $project->id }}"
                                                  class="skill-project-chip skill-project-link"
                                                  data-skill="{{ $skill->id }}"
                                                  data-project="{{ $project->id }}"
                                                  title="Ver proyecto: {{ $project->name }}">
                                                   {{ $project->name }} →
                                               </a>
                                           @endforeach
                                       </div>
                                    </div>{{-- /skill-card-info --}}
                      
                                    <span class="badge-nivel badge-{{ $skill->levelBadgeClass() }}">
                                        {{ $skill->levelLabel() }}
                                    </span>

                                    <div class="skill-actions">
                                        <a href="{{ route('skills.tecnicas') }}?edit={{ $skill->id }}" class="btn-sm">
                                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                                                <path d="M9.5 1.5l2 2-7 7H2.5v-2l7-7z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                                            </svg>
                                            Editar
                                        </a>
                                        <button class="btn-sm danger"
                                                onclick="openDeleteModal('{{ $skill->id }}', '{{ addslashes($skill->name) }}')">
                                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                                                <path d="M2 3.5h9M5 3.5V2.5h3v1M4.5 3.5l.5 7M8.5 3.5l-.5 7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </div>
                                </div>{{-- /skill-card --}}

                            @empty
                                <div class="skills-empty">
                                    <div class="skills-empty-icon">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <circle cx="10" cy="10" r="8" stroke="#c8cdd8" stroke-width="1.5"/>
                                            <path d="M10 7v4M10 13v.5" stroke="#c8cdd8" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                    Aún no has agregado habilidades técnicas.
                                </div>
                            @endforelse

                        </div>{{-- /section-card --}}
                    </div>{{-- /main --}}
                </div>{{-- /body-row --}}
            </div>{{-- /shell --}}
        </div>
    </div>

    {{-- Modal selector de proyectos (HU-24) --}}
    <div class="modal-backdrop" id="project-selector-modal">
        <div class="modal-box" style="max-width:480px;text-align:left;">
            <h3 style="margin-bottom:6px;">Vincular proyecto</h3>
            <p style="margin-bottom:16px;font-size:13px;">Selecciona un proyecto de tu portafolio como evidencia de esta habilidad.</p>

            <input type="text" id="project-search" class="form-input"
                   placeholder="Buscar proyecto..."
                   style="margin-bottom:12px;"
                   oninput="filterProjects()">
 
            <div id="project-list" style="max-height:260px;overflow-y:auto;display:flex;flex-direction:column;gap:6px;">
                {{-- Se rellena desde JS --}}
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:20px;">
                <button type="button" class="btn" onclick="closeProjectSelector()">Cancelar</button>
            </div>
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
     {{-- Datos para JS --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.userProjects = {!! json_encode($userProjects->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'technologies' => $p->technologies->pluck('name')->toArray()])->values()) !!};
    </script>
    <script src="{{ asset('js/habilidades-tecnicas.js') }}"></script>
</x-app-layout>

