{{-- resources/views/secciones/evidencia.blade.php --}}
{{-- HU-EV: Gestionar evidencias de un proyecto (imágenes + enlaces + repositorios) --}}
{{-- Compatible con proyectos.blade.php — se activa desde el botón "Agregar Evidencias" --}}
 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
 
<style>
    /* ── Variables (heredadas del sistema) ── */
    :root {
        --burg-deep:  #2d0a1e;
        --teal:       #0abf9e;
        --teal-dim:   #07866e;
        --teal-light: #1de8c0;
    }
 
    /* ══════════════════════════════════════
       HEADER DE EVIDENCIAS
    ══════════════════════════════════════ */
    .ev-header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 28px;
    }
 
    .ev-title-wrap h2 {
        font-size: 24px;
        font-weight: 800;
        color: var(--burg-deep);
        letter-spacing: -.5px;
        position: relative;
        display: inline-block;
        margin-bottom: 6px;
    }
 
    .ev-title-wrap h2::after {
        content: '';
        position: absolute;
        bottom: -6px; left: 0;
        width: 48px; height: 3px;
        background: linear-gradient(90deg, var(--teal), var(--teal-light));
        border-radius: 3px;
    }
 
    .ev-title-wrap p {
        font-size: 13px;
        color: #9ca3af;
        margin-top: 14px;
    }
 
    /* Breadcrumb / volver */
    .ev-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 20px;
    }
 
    .ev-breadcrumb a {
        color: var(--teal);
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
 
    .ev-breadcrumb a:hover { text-decoration: underline; }
    .ev-breadcrumb-sep { font-size: 16px; color: #e2e8f0; }
 
    /* Proyecto info banner */
    .ev-proyecto-banner {
        display: flex;
        align-items: center;
        gap: 14px;
        background: linear-gradient(135deg, var(--burg-deep) 0%, #4a1030 100%);
        border-radius: 16px;
        padding: 16px 20px;
        margin-bottom: 28px;
        color: white;
    }
 
    .ev-proyecto-banner-icon {
        width: 42px; height: 42px;
        background: rgba(255,255,255,.15);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
 
    .ev-proyecto-banner-info strong {
        font-size: 15px;
        font-weight: 700;
        display: block;
        margin-bottom: 3px;
    }
 
    .ev-proyecto-banner-info span {
        font-size: 12px;
        opacity: .7;
    }
 
    .ev-proyecto-banner-badge {
        margin-left: auto;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        background: rgba(10,191,158,.25);
        color: var(--teal-light);
        border: 1px solid rgba(10,191,158,.3);
        white-space: nowrap;
    }
 
    /* ══════════════════════════════════════
       ESTADÍSTICAS RÁPIDAS
    ══════════════════════════════════════ */
    .ev-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 28px;
    }
 
    .ev-stat-card {
        background: white;
        border: 1px solid #edf0f4;
        border-radius: 14px;
        padding: 16px;
        text-align: center;
        transition: all .2s;
    }
 
    .ev-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,.06);
    }
 
    .ev-stat-icon {
        font-size: 22px;
        margin-bottom: 8px;
    }
 
    .ev-stat-num {
        font-size: 26px;
        font-weight: 800;
        color: var(--burg-deep);
        line-height: 1;
        margin-bottom: 4px;
    }
 
    .ev-stat-label {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
 
    /* ══════════════════════════════════════
       FORMULARIO AGREGAR EVIDENCIA
    ══════════════════════════════════════ */
    .ev-form-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 28px;
        display: none;
    }
 
    .ev-form-card.open {
        display: block;
        animation: evFadeIn .3s ease;
    }
 
    @keyframes evFadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
 
    .ev-form-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--burg-deep);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--teal);
        display: inline-block;
    }
 
    /* Tabs tipo evidencia */
    .ev-tipo-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 22px;
    }
 
    .ev-tipo-tab {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 14px 10px;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        background: white;
        cursor: pointer;
        transition: all .2s;
        font-family: inherit;
    }
 
    .ev-tipo-tab:hover {
        border-color: var(--teal);
        background: rgba(10,191,158,.04);
    }
 
    .ev-tipo-tab.active {
        border-color: var(--teal);
        background: rgba(10,191,158,.08);
    }
 
    .ev-tipo-tab-icon { font-size: 24px; }
    .ev-tipo-tab-label { font-size: 12px; font-weight: 700; color: #374151; }
    .ev-tipo-tab-desc  { font-size: 10px; color: #94a3b8; text-align: center; }
 
    /* Paneles de formulario por tipo */
    .ev-panel { display: none; }
    .ev-panel.active { display: block; animation: evFadeIn .2s ease; }
 
    /* ── Drop zone imágenes ── */
    .ev-dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 36px 24px;
        text-align: center;
        cursor: pointer;
        transition: all .2s;
        position: relative;
        background: #fafbfc;
    }
 
    .ev-dropzone:hover, .ev-dropzone.drag-over {
        border-color: var(--teal);
        background: rgba(10,191,158,.04);
    }
 
    .ev-dropzone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
 
    .ev-dropzone-icon { font-size: 40px; color: #cbd5e1; margin-bottom: 10px; }
    .ev-dropzone-text { font-size: 14px; color: #64748b; font-weight: 600; }
    .ev-dropzone-hint { font-size: 12px; color: #94a3b8; margin-top: 6px; }
 
    /* Preview imágenes nuevas */
    .ev-new-previews {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
    }
 
    .ev-new-thumb {
        position: relative;
        width: 90px; height: 90px;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #e2e8f0;
    }
 
    .ev-new-thumb img { width: 100%; height: 100%; object-fit: cover; }
 
    .ev-new-thumb-rm {
        position: absolute;
        top: 4px; right: 4px;
        width: 22px; height: 22px;
        background: rgba(239,68,68,.85);
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 11px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
 
    /* ── Panel enlace / repositorio ── */
    .ev-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 16px;
    }
 
    .ev-field label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
 
    .ev-field label span { color: var(--teal); }
 
    .ev-inp, .ev-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 14px;
        font-family: inherit;
        background: white;
        width: 100%;
        transition: all .2s;
    }
 
    .ev-inp:focus, .ev-select:focus {
        outline: none;
        border-color: var(--teal);
        box-shadow: 0 0 0 3px rgba(10,191,158,.1);
    }
 
    .ev-inp.ev-err { border-color: #ef4444; }
    .ev-err-msg { font-size: 11px; color: #ef4444; display: none; }
    .ev-err-msg.visible { display: block; }
 
    .ev-inp-icon-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
 
    .ev-inp-icon {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        font-size: 14px;
    }
 
    .ev-inp-icon-wrap .ev-inp { padding-left: 40px; }
 
    /* Form actions */
    .ev-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }
 
    .ev-btn-cancel {
        padding: 9px 24px;
        border-radius: 40px;
        border: 1.5px solid #e2e8f0;
        background: white;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all .2s;
    }
 
    .ev-btn-cancel:hover { background: #f1f5f9; }
 
    .ev-btn-save {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 28px;
        border-radius: 40px;
        border: none;
        background: linear-gradient(135deg, var(--teal), var(--teal-dim));
        color: white;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: all .2s;
    }
 
    .ev-btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(10,191,158,.4);
    }
 
    .ev-btn-save:disabled { opacity: .6; cursor: not-allowed; transform: none; }
 
    /* ══════════════════════════════════════
       FILTROS Y BUSCADOR
    ══════════════════════════════════════ */
    .ev-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        margin-bottom: 20px;
    }
 
    .ev-search-wrap {
        flex: 1;
        min-width: 200px;
        position: relative;
        display: flex;
        align-items: center;
    }
 
    .ev-search-icon {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        font-size: 13px;
    }
 
    .ev-search-inp {
        width: 100%;
        padding: 10px 14px 10px 40px;
        border: 1.5px solid #e2e8f0;
        border-radius: 40px;
        font-size: 13px;
        font-family: inherit;
        background: white;
        transition: all .2s;
    }
 
    .ev-search-inp:focus {
        outline: none;
        border-color: var(--teal);
        box-shadow: 0 0 0 3px rgba(10,191,158,.1);
    }
 
    .ev-filter-btns {
        display: flex;
        gap: 6px;
    }
 
    .ev-filter-btn {
        padding: 8px 16px;
        border-radius: 40px;
        border: 1.5px solid #e2e8f0;
        background: white;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
 
    .ev-filter-btn:hover { border-color: var(--teal); color: var(--teal); }
    .ev-filter-btn.active { background: var(--teal); border-color: var(--teal); color: white; }
 
    .ev-btn-agregar-ev {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        border-radius: 40px;
        border: none;
        background: linear-gradient(135deg, var(--teal), var(--teal-dim));
        color: white;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
        white-space: nowrap;
    }
 
    .ev-btn-agregar-ev:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(10,191,158,.45);
    }
 
    /* ══════════════════════════════════════
       GRID DE EVIDENCIAS
    ══════════════════════════════════════ */
    .ev-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
 
    /* Tarjeta imagen */
    .ev-card {
        background: white;
        border: 1px solid #edf0f4;
        border-radius: 16px;
        overflow: hidden;
        transition: all .2s;
        position: relative;
    }
 
    .ev-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,.08);
    }
 
    .ev-card-band { height: 3px; }
    .ev-card-band-img   { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
    .ev-card-band-link  { background: linear-gradient(90deg, var(--teal), var(--teal-light)); }
    .ev-card-band-repo  { background: linear-gradient(90deg, #f59e0b, #ef4444); }
 
    /* Imagen preview en la card */
    .ev-card-img-wrap {
        width: 100%;
        height: 160px;
        overflow: hidden;
        background: #f1f5f9;
        position: relative;
    }
 
    .ev-card-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .3s;
    }
 
    .ev-card:hover .ev-card-img-wrap img { transform: scale(1.04); }
 
    .ev-card-img-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,.0);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all .2s;
    }
 
    .ev-card:hover .ev-card-img-overlay { background: rgba(0,0,0,.35); }
 
    .ev-card-img-overlay a,
    .ev-card-img-overlay button {
        opacity: 0;
        transform: scale(.8);
        transition: all .2s;
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
        border: none;
        cursor: pointer;
    }
 
    .ev-card:hover .ev-card-img-overlay a,
    .ev-card:hover .ev-card-img-overlay button {
        opacity: 1;
        transform: scale(1);
    }
 
    .ev-card-img-overlay a { background: white; color: var(--teal); text-decoration: none; }
    .ev-card-img-overlay button { background: rgba(239,68,68,.85); color: white; }
 
    /* Cuerpo de la card */
    .ev-card-body { padding: 14px; }
 
    .ev-card-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 8px;
    }
 
    .ev-badge-img  { background: #ede9fe; color: #5b21b6; }
    .ev-badge-link { background: #d1fae5; color: #065f46; }
    .ev-badge-repo { background: #fef3c7; color: #92400e; }
 
    .ev-card-nombre {
        font-size: 14px;
        font-weight: 700;
        color: var(--burg-deep);
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
 
    .ev-card-url {
        font-size: 11px;
        color: var(--teal);
        text-decoration: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        margin-bottom: 8px;
    }
 
    .ev-card-url:hover { text-decoration: underline; }
 
    .ev-card-meta {
        font-size: 11px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #f1f5f9;
    }
 
    .ev-card-actions-row {
        display: flex;
        gap: 4px;
    }
 
    .ev-icon-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px 8px;
        border-radius: 8px;
        font-size: 12px;
        transition: background .2s;
        color: #64748b;
    }
 
    .ev-icon-btn:hover { background: #f1f5f9; }
    .ev-icon-btn.danger:hover { background: #fee2e2; color: #ef4444; }
 
    /* Card para enlace/repo (sin imagen) */
    .ev-card-link-icon {
        width: 100%;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }
 
    /* ══════════════════════════════════════
       ESTADO VACÍO
    ══════════════════════════════════════ */
    .ev-empty {
        text-align: center;
        padding: 60px 20px;
        background: #f8fafc;
        border-radius: 20px;
        border: 2px dashed #e2e8f0;
        grid-column: 1 / -1;
    }
 
    .ev-empty-icon { font-size: 48px; margin-bottom: 16px; }
 
    .ev-empty h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--burg-deep);
        margin-bottom: 8px;
    }
 
    .ev-empty p {
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 20px;
    }
 
    .ev-empty-btn {
        background: linear-gradient(135deg, var(--teal), var(--teal-dim));
        color: white;
        border: none;
        padding: 10px 28px;
        border-radius: 40px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
    }
 
    .ev-empty-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(10,191,158,.4);
    }
 
    /* ══════════════════════════════════════
       LIGHTBOX (ver imagen grande)
    ══════════════════════════════════════ */
    .ev-lightbox {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.85);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
 
    .ev-lightbox.open { display: flex; }
 
    .ev-lightbox-inner {
        position: relative;
        max-width: 90vw;
        max-height: 90vh;
    }
 
    .ev-lightbox-inner img {
        max-width: 100%;
        max-height: 85vh;
        border-radius: 12px;
        object-fit: contain;
        box-shadow: 0 24px 60px rgba(0,0,0,.5);
    }
 
    .ev-lightbox-close {
        position: absolute;
        top: -14px; right: -14px;
        width: 32px; height: 32px;
        background: white;
        border: none;
        border-radius: 50%;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,.3);
    }
 
    /* TOASTS */
    .ev-toast {
        position: fixed;
        bottom: 20px; right: 20px;
        background: #10b981;
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 500;
        z-index: 99999;
        animation: toastIn .3s ease forwards;
        font-size: 14px;
    }
 
    .ev-toast.error { background: #ef4444; }
 
    @keyframes toastIn {
        from { opacity: 0; transform: translateX(100px); }
        to   { opacity: 1; transform: translateX(0); }
    }
 
    @media (max-width: 640px) {
        .ev-stats { grid-template-columns: repeat(3, 1fr); }
        .ev-tipo-tabs { flex-direction: column; }
        .ev-controls { flex-direction: column; align-items: stretch; }
        .ev-filter-btns { flex-wrap: wrap; }
        .ev-grid { grid-template-columns: 1fr; }
    }
</style>
 
{{-- ══ BREADCRUMB ══════════════════════════════════ --}}
<div class="ev-breadcrumb">
    <a href="javascript:void(0)" id="evBtnVolver">
        <i class="fas fa-arrow-left"></i> Mis Proyectos
    </a>
    <span class="ev-breadcrumb-sep">›</span>
    <span id="evBreadcrumbNombre">Evidencias</span>
</div>
 
{{-- ══ HEADER ══════════════════════════════════════ --}}
<div class="ev-header">
    <div class="ev-title-wrap">
        <h2>Evidencias</h2>
        <p>Imágenes, enlaces y repositorios asociados al proyecto</p>
    </div>
    <button class="ev-btn-agregar-ev" id="evBtnMostrarForm">
        <i class="fas fa-plus"></i> Agregar Evidencia
    </button>
</div>
 
{{-- ══ BANNER DEL PROYECTO ═══════════════════════ --}}
<div class="ev-proyecto-banner" id="evProyectoBanner">
    <div class="ev-proyecto-banner-icon">📁</div>
    <div class="ev-proyecto-banner-info">
        <strong id="evBannerNombre">Cargando...</strong>
        <span id="evBannerDesc">—</span>
    </div>
    <span class="ev-proyecto-banner-badge" id="evBannerEstado">—</span>
</div>
 
{{-- ══ ESTADÍSTICAS ═══════════════════════════════ --}}
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
 
{{-- ══ FORMULARIO AGREGAR ══════════════════════════ --}}
<div class="ev-form-card" id="evFormCard">
    <div class="ev-form-title" id="evFormTitle">📎 Agregar Evidencia</div>
 
    {{-- Selector tipo de evidencia --}}
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
 
{{-- ══ CONTROLES / FILTROS ════════════════════════ --}}
<div class="ev-controls">
    <div class="ev-search-wrap">
        <i class="fas fa-search ev-search-icon"></i>
        <input type="text" id="evSearchInp" class="ev-search-inp" placeholder="Buscar evidencias...">
    </div>
    <div class="ev-filter-btns">
        <button class="ev-filter-btn active" data-filter="todos">
            <i class="fas fa-border-all"></i> Todos
        </button>
        <button class="ev-filter-btn" data-filter="imagen">
            <i class="fas fa-image"></i> Imágenes
        </button>
        <button class="ev-filter-btn" data-filter="enlace">
            <i class="fas fa-link"></i> Enlaces
        </button>
        <button class="ev-filter-btn" data-filter="repositorio">
            <i class="fab fa-github"></i> Repos
        </button>
    </div>
</div>
 
{{-- ══ GRID DE EVIDENCIAS ══════════════════════════ --}}
<div class="ev-grid" id="evGrid"></div>
 
{{-- ══ LIGHTBOX ════════════════════════════════════ --}}
<div class="ev-lightbox" id="evLightbox">
    <div class="ev-lightbox-inner">
        <img id="evLightboxImg" src="" alt="Evidencia">
        <button class="ev-lightbox-close" id="evLightboxClose">✕</button>
    </div>
</div>
 
<script>
(function() {
    // ─────────────────────────────────────────────────────
    // ESTADO
    // ─────────────────────────────────────────────────────
    let evidencias      = [];
    let proyectoActual  = null;   // objeto proyecto inyectado desde proyectos.blade.php
    let tipoActivo      = 'imagen';
    let archivosNuevos  = [];
    let filtroActivo    = 'todos';
    let busqueda        = '';
 
    // ─────────────────────────────────────────────────────
    // CSRF
    // ─────────────────────────────────────────────────────
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
 
    // ─────────────────────────────────────────────────────
    // REFS DOM
    // ─────────────────────────────────────────────────────
    const formCard      = document.getElementById('evFormCard');
    const btnMostrar    = document.getElementById('evBtnMostrarForm');
    const btnCancelar   = document.getElementById('evBtnCancelar');
    const btnGuardar    = document.getElementById('evBtnGuardar');
    const grid          = document.getElementById('evGrid');
    const dropzone      = document.getElementById('evDropzone');
    const fileInput     = document.getElementById('evFileInput');
    const newPreviews   = document.getElementById('evNewPreviews');
    const searchInp     = document.getElementById('evSearchInp');
    const lightbox      = document.getElementById('evLightbox');
    const lightboxImg   = document.getElementById('evLightboxImg');
    const lightboxClose = document.getElementById('evLightboxClose');
 
    // ─────────────────────────────────────────────────────
    // API PÚBLICA — llamar desde proyectos.blade.php
    // window.evInit(proyecto) al hacer clic en "Agregar Evidencias"
    // ─────────────────────────────────────────────────────
    window.evInit = function(proyecto) {
        proyectoActual = proyecto;
        // Llenar banner
        document.getElementById('evBannerNombre').textContent = proyecto.nombre || '—';
        document.getElementById('evBannerDesc').textContent   = proyecto.descripcion
            ? (proyecto.descripcion.length > 80
                ? proyecto.descripcion.slice(0, 80) + '…'
                : proyecto.descripcion)
            : '—';
        document.getElementById('evBannerEstado').textContent = proyecto.estado || '—';
        document.getElementById('evBreadcrumbNombre').textContent = proyecto.nombre || 'Evidencias';
        cargarEvidencias();
    };
 
    // ─────────────────────────────────────────────────────
    // TABS DE TIPO
    // ─────────────────────────────────────────────────────
    document.querySelectorAll('.ev-tipo-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.ev-tipo-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.ev-panel').forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            tipoActivo = tab.dataset.tipo;
            document.getElementById('evPanel' + cap(tipoActivo)).classList.add('active');
            limpiarErrores();
        });
    });
 
    // ─────────────────────────────────────────────────────
    // DROPZONE
    // ─────────────────────────────────────────────────────
    dropzone?.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('drag-over'); });
    dropzone?.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
    dropzone?.addEventListener('drop', e => {
        e.preventDefault();
        dropzone.classList.remove('drag-over');
        agregarArchivos([...e.dataTransfer.files]);
    });
    fileInput?.addEventListener('change', () => { agregarArchivos([...fileInput.files]); fileInput.value = ''; });
 
    function agregarArchivos(files) {
        files.forEach(f => {
            if (!['image/jpeg','image/jpg','image/png'].includes(f.type)) {
                toast(`❌ "${f.name}" no es JPG/PNG`, 'error'); return;
            }
            if (f.size > 5 * 1024 * 1024) {
                toast(`❌ "${f.name}" supera 5 MB`, 'error'); return;
            }
            archivosNuevos.push(f);
            const reader = new FileReader();
            reader.onload = ev => agregarThumb(f, ev.target.result);
            reader.readAsDataURL(f);
        });
    }
 
    function agregarThumb(file, src) {
        const wrap = document.createElement('div');
        wrap.className = 'ev-new-thumb';
        wrap.innerHTML = `<img src="${src}" alt="${esc(file.name)}"><button class="ev-new-thumb-rm" title="Quitar">✕</button>`;
        wrap.querySelector('button').addEventListener('click', () => {
            archivosNuevos = archivosNuevos.filter(f => f !== file);
            wrap.remove();
        });
        newPreviews?.appendChild(wrap);
    }
 
    // ─────────────────────────────────────────────────────
    // FORMULARIO — mostrar / ocultar / limpiar
    // ─────────────────────────────────────────────────────
    btnMostrar?.addEventListener('click', () => {
        formCard.classList.add('open');
        document.getElementById('evFormTitle').textContent = '📎 Agregar Evidencia';
    });
 
    btnCancelar?.addEventListener('click', ocultarForm);
 
    function ocultarForm() {
        formCard.classList.remove('open');
        limpiarForm();
    }
 
    function limpiarForm() {
        archivosNuevos = [];
        newPreviews.innerHTML = '';
        tipoActivo = 'imagen';
        document.querySelectorAll('.ev-tipo-tab').forEach((t, i) => { t.classList.toggle('active', i === 0); });
        document.querySelectorAll('.ev-panel').forEach((p, i) => { p.classList.toggle('active', i === 0); });
        ['evImgNombre','evLinkNombre','evLinkUrl','evLinkDesc','evRepoNombre','evRepoUrl','evRepoDesc'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        const sel = document.getElementById('evRepoPlataforma');
        if (sel) sel.value = 'GitHub';
        limpiarErrores();
    }
 
    function limpiarErrores() {
        document.querySelectorAll('.ev-err-msg').forEach(e => e.classList.remove('visible'));
        document.querySelectorAll('.ev-inp.ev-err').forEach(e => e.classList.remove('ev-err'));
    }
 
    // ─────────────────────────────────────────────────────
    // GUARDAR
    // ─────────────────────────────────────────────────────
    btnGuardar?.addEventListener('click', guardarEvidencia);
 
    async function guardarEvidencia() {
        limpiarErrores();
        if (!proyectoActual) { toast('❌ No hay proyecto seleccionado', 'error'); return; }
 
        let isValid = true;
 
        if (tipoActivo === 'imagen') {
            const nombre = document.getElementById('evImgNombre').value.trim();
            if (!nombre) { setErr('evImgNombre', 'evErrImgNombre'); isValid = false; }
            if (archivosNuevos.length === 0) {
                document.getElementById('evErrImgFile').classList.add('visible'); isValid = false;
            }
            if (!isValid) return;
 
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
 
            const fd = new FormData();
            fd.append('tipo', 'imagen');
            fd.append('nombre', nombre);
            fd.append('proyecto_id', proyectoActual.id);
            archivosNuevos.forEach(f => fd.append('imagenes[]', f));
 
            await enviar(fd);
 
        } else if (tipoActivo === 'enlace') {
            const nombre = document.getElementById('evLinkNombre').value.trim();
            const url    = document.getElementById('evLinkUrl').value.trim();
            const desc   = document.getElementById('evLinkDesc').value.trim();
            if (!nombre) { setErr('evLinkNombre', 'evErrLinkNombre'); isValid = false; }
            if (!url || !isValidUrl(url)) { setErr('evLinkUrl', 'evErrLinkUrl'); isValid = false; }
            if (!isValid) return;
 
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
 
            const fd = new FormData();
            fd.append('tipo', 'enlace');
            fd.append('etiqueta', nombre);
            fd.append('url', url);
            fd.append('descripcion', desc);
            fd.append('proyecto_id', proyectoActual.id);
 
            await enviar(fd);
 
        } else {
            const nombre      = document.getElementById('evRepoNombre').value.trim();
            const url         = document.getElementById('evRepoUrl').value.trim();
            const plataforma  = document.getElementById('evRepoPlataforma').value;
            const desc        = document.getElementById('evRepoDesc').value.trim();
            if (!nombre) { setErr('evRepoNombre', 'evErrRepoNombre'); isValid = false; }
            if (!url || !isValidUrl(url)) { setErr('evRepoUrl', 'evErrRepoUrl'); isValid = false; }
            if (!isValid) return;
 
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
 
            const fd = new FormData();
            fd.append('tipo', 'repositorio');
            fd.append('etiqueta', nombre);
            fd.append('url', url);
            fd.append('plataforma', plataforma);
            fd.append('descripcion', desc);
            fd.append('proyecto_id', proyectoActual.id);
 
            await enviar(fd);
        }
    }
 
    async function enviar(fd) {
        try {
            const res  = await fetch(`/proyectos/${proyectoActual.id}/evidencias`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: fd,
            });
            const data = await res.json();
            if (!res.ok) {
                const errs = data.errors ? Object.values(data.errors).flat() : [data.message || 'Error'];
                toast('❌ ' + errs[0], 'error');
                return;
            }
            // Agregar a la lista local
            if (Array.isArray(data.evidencias)) {
                evidencias.push(...data.evidencias);
            } else if (data.evidencia) {
                evidencias.push(data.evidencia);
            }
            toast('✅ Evidencia guardada');
            ocultarForm();
            renderizar();
        } catch {
            toast('❌ Error de conexión', 'error');
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar evidencia';
        }
    }
 
    // ─────────────────────────────────────────────────────
    // ELIMINAR
    // ─────────────────────────────────────────────────────
    async function eliminarEvidencia(id) {
        if (!confirm('¿Eliminar esta evidencia? No se puede deshacer.')) return;
        try {
            const res = await fetch(`/evidencias/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            if (!res.ok) throw new Error();
            evidencias = evidencias.filter(e => e.id !== id);
            toast('Evidencia eliminada');
            renderizar();
        } catch {
            toast('❌ No se pudo eliminar', 'error');
        }
    }
 
    // ─────────────────────────────────────────────────────
    // CARGAR desde API
    // ─────────────────────────────────────────────────────
    async function cargarEvidencias() {
        if (!proyectoActual) return;
        grid.innerHTML = '<p style="color:#94a3b8;text-align:center;padding:30px;grid-column:1/-1">Cargando evidencias...</p>';
        try {
            const res = await fetch(`/proyectos/${proyectoActual.id}/evidencias`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            evidencias = await res.json();
            renderizar();
        } catch {
            grid.innerHTML = '<p style="color:#ef4444;text-align:center;padding:30px;grid-column:1/-1">❌ No se pudieron cargar las evidencias</p>';
        }
    }
 
    // ─────────────────────────────────────────────────────
    // FILTROS Y BÚSQUEDA
    // ─────────────────────────────────────────────────────
    document.querySelectorAll('.ev-filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.ev-filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filtroActivo = btn.dataset.filter;
            renderizar();
        });
    });
 
    searchInp?.addEventListener('input', () => {
        busqueda = searchInp.value.trim().toLowerCase();
        renderizar();
    });
 
    function filtradas() {
        return evidencias.filter(e => {
            const matchTipo = filtroActivo === 'todos' || e.tipo === filtroActivo;
            const texto = ((e.etiqueta || '') + ' ' + (e.archivo_nombre || '') + ' ' + (e.url_publica || '') + ' ' + (e.descripcion || '')).toLowerCase();
            const matchBusqueda = !busqueda || texto.includes(busqueda);
            return matchTipo && matchBusqueda;
        });
    }
 
    // ─────────────────────────────────────────────────────
    // RENDERIZAR GRID
    // ─────────────────────────────────────────────────────
    function renderizar() {
        // Stats
        document.getElementById('evStatImgs').textContent  = evidencias.filter(e => e.tipo === 'imagen').length;
        document.getElementById('evStatLinks').textContent = evidencias.filter(e => e.tipo === 'enlace').length;
        document.getElementById('evStatRepos').textContent = evidencias.filter(e => e.tipo === 'repositorio').length;
 
        if (!grid) return;
 
        const lista = filtradas();
 
        if (lista.length === 0) {
            grid.innerHTML = `
                <div class="ev-empty">
                    <div class="ev-empty-icon">📎</div>
                    <h3>Sin evidencias${filtroActivo !== 'todos' ? ' en esta categoría' : ''}</h3>
                    <p>${busqueda ? 'No se encontraron resultados para tu búsqueda.' : 'Agrega imágenes, enlaces o repositorios a este proyecto.'}</p>
                    ${!busqueda && filtroActivo === 'todos' ? '<button class="ev-empty-btn" id="evEmptyBtn">+ Agregar primera evidencia</button>' : ''}
                </div>`;
            document.getElementById('evEmptyBtn')?.addEventListener('click', () => {
                formCard.classList.add('open');
            });
            return;
        }
 
        grid.innerHTML = '';
 
        lista.forEach(ev => {
            const card = document.createElement('div');
            card.className = 'ev-card';
 
            if (ev.tipo === 'imagen') {
                card.innerHTML = `
                    <div class="ev-card-band ev-card-band-img"></div>
                    <div class="ev-card-img-wrap">
                        <img src="${esc(ev.url_publica)}" alt="${esc(ev.archivo_nombre || 'imagen')}">
                        <div class="ev-card-img-overlay">
                            <a href="${esc(ev.url_publica)}" target="_blank" title="Abrir"><i class="fas fa-expand"></i></a>
                            <button class="btn-lightbox" data-src="${esc(ev.url_publica)}" title="Ver"><i class="fas fa-eye"></i></button>
                            <button class="btn-del" data-id="${ev.id}" style="margin-left:4px"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    <div class="ev-card-body">
                        <span class="ev-card-type-badge ev-badge-img"><i class="fas fa-image"></i> Imagen</span>
                        <div class="ev-card-nombre">${esc(ev.etiqueta || ev.archivo_nombre || 'Imagen')}</div>
                        <div class="ev-card-meta">
                            <span>${esc(ev.archivo_nombre || '')}</span>
                            <div class="ev-card-actions-row">
                                <button class="ev-icon-btn danger btn-del" data-id="${ev.id}" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                    </div>`;
 
                card.querySelectorAll('.btn-lightbox').forEach(b => {
                    b.addEventListener('click', () => abrirLightbox(b.dataset.src));
                });
 
            } else if (ev.tipo === 'enlace') {
                card.innerHTML = `
                    <div class="ev-card-band ev-card-band-link"></div>
                    <div class="ev-card-link-icon">🔗</div>
                    <div class="ev-card-body">
                        <span class="ev-card-type-badge ev-badge-link"><i class="fas fa-link"></i> Enlace</span>
                        <div class="ev-card-nombre">${esc(ev.etiqueta || 'Enlace')}</div>
                        <a class="ev-card-url" href="${esc(ev.url_publica)}" target="_blank">${esc(ev.url_publica)}</a>
                        ${ev.descripcion ? `<div style="font-size:12px;color:#64748b">${esc(ev.descripcion)}</div>` : ''}
                        <div class="ev-card-meta">
                            <span style="color:#94a3b8">Enlace externo</span>
                            <div class="ev-card-actions-row">
                                <a class="ev-icon-btn" href="${esc(ev.url_publica)}" target="_blank" title="Abrir"><i class="fas fa-external-link-alt"></i></a>
                                <button class="ev-icon-btn danger btn-del" data-id="${ev.id}" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                    </div>`;
 
            } else {
                const plataformaIcon = (ev.plataforma === 'GitLab') ? 'fa-gitlab' : (ev.plataforma === 'Bitbucket') ? 'fa-bitbucket' : 'fa-github';
                card.innerHTML = `
                    <div class="ev-card-band ev-card-band-repo"></div>
                    <div class="ev-card-link-icon"><i class="fab ${plataformaIcon}" style="font-size:32px;color:#374151"></i></div>
                    <div class="ev-card-body">
                        <span class="ev-card-type-badge ev-badge-repo"><i class="fab ${plataformaIcon}"></i> ${esc(ev.plataforma || 'Repositorio')}</span>
                        <div class="ev-card-nombre">${esc(ev.etiqueta || 'Repositorio')}</div>
                        <a class="ev-card-url" href="${esc(ev.url_publica)}" target="_blank">${esc(ev.url_publica)}</a>
                        ${ev.descripcion ? `<div style="font-size:12px;color:#64748b">${esc(ev.descripcion)}</div>` : ''}
                        <div class="ev-card-meta">
                            <span style="color:#94a3b8">${esc(ev.plataforma || 'Git')}</span>
                            <div class="ev-card-actions-row">
                                <a class="ev-icon-btn" href="${esc(ev.url_publica)}" target="_blank" title="Abrir repo"><i class="fas fa-external-link-alt"></i></a>
                                <button class="ev-icon-btn danger btn-del" data-id="${ev.id}" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                    </div>`;
            }
 
            card.querySelectorAll('.btn-del').forEach(b => {
                b.addEventListener('click', () => eliminarEvidencia(parseInt(b.dataset.id)));
            });
 
            grid.appendChild(card);
        });
    }
 
    // ─────────────────────────────────────────────────────
    // LIGHTBOX
    // ─────────────────────────────────────────────────────
    function abrirLightbox(src) {
        lightboxImg.src = src;
        lightbox.classList.add('open');
    }
 
    lightboxClose?.addEventListener('click', () => lightbox.classList.remove('open'));
    lightbox?.addEventListener('click', e => { if (e.target === lightbox) lightbox.classList.remove('open'); });
 
    // ─────────────────────────────────────────────────────
    // BOTÓN VOLVER (comunica con proyectos.blade.php)
    // ─────────────────────────────────────────────────────
    document.getElementById('evBtnVolver')?.addEventListener('click', () => {
        // Disparar evento para que proyectos.blade.php muestre su vista
        document.dispatchEvent(new CustomEvent('ev:volver'));
    });
 
    // ─────────────────────────────────────────────────────
    // UTILIDADES
    // ─────────────────────────────────────────────────────
    function setErr(inputId, errId) {
        document.getElementById(inputId)?.classList.add('ev-err');
        document.getElementById(errId)?.classList.add('visible');
    }
 
    function cap(str) { return str.charAt(0).toUpperCase() + str.slice(1); }
 
    function isValidUrl(str) {
        try { new URL(str); return true; } catch { return false; }
    }
 
    function esc(str) {
        if (!str) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
 
    function toast(msg, tipo) {
        const el = document.createElement('div');
        el.className = 'ev-toast' + (tipo === 'error' ? ' error' : '');
        el.textContent = msg;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3200);
    }
})();
</script>