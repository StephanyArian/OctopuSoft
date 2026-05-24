<x-app-layout>
    
    <link rel="stylesheet" href="{{ asset('css/informacion-academica.css') }}">
 
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
 
            <div class="shell">
                <div class="header-bar" style="display: none;"></div>
                <div class="navbar">
                    <div class="nav-tab active">COMPLETAR</div>
                    <a href="{{ route('preview') }}" class="nav-tab muted">VER PERFIL</a>
                </div>
                <div class="body-row">
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

    <a href="{{ route('informacion.academica') }}" class="sidebar-item active">
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

    <a href="{{ route('skills.blandas') }}" class="sidebar-item">
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
 
                    <div class="main">
                        <div class="page-title">Información académica</div>
 
                        <!-- Formulario -->
                        <div class="section-card">
                            <div class="section-subtitle">
                                Registra tus estudios con institución, título, período y descripción para enriquecer tu perfil.
                            </div>

                            @if(session('success'))
                                <div class="alert-skill success" style="max-width:500px;">
                                    <div class="alert-skill-icon">✓</div>
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif
 
                            <form id="academicForm" action="{{ route('informacion.academica.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-grid">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Institución <span class="required">*</span></label>
                                            <textarea class="form-input" name="institucion" id="institucion"
                                                placeholder="Ej. Universidad Mayor de San Simón" maxlength="60" rows="2"
                                                style="resize: none;">{{ old('institucion') }}</textarea>
                                            <div id="institucionError" class="error-message hidden">La institución es obligatoria</div>
                                            @error('institucion')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror     
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Título obtenido <span class="required">*</span></label>
                                            <input class="form-input" type="text" name="titulo_obtenido" id="tituloObtenido"
                                                placeholder="Ej. Ingeniería de Sistemas" value="{{ old('titulo_obtenido') }}" maxlength="30">
                                            <div id="tituloObtenidoError" class="error-message hidden">El título obtenido es obligatorio</div>
                                            @error('titulo_obtenido')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Especialidad (opcional)</label>
                                            <input class="form-input" type="text" name="especialidad" id="especialidad"
                                                placeholder="Ej. Inteligencia Artificial, Redes, Desarrollo Web, QA, DevOps"
                                                value="{{ old('especialidad') }}" maxlength="50">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Tipo de formación <span class="required">*</span></label>
                                            <div class="custom-dropdown" id="tipoFormacionDropdown">
                                                <button type="button" class="custom-dropdown-toggle">
                                                    <span class="dropdown-label muted" id="tipoFormacionLabel">— Seleccionar tipo —</span>
                                                    <span class="dropdown-arrow">▲</span>
                                                </button>
                                                <ul class="custom-dropdown-menu">
                                                    <li data-value="" class="placeholder-opt selected">— Seleccionar tipo —</li>
                                                    <li class="dropdown-group-title">Educación formal</li>
                                                    <li data-value="Colegio / Bachillerato">Colegio / Bachillerato</li>
                                                    <li data-value="Técnico Superior">Técnico Superior</li>
                                                    <li data-value="Licenciatura / Ingeniería">Licenciatura / Ingeniería</li>
                                                    <li data-value="Maestría">Maestría</li>
                                                    <li data-value="Doctorado / PhD">Doctorado / PhD</li>
                                                    <li class="dropdown-group-title">Formación complementaria</li>
                                                    <li data-value="Bootcamp">Bootcamp</li>
                                                    <li data-value="Curso online">Curso online</li>
                                                    <li data-value="Certificación profesional">Certificación profesional</li>
                                                    <li data-value="Diplomado">Diplomado</li>
                                                    <li data-value="Intercambio académico">Intercambio académico</li>
                                                    <li data-value="Otro">Otro</li>
                                                </ul>
                                                <input type="hidden" name="tipo_formacion" id="tipoFormacionHidden" value="{{ old('tipo_formacion') }}">
                                            </div>
                                            
                                            <!-- Campo "Otro" - se mostrará cuando seleccionen Otro -->
                                            <div id="otroTipoFormacionContainer" style="display: none; margin-top: 12px;">
                                                <label class="form-label">Especificar otro tipo de formación <span class="required">*</span></label>
                                                <input type="text" 
                                                       name="otro_tipo_formacion" 
                                                       id="otroTipoFormacion" 
                                                       class="form-input" 
                                                       placeholder="Ej. Microcredencial, Taller especializado, Curso presencial, etc."
                                                       maxlength="50"
                                                       value="{{ old('otro_tipo_formacion') }}">
                                                <div id="otroTipoFormacionError" class="error-message hidden"></div>
                                            </div>
                                            
                                            <div id="tipoFormacionError" class="error-message hidden">El tipo de formación es obligatorio</div>
                                            @error('tipo_formacion')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                            @error('otro_tipo_formacion')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Fecha de inicio <span class="required">*</span></label>
                                            <input class="form-input" type="date" name="fecha_inicio" id="fechaInicio"
                                                value="{{ old('fecha_inicio') }}"
                                                min="1950-01-01" max="{{ date('Y-m-d') }}">
                                            <div id="fechaInicioError" class="error-message hidden">La fecha de inicio es obligatoria</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Fecha de fin</label>
                                            <input class="form-input" type="date" name="fecha_fin" id="fechaFin"
                                                value="{{ old('fecha_fin') }}"
                                                min="1950-01-01" max="{{ date('Y-m-d') }}">
                                            <div id="fechaFinError" class="error-message hidden"></div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <div class="form-checkbox-row" style="margin-top: 24px;">
                                                <input type="checkbox" name="estudio_actual" id="estudioActual" value="1"
                                                    {{ old('estudio_actual') ? 'checked' : '' }}
                                                    onchange="toggleFechaFin(this)">
                                                <label for="estudioActual">Estudio actual</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {{-- Espacio vacío --}}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Descripción</label>
                                        
                                        {{-- Editor Quill con más opciones --}}
                                        <div id="editorDescripcion" style="height: 200px; margin-bottom: 45px;"></div>
                                        <textarea name="descripcion" id="descripcionHidden" style="display: none;">{{ old('descripcion') }}</textarea>
                                        
                                        <div class="char-counter" id="descripcionCounter">0 / 500</div>
                                        <div id="descripcionError" class="error-message hidden">La descripción no puede exceder los 500 caracteres</div>
                                    </div> 
                                     
                                </div>
                                
                                <div class="form-group" style="margin-top: 8px;">
                                    <label class="form-label">
                                        Evidencias
                                        <span style="font-size:10px; font-weight:400; color:var(--gray-500); text-transform:none; margin-left:6px;">
                                            Opcional — JPG, PNG o PDF, máx. 2MB por archivo
                                        </span>
                                    </label>
                                
                                    {{-- Grid de previsualizaciones --}}
                                    <div id="evidenciasGrid" style="display:flex; flex-wrap:wrap; gap:12px; margin-bottom:12px;"></div>
                                
                                    {{-- Zona de arrastre --}}
                                    <div id="uploadZone"
                                        style="border:2px dashed var(--gray-300); border-radius:12px; padding:20px;
                                                text-align:center; cursor:pointer; background:var(--off); transition:all 0.2s;"
                                        ondragover="event.preventDefault(); this.style.borderColor='var(--teal)'; this.style.background='rgba(10,191,158,0.05)'"
                                        ondragleave="this.style.borderColor='var(--gray-300)'; this.style.background='var(--off)'"
                                        ondrop="handleDrop(event)"
                                        onclick="document.getElementById('evidenciasInput').click()">
                                        <div style="font-size:28px; margin-bottom:6px;">📎</div>
                                        <div style="font-size:13px; font-weight:600; color:var(--gray-700); margin-bottom:4px;">
                                            Arrastra tu archivo aquí
                                        </div>
                                        <div style="font-size:12px; color:var(--gray-500); margin-bottom:10px;">
                                            o haz clic para seleccionar
                                        </div>
                                        <span style="background:var(--burg-deep); color:#fff; font-size:11px;
                                                    padding:6px 16px; border-radius:20px; cursor:pointer;">
                                            Seleccionar archivo
                                        </span>
                                    </div>
                                
                                    {{-- Input oculto --}}
                                    <input type="file" id="evidenciasInput" name="evidencias[]"
                                        accept=".jpg,.jpeg,.png,.pdf" multiple
                                        style="display:none" onchange="handleFiles(this.files)">
                                
                                    {{-- Mensaje de error --}}
                                    <div id="evidenciasError" class="error-message hidden"></div>
                                
                                    @error('evidencias.*')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
 
                                <div class="btn-row">
                                    <button type="submit" class="btn primary">Guardar formación</button>
                                    <button type="button" class="btn" onclick="resetForm()">Cancelar</button>
                                </div>
                            </form>
                        </div>

                        <div id="historial-react" data-formaciones='@json($formaciones)'></div>

                    </div>{{-- /main --}}
                </div>{{-- /body-row --}}
            </div>{{-- /shell --}}
        </div>
    </div>
 
    <script src="{{ asset('js/informacion-academica.js') }}"></script>
    @viteReactRefresh
    @vite('resources/js/informacion-academica.jsx')
</x-app-layout>