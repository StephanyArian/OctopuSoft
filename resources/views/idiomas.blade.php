<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/idiomas.css') }}">

    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="success-message">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                                        <button class="btn-sm" onclick="mostrarEditar({{ $idioma->id }})">✏️ Editar</button>
                                        <form action="{{ route('idiomas.destroy', $idioma->id) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar este idioma?')" style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-sm danger">🗑 Eliminar</button>
                                        </form>
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

    <script src="{{ asset('js/idiomas.js') }}"></script>
</x-app-layout>