<x-app-layout>
    
 
    <link rel="stylesheet" href="{{ asset('css/informacion-academica.css') }}">
 
    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
 
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif



         <!-- MOSTRAR ERRORES DE VALIDACIÓN DEL SERVIDOR -->
            @if($errors->any())
                <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px; padding: 12px; margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
 
            <div class="shell">
                <div class="header-bar" style="display: none;"></div>
                <div class="navbar">
                    <div class="nav-tab active">COMPLETAR</div>
                    <a href="{{ route('preview') }}" class="nav-tab muted">VER PERFIL</a>
                </div>
                <div class="body-row">
                    <div class="sidebar">
                        <a href="{{ route('profile.create') }}" class="sidebar-item">Personal</a>
                        <a href="{{ route('experiencia.laboral') }}" class="sidebar-item">Experiencia laboral</a>
                        <a href="{{ route('informacion.academica') }}" class="sidebar-item active">Información académica</a>
                        <a href="{{ route('skills.tecnicas') }}" class="sidebar-item">Habilidades técnicas</a>
                        <a href="{{ route('skills.blandas') }}" class="sidebar-item">Habilidades blandas</a>
                        <a href="{{ route('proyectos') }}" class="sidebar-item" >Proyectos</a>  
                        <a href="{{ route('idiomas.index') }}"        class="sidebar-item">Idiomas</a>
                        <a href="{{ route('redes.index') }}" class="sidebar-item">Redes profesionales y contacto  </a>
                    </div>
 
                    <div class="main">
                        <div class="page-title">Información académica</div>
 
                        <!-- Formulario -->
                        <div class="section-card">
                            <div class="section-subtitle">
                                Registra tus estudios con institución, título, período y descripción para enriquecer tu perfil.
                            </div>
 
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
                                            <div id="especialidadError" class="error-message hidden"></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;</label>
                                            <div style="visibility: hidden;">&nbsp;</div>
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
                                        <textarea class="form-textarea" name="descripcion" id="descripcion" 
                                            placeholder="Describe brevemente tus logros, materias destacadas o proyectos en esta formación..." maxlength="500">{{ old('descripcion') }}</textarea>
                                        <div class="char-counter" id="descripcionCounter">0 / 500</div>
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
                                
                                    {{-- Zona de arrastre (oculta al inicio si hay archivos) --}}
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
                    </div>{{-- /main --}}
                </div>{{-- /body-row --}}
            </div>{{-- /shell --}}
        </div>
    </div>
 
    <script src="{{ asset('js/informacion-academica.js') }}"></script>
    @viteReactRefresh
    @vite('resources/js/informacion-academica.jsx')
</x-app-layout>
