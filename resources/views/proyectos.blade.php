{{-- resources/views/proyectos.blade.php --}}
{{-- HU-10: Gestionar mis proyectos + HU-22: Agregar tecnologías --}}

<x-app-layout>
    

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/proyectos.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="shell">
                <div class="header-bar">BIENVENIDO</div>
                <div class="navbar">
                    <div class="nav-tab active">COMPLETAR</div>
                    <div class="nav-tab muted">VER PERFIL</div>
                </div>
                <div class="body-row">

                    {{-- ── SIDEBAR ── --}}
                    <div class="sidebar">
                        <a href="{{ route('profile.create') }}"      class="sidebar-item">Personal</a>
                        <a href="{{ route('experiencia.laboral') }}"  class="sidebar-item">Experiencia laboral</a>
                        <a href="{{ route('informacion.academica') }}" class="sidebar-item">Información académica</a>
                        <a href="{{ route('skills.tecnicas') }}"     class="sidebar-item">Habilidades técnicas</a>
                        <a href="{{ route('skills.blandas') }}"      class="sidebar-item">Habilidades blandas</a>
                        <a href="{{ route('proyectos') }}"           class="sidebar-item active">Proyectos</a>
                        <a href="{{ route('redes.index') }}"         class="sidebar-item">Redes profesionales y contacto</a>
                    </div>

                    {{-- ── PANEL PRINCIPAL ── --}}
                    <div class="main-panel">

                        {{-- HEADER --}}
                        <div class="proy-header" id="proyHeader">
                            <div class="proy-title-wrap">
                                <h2>Mis Proyectos</h2>
                                <p>Gestiona y organiza todos tus proyectos profesionales</p>
                            </div>
                            <button class="proy-btn-nuevo" id="proyBtnMostrarForm">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white">
                                    <line x1="12" y1="5"  x2="12" y2="19"/>
                                    <line x1="5"  y1="12" x2="19" y2="12"/>
                                </svg>
                                Nuevo Proyecto
                            </button>
                        </div>

                        {{-- FORMULARIO --}}
                        <div class="proy-form-card" id="proyFormCard">
                            <div class="proy-form-title" id="proyFormTitle">➕ Nuevo Proyecto</div>

                            <div class="proy-form-grid">
                                <div class="proy-field-full">
                                    <label>Nombre del proyecto <span>*</span></label>
                                    <input type="text" id="proyNombre" class="proy-inp" placeholder="Ej: Portafolio Web Personal" maxlength="100">
                                    <span class="proy-err-msg" id="proyErrNombre">El nombre es obligatorio</span>
                                </div>

                                <div class="proy-field-full">
                                    <label>Descripción <span>*</span></label>
                                    <textarea id="proyDesc" class="proy-textarea" placeholder="Describe brevemente el proyecto..." maxlength="500"></textarea>
                                    <span class="proy-err-msg" id="proyErrDesc">La descripción es obligatoria</span>
                                </div>

                                <div class="proy-field">
                                    <label>Fecha</label>
                                    <input type="date" id="proyFecha" class="proy-inp">
                                </div>

                                <div class="proy-field">
                                    <label>Estado</label>
                                    <select id="proyEstado" class="proy-select">
                                        <option value="En curso">🟡 En curso</option>
                                        <option value="Completado">🟢 Completado</option>
                                        <option value="En pausa">⚪ En pausa</option>
                                    </select>
                                </div>
                            </div>

                            {{-- TECNOLOGÍAS --}}
                            <div class="tec-section">
                                <div class="tec-header">
                                    <div class="tec-header-left">
                                        <span class="tec-icon">🛠️</span>
                                        <span class="tec-title">Stack Tecnológico</span>
                                    </div>
                                    <div class="tec-badge-count" id="tecCountBadge">0</div>
                                </div>
                                <div class="tec-subtitle">Tecnologías, frameworks y lenguajes utilizados en este proyecto</div>
                                <div id="tecBadgesContainer" class="tec-badges">
                                    <div class="tec-empty-state">
                                        <div class="tec-empty-icon">🔧</div>
                                        <div class="tec-empty-text">Aún no hay tecnologías agregadas</div>
                                        <div class="tec-empty-hint">Comienza escribiendo el nombre de una tecnología</div>
                                    </div>
                                </div>
                                <div class="tec-input-wrapper">
                                    <div class="tec-input-group">
                                        <div class="tec-input-icon"><i class="fas fa-search"></i></div>
                                        <input type="text" id="tecInput" class="tec-input"
                                               placeholder="Ej: Laravel, React, Vue, Tailwind, Node.js..."
                                               maxlength="50" autocomplete="off">
                                    </div>
                                    <button type="button" id="tecBtnAgregar" class="tec-btn-add">
                                        <i class="fas fa-plus"></i> Agregar
                                    </button>
                                </div>
                                <div class="tec-suggestions">
                                    <span class="tec-suggestions-label"><i class="fas fa-fire"></i> Sugerencias:</span>
                                    <button type="button" class="tec-suggestion-chip" data-tec="Laravel"><i class="fab fa-laravel"></i> Laravel</button>
                                    <button type="button" class="tec-suggestion-chip" data-tec="React"><i class="fab fa-react"></i> React</button>
                                    <button type="button" class="tec-suggestion-chip" data-tec="Vue.js"><i class="fab fa-vuejs"></i> Vue.js</button>
                                    <button type="button" class="tec-suggestion-chip" data-tec="Node.js"><i class="fab fa-node-js"></i> Node.js</button>
                                    <button type="button" class="tec-suggestion-chip" data-tec="Tailwind"><i class="fab fa-css3-alt"></i> Tailwind</button>
                                    <button type="button" class="tec-suggestion-chip" data-tec="Python"><i class="fab fa-python"></i> Python</button>
                                    <button type="button" class="tec-suggestion-chip" data-tec="Flutter"><i class="fab fa-flutter"></i> Flutter</button>
                                    <button type="button" class="tec-suggestion-chip" data-tec="Docker"><i class="fab fa-docker"></i> Docker</button>
                                    <button type="button" class="tec-suggestion-chip" data-tec="PHP"><i class="fab fa-php"></i> PHP</button>
                                    <button type="button" class="tec-suggestion-chip" data-tec="MySQL"><i class="fas fa-database"></i> MySQL</button>
                                </div>
                                <div class="tec-footer">
                                    <span><i class="fas fa-lightbulb"></i></span>
                                    <span>Máximo 15 tecnologías • Sin duplicados • Haz clic en ✕ para eliminar</span>
                                </div>
                            </div>

                            {{-- BOTÓN EVIDENCIAS --}}
                            <div class="ev-trigger-wrap">
                                <button type="button" class="ev-trigger-btn" id="proyBtnEvidencias">
                                    <div class="ev-trigger-left">
                                        <div class="ev-trigger-icon-box">📎</div>
                                        <div class="ev-trigger-texts">
                                            <strong>Agregar Evidencias</strong>
                                            <span>Imágenes, enlaces y repositorios del proyecto</span>
                                        </div>
                                    </div>
                                    <div class="ev-trigger-right">
                                        <span class="ev-trigger-count" id="evTriggerCount">0</span>
                                        <i class="fas fa-chevron-right ev-trigger-arrow"></i>
                                    </div>
                                </button>
                            </div>

                            <div class="proy-form-actions">
                                <button class="proy-btn-cancel" id="proyBtnCancelarForm">Cancelar</button>
                                <button class="proy-btn-save"   id="proyBtnGuardarForm">Guardar proyecto</button>
                            </div>
                        </div>

                        {{-- GRID --}}
                        <div class="proy-grid" id="proyGrid"></div>

                        {{-- EVIDENCIAS (oculto por defecto) --}}
                        <div id="evSectionWrapper" style="display:none">
                            @include('secciones.evidencia')
                        </div>

                    </div>{{-- /main-panel --}}
                </div>{{-- /body-row --}}
            </div>{{-- /shell --}}
        </div>
    </div>


<script src="{{ asset('js/proyectos.js') }}"></script>
<script src="{{ asset('js/evidencia.js') }}"></script> 
</x-app-layout>