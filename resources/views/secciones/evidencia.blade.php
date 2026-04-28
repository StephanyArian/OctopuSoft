{{-- resources/views/evidencia.blade.php --}}
{{-- HU-EV: Gestionar evidencias de un proyecto (imágenes + enlaces + repositorios) --}}

<x-app-layout>

    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h2 class="font-semibold text-xl leading-tight" style="color: #2d0a1e;">
                {{ __('Formulario de Perfil Profesional') }}
            </h2>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" style="background: linear-gradient(90deg, #2d0a1e, #0abf9e); color: white; padding: 8px 20px; border-radius: 40px; font-weight: 600; font-size: 14px; border: none; cursor: pointer;">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/evidencia.css') }}">

    

    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="shell">
                <div class="navbar">
                    <div class="nav-tab active">COMPLETAR</div>
                    <div class="nav-tab muted">VER PERFIL</div>
                </div>
                <div class="body-row">

                    {{-- ── SIDEBAR ── --}}
                    <div class="sidebar">
                        <a href="{{ route('profile.create') }}"       class="sidebar-item">Personal</a>
                        <a href="{{ route('experiencia.laboral') }}"  class="sidebar-item">Experiencia laboral</a>
                        <a href="{{ route('informacion.academica') }}" class="sidebar-item">Información académica</a>
                        <a href="{{ route('skills.tecnicas') }}"      class="sidebar-item">Habilidades técnicas</a>
                        <a href="{{ route('skills.blandas') }}"       class="sidebar-item">Habilidades blandas</a>
                        <a href="{{ route('proyectos') }}"            class="sidebar-item active">Proyectos</a>
                        <a href="{{ route('redes.index') }}"          class="sidebar-item">Redes profesionales y contacto</a>
                    </div>

                    {{-- ── PANEL PRINCIPAL ── --}}
                    <div class="main-panel">

                        {{-- BREADCRUMB --}}
                        <div class="ev-breadcrumb">
                            <a href="{{ route('proyectos') }}">
                                <i class="fas fa-arrow-left"></i> Mis Proyectos
                            </a>
                            <span class="ev-breadcrumb-sep">›</span>
                            <span id="evBreadcrumbNombre">Evidencias</span>
                        </div>

                        {{-- HEADER --}}
                        <div class="ev-header">
                            <div class="ev-title-wrap">
                                <h2>Evidencias</h2>
                                <p>Imágenes, enlaces y repositorios asociados al proyecto</p>
                            </div>
                            <button class="ev-btn-agregar-ev" id="evBtnMostrarForm">
                                <i class="fas fa-plus"></i> Agregar Evidencia
                            </button>
                        </div>

                        {{-- BANNER DEL PROYECTO --}}
                        <div class="ev-proyecto-banner" id="evProyectoBanner">
                            <div class="ev-proyecto-banner-icon">📁</div>
                            <div class="ev-proyecto-banner-info">
                                <strong id="evBannerNombre">Cargando...</strong>
                                <span id="evBannerDesc">—</span>
                            </div>
                            <span class="ev-proyecto-banner-badge" id="evBannerEstado">—</span>
                        </div>

                        {{-- ESTADÍSTICAS --}}
                        <div class="ev-stats">
                            <div class="ev-stat-card">
                                <div class="ev-stat-icon">🖼️</div>
                                <div class="ev-stat-num" id="evStatImgs">0</div>
                                <div class="ev-stat-label">Imágenes</div>
                            </div>
                            <div class="ev-stat-card">
                                <div class="ev-stat-icon">🔗</div>
                                <div class="ev-stat-num" id="evStatLinks">0</div>
                                <div class="ev-stat-label">Enlaces</div>
                            </div>
                            <div class="ev-stat-card">
                                <div class="ev-stat-icon">📦</div>
                                <div class="ev-stat-num" id="evStatRepos">0</div>
                                <div class="ev-stat-label">Repositorios</div>
                            </div>
                        </div>

                        {{-- FORMULARIO AGREGAR --}}
                        <div class="ev-form-card" id="evFormCard">
                            <div class="ev-form-title" id="evFormTitle">📎 Agregar Evidencia</div>
                            <div class="ev-tipo-tabs">
                                <button type="button" class="ev-tipo-tab active" data-tipo="imagen">
                                    <span class="ev-tipo-tab-icon">🖼️</span>
                                    <span class="ev-tipo-tab-label">Imagen</span>
                                    <span class="ev-tipo-tab-desc">PNG, JPG • Máx 5MB</span>
                                </button>
                                <button type="button" class="ev-tipo-tab" data-tipo="enlace">
                                    <span class="ev-tipo-tab-icon">🔗</span>
                                    <span class="ev-tipo-tab-label">Enlace</span>
                                    <span class="ev-tipo-tab-desc">Demo, web, video</span>
                                </button>
                                <button type="button" class="ev-tipo-tab" data-tipo="repositorio">
                                    <span class="ev-tipo-tab-icon">📦</span>
                                    <span class="ev-tipo-tab-label">Repositorio</span>
                                    <span class="ev-tipo-tab-desc">GitHub, GitLab, etc.</span>
                                </button>
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
                                    <div class="ev-dropzone-icon"><i class="fas fa-cloud-upload-alt"></i></div>
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
                                        <i class="fas fa-globe ev-inp-icon"></i>
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
                                        <i class="fab fa-github ev-inp-icon"></i>
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
                                    <i class="fas fa-save"></i> Guardar evidencia
                                </button>
                            </div>
                        </div>

                        {{-- CONTROLES / FILTROS --}}
                        <div class="ev-controls">
                            <div class="ev-search-wrap">
                                <i class="fas fa-search ev-search-icon"></i>
                                <input type="text" id="evSearchInp" class="ev-search-inp" placeholder="Buscar evidencias...">
                            </div>
                            <div class="ev-filter-btns">
                                <button class="ev-filter-btn active" data-filter="todos"><i class="fas fa-border-all"></i> Todos</button>
                                <button class="ev-filter-btn" data-filter="imagen"><i class="fas fa-image"></i> Imágenes</button>
                                <button class="ev-filter-btn" data-filter="enlace"><i class="fas fa-link"></i> Enlaces</button>
                                <button class="ev-filter-btn" data-filter="repositorio"><i class="fab fa-github"></i> Repos</button>
                            </div>
                        </div>

                        {{-- GRID DE EVIDENCIAS --}}
                        <div class="ev-grid" id="evGrid"></div>

                        {{-- LIGHTBOX --}}
                        <div class="ev-lightbox" id="evLightbox">
                            <div class="ev-lightbox-inner">
                                <img id="evLightboxImg" src="" alt="Evidencia">
                                <button class="ev-lightbox-close" id="evLightboxClose">✕</button>
                            </div>
                        </div>

                    </div>{{-- /main-panel --}}

                </div>{{-- /body-row --}}
            </div>{{-- /shell --}}


                        <!-- Modal confirmar eliminar -->
            <div id="evModalEliminar">
                <div class="ev-modal-box">
                    <div class="ev-modal-icon">🗑️</div>
                    <h3>¿Eliminar evidencia?</h3>
                    <p>Esta acción no se puede deshacer.</p>
                    <div class="ev-modal-btns">
                        <button id="evModalCancelar">Cancelar</button>
                        <button id="evModalConfirmar">Sí, eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/evidencia.js') }}"></script>

    
</x-app-layout>
