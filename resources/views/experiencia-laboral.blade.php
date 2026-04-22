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

    <link rel="stylesheet" href="{{ asset('css/experiencia-laboral.css') }}">

    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="success-message">{{ session('success') }}</div>
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
                        <a href="{{ route('experiencia.laboral') }}" class="sidebar-item active">Experiencia laboral</a>
                        <a href="{{ route('informacion.academica') }}" class="sidebar-item">Información académica</a>
                        <a href="{{ route('skills.tecnicas') }}" class="sidebar-item">Habilidades técnicas</a>
                        <a href="{{ route('skills.blandas') }}" class="sidebar-item">Habilidades blandas</a>
                        <div class="sidebar-item">Proyectos</div>
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

                                    {{-- Empresa y Cargo --}}
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Empresa <span class="required">*</span></label>
                                            <input class="form-input" type="text" name="empresa" id="empresa"
                                                placeholder="Ej. Google Bolivia" value="{{ old('empresa') }}">
                                            <div id="empresaError" class="error-message hidden">La empresa es obligatoria</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Cargo <span class="required">*</span></label>
                                            <input class="form-input" type="text" name="cargo" id="cargo"
                                                placeholder="Ej. Desarrollador Backend" value="{{ old('cargo') }}">
                                            <div id="cargoError" class="error-message hidden">El cargo es obligatorio</div>
                                        </div>
                                    </div>

                                    {{-- Ubicación --}}
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Ubicación</label>
                                            <input class="form-input" type="text" name="location"
                                                placeholder="Ej. Cochabamba, Bolivia" value="{{ old('location') }}">
                                        </div>
                                    </div>

                                    {{-- Fecha de inicio: día, mes, año --}}
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

                                        {{-- Fecha de fin: día, mes, año --}}
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

                    </div>{{-- /main --}}
                </div>{{-- /body-row --}}
            </div>{{-- /shell --}}
        </div>
    </div>

    <script>
        function toggleFechaFin(checkbox) {
            const group = document.getElementById('fechaFinGroup');
            const ids   = ['fechaFinDia','fechaFinMes','fechaFinAnio'];
            ids.forEach(id => {
                document.getElementById(id).disabled = checkbox.checked;
                if (checkbox.checked) document.getElementById(id).value = '';
            });
            group.style.opacity = checkbox.checked ? '0.4' : '1';
        }

        function resetForm() {
            document.getElementById('experienciaForm').reset();
            document.getElementById('fechaFinGroup').style.opacity = '1';
            ['fechaFinDia','fechaFinMes','fechaFinAnio'].forEach(id => {
                document.getElementById(id).disabled = false;
            });
        }

        document.getElementById('experienciaForm').addEventListener('submit', function(e) {
            let isValid = true;

            const empresa = document.getElementById('empresa').value.trim();
            const empresaError = document.getElementById('empresaError');
            if (!empresa) { empresaError.classList.remove('hidden'); isValid = false; }
            else { empresaError.classList.add('hidden'); }

            const cargo = document.getElementById('cargo').value.trim();
            const cargoError = document.getElementById('cargoError');
            if (!cargo) { cargoError.classList.remove('hidden'); isValid = false; }
            else { cargoError.classList.add('hidden'); }

            const inicioDia  = document.getElementById('fechaInicioDia').value;
            const inicioMes  = document.getElementById('fechaInicioMes').value;
            const inicioAnio = document.getElementById('fechaInicioAnio').value;
            const fechaInicioError = document.getElementById('fechaInicioError');
            if (!inicioDia || !inicioMes || !inicioAnio) {
                fechaInicioError.classList.remove('hidden'); isValid = false;
            } else { fechaInicioError.classList.add('hidden'); }

            const finDia   = document.getElementById('fechaFinDia').value;
            const finMes   = document.getElementById('fechaFinMes').value;
            const finAnio  = document.getElementById('fechaFinAnio').value;
            const actual   = document.getElementById('trabajoActual').checked;
            const fechaFinError = document.getElementById('fechaFinError');

            if (!actual && finDia && finMes && finAnio && inicioDia && inicioMes && inicioAnio) {
                const inicio = new Date(inicioAnio, parseInt(inicioMes)-1, parseInt(inicioDia));
                const fin    = new Date(finAnio,    parseInt(finMes)-1,    parseInt(finDia));
                if (fin < inicio) {
                    fechaFinError.classList.remove('hidden'); isValid = false;
                } else { fechaFinError.classList.add('hidden'); }
            } else { fechaFinError.classList.add('hidden'); }

            if (!isValid) e.preventDefault();
        });

        window.addEventListener('DOMContentLoaded', function() {
            const cb = document.getElementById('trabajoActual');
            if (cb.checked) toggleFechaFin(cb);
        });
    </script>

    @viteReactRefresh
    @vite('resources/js/experiencia-laboral.jsx')

</x-app-layout>