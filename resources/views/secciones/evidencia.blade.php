{{-- resources/views/secciones/evidencia.blade.php --}}

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/evidencia.css') }}">

{{-- HEADER PRINCIPAL (sin breadcrumb) --}}
<div class="ev-header">
    <div class="ev-title-wrap">
        <h2>Evidencias</h2>
        <p>Imágenes, enlaces y repositorios asociados al proyecto</p>
    </div>

    {{-- DROPDOWN AGREGAR EVIDENCIA --}}
    <div class="ev-dropdown-agregar">
        <button class="ev-btn-agregar-ev" id="evBtnDropdownTrigger">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Agregar Evidencia
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: 6px;">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>
        <div class="ev-dropdown-menu" id="evDropdownMenu">
            <button type="button" class="ev-dropdown-item" data-tipo="imagen">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                <div class="ev-dropdown-item-text">
                    <strong>Imagen</strong>
                    <span>PNG, JPG • Máx 5MB</span>
                </div>
            </button>
            <button type="button" class="ev-dropdown-item" data-tipo="enlace">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                </svg>
                <div class="ev-dropdown-item-text">
                    <strong>Enlace</strong>
                    <span>Demo, web, video</span>
                </div>
            </button>
            <button type="button" class="ev-dropdown-item" data-tipo="repositorio">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                    <polyline points="2 17 12 22 22 17"/>
                    <polyline points="2 12 12 17 22 12"/>
                </svg>
                <div class="ev-dropdown-item-text">
                    <strong>Repositorio</strong>
                    <span>GitHub, GitLab, etc.</span>
                </div>
            </button>
        </div>
    </div>
</div>

{{-- CONTADORES PEQUEÑOS EN ESQUINA --}}
<div class="ev-stats-compact">
    <div class="ev-stat-compact" id="evStatImgs">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
        </svg>
        <span>0</span>
    </div>
    <div class="ev-stat-compact" id="evStatLinks">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
        </svg>
        <span>0</span>
    </div>
    <div class="ev-stat-compact" id="evStatRepos">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="12 2 2 7 12 12 22 7 12 2"/>
            <polyline points="2 17 12 22 22 17"/>
            <polyline points="2 12 12 17 22 12"/>
        </svg>
        <span>0</span>
    </div>
</div>

{{-- FORMULARIO AGREGAR --}}
<div class="ev-form-card" id="evFormCard">
    <div class="ev-form-title" id="evFormTitle">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
        </svg>
        Agregar Evidencia
    </div>

    {{-- Panel: Imagen --}}
    <div class="ev-panel active" id="evPanelImagen">
        <div class="ev-field">
            <label>Nombre / descripción <span>*</span></label>
            <input type="text" id="evImgNombre" class="ev-inp" placeholder="Ej: Captura del dashboard principal" maxlength="200">
            <span class="ev-err-msg" id="evErrImgNombre">El nombre es obligatorio</span>
        </div>
        <div class="ev-dropzone" id="evDropzone">
            <input type="file" id="evFileInput" multiple accept=".jpg,.jpeg,.png">
            <div class="ev-dropzone-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M16 16l-4-4-4 4"/>
                    <path d="M12 12v9"/>
                    <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                    <polyline points="16 16 12 12 8 16"/>
                </svg>
            </div>
            <div class="ev-dropzone-text">Arrastra imágenes aquí o haz clic para seleccionar</div>
            <div class="ev-dropzone-hint">PNG o JPG • Máx. 5 MB por imagen • Puedes subir varias a la vez</div>
        </div>
        <div class="ev-new-previews" id="evNewPreviews"></div>
        <span class="ev-err-msg" id="evErrImgFile" style="margin-top:8px">Selecciona al menos una imagen</span>
    </div>

    {{-- Panel: Enlace --}}
    <div class="ev-panel" id="evPanelEnlace">
        <div class="ev-field">
            <label>Etiqueta / nombre <span>*</span></label>
            <input type="text" id="evLinkNombre" class="ev-inp" placeholder="Ej: Ver demo en vivo" maxlength="150">
            <span class="ev-err-msg" id="evErrLinkNombre">La etiqueta es obligatoria</span>
        </div>
        <div class="ev-field">
            <label>URL del enlace <span>*</span></label>
            <div class="ev-inp-icon-wrap">
                <span class="ev-inp-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                </span>
                <input type="url" id="evLinkUrl" class="ev-inp" placeholder="https://mi-proyecto.com">
            </div>
            <span class="ev-err-msg" id="evErrLinkUrl">Ingresa una URL válida</span>
        </div>
        <div class="ev-field">
            <label>Descripción <span style="color:#94a3b8;font-weight:400">(opcional)</span></label>
            <input type="text" id="evLinkDesc" class="ev-inp" placeholder="Ej: Aplicación desplegada en Vercel" maxlength="200">
        </div>
    </div>

    {{-- Panel: Repositorio --}}
    <div class="ev-panel" id="evPanelRepositorio">
        <div class="ev-field">
            <label>Nombre del repositorio <span>*</span></label>
            <input type="text" id="evRepoNombre" class="ev-inp" placeholder="Ej: portafolio-web" maxlength="150">
            <span class="ev-err-msg" id="evErrRepoNombre">El nombre es obligatorio</span>
        </div>
        <div class="ev-field">
            <label>URL del repositorio <span>*</span></label>
            <div class="ev-inp-icon-wrap">
                <span class="ev-inp-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                        <polyline points="2 17 12 22 22 17"/>
                        <polyline points="2 12 12 17 22 12"/>
                    </svg>
                </span>
                <input type="url" id="evRepoUrl" class="ev-inp" placeholder="https://github.com/usuario/repo">
            </div>
            <span class="ev-err-msg" id="evErrRepoUrl">Ingresa una URL válida</span>
        </div>
        <div class="ev-field">
            <label>Plataforma</label>
            <select id="evRepoPlataforma" class="ev-select">
                <option value="GitHub">GitHub</option>
                <option value="GitLab">GitLab</option>
                <option value="Bitbucket">Bitbucket</option>
                <option value="Otro">Otro</option>
            </select>
        </div>
        <div class="ev-field">
            <label>Descripción <span style="color:#94a3b8;font-weight:400">(opcional)</span></label>
            <input type="text" id="evRepoDesc" class="ev-inp" placeholder="Ej: Repositorio con el código fuente completo" maxlength="200">
        </div>
    </div>

    <div class="ev-form-actions">
        <button class="ev-btn-cancel" id="evBtnCancelar">Cancelar</button>
        <button class="ev-btn-save" id="evBtnGuardar">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            Guardar evidencia
        </button>
    </div>
</div>

{{-- CONTROLES / FILTROS --}}
<div class="ev-controls">
    <div class="ev-search-wrap">
        <span class="ev-search-icon">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
        </span>
        <input type="text" id="evSearchInp" class="ev-search-inp" placeholder="Buscar evidencias...">
    </div>
    <div class="ev-filter-btns">
        <button class="ev-filter-btn active" data-filter="todos">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
            </svg>
            Todos
        </button>
        <button class="ev-filter-btn" data-filter="imagen">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            Imágenes
        </button>
        <button class="ev-filter-btn" data-filter="enlace">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>
            Enlaces
        </button>
        <button class="ev-filter-btn" data-filter="repositorio">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                <polyline points="2 17 12 22 22 17"/>
                <polyline points="2 12 12 17 22 12"/>
            </svg>
            Repos
        </button>
    </div>
</div>

{{-- GRID DE EVIDENCIAS --}}
<div class="ev-grid" id="evGrid"></div>

{{-- LIGHTBOX --}}
<div class="ev-lightbox" id="evLightbox">
    <div class="ev-lightbox-inner">
        <img id="evLightboxImg" src="" alt="Evidencia">
        <button class="ev-lightbox-close" id="evLightboxClose">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
</div>

{{-- Modal confirmar eliminar --}}
<div id="evModalEliminar">
    <div class="ev-modal-box">
        <div class="ev-modal-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.8">
                <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                <polyline points="2 17 12 22 22 17"/>
                <polyline points="2 12 12 17 22 12"/>
            </svg>
        </div>
        <h3>¿Eliminar evidencia?</h3>
        <p>Esta acción no se puede deshacer.</p>
        <div class="ev-modal-btns">
            <button id="evModalCancelar">Cancelar</button>
            <button id="evModalConfirmar">Sí, eliminar</button>
        </div>
    </div>
</div>