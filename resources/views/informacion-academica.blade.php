<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h2 class="font-semibold text-xl leading-tight" style="color: var(--burg-deep);">
                {{ __('Formulario de Perfil Profesional') }}
            </h2>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="logout-btn" style="background: linear-gradient(90deg, var(--burg-deep), var(--teal)); color: white; padding: 8px 20px; border-radius: 40px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; border: none; cursor: pointer;">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </x-slot>
 
    <link rel="stylesheet" href="{{ asset('css/informacion-academica.css') }}">
 
    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
 
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif
 
            <div class="shell">
                <div class="header-bar">BIENVENIDO</div>
                <div class="navbar">
                    <div class="nav-tab active">COMPLETAR</div>
                    <div class="nav-tab muted">VER PERFIL</div>
                </div>
                <div class="body-row">
                    <div class="sidebar">
                        <a href="{{ route('profile.create') }}" class="sidebar-item">Personal</a>
                        <a href="{{ route('experiencia.laboral') }}" class="sidebar-item">Experiencia laboral</a>
                        <a href="{{ route('informacion.academica') }}" class="sidebar-item active">Información académica</a>
                        <div class="sidebar-item">Habilidades técnicas</div>
                        <div class="sidebar-item">Habilidades blandas</div>
                        <div class="sidebar-item">Proyectos</div>
                        <div class="sidebar-item">Redes profesionales y contacto</div>
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
                                            <input class="form-input" type="text" name="institucion" id="institucion"
                                                placeholder="Ej. Universidad Mayor de San Simón" value="{{ old('institucion') }}">
                                            <div id="institucionError" class="error-message hidden">La institución es obligatoria</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Título obtenido <span class="required">*</span></label>
                                            <input class="form-input" type="text" name="titulo_obtenido" id="tituloObtenido"
                                                placeholder="Ej. Ingeniería de Sistemas" value="{{ old('titulo_obtenido') }}">
                                            <div id="tituloObtenidoError" class="error-message hidden">El título obtenido es obligatorio</div>
                                        </div>
                                    </div>
 
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Título</label>
                                            <input class="form-input" type="text" name="titulo" id="titulo"
                                                placeholder="Licenciatura, Maestría..." value="{{ old('titulo') }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Especialidad</label>
                                            <input class="form-input" type="text" name="especialidad" id="especialidad"
                                                placeholder="Campo de estudio" value="{{ old('especialidad') }}">
                                        </div>
                                    </div>
 
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Fecha de inicio <span class="required">*</span></label>
                                            <input class="form-input" type="month" name="fecha_inicio" id="fechaInicio"
                                                value="{{ old('fecha_inicio') }}">
                                            <div id="fechaInicioError" class="error-message hidden">La fecha de inicio es obligatoria</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Fecha de fin</label>
                                            <input class="form-input" type="month" name="fecha_fin" id="fechaFin"
                                                value="{{ old('fecha_fin') }}">
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
                                            placeholder="Describe brevemente tus logros, materias destacadas o proyectos en esta formación...">{{ old('descripcion') }}</textarea>
                                    </div>
                                </div>
 
                                <div class="btn-row">
                                    <button type="submit" class="btn primary">Guardar formación</button>
                                    <button type="button" class="btn" onclick="resetForm()">Cancelar</button>
                                </div>
                            </form>
                        </div>
 


                        <div id="historial-react" data-formaciones="{{ json_encode($formaciones) }}"></div>

                    </div>{{-- /main --}}                         
                    </div>{{-- /main --}}
                </div>{{-- /body-row --}}
            </div>{{-- /shell --}}
        </div>
    </div>
 
    <script>
        function toggleFechaFin(checkbox) {
            const fechaFin = document.getElementById('fechaFin');
            fechaFin.disabled = checkbox.checked;
            if (checkbox.checked) {
                fechaFin.value = '';
            }
        }
 
        function resetForm() {
            document.getElementById('academicForm').reset();
            document.getElementById('fechaFin').disabled = false;
        }
 
        document.getElementById('academicForm').addEventListener('submit', function(e) {
            let isValid = true;
 
            const institucion = document.getElementById('institucion').value.trim();
            const institucionError = document.getElementById('institucionError');
            if (!institucion) {
                institucionError.classList.remove('hidden');
                isValid = false;
            } else {
                institucionError.classList.add('hidden');
            }
 
            const tituloObtenido = document.getElementById('tituloObtenido').value.trim();
            const tituloObtenidoError = document.getElementById('tituloObtenidoError');
            if (!tituloObtenido) {
                tituloObtenidoError.classList.remove('hidden');
                isValid = false;
            } else {
                tituloObtenidoError.classList.add('hidden');
            }
 
            const fechaInicio = document.getElementById('fechaInicio').value;
            const fechaInicioError = document.getElementById('fechaInicioError');
            if (!fechaInicio) {
                fechaInicioError.classList.remove('hidden');
                isValid = false;
            } else {
                fechaInicioError.classList.add('hidden');
            }
 
            if (!isValid) e.preventDefault();
        });
    </script>
    @viteReactRefresh
    @vite('resources/js/informacion-academica.jsx')
</x-app-layout>