@extends('layouts.app-completar')

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proyectos.css') }}">
@endpush

@section('content')


                        {{-- ============================================================ --}}
                        {{-- BANNER DE ÉXITO (estilo habilidades blandas)                --}}
                        {{-- ============================================================ --}}
                        <div id="proyBannerExito" style="display:none; align-items:center; gap:10px; background:#d1fae5; border:1px solid #6ee7b7; border-radius:8px; padding:12px 16px; margin-bottom:16px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;">
                                <circle cx="12" cy="12" r="10" fill="#10b981"/>
                                <path d="M7 13l3 3 7-7" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span id="proyBannerTexto" style="color:#065f46; font-size:14px; font-weight:500;"></span>
                        </div>

                        <div class="proy-header" id="proyHeader">
                            <div class="proy-title-wrap">
                                <h2>Mis Proyectos</h2>
                                <p>Gestiona y organiza todos tus proyectos profesionales</p>
                            </div>
                            <div class="proy-header-right">
                                <button class="filtros-toggle-btn" id="filtrosToggleBtn">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg> Filtros
                                </button>
                                <div class="filtros-toolbar-inline" id="filtrosToolbar">
                                    <div class="ft-search-box ft-search-sm">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                        <input type="text" id="buscadorProyectos" class="ft-search-input" placeholder="Buscar..." maxlength="50" autocomplete="off">
                                        <button id="limpiarBuscador" class="ft-search-clear" style="display:none"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                                    </div>
                                    <div class="ft-btn-wrap">
                                        <button class="ft-btn-sm" id="btnOrdenar"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M6 12h12M10 18h4"/></svg> Ordenar <span class="ft-caret">▾</span></button>
                                        <div class="ft-dropdown" id="ddOrdenar">
                                            <div class="ft-dd-section"><div class="ft-dd-label">Ordenar por</div><div class="ft-dd-item selected" data-filtro="ordenar" data-val="fecha_desc"><span class="ft-dot"></span>Fecha de inicio</div><div class="ft-dd-item" data-filtro="ordenar" data-val="nombre_asc"><span class="ft-dot"></span>Nombre</div><div class="ft-dd-item" data-filtro="ordenar" data-val="rol_asc"><span class="ft-dot"></span>Mi rol</div></div>
                                            <div class="ft-dd-section"><div class="ft-dd-label">Dirección</div><div class="ft-dd-item selected" data-filtro="direccion" data-val="desc"><span class="ft-dot"></span>Descendente</div><div class="ft-dd-item" data-filtro="direccion" data-val="asc"><span class="ft-dot"></span>Ascendente</div></div>
                                        </div>
                                    </div>
                                    <div class="ft-btn-wrap">
                                        <button class="ft-btn-sm" id="btnFiltrar"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg> Filtrar <span class="ft-caret">▾</span></button>
                                        <div class="ft-dropdown" id="ddFiltrar">
                                            <div class="ft-dd-section"><div class="ft-dd-label">Estado</div><div class="ft-dd-item selected" data-filtro="estado" data-val="todos"><span class="ft-dot"></span>Todos</div><div class="ft-dd-item" data-filtro="estado" data-val="En curso"><span class="ft-dot"></span><span class="ft-status-dot" style="background:#f59e0b"></span>En curso</div><div class="ft-dd-item" data-filtro="estado" data-val="Completado"><span class="ft-dot"></span><span class="ft-status-dot" style="background:#10b981"></span>Completado</div><div class="ft-dd-item" data-filtro="estado" data-val="En pausa"><span class="ft-dot"></span><span class="ft-status-dot" style="background:#94a3b8"></span>En pausa</div></div>
                                            <div class="ft-dd-section"><div class="ft-dd-label">Visibilidad</div><div class="ft-dd-item selected" data-filtro="visibilidad" data-val="todos"><span class="ft-dot"></span>Todos</div><div class="ft-dd-item" data-filtro="visibilidad" data-val="publico"><span class="ft-dot"></span>Públicos</div><div class="ft-dd-item" data-filtro="visibilidad" data-val="privado"><span class="ft-dot"></span>Privados</div></div>
                                            <div class="ft-dd-section" id="rolFilterSection"><div class="ft-dd-label">Mi rol</div><div class="ft-dd-item selected" data-filtro="rol" data-val="todos"><span class="ft-dot"></span>Todos los roles</div></div>
                                        </div>
                                    </div>
                                    <div class="ft-btn-wrap">
                                        <button class="ft-btn-sm" id="btnTecnologia"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg> Tec <span class="ft-caret">▾</span></button>
                                        <div class="ft-dropdown" id="ddTecnologia"><div class="ft-dd-section" id="tecnologiaFilterSection"><div class="ft-dd-item selected" data-filtro="tecnologia" data-val="todas"><span class="ft-dot"></span>Todas</div></div></div>
                                    </div>
                                    <span class="ft-counter-sm">Mostrando <strong id="conteoMostrados">0</strong>/<strong id="conteoTotal">0</strong></span>
                                    <button id="limpiarFiltros" class="ft-clear-btn"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg></button>
                                </div>
                                <button class="proy-btn-nuevo" id="proyBtnMostrarForm"><svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Nuevo Proyecto</button>
                            </div>
                        </div>

                        {{-- FORMULARIO --}}
                        <div class="proy-form-card" id="proyFormCard">
                            <div class="proy-form-title" id="proyFormTitle"> Nuevo Proyecto</div>
                            <div class="proy-form-grid">
                                <div class="proy-field-full"><label>Nombre del proyecto <span>*</span></label><input type="text" id="proyNombre" class="proy-inp" placeholder="Ej: Portafolio Web Personal" maxlength="100"><span class="proy-err-msg" id="proyErrNombre">El nombre es obligatorio</span></div>
                                <div class="proy-field-full"><label>Descripción <span>*</span></label><div class="textarea-wrapper"><div id="quillEditor" style="min-height:120px;background:white;border-radius:12px;"></div><textarea id="proyDesc" style="display:none;"></textarea><span class="word-count" id="contadorDesc">0/5000 caracteres</span></div><span class="proy-err-msg" id="proyErrDesc">La descripción es obligatoria</span></div>
                                <div class="proy-field"><label>Fecha de inicio</label><input type="date" id="proyFecha" class="proy-inp"></div>
                                <div class="proy-field"><label>Fecha de fin</label><input type="date" id="proyFechaFin" class="proy-inp" disabled><small style="color:#94a3b8;font-size:10px;">Solo disponible para "Completado"</small></div>
                                <div class="proy-field"><label>Estado</label><div class="custom-dropdown" id="dropdownEstado"><button type="button" class="custom-dropdown-btn" id="btnEstado"><span id="btnEstadoText">En curso</span><span class="custom-dropdown-arrow">▾</span></button><ul class="custom-dropdown-menu" id="menuEstado"><li data-value="En curso" class="selected">En curso</li><li data-value="Completado">Completado</li><li data-value="En pausa">En pausa</li></ul><input type="hidden" id="proyEstado" value="En curso"></div></div>
                                <div class="proy-field"><label>Mi rol en el proyecto</label><div class="custom-dropdown" id="dropdownRol"><button type="button" class="custom-dropdown-btn" id="btnRol"><span id="btnRolText">— Seleccionar rol —</span><span class="custom-dropdown-arrow">▾</span></button><ul class="custom-dropdown-menu" id="menuRol"><li data-value="">— Seleccionar rol —</li><li data-value="Frontend Developer">Frontend Developer</li><li data-value="Backend Developer">Backend Developer</li><li data-value="Full Stack Developer">Full Stack Developer</li><li data-value="UI/UX Designer">UI/UX Designer</li><li data-value="DevOps Engineer">DevOps Engineer</li><li data-value="Mobile Developer">Mobile Developer</li><li data-value="Project Manager">Project Manager</li><li data-value="QA Tester">QA Tester</li><li data-value="Database Administrator">Database Administrator</li><li data-value="Technical Leader">Technical Leader</li><li data-value="Data Analyst">Data Analyst</li><li data-value="Scrum Master">Scrum Master</li><li data-value="Product Owner">Product Owner</li></ul><input type="hidden" id="proyRol" value=""></div></div>
                                <div class="proy-field"><label>Empresa / Cliente</label><input type="text" id="proyCliente" class="proy-inp" placeholder="Ej: Google, Freelance..." maxlength="60"></div>
                            </div>

                            {{-- STACK TECNOLÓGICO --}}
                            <div class="tec-section">
                                <div class="tec-header">
                                    <div class="tec-header-left">
                                        <span class="tec-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0abf9e" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></span>
                                        <span class="tec-title">Stack Tecnológico</span>
                                    </div>
                                    <div class="tec-badge-count" id="tecCountBadge">0</div>
                                </div>
                                <div class="tec-content-row"><div id="tecBadgesContainer" class="tec-badges-col"><div class="tec-empty-state"><div class="tec-empty-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></div><div class="tec-empty-text">Aún no hay tecnologías</div></div></div><div class="stack-dropdown" id="stackDropdown"><div class="stack-input-wrapper" id="stackInputWrapper"><input type="text" class="stack-search" id="stackSearch" placeholder="Agregar tecnología..." maxlength="20" autocomplete="off"><span class="stack-arrow">▾</span></div><div class="stack-menu" id="stackMenu"><div class="stack-list" id="stackList"></div></div></div></div>
                                <div class="tec-footer"> Escribe para buscar o presiona Enter para agregar</div>
                            </div>

                            {{-- EVIDENCIAS --}}
                            <div class="ev-trigger-wrap">
                                <button type="button" class="ev-trigger-btn" id="proyBtnEvidencias">
                                    <div class="ev-trigger-left"><div class="ev-trigger-icon-box"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg></div><div class="ev-trigger-texts"><strong></strong><span>Imágenes, enlaces y repositorios</span></div></div>
                                    <div class="ev-trigger-right"><span class="ev-trigger-count" id="evTriggerCount">0</span><i class="fas fa-chevron-right ev-trigger-arrow"></i></div>
                                </button>
                                <div class="ev-inline-section" id="evInlineSection"><div class="ev-inline-body" id="evInlineBody">@include('secciones.evidencia')</div></div>
                            </div>

                            <div class="proy-form-actions"><button class="proy-btn-cancel" id="proyBtnCancelarForm">Cancelar</button><button class="proy-btn-save" id="proyBtnGuardarForm">Guardar proyecto</button></div>
                        </div>

                        {{-- GRID --}}
                        <div class="proy-grid" id="proyGrid"></div>
                        {{-- VISTA PREVIA --}}
                        <div class="preview-page" id="previewPage"></div>
 @endsection

@push('modals')               

    {{-- MODAL ELIMINAR PROYECTO --}}
    <div class="modal-backdrop" id="delete-project-modal">
        <div class="modal-box">
            <h3>Eliminar proyecto</h3>
            <p>¿Estás seguro de que deseas eliminar <strong id="modal-project-name"></strong>?<br>Esta acción no se puede deshacer.</p>
            <form id="delete-project-form" method="POST">
                @csrf @method('DELETE')
                <div style="display:flex;gap:12px;justify-content:center;">
                    <button type="submit" class="btn-danger">Sí, eliminar</button>
                    <button type="button" class="btn-cancel-modal" onclick="closeDeleteProjectModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    @endpush   

    @push('scripts')

    <script src="{{ asset('js/proyectos.js') }}"></script>
    <script src="{{ asset('js/evidencia.js') }}"></script>
    <script>
        document.getElementById('filtrosToggleBtn')?.addEventListener('click', function() {
            document.getElementById('filtrosToolbar').classList.toggle('open');
        });
        document.addEventListener('DOMContentLoaded', function () {
            const params = new URLSearchParams(window.location.search);
            const previewId = params.get('preview');
            if (!previewId) return;
            let intentos = 0;
            const intervalo = setInterval(function () {
                const card = document.getElementById('proyecto-' + previewId);
                if (card) { clearInterval(intervalo); setTimeout(() => card.click(), 100); }
                if (++intentos > 20) clearInterval(intervalo);
            }, 100);
        });
    </script>
@endpush