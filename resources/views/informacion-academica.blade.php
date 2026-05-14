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
                        <a href="{{ route('redes.index') }}" class="sidebar-item">Redes profesionales y contacto  </a>
                    </div>
 
                    <div class="main">
                        <div class="page-title">Información académica</div>
 
                        <!-- Formulario -->
                        <div class="section-card">
                            <div class="section-subtitle">
                                Registra tus estudios con institución, título, período y descripción para enriquecer tu perfil.
                            </div>
 
                            <form id="academicForm" action="{{ route('informacion.academica.store') }}" method="POST">
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
                                                placeholder="Ej. Ingeniería de Sistemas" value="{{ old('titulo_obtenido') }}"  maxlength ="30" >
                                            <div id="tituloObtenidoError" class="error-message hidden">El título obtenido es obligatorio</div>
                                            @error('titulo_obtenido')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror

                                        </div>
                                    </div>
 
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Título</label>
                                            <input class="form-input" type="text" name="titulo" id="titulo"
                                                placeholder="Licenciatura, Maestría..." value="{{ old('titulo') }}" maxlength ="30">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Especialidad</label>
                                            <input class="form-input" type="text" name="especialidad" id="especialidad"
                                                placeholder="Campo de estudio" value="{{ old('especialidad') }}"  maxlength="30" >
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
 
                                    <div class="form-checkbox-row">
                                        <input type="checkbox" name="estudio_actual" id="estudioActual" value="1"
                                            {{ old('estudio_actual') ? 'checked' : '' }}
                                            onchange="toggleFechaFin(this)">
                                        <label for="estudioActual">Estudio actual</label>
                                    </div>
 
                                    <div class="form-group">
                                        <label class="form-label">Descripción</label>
                                        <textarea class="form-textarea" name="descripcion" id="descripcion" 
                                            placeholder="Describe brevemente tus logros, materias destacadas o proyectos en esta formación..." maxlength="500" >{{ old('descripcion') }}</textarea>
                                            <div class="char-counter" id="descripcionCounter">0 / 500</div>
                                    </div>
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
