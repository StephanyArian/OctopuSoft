@extends('layouts.app-completar')

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/experiencia-laboral.css') }}">
@endpush

@section('content')


                        

                        <div class="page-title">Experiencia laboral</div>

                        <div class="section-card">
                            <div class="section-subtitle">
                                Registra tus experiencias laborales con empresa, cargo, período y descripción para enriquecer tu portafolio.
                            </div>

                            <form id="experienciaForm" action="{{ route('experiencia.laboral.store') }}" method="POST">
                                @csrf
                                <div class="form-grid">
                                    {{-- Mensaje de éxito --}}
                                    @if(session('success'))
                                        <div class="success-message">{{ session('success') }}</div>
                                    @endif
                                    {{-- Empresa --}}
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Empresa <span class="required">*</span></label>
                                            <input class="form-input" type="text" name="empresa" id="empresa"
                                                placeholder="Ej. Google Bolivia" value="{{ old('empresa') }}"
                                                maxlength="100" oninput="updateCounter('empresa','empresaCount')">
                                            <div class="char-counter"><span id="empresaCount">0</span>/100</div>
                                            <div id="empresaError" class="error-message hidden">La empresa es obligatoria</div>
                                        </div>
                                    </div>

                                    {{-- Cargos --}}
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
                                                        <li data-value="Business Analyst">Business Analyst</li>
                                                        <li data-value="Security Engineer">Security Engineer</li>
                                                        <li data-value="Data Engineer">Data Engineer</li>
                                                        <li data-value="Cloud Engineer">Cloud Engineer</li>
                                                        <li data-value="AI Engineer">AI Engineer</li>
                                                        <li data-value="Systems Analyst">Systems Analyst</li>
                                                    </ul>
                                                    <input type="hidden" name="cargos[]" value="{{ old('cargos.0', '') }}">
                                                </div>
                                                <button type="button" class="btn-remove-cargo" onclick="removeCargo(this)" disabled title="Eliminar cargo">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div id="cargosError" class="error-message hidden"></div>

                                        <button type="button" id="btnAgregarCargo" class="btn-add-cargo" onclick="agregarCargo()">
                                            + Agregar otro cargo
                                        </button>
                                    </div>

                                    {{-- Ubicación --}}
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Ubicación</label>
                                            <input class="form-input" type="text" name="location"
                                                placeholder="Ej. Cochabamba, Bolivia" value="{{ old('location') }}"
                                                maxlength="100" oninput="updateCounter('location','locationCount')">
                                            <div class="char-counter"><span id="locationCount">0</span>/100</div>
                                        </div>
                                    </div>

                                    {{-- Fechas --}}
                                    <div class="form-row">
                                        {{-- Fecha de inicio --}}
                                        <div class="form-group">
                                            <label class="form-label">Fecha de inicio <span class="required">*</span></label>
                                            <div class="date-picker-wrap">
                                                <input type="date" id="fechaInicioPicker" class="form-input date-picker"
                                                    min="1950-01-01" max="{{ date('Y-m-d') }}"
                                                    value="{{ old('fecha_inicio_anio') && old('fecha_inicio_mes') && old('fecha_inicio_dia') ? old('fecha_inicio_anio').'-'.old('fecha_inicio_mes').'-'.old('fecha_inicio_dia') : '' }}"
                                                    onchange="syncFechaInicio(this.value)">
                                            </div>
                                            <input type="hidden" name="fecha_inicio"      id="fechaInicioFull" value="">
                                            <input type="hidden" name="fecha_inicio_dia"  id="fechaInicioDia"  value="{{ old('fecha_inicio_dia') }}">
                                            <input type="hidden" name="fecha_inicio_mes"  id="fechaInicioMes"  value="{{ old('fecha_inicio_mes') }}">
                                            <input type="hidden" name="fecha_inicio_anio" id="fechaInicioAnio" value="{{ old('fecha_inicio_anio') }}">
                                            <div id="fechaInicioError" class="error-message hidden">La fecha de inicio es obligatoria</div>
                                        </div>

                                        {{-- Fecha de fin --}}
                                        <div class="form-group" id="fechaFinGroup">
                                            <label class="form-label">Fecha de fin <span id="fechaFinRequired" class="required">*</span></label>
                                            <div class="date-picker-wrap">
                                                <input type="date" id="fechaFinPicker" class="form-input date-picker"
                                                    min="1950-01-01" max="{{ date('Y-m-d') }}"
                                                    value="{{ old('fecha_fin_anio') && old('fecha_fin_mes') && old('fecha_fin_dia') ? old('fecha_fin_anio').'-'.old('fecha_fin_mes').'-'.old('fecha_fin_dia') : '' }}"
                                                    onchange="syncFechaFin(this.value)">
                                            </div>
                                            <input type="hidden" name="fecha_fin"      id="fechaFinFull" value="">
                                            <input type="hidden" name="fecha_fin_dia"  id="fechaFinDia"  value="{{ old('fecha_fin_dia') }}">
                                            <input type="hidden" name="fecha_fin_mes"  id="fechaFinMes"  value="{{ old('fecha_fin_mes') }}">
                                            <input type="hidden" name="fecha_fin_anio" id="fechaFinAnio" value="{{ old('fecha_fin_anio') }}">
                                            <div id="fechaFinError" class="error-message hidden"></div>
                                        </div>
                                    </div>

                                    {{-- Trabajo actual --}}
                                    <div class="form-checkbox-row">
                                        <input type="checkbox" name="trabajo_actual" id="trabajoActual" value="1"
                                            {{ old('trabajo_actual') ? 'checked' : '' }}
                                            onchange="toggleFechaFin(this)">
                                        <label for="trabajoActual">Trabajo actual</label>
                                    </div>

                                    {{-- Descripción con Quill --}}
                                    <div class="form-group">
                                        <label class="form-label">Descripción</label>

                                        {{-- Hidden input que recibirá el HTML de Quill antes de enviar --}}
                                        <input type="hidden" name="descripcion" id="descripcionHidden"
                                               value="{{ old('descripcion') }}">

                                        {{-- Contador de caracteres --}}
                                        <div id="descripcionCount" class="char-counter" style="text-align:right; margin-bottom:4px;">
                                            0 / 500 caracteres
                                        </div>

                                        {{-- Contenedor del editor Quill --}}
                                        <div id="quillEditorAdd" class="quill-editor-wrap"></div>

                                        <div id="descripcionError" class="error-message hidden"></div>
                                    </div>

                                </div>

                                <div class="btn-row">
                                    <button type="submit" class="btn primary">Guardar experiencia</button>
                                    <button type="button" class="btn" onclick="resetForm()">Cancelar</button>
                                </div>
                            </form>
                        </div>

                        <div id="historial-laboral-react" data-experiencias="{{ json_encode($experiencias) }}"></div>
     @endsection        
     @push('scripts')
    {{-- Quill JS --}}
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="{{ asset('js/experiencia-laboral.js') }}"></script>

    @viteReactRefresh
    @vite('resources/js/experiencia-laboral.jsx')

    {{-- Inicializar Quill en el formulario de añadir --}}
    <script>
    (function () {
        /* ── Quill toolbar personalizado ── */
        const TOOLBAR = [
            [{ font: [] }],
            ['bold', 'italic', 'underline'],
            [{ color: [] }],
            ['link'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['clean']
        ];

        const MAX_CHARS = 500;

        /* ── Instancia global para el formulario de añadir ── */
        window.quillAdd = new Quill('#quillEditorAdd', {
            theme: 'snow',
            placeholder: 'Describe brevemente tus responsabilidades y logros en este cargo...',
            modules: { toolbar: TOOLBAR }
        });

        const counterEl = document.getElementById('descripcionCount');
        const hiddenEl  = document.getElementById('descripcionHidden');
        const errorEl   = document.getElementById('descripcionError');

        /* Actualizar contador y hidden en cada cambio */
        window.quillAdd.on('text-change', function () {
            const text   = window.quillAdd.getText().trim();
            const len    = text.length;
            const html   = window.quillAdd.root.innerHTML;

            /* Límite de texto plano */
            if (len > MAX_CHARS) {
                window.quillAdd.deleteText(MAX_CHARS, len - MAX_CHARS);
                return;
            }

            /* El "vacío" de Quill es '<p><br></p>' — lo normalizamos a '' */
            hiddenEl.value = (html === '<p><br></p>') ? '' : html;
            counterEl.textContent = len + ' / ' + MAX_CHARS + ' caracteres';
        });

        /* Pre-cargar valor antiguo (old('descripcion')) si existe */
        const oldVal = hiddenEl.value;
        if (oldVal) {
            window.quillAdd.root.innerHTML = oldVal;
            const text = window.quillAdd.getText().trim();
            counterEl.textContent = text.length + ' / ' + MAX_CHARS + ' caracteres';
        }

        /* ── Sincronizar el hidden ANTES de que el form envíe ── */
        document.getElementById('experienciaForm').addEventListener('submit', function (e) {
            const html = window.quillAdd.root.innerHTML;
            hiddenEl.value = (html === '<p><br></p>') ? '' : html;
        }, true); /* capture=true para que ocurra ANTES del listener de validación */

        /* ── Extender resetForm para limpiar el editor ── */
        const originalReset = window.resetForm;
        window.resetForm = function () {
            originalReset();
            window.quillAdd.setContents([]);
            hiddenEl.value = '';
            counterEl.textContent = '0 / ' + MAX_CHARS + ' caracteres';
        };
    })();
    </script>
@endpush