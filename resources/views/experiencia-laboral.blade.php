<x-app-layout>
    
    <link rel="stylesheet" href="{{ asset('css/experiencia-laboral.css') }}">

    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="success-message">{{ session('success') }}</div>
            @endif

            <div class="shell">
            
                <div class="navbar">
                    <div class="nav-tab active">COMPLETAR</div>
                    <div class="nav-tab muted">VER PERFIL</div>
                </div>
                <div class="body-row">
                    <div class="sidebar">
                        <a href="{{ route('profile.create') }}" class="sidebar-item">Personal</a>
                        <a href="{{ route('experiencia.laboral') }}" class="sidebar-item active">Experiencia laboral</a>
                        <a href="{{ route('informacion.academica') }}" class="sidebar-item">Información académica</a>
                        <a href="{{ route('skills.tecnicas') }}" class="sidebar-item">Habilidades técnicas</a>
                        <a href="{{ route('skills.blandas') }}" class="sidebar-item">Habilidades blandas</a>
                        <a href="{{ route('proyectos') }}" class="sidebar-item">Proyectos</a>  
                        <a href="{{ route('redes.index') }}" class="sidebar-item">Redes profesionales y contacto</a>
                    </div>

                    <div class="main">
                        <div class="page-title">Experiencia laboral</div>

                        <div class="section-card">
                            <div class="section-subtitle">
                                Registra tus experiencias laborales con empresa, cargo, período y descripción para enriquecer tu portafolio.
                            </div>

                            <form id="experienciaForm" action="{{ route('experiencia.laboral.store') }}" method="POST">
                                @csrf
                                <div class="form-grid">

                                    {{-- Empresa --}}
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Empresa <span class="required">*</span></label>
                                            <input class="form-input" type="text" name="empresa" id="empresa"
                                                placeholder="Ej. Google Bolivia" value="{{ old('empresa') }}">
                                            <div id="empresaError" class="error-message hidden">La empresa es obligatoria</div>
                                        </div>
                                    </div>

                                    {{-- ── CARGOS con dropdown personalizado (HU-12) ── --}}
                                    <div class="form-group">
                                        <label class="form-label">
                                            Cargos <span class="required">*</span>
                                            <span class="cargos-hint">(máx. 5)</span>
                                        </label>

                                        <div id="cargos-list">
                                            <div class="cargo-item" data-index="0">
                                                <div class="custom-dropdown">
                                                    <button type="button" class="custom-dropdown-toggle" onclick="toggleDropdown(this)">
                                                        <span class="dropdown-label muted">— Seleccionar cargo —</span>
                                                        <span class="dropdown-arrow">▲</span>
                                                    </button>
                                                    <ul class="custom-dropdown-menu">
                                                        <li data-value="" class="placeholder-opt selected">— Seleccionar cargo —</li>
                                                        <li data-value="Frontend Developer">Frontend Developer</li>
                                                        <li data-value="Backend Developer">Backend Developer</li>
                                                        <li data-value="Full Stack Developer">Full Stack Developer</li>
                                                        <li data-value="UI/UX Designer">UI/UX Designer</li>
                                                        <li data-value="DevOps Engineer">DevOps Engineer</li>
                                                        <li data-value="Mobile Developer">Mobile Developer</li>
                                                        <li data-value="Project Manager">Project Manager</li>
                                                        <li data-value="QA Tester">QA Tester</li>
                                                        <li data-value="Database Administrator">Database Administrator</li>
                                                        <li data-value="Technical Leader">Technical Leader</li>
                                                        <li data-value="Data Analyst">Data Analyst</li>
                                                        <li data-value="Scrum Master">Scrum Master</li>
                                                        <li data-value="Product Owner">Product Owner</li>
                                                    </ul>
                                                    <input type="hidden" name="cargos[]" value="{{ old('cargos.0', '') }}">
                                                </div>
                                                <button type="button" class="btn-remove-cargo" onclick="removeCargo(this)" disabled title="Eliminar cargo">×</button>
                                            </div>
                                        </div>

                                        <div id="cargosError" class="error-message hidden"></div>

                                        <button type="button" id="btnAgregarCargo" class="btn-add-cargo" onclick="agregarCargo()">
                                            + Agregar otro cargo
                                        </button>
                                    </div>
                                    {{-- ── FIN CARGOS ── --}}

                                    {{-- Ubicación --}}
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Ubicación</label>
                                            <input class="form-input" type="text" name="location"
                                                placeholder="Ej. Cochabamba, Bolivia" value="{{ old('location') }}">
                                        </div>
                                    </div>

                                    {{-- Fecha de inicio --}}
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Fecha de inicio <span class="required">*</span></label>
                                            <div style="display:flex; gap:8px;">
                                                <select name="fecha_inicio_dia" id="fechaInicioDia" class="form-input" style="width:80px;">
                                                    <option value="">Día</option>
                                                    @for($d = 1; $d <= 31; $d++)
                                                        <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}"
                                                            {{ old('fecha_inicio_dia') == str_pad($d, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                            {{ $d }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                <select name="fecha_inicio_mes" id="fechaInicioMes" class="form-input" style="flex:1;">
                                                    <option value="">Mes</option>
                                                    @foreach(['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'] as $num => $nombre)
                                                        <option value="{{ $num }}" {{ old('fecha_inicio_mes') == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="number" name="fecha_inicio_anio" id="fechaInicioAnio"
                                                    class="form-input" style="width:90px;"
                                                    placeholder="Año" min="1950" max="{{ date('Y') }}"
                                                    value="{{ old('fecha_inicio_anio') }}">
                                            </div>
                                            <div id="fechaInicioError" class="error-message hidden">La fecha de inicio es obligatoria</div>
                                        </div>

                                        <div class="form-group" id="fechaFinGroup">
                                            <label class="form-label">Fecha de fin</label>
                                            <div style="display:flex; gap:8px;">
                                                <select name="fecha_fin_dia" id="fechaFinDia" class="form-input" style="width:80px;">
                                                    <option value="">Día</option>
                                                    @for($d = 1; $d <= 31; $d++)
                                                        <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}"
                                                            {{ old('fecha_fin_dia') == str_pad($d, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                            {{ $d }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                <select name="fecha_fin_mes" id="fechaFinMes" class="form-input" style="flex:1;">
                                                    <option value="">Mes</option>
                                                    @foreach(['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'] as $num => $nombre)
                                                        <option value="{{ $num }}" {{ old('fecha_fin_mes') == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="number" name="fecha_fin_anio" id="fechaFinAnio"
                                                    class="form-input" style="width:90px;"
                                                    placeholder="Año" min="1950" max="{{ date('Y') }}"
                                                    value="{{ old('fecha_fin_anio') }}">
                                            </div>
                                            <div id="fechaFinError" class="error-message hidden">La fecha de fin no puede ser anterior a la de inicio</div>
                                        </div>
                                    </div>

                                    {{-- Trabajo actual --}}
                                    <div class="form-checkbox-row">
                                        <input type="checkbox" name="trabajo_actual" id="trabajoActual" value="1"
                                            {{ old('trabajo_actual') ? 'checked' : '' }}
                                            onchange="toggleFechaFin(this)">
                                        <label for="trabajoActual">Trabajo actual</label>
                                    </div>

                                    {{-- Descripción --}}
                                    <div class="form-group">
                                        <label class="form-label">Descripción</label>
                                        <textarea class="form-textarea" name="descripcion"
                                            placeholder="Describe brevemente tus responsabilidades y logros en este cargo...">{{ old('descripcion') }}</textarea>
                                    </div>

                                </div>

                                <div class="btn-row">
                                    <button type="submit" class="btn primary">Guardar experiencia</button>
                                    <button type="button" class="btn" onclick="resetForm()">Cancelar</button>
                                </div>
                            </form>
                        </div>

                        <div id="historial-laboral-react" data-experiencias="{{ json_encode($experiencias) }}"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/experiencia-laboral.js') }}"></script>

    @viteReactRefresh
    @vite('resources/js/experiencia-laboral.jsx')

</x-app-layout>