<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/idiomas.css') }}">

    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        

            

            <div class="shell">
                <div class="navbar">
                    <div class="nav-tab active">COMPLETAR</div>
                    <a href="{{ route('preview') }}" class="nav-tab muted">VER PERFIL</a>
                </div>

                <div class="body-row">
                    <!-- SIDEBAR -->
                    <div class="sidebar">
                        <a href="{{ route('profile.create') }}"       class="sidebar-item">Personal</a>
                        <a href="{{ route('experiencia.laboral') }}"  class="sidebar-item">Experiencia laboral</a>
                        <a href="{{ route('informacion.academica') }}" class="sidebar-item">Información académica</a>
                        <a href="{{ route('skills.tecnicas') }}"      class="sidebar-item">Habilidades técnicas</a>
                        <a href="{{ route('skills.blandas') }}"       class="sidebar-item">Habilidades blandas</a>
                        <a href="{{ route('proyectos') }}"            class="sidebar-item">Proyectos</a>
                        <a href="{{ route('idiomas.index') }}"        class="sidebar-item active">Idiomas</a>
                        <a href="{{ route('redes.index') }}"          class="sidebar-item">Redes profesionales y contacto</a>
                    </div>

                    <div class="main">
                        <div class="page-title">Idiomas</div>

                        <!-- FORMULARIO AGREGAR -->
                        <div class="section-card" id="formCard">
                            <div class="section-subtitle">
                                Agrega los idiomas que dominas con su nivel y opcionalmente un certificado que lo respalde.
                            </div>

                            @if(session('success'))
                                <div class="alert-skill success" style="max-width: 500px;">
                                    <div class="alert-skill-icon">✓</div>
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif

                            <form id="idiomaForm" action="{{ route('idiomas.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Idioma <span class="required">*</span></label>
                                        <input class="form-input" type="text" name="nombre" id="nombre"
                                               placeholder="Ej. Inglés, Francés, Portugués..."
                                               value="{{ old('nombre') }}" maxlength="100">
                                        <div id="nombreError" class="error-message hidden">El nombre del idioma es obligatorio</div>
                                        @error('nombre')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Nivel <span class="required">*</span></label>
                                        <select class="form-input" name="nivel" id="nivel">
                                            <option value="">Seleccionar nivel</option>
                                            @foreach(['A1' => 'A1 — Principiante', 'A2' => 'A2 — Básico', 'B1' => 'B1 — Intermedio', 'B2' => 'B2 — Intermedio alto', 'C1' => 'C1 — Avanzado', 'C2' => 'C2 — Maestría', 'Nativo' => 'Nativo'] as $val => $label)
                                                <option value="{{ $val }}" {{ old('nivel') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <div id="nivelError" class="error-message hidden">El nivel es obligatorio</div>
                                        @error('nivel')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- EVIDENCIA -->
                                <div class="form-group" style="margin-bottom: 16px;">
                                    <label class="form-label">
                                        Certificado
                                        <span class="optional-tag">Opcional</span>
                                    </label>

                                    <div id="uploadZoneIdioma"
                                         class="upload-zone"
                                         ondragover="event.preventDefault(); this.classList.add('drag-over')"
                                         ondragleave="this.classList.remove('drag-over')"
                                         ondrop="handleDropIdioma(event)"
                                         onclick="document.getElementById('evidenciaInput').click()">
                                        <div class="upload-icon">📎</div>
                                        <div class="upload-txt">Arrastra tu certificado aquí o haz clic</div>
                                        <span class="upload-btn-sm">Seleccionar archivo</span>
                                    </div>

                                    <div id="evidenciaPreview" class="evidencia-preview hidden"></div>

                                    <input type="file" id="evidenciaInput" name="evidencia"
                                           accept=".jpg,.jpeg,.png,.pdf"
                                           style="display:none"
                                           onchange="handleFileIdioma(this.files[0])">

                                    <div id="evidenciaError" class="error-message hidden"></div>
                                    <div class="hint">ℹ️ JPG, PNG o PDF — Máx. 2MB</div>

                                    @error('evidencia')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="btn-row">
                                    <button type="submit" class="btn primary">Guardar idioma</button>
                                    <button type="button" class="btn" onclick="resetIdiomaForm()">Cancelar</button>
                                </div>
                            </form>
                        </div>

                        <!-- HISTORIAL -->
                        @if($idiomas->count() > 0)
                        <div class="historial-divider"><span>— HISTORIAL —</span></div>

                        <div id="listaIdiomas">
                            @foreach($idiomas as $idioma)
                            @php
                                $nivelesMap = [1=>'A1',2=>'A2',3=>'B1',4=>'B2',5=>'C1',6=>'C2',7=>'Nativo'];
                                $nivelLabel = $nivelesMap[$idioma->level] ?? 'A1';
                                $nivelesNombre = ['A1'=>'Principiante','A2'=>'Básico','B1'=>'Intermedio','B2'=>'Intermedio alto','C1'=>'Avanzado','C2'=>'Maestría','Nativo'=>'Nativo'];
                                $nivelNombre = $nivelesNombre[$nivelLabel] ?? '';
                                $porcentaje = ['A1'=>15,'A2'=>30,'B1'=>50,'B2'=>65,'C1'=>80,'C2'=>95,'Nativo'=>100];
                                $pct = $porcentaje[$nivelLabel] ?? 50;
                                $banderas = ['inglés'=>'🇬🇧','español'=>'🇧🇴','portugués'=>'🇧🇷','francés'=>'🇫🇷','alemán'=>'🇩🇪','italiano'=>'🇮🇹','chino'=>'🇨🇳','japonés'=>'🇯🇵'];
                                $bandera = $banderas[strtolower($idioma->name)] ?? '🌐';
                            @endphp

                            <div class="idioma-card" id="idioma-{{ $idioma->id }}">

                                {{-- MODO VISTA --}}
                                <div class="idioma-vista" id="vista-{{ $idioma->id }}">
                                    <div class="idioma-flag">{{ $bandera }}</div>
                                    <div class="idioma-info">
                                        <div class="idioma-nombre">{{ $idioma->name }}</div>
                                        <div class="idioma-nivel">{{ $nivelLabel }} — {{ $nivelNombre }}</div>
                                        <div class="nivel-bar-wrap">
                                            <div class="nivel-bar" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>

                                    @if($idioma->evidence_url)
                                    <a href="{{ asset('storage/' . $idioma->evidence_url) }}" target="_blank" class="cert-badge">
                                        📄 Ver certificado
                                    </a>
                                    @else
                                    <div style="width: 110px;"></div>
                                    @endif

                                    <div class="idioma-actions">
                                    <button class="btn-sm" onclick="mostrarEditar({{ $idioma->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                        </svg>
                                        Editar
                                    </button>
                                        <button type="button" class="btn-sm danger"
                                                onclick="openDeleteModalIdioma('{{ $idioma->id }}', '{{ addslashes($idioma->name) }}')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </div>
                                </div>

                                {{-- MODO EDICIÓN --}}
                                <div class="idioma-edicion hidden" id="edicion-{{ $idioma->id }}">
                                    <form action="{{ route('idiomas.update', $idioma->id) }}" method="POST"
                                          enctype="multipart/form-data" style="width:100%">
                                        @csrf @method('PUT')

                                        <div class="form-row">
                                            <div class="form-group">
                                                <label class="form-label">Idioma <span class="required">*</span></label>
                                                <input class="form-input" type="text" name="nombre"
                                                       value="{{ $idioma->name }}" maxlength="100" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Nivel <span class="required">*</span></label>
                                                <select class="form-input" name="nivel" required>
                                                    @foreach(['A1'=>'A1 — Principiante','A2'=>'A2 — Básico','B1'=>'B1 — Intermedio','B2'=>'B2 — Intermedio alto','C1'=>'C1 — Avanzado','C2'=>'C2 — Maestría','Nativo'=>'Nativo'] as $val => $label)
                                                        <option value="{{ $val }}" {{ $nivelLabel == $val ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Evidencia existente -->
                                        @if($idioma->evidence_url)
                                        <div style="display:flex; align-items:center; gap:10px; background:#e1f5ee; border:1px solid #5dcaa5; border-radius:10px; padding:10px 14px; margin-bottom:10px;">
                                            <span style="font-size:18px;">📄</span>
                                            <span style="font-size:12px; color:#0f6e56; flex:1;">Certificado actual</span>
                                            <a href="{{ asset('storage/' . $idioma->evidence_url) }}" target="_blank"
                                               style="font-size:11px; color:#0abf9e; text-decoration:underline;">Ver</a>
                                            <label style="font-size:11px; color:#e74c3c; cursor:pointer; display:flex; align-items:center; gap:4px;">
                                                <input type="checkbox" name="eliminar_evidencia" value="1"> Eliminar
                                            </label>
                                        </div>
                                        @endif

                                        
                                        <!-- Subir nueva evidencia -->
                                        <div class="form-group" style="margin-bottom:14px;">
                                            <label class="form-label">
                                                {{ $idioma->evidence_url ? 'Reemplazar certificado' : 'Certificado' }}
                                                <span class="optional-tag">Opcional</span>
                                            </label>

                                            <div id="uploadZoneEdit-{{ $idioma->id }}"
                                                class="upload-zone"
                                                ondragover="event.preventDefault(); this.classList.add('drag-over')"
                                                ondragleave="this.classList.remove('drag-over')"
                                                ondrop="handleDropEdit(event, {{ $idioma->id }})"
                                                onclick="document.getElementById('evidenciaInputEdit-{{ $idioma->id }}').click()">
                                                <div class="upload-icon">📎</div>
                                                <div class="upload-txt">Arrastra tu certificado aquí o haz clic</div>
                                                <span class="upload-btn-sm">Seleccionar archivo</span>
                                            </div>

                                            <div id="evidenciaPreviewEdit-{{ $idioma->id }}" class="evidencia-preview hidden"></div>

                                            <input type="file"
                                                id="evidenciaInputEdit-{{ $idioma->id }}"
                                                name="evidencia"
                                                accept=".jpg,.jpeg,.png,.pdf"
                                                style="display:none"
                                                onchange="handleFileEdit(this.files[0], {{ $idioma->id }})">

                                            <div class="hint">ℹ️ JPG, PNG o PDF — Máx. 2MB</div>
                                        </div>

                                        <div class="btn-row">
                                            <button type="submit" class="btn primary">Guardar cambios</button>
                                            <button type="button" class="btn" onclick="ocultarEditar({{ $idioma->id }})">Cancelar</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                            @endforeach
                        </div>
                        @endif

                    </div>{{-- /main --}}
                </div>{{-- /body-row --}}
            </div>{{-- /shell --}}
        </div>
    </div>

    {{-- Modal eliminar idioma --}}
<div class="modal-backdrop" id="delete-modal-idioma">
    <div class="modal-box">
        <h3>Eliminar idioma</h3>
        <p>¿Estás seguro de que deseas eliminar <strong id="modal-idioma-name"></strong>?<br>Esta acción no se puede deshacer.</p>
        <form id="delete-form-idioma" method="POST">
            @csrf @method('DELETE')
            <div style="display:flex;gap:12px;justify-content:center;">
                <button type="submit" class="btn-danger">Sí, eliminar</button>
                <button type="button" class="btn" onclick="closeDeleteModalIdioma()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

    <script src="{{ asset('js/idiomas.js') }}"></script>
</x-app-layout>