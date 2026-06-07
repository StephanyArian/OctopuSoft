<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>{{ $user->first_name ?? 'Portafolio' }} {{ $user->last_name ?? '' }} | Portafolio Profesional</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/preview.css') }}?v={{ time() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        /* ===== BOTONES FLOTANTES ===== */
        .btn-volver-flotante {
            position: fixed; top: 20px; left: 20px; z-index: 999;
            background: #0abf9e; border: none; color: white;
            padding: 10px 20px; border-radius: 40px; cursor: pointer;
            font-size: 14px; font-weight: 600; display: flex; align-items: center;
            gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.2s; font-family: inherit; text-decoration: none;
        }
        .btn-volver-flotante:hover { transform: translateY(-2px); background: #07866e; }
        .fab-container-top { position: fixed; top: 20px; right: 20px; z-index: 999; display: flex; flex-direction: column; align-items: flex-end; gap: 0; }
        .fab-button-top { background: #fff; color: #4a1030; border: 2px solid #4a1030; padding: 10px 18px; border-radius: 40px; cursor: pointer; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 9px; box-shadow: 0 4px 16px rgba(0,0,0,0.15); transition: all 0.25s ease; font-family: inherit; letter-spacing: 0.3px; position: relative; z-index: 1; }
        .fab-button-top:hover { background: #4a1030; color: white; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.2); }
        .fab-menu-top { position: absolute; top: calc(100% + 10px); right: 0; background: #fff; border-radius: 14px; box-shadow: 0 8px 30px rgba(0,0,0,0.18); border: 1px solid #edf0f4; min-width: 200px; overflow: hidden; opacity: 0; transform: translateY(-10px) scale(0.97); pointer-events: none; transition: opacity 0.2s ease, transform 0.2s ease; }
        .fab-menu-top.open { opacity: 1; transform: translateY(0) scale(1); pointer-events: all; }
        .fab-item-top { display: flex; align-items: center; gap: 12px; width: 100%; padding: 12px 18px; background: none; border: none; font-family: inherit; font-size: 13px; font-weight: 600; color: #2d0a1e; cursor: pointer; transition: background 0.15s, color 0.15s; text-decoration: none; }
        .fab-item-top:hover { background: #f5f6f8; color: #07866e; }
        .fab-item-top i { width: 20px; text-align: center; font-size: 14px; color: #0abf9e; flex-shrink: 0; }
        .fab-divider-top { height: 1px; background: #edf0f4; margin: 0; }

        /* ===== BOTÓN VER TODOS ===== */
        .btn-ver-todos { display: flex; justify-content: center; margin-top: 30px; }
        .btn-ver-todos button { background: #0abf9e; color: white; border: none; padding: 10px 24px; border-radius: 40px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s; }
        .btn-ver-todos button:hover { background: #07866e; transform: translateY(-2px); }

        /* ===== CARDS PRINCIPALES ===== */
        .card { transition: all 0.25s ease; cursor: pointer; border: 1px solid #e2e8f0; background: white; position: relative; }
        .card:hover { border-color: #0abf9e; box-shadow: 0 12px 28px -10px rgba(10,191,158,0.25); transform: translateY(-4px); }
        .card:hover h3 { color: #0abf9e !important; transform: translateX(3px); }
        .card h3 { transition: all 0.2s; display: inline-block; }

        /* ===== LIGHTBOX ===== */
        #lightbox-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 20000; align-items: center; justify-content: center; cursor: pointer; }

        /* ===== BARRA IDIOMAS ===== */
        .idioma-barra-wrap { height: 8px; background: #edf0f4; border-radius: 10px; overflow: hidden; margin-top: 10px; }
        .idioma-barra-fill-custom { height: 100%; border-radius: 10px; transition: width 0.8s ease; }

        /* ===== MODALES BASE ===== */
        .modal-overlay {
            display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65);
            z-index: 11000; align-items: center; justify-content: center;
            backdrop-filter: blur(3px);
        }
        .modal-box {
            background: #fff; border-radius: 20px; max-width: 960px; width: 92%;
            max-height: 88vh; overflow-y: auto; position: relative;
            animation: mFadeIn 0.22s ease;
        }
        @keyframes mFadeIn { from { opacity:0; transform: scale(0.96) translateY(10px); } to { opacity:1; transform: scale(1) translateY(0); } }
        .modal-header {
            padding: 20px 28px; border-bottom: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; background: white; z-index: 10;
        }
        .modal-header h2 { margin: 0; font-size: 1.15rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 10px; }
        .modal-header h2 i { color: #0abf9e; }
        .modal-close-btn { background: none; border: none; font-size: 22px; cursor: pointer; color: #94a3b8; transition: color 0.2s; line-height: 1; padding: 0; }
        .modal-close-btn:hover { color: #ef4444; }

        /* ===== MODAL PROYECTOS — FOLDER CARDS ===== */
        .projects-modal-body { padding: 28px; display: flex; flex-direction: column; gap: 28px; }
        .projects-folder-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .folder-card {
            background: #fff; border: 1.5px solid #e2e8f0; border-radius: 18px;
            overflow: hidden; cursor: pointer; transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .folder-card:hover { transform: translateY(-5px); box-shadow: 0 16px 32px -8px rgba(10,191,158,0.2); border-color: #0abf9e; }
        .folder-card-top {
            height: 80px; position: relative; display: flex; align-items: flex-end;
            padding: 0 18px 14px;
        }
        .folder-card-tab {
            position: absolute; top: 0; left: 18px; width: 60px; height: 20px;
            border-radius: 8px 8px 0 0;
        }
        .folder-card-icon {
            width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: white; backdrop-filter: blur(4px);
        }
        .folder-card-status {
            margin-left: auto; padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.03em;
        }
        .folder-card-body { padding: 16px 18px 18px; }
        .folder-card-body h4 { margin: 0 0 6px; font-size: 0.95rem; font-weight: 700; color: #0f172a; line-height: 1.3; }
        .folder-card-meta { font-size: 11px; color: #94a3b8; display: flex; align-items: center; gap: 6px; margin-bottom: 10px; }
        .folder-card-meta i { font-size: 10px; color: #0abf9e; }
        .folder-card-desc { font-size: 12px; color: #64748b; line-height: 1.55; margin-bottom: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .folder-card-chips { display: flex; flex-wrap: wrap; gap: 5px; }
        .folder-chip { background: #f0fdf9; color: #0abf9e; border: 1px solid #d1fae5; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; }
        .folder-chip-more { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; }
        .folder-card-footer { padding: 12px 18px; border-top: 1px solid #f0f4f8; display: flex; align-items: center; justify-content: space-between; background: #fafbfc; }
        .folder-card-rol { font-size: 11px; color: #64748b; display: flex; align-items: center; gap: 5px; }
        .folder-card-rol i { color: #0abf9e; font-size: 10px; }
        .folder-card-arrow { width: 28px; height: 28px; border-radius: 50%; background: #f0fdf9; border: 1px solid #d1fae5; display: flex; align-items: center; justify-content: center; color: #0abf9e; font-size: 11px; transition: all 0.2s; }
        .folder-card:hover .folder-card-arrow { background: #0abf9e; color: white; border-color: #0abf9e; }

        /* ===== TIMELINE MODAL ===== */
        .timeline-container { padding: 32px 28px; }
        .timeline-track { position: relative; padding-left: 48px; }
        .timeline-track::before { content: ''; position: absolute; left: 18px; top: 0; bottom: 0; width: 2px; background: linear-gradient(180deg, #0abf9e 0%, #e2e8f0 100%); }
        .timeline-item { position: relative; margin-bottom: 24px; }
        .timeline-item:last-child { margin-bottom: 0; }
        .timeline-dot {
            position: absolute; left: -39px; top: 18px;
            width: 20px; height: 20px; border-radius: 50%;
            background: #fff; border: 3px solid #0abf9e;
            display: flex; align-items: center; justify-content: center;
            z-index: 2; transition: all 0.2s;
        }
        .timeline-dot-inner { width: 8px; height: 8px; border-radius: 50%; background: #0abf9e; }
        .timeline-card {
            background: #fff; border: 1.5px solid #e2e8f0; border-radius: 16px;
            padding: 18px 20px; cursor: pointer; transition: all 0.22s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .timeline-card:hover { border-color: #0abf9e; box-shadow: 0 8px 24px -6px rgba(10,191,158,0.2); transform: translateX(4px); }
        .timeline-card:hover .timeline-dot { background: #0abf9e; box-shadow: 0 0 0 4px rgba(10,191,158,0.15); }
        .timeline-card:hover .timeline-dot-inner { background: #fff; }
        .timeline-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 8px; }
        .timeline-card-title { font-size: 0.95rem; font-weight: 700; color: #0f172a; margin: 0 0 3px; }
        .timeline-card-sub { font-size: 12px; color: #0abf9e; font-weight: 600; }
        .timeline-card-date { font-size: 11px; color: #94a3b8; white-space: nowrap; background: #f8fafc; padding: 3px 10px; border-radius: 20px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 4px; flex-shrink: 0; }
        .timeline-card-loc { font-size: 11px; color: #64748b; display: flex; align-items: center; gap: 5px; margin-bottom: 8px; }
        .timeline-card-desc-preview { font-size: 12px; color: #64748b; line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .timeline-card-hint { font-size: 11px; color: #0abf9e; margin-top: 8px; display: flex; align-items: center; gap: 4px; font-weight: 600; }

        /* ===== MODAL DETALLE (exp / aca) ===== */
        .detail-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 12000; align-items: center; justify-content: center; backdrop-filter: blur(2px); }
        .detail-modal-box { background: #fff; border-radius: 20px; max-width: 640px; width: 92%; max-height: 88vh; overflow-y: auto; animation: mFadeIn 0.2s ease; }
        .detail-modal-hero { padding: 24px 28px 20px; border-bottom: 1px solid #e2e8f0; position: relative; }
        .detail-modal-hero-badge { display: inline-flex; align-items: center; gap: 6px; background: #f0fdf9; color: #0abf9e; border: 1px solid #d1fae5; font-size: 11px; font-weight: 700; padding: 3px 12px; border-radius: 20px; margin-bottom: 10px; }
        .detail-modal-hero h3 { margin: 0 0 4px; font-size: 1.2rem; font-weight: 700; color: #0f172a; }
        .detail-modal-hero .sub { font-size: 14px; color: #0abf9e; font-weight: 600; margin-bottom: 12px; }
        .detail-modal-meta-row { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
        .detail-meta-pill { display: inline-flex; align-items: center; gap: 5px; background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b; font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 20px; }
        .detail-meta-pill i { color: #0abf9e; font-size: 10px; }
        .detail-modal-close { position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 20px; cursor: pointer; color: #94a3b8; transition: color 0.2s; }
        .detail-modal-close:hover { color: #ef4444; }
        .detail-modal-body { padding: 24px 28px; display: flex; flex-direction: column; gap: 20px; }
        .detail-section-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
        .detail-section-label i { color: #0abf9e; }
        .detail-desc-wrap { font-size: 13px; color: #475569; line-height: 1.75; }
        .detail-desc-wrap .ql-snow { border: none !important; }
        .detail-desc-wrap .ql-editor { padding: 0 !important; min-height: 0 !important; }
        .detail-certs-wrap { display: flex; flex-wrap: wrap; gap: 8px; padding-top: 16px; border-top: 1px solid #f0f0f0; }

        /* ===== MISC ===== */
        .modal-search-bar { padding: 0 28px 20px; }
        .modal-search-bar input { width: 100%; padding: 10px 16px; border: 1.5px solid #e2e8f0; border-radius: 40px; font-size: 13px; outline: none; font-family: inherit; transition: border-color 0.2s; box-sizing: border-box; }
        .modal-search-bar input:focus { border-color: #0abf9e; }
        .modal-count-badge { background: #f0fdf9; color: #0abf9e; border: 1px solid #d1fae5; font-size: 12px; font-weight: 700; padding: 3px 12px; border-radius: 20px; }
        .empty-state { text-align: center; padding: 40px; color: #94a3b8; font-size: 14px; }
        .empty-state i { font-size: 32px; margin-bottom: 10px; display: block; }

        /* ===== BOTONES DE CERTIFICADO ===== */
        .cert-btn {
            display: inline-flex; align-items: center; gap: 6px; color: #0abf9e;
            font-size: 12px; font-weight: 600; text-decoration: none;
            padding: 6px 14px; border-radius: 20px; background: #f0fdf9;
            border: 1px solid #d1fae5; transition: all 0.2s; cursor: pointer;
            font-family: inherit;
        }
        .cert-btn:hover { background: #d1fae5; transform: translateY(-1px); }

        /* ===== MODAL IDIOMAS - ESTILOS MEJORADOS ===== */
        .idiomas-modal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 24px;
        }
        .idioma-modal-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px;
            transition: all 0.2s ease;
        }
        .idioma-modal-card:hover {
            border-color: #0abf9e;
            box-shadow: 0 8px 20px -6px rgba(10,191,158,0.15);
            transform: translateY(-2px);
        }
        .idioma-modal-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        .idioma-modal-flag {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: #f0fdf9;
        }
        .idioma-modal-code {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: white;
        }
        .idioma-modal-info {
            flex: 1;
        }
        .idioma-modal-name {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .idioma-modal-level {
            font-size: 11px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .idioma-modal-level i {
            color: #0abf9e;
            font-size: 10px;
        }
        .idioma-modal-bar {
            margin: 12px 0;
        }
        .idioma-modal-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f0f0f0;
        }

        @media (max-width: 768px) {
            .btn-volver-flotante { top: 10px; left: 10px; padding: 8px 16px; font-size: 12px; }
            .fab-container-top { top: 10px; right: 10px; }
            .fab-button-top { padding: 8px 14px; font-size: 12px; }
            .projects-folder-grid { grid-template-columns: 1fr; }
            .timeline-container { padding: 20px 16px; }
            .detail-modal-hero, .detail-modal-body { padding: 18px; }
            .modal-header { padding: 16px 18px; }
            .idiomas-modal-grid { grid-template-columns: 1fr; padding: 16px; }
            
            .top-actions-bar {
                padding: 10px 20px !important;
            }
        }

        /* ===== BARRA DE ACCIONES SUPERIOR ===== */
        .top-actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 40px;
            background: #ffffff;
            border-bottom: 1px solid var(--gray-100);
            position: sticky;
            top: 0;
            z-index: 1001;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .top-actions-bar .btn-volver-flotante {
            position: relative;
            top: auto;
            left: auto;
            z-index: auto;
            margin: 0;
            box-shadow: none;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            transition: all 0.2s ease;
        }
        .top-actions-bar .btn-volver-flotante:hover {
            background: #f0fdf9;
            color: #0abf9e;
            border-color: rgba(10, 191, 158, 0.3);
            transform: translateY(-1px);
        }
        .top-actions-bar .btn-volver-flotante svg {
            stroke: #475569;
            transition: stroke 0.2s ease;
        }
        .top-actions-bar .btn-volver-flotante:hover svg {
            stroke: #0abf9e;
        }
        
        /* Theme overrides for hover state of Volver button */
        .theme-sunset.top-actions-bar .btn-volver-flotante:hover { background: #fff7ed; color: #f97316; border-color: rgba(249, 115, 22, 0.3); }
        .theme-sunset.top-actions-bar .btn-volver-flotante:hover svg { stroke: #f97316; }
        
        .theme-emerald.top-actions-bar .btn-volver-flotante:hover { background: #ecfdf5; color: #10b981; border-color: rgba(16, 185, 129, 0.3); }
        .theme-emerald.top-actions-bar .btn-volver-flotante:hover svg { stroke: #10b981; }
        
        .theme-midnight.top-actions-bar .btn-volver-flotante:hover { background: #fdf4ff; color: #a855f7; border-color: rgba(168, 85, 247, 0.3); }
        .theme-midnight.top-actions-bar .btn-volver-flotante:hover svg { stroke: #a855f7; }
        
        .theme-ocean.top-actions-bar .btn-volver-flotante:hover { background: #f0fdfa; color: #00b4d8; border-color: rgba(0, 180, 216, 0.3); }
        .theme-ocean.top-actions-bar .btn-volver-flotante:hover svg { stroke: #00b4d8; }
        
        .theme-sakura.top-actions-bar .btn-volver-flotante:hover { background: #fdf2f8; color: #ec4899; border-color: rgba(236, 72, 153, 0.3); }
        .theme-sakura.top-actions-bar .btn-volver-flotante:hover svg { stroke: #ec4899; }

        .top-actions-bar .fab-container-top {
            position: relative;
            top: auto;
            right: auto;
            z-index: auto;
        }
        .top-actions-bar .fab-button-top {
            position: relative;
            box-shadow: none;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            padding: 10px 18px;
            border-radius: 40px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 9px;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        .top-actions-bar .fab-button-top:hover {
            background: #f0fdf9;
            color: #0abf9e;
            border-color: rgba(10, 191, 158, 0.3);
            transform: translateY(-1px);
        }
        
        .theme-sunset.top-actions-bar .fab-button-top:hover { background: #fff7ed; color: #f97316; border-color: rgba(249, 115, 22, 0.3); }
        .theme-emerald.top-actions-bar .fab-button-top:hover { background: #ecfdf5; color: #10b981; border-color: rgba(16, 185, 129, 0.3); }
        .theme-midnight.top-actions-bar .fab-button-top:hover { background: #fdf4ff; color: #a855f7; border-color: rgba(168, 85, 247, 0.3); }
        .theme-ocean.top-actions-bar .fab-button-top:hover { background: #f0fdfa; color: #00b4d8; border-color: rgba(0, 180, 216, 0.3); }
        .theme-sakura.top-actions-bar .fab-button-top:hover { background: #fdf2f8; color: #ec4899; border-color: rgba(236, 72, 153, 0.3); }

        .top-actions-bar .fab-menu-top {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            left: auto;
            bottom: auto;
            z-index: 1002;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
            margin-bottom: 0;
            margin-top: 0;
            pointer-events: none;
            opacity: 0;
            transform: translateY(-10px) scale(0.97);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .top-actions-bar .fab-menu-top.open {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: all;
        }
        /* ===== RESTAURAR FLOTANTES PÚBLICO ===== */
.btn-volver-flotante {
    position: fixed !important;
    top: 20px !important;
    left: 20px !important;
    z-index: 1100 !important;
    background: #0abf9e !important;
    border: none !important;
    color: white !important;
    padding: 10px 20px !important;
    border-radius: 40px !important;
    cursor: pointer !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
    transition: all 0.2s !important;
    font-family: inherit !important;
    text-decoration: none !important;
}
.btn-volver-flotante:hover { transform: translateY(-2px) !important; background: #07866e !important; }

.fab-container-top {
    position: fixed !important;
    bottom: 30px !important;
    left: 30px !important;
    top: auto !important;
    right: auto !important;
    z-index: 1100 !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
}
.fab-menu-top {
    position: relative !important;
    top: auto !important;
    right: auto !important;
    bottom: auto !important;
    margin-bottom: 10px !important;
    transform: none !important;
}
.fab-menu-top.open {
    opacity: 1 !important;
    transform: none !important;
    pointer-events: all !important;
}
.fab-button-top {
    background: #fff !important;
    color: #4a1030 !important;
    border: 2px solid #4a1030 !important;
    padding: 12px 22px !important;
    border-radius: 40px !important;
    cursor: pointer !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    box-shadow: 0 4px 16px rgba(0,0,0,0.2) !important;
    transition: all 0.2s !important;
    font-family: inherit !important;
}
.fab-button-top:hover { background: #4a1030 !important; color: white !important; transform: translateY(-2px) !important; }

/* Quitar barra sticky */
.top-actions-bar { display: none !important; }

@media (max-width: 768px) {
    .btn-volver-flotante { top: 12px !important; left: 12px !important; padding: 8px 16px !important; font-size: 12px !important; }
    .fab-container-top { bottom: 20px !important; left: 20px !important; }
}
    </style>
</head>
<body>



@php
    $allowedHtmlTags = '<p><br><strong><b><em><i><u><s><strike><del><sup><sub><ul><ol><li><a><span><h1><h2><h3><blockquote><pre><div>';
    $proyectosRecientes = $proyectos->take(2);
    $proyectosRestantes = $proyectos->skip(2);
    $limiteMostrar = 2;
    $experienciasRecientes = $experiencias->take($limiteMostrar);
    $experienciasRestantes = $experiencias->skip($limiteMostrar);
    $academicasRecientes = $academicas->take($limiteMostrar);
    $academicasRestantes = $academicas->skip($limiteMostrar);
    $habilidadesFrontendRecientes = ($habilidadesTecnicasFrontend ?? collect())->take($limiteMostrar);
    $habilidadesFrontendRestantes = ($habilidadesTecnicasFrontend ?? collect())->skip($limiteMostrar);
    $habilidadesBackendRecientes = ($habilidadesTecnicasBackend ?? collect())->take($limiteMostrar);
    $habilidadesBackendRestantes = ($habilidadesTecnicasBackend ?? collect())->skip($limiteMostrar);
    $habilidadesBlandasRecientes = $habilidadesBlandas->take($limiteMostrar);
    $habilidadesBlandasRestantes = $habilidadesBlandas->skip($limiteMostrar);
    $idiomasRecientes = $idiomas->take($limiteMostrar);
    $idiomasRestantes = $idiomas->skip($limiteMostrar);
    $banderas = ['inglés'=>'🇬🇧','ingles'=>'🇬🇧','español'=>'🇧🇴','espanol'=>'🇧🇴','portugués'=>'🇧🇷','portugues'=>'🇧🇷','francés'=>'🇫🇷','frances'=>'🇫🇷','alemán'=>'🇩🇪','aleman'=>'🇩🇪','italiano'=>'🇮🇹','chino'=>'🇨🇳','japonés'=>'🇯🇵','japones'=>'🇯🇵','coreano'=>'🇰🇷','árabe'=>'🇸🇦','arabe'=>'🇸🇦','ruso'=>'🇷🇺','hindi'=>'🇮🇳','hindú'=>'🇮🇳','indu'=>'🇮🇳','holandés'=>'🇳🇱','holandes'=>'🇳🇱','sueco'=>'🇸🇪','noruego'=>'🇳🇴','danés'=>'🇩🇰','danes'=>'🇩🇰','polaco'=>'🇵🇱','turco'=>'🇹🇷','griego'=>'🇬🇷','hebreo'=>'🇮🇱','tailandés'=>'🇹🇭','tailandes'=>'🇹🇭','vietnamita'=>'🇻🇳','indonesio'=>'🇮🇩','catalán'=>'🏳️','catalan'=>'🏳️','mandarin'=>'🇨🇳','mandarín'=>'🇨🇳'];
    $codigos = ['inglés'=>'EN','ingles'=>'EN','español'=>'ES','espanol'=>'ES','francés'=>'FR','frances'=>'FR','alemán'=>'DE','aleman'=>'DE','portugués'=>'PT','portugues'=>'PT','italiano'=>'IT','chino'=>'ZH','japonés'=>'JP','japones'=>'JP','coreano'=>'KO','árabe'=>'AR','arabe'=>'AR','ruso'=>'RU','hindi'=>'HI','indu'=>'HI','mandarin'=>'ZH','mandarín'=>'ZH'];
    $colorFondos = ['#0abf9e','#3b82f6','#a855f7','#f59e0b','#f43f5e','#22d3ee','#4ade80','#fb923c'];
    $coloresBarra = ['linear-gradient(90deg,#07866e,#0abf9e)','linear-gradient(90deg,#1d4ed8,#3b82f6)','linear-gradient(90deg,#7c3aed,#a855f7)','linear-gradient(90deg,#b45309,#f59e0b)','linear-gradient(90deg,#be123c,#f43f5e)','linear-gradient(90deg,#0e7490,#22d3ee)','linear-gradient(90deg,#15803d,#4ade80)','linear-gradient(90deg,#9a3412,#fb923c)'];

    $folderColors = [
        ['bg'=>'#0abf9e','tab'=>'#07866e','light'=>'#f0fdf9'],
        ['bg'=>'#6366f1','tab'=>'#4f46e5','light'=>'#eef2ff'],
        ['bg'=>'#f59e0b','tab'=>'#d97706','light'=>'#fffbeb'],
        ['bg'=>'#ec4899','tab'=>'#db2777','light'=>'#fdf2f8'],
        ['bg'=>'#14b8a6','tab'=>'#0d9488','light'=>'#f0fdfa'],
        ['bg'=>'#8b5cf6','tab'=>'#7c3aed','light'=>'#f5f3ff'],
        ['bg'=>'#f97316','tab'=>'#ea580c','light'=>'#fff7ed'],
        ['bg'=>'#3b82f6','tab'=>'#2563eb','light'=>'#eff6ff'],
    ];

    $temaActual = $portfolio->color_theme ?? 'default';
    $claseTema = $temaActual !== 'default' ? 'theme-' . $temaActual : '';
@endphp

<!-- Barra de acciones superior (Volver y Más opciones) -->
{{-- Botón Volver flotante --}}
<button class="btn-volver-flotante" onclick="window.history.back()">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    <span>Volver</span>
</button>

{{-- FAB flotante abajo-izquierda --}}
<div class="fab-container-top" id="fabContainerTop">
    <div class="fab-menu-top" id="fabMenuTop">
        <button class="fab-item-top" onclick="descargarPDF()"><i class="fas fa-file-pdf"></i><span>Descargar PDF</span></button>
        <button class="fab-item-top" onclick="descargarImagen()"><i class="fas fa-image"></i><span>Descargar imagen</span></button>
    </div>
    <button class="fab-button-top" id="fabButtonTop" onclick="toggleFabMenuTop()">
        <i class="fas fa-ellipsis-h" id="fabIconTop"></i><span>Más opciones</span>
    </button>
</div>

<div class="preview-container {{ $claseTema }}">

    <!-- ==================== CABECERA ==================== -->
    <div class="profile-header">
        <div class="profile-info">
            <h1>{{ $user->first_name ?? 'Usuario' }} {{ $user->last_name ?? '' }}</h1>
            @if(!empty($user->profession->name))
            <div class="title">{{ $user->profession->name }}</div>
            @endif

            <div class="profile-contact-list">
                @if($user->city || $user->country)
                <div class="contact-row"><i class="fas fa-map-marker-alt"></i><span>{{ $user->city ?? '' }}{{ $user->country ? ', ' . $user->country : '' }}</span></div>
                @endif
                @if(!empty($redes['correo']))
                <div class="contact-row"><i class="fas fa-envelope"></i><span>{{ $redes['correo'] }}</span></div>
                @endif
                @if(!empty($redes['whatsapp']))
                <div class="contact-row"><i class="fab fa-whatsapp"></i><span>{{ $redes['whatsapp'] }}</span></div>
                @endif
                @if(!empty($user->biography))
                <div class="contact-row" style="align-items:flex-start;">
                    <i class="fas fa-quote-left" style="margin-top:4px;"></i>
                    <div class="ql-snow" style="width:100%;"><div class="ql-editor" style="padding:0;min-height:0;font-family:inherit;">{!! strip_tags($user->biography, $allowedHtmlTags) !!}</div></div>
                </div>
                @endif
            </div>

            <div class="profile-social-icons">
                @if(!empty($redes['linkedin']))<a href="{{ $redes['linkedin'] }}" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>@endif
                @if(!empty($redes['github']))<a href="{{ $redes['github'] }}" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>@endif
                @if(!empty($redes['maps_url']))<a href="{{ $redes['maps_url'] }}" target="_blank" title="Mapa"><i class="fas fa-map-marker-alt"></i></a>@endif
                @if(!empty($redes['whatsapp']))
                    @php $wpNum = preg_replace('/[^0-9]/', '', $redes['whatsapp']); @endphp
                    <a href="https://wa.me/{{ $wpNum }}" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                @endif
                @if($user->portfolio && $user->portfolio->is_public)<a href="javascript:void(0)" onclick="abrirModalCompartir()" title="Compartir"><i class="fas fa-share-nodes"></i></a>@endif
                @if(!empty($redes['correo']))<a href="mailto:{{ $redes['correo'] }}" title="Email"><i class="fas fa-envelope"></i></a>@endif
                @if(!empty($redes['otros']))<a href="{{ $redes['otros'] }}" target="_blank" title="Otro"><i class="fas fa-globe"></i></a>@endif
            </div>
        </div>
        <div class="profile-avatar-side">
            @if($user->photo_base64)
                <img src="{{ $user->photo_base64 }}" alt="Foto de perfil">
            @else
                <div class="avatar-placeholder"><i class="fas fa-user-circle"></i></div>
            @endif
        </div>
    </div>

    <!-- ==================== EXPERIENCIA LABORAL ==================== -->
    @if($experiencias->count() > 0)
    <div class="section" id="section-experiencias">
        <h2><i class="fas fa-briefcase"></i> Experiencia laboral</h2>
        <div class="cards-grid" id="experiencias-grid">
            @foreach($experienciasRecientes as $i => $exp)
            <div class="card" onclick="abrirDetalleExpDirecto({{ $i }})">
                <h3>{{ $exp->empresa }}</h3>
                @if(!empty($exp->ubicacion))<div class="subtitle">{{ $exp->ubicacion }}</div>@endif
                @if(str_contains($exp->cargo, ' / '))
                    <ul class="roles-list">@foreach(explode(' / ', $exp->cargo) as $rol)<li>{{ $rol }}</li>@endforeach</ul>
                @else
                    <div class="role-single">{{ $exp->cargo }}</div>
                @endif
                <div class="date">
                    {{ \Carbon\Carbon::parse($exp->fecha_inicio)->format('d F Y') }}
                    @if($exp->fecha_fin) — {{ \Carbon\Carbon::parse($exp->fecha_fin)->format('d F Y') }}
                    @elseif($exp->trabajo_actual) — Actualidad @endif
                </div>
                @if(!empty($exp->descripcion))
                <div class="description-wrapper">
                    <div class="description collapsed" id="desc-exp-{{ $i }}">
                        <div class="ql-snow"><div class="ql-editor">{!! strip_tags($exp->descripcion, $allowedHtmlTags) !!}</div></div>
                    </div>
                    @if(mb_strlen(trim(strip_tags($exp->descripcion))) > 150)
                        <button class="ver-mas-btn" onclick="event.stopPropagation(); toggleDesc('desc-exp-{{ $i }}', this)">Ver más</button>
                    @endif
                </div>
                @endif
                <div style="font-size:11px;color:#0abf9e;margin-top:10px;display:flex;align-items:center;gap:4px;font-weight:600;">
                    <i class="fas fa-expand-alt" style="font-size:9px;"></i> Clic para ver detalle completo
                </div>
            </div>
            @endforeach
        </div>

        @if($experienciasRestantes->count() > 0)
        <div class="btn-ver-todos">
            <button onclick="abrirModalExperiencias()">
                <i class="fas fa-briefcase"></i> Ver todas las experiencias ({{ $experiencias->count() }})
            </button>
        </div>
        @endif

        <!-- DIV OCULTO CON TODAS LAS EXPERIENCIAS PARA PDF -->
        <div id="experiencias-completas" style="display:none;">
            <div class="cards-grid">
                @foreach($experiencias as $exp)
                <div class="card">
                    <h3>{{ $exp->empresa }}</h3>
                    @if(!empty($exp->ubicacion))<div class="subtitle">{{ $exp->ubicacion }}</div>@endif
                    <div class="role-single">{{ $exp->cargo }}</div>
                    <div class="date">
                        {{ \Carbon\Carbon::parse($exp->fecha_inicio)->format('d/m/Y') }}
                        @if($exp->fecha_fin) — {{ \Carbon\Carbon::parse($exp->fecha_fin)->format('d/m/Y') }}
                        @elseif($exp->trabajo_actual) — Actualidad @endif
                    </div>
                    @if(!empty($exp->descripcion))
                    <div class="description expanded">
                        <div class="ql-snow"><div class="ql-editor">{!! strip_tags($exp->descripcion, $allowedHtmlTags) !!}</div></div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ==================== INFORMACIÓN ACADÉMICA ==================== -->
    @if($academicas->count() > 0)
    <div class="section" id="section-academicas">
        <h2><i class="fas fa-graduation-cap"></i> Información académica</h2>
        <div class="cards-grid" id="academicas-grid">
            @foreach($academicasRecientes as $i => $aca)
            <div class="card" onclick="abrirDetalleAcaDirecto({{ $i }})">
                <h3>{{ $aca->institucion }}</h3>
                <div class="subtitle">{{ $aca->titulo }}</div>
                @if(isset($aca->specialty) && !empty($aca->specialty))
                    <div class="specialty-badge"><i class="fas fa-tag"></i> {{ $aca->specialty }}</div>
                @endif
                <div class="date">
                    {{ \Carbon\Carbon::parse($aca->fecha_inicio)->format('F Y') }}
                    @if($aca->fecha_fin) — {{ \Carbon\Carbon::parse($aca->fecha_fin)->format('F Y') }}
                    @elseif($aca->estudio_actual) — Actualidad @endif
                </div>
                @if(!empty($aca->descripcion))
                <div class="description-wrapper">
                    <div class="description collapsed" id="desc-aca-{{ $i }}">
                        <div class="ql-snow"><div class="ql-editor">{!! strip_tags($aca->descripcion, $allowedHtmlTags) !!}</div></div>
                    </div>
                    @if(mb_strlen(trim(strip_tags($aca->descripcion))) > 150)
                        <button class="ver-mas-btn" onclick="event.stopPropagation(); toggleDesc('desc-aca-{{ $i }}', this)">Ver más</button>
                    @endif
                </div>
                @endif
                <div style="font-size:11px;color:#0abf9e;margin-top:10px;display:flex;align-items:center;gap:4px;font-weight:600;">
                    <i class="fas fa-expand-alt" style="font-size:9px;"></i> Clic para ver detalle completo
                </div>
            </div>
            @endforeach
        </div>

        @if($academicasRestantes->count() > 0)
        <div class="btn-ver-todos">
            <button onclick="abrirModalAcademicas()">
                <i class="fas fa-graduation-cap"></i> Ver toda la formación académica ({{ $academicas->count() }})
            </button>
        </div>
        @endif

        <!-- DIV OCULTO CON TODAS LAS ACADÉMICAS PARA PDF -->
        <div id="academicas-completas" style="display:none;">
            <div class="cards-grid">
                @foreach($academicas as $aca)
                <div class="card">
                    <h3>{{ $aca->institucion }}</h3>
                    <div class="subtitle">{{ $aca->titulo }}</div>
                    @if(!empty($aca->specialty))<div class="specialty-badge">{{ $aca->specialty }}</div>@endif
                    <div class="date">
                        {{ \Carbon\Carbon::parse($aca->fecha_inicio)->format('d/m/Y') }}
                        @if($aca->fecha_fin) — {{ \Carbon\Carbon::parse($aca->fecha_fin)->format('d/m/Y') }}
                        @elseif($aca->estudio_actual) — Actualidad @endif
                    </div>
                    @if(!empty($aca->descripcion))
                    <div class="description expanded">
                        <div class="ql-snow"><div class="ql-editor">{!! strip_tags($aca->descripcion, $allowedHtmlTags) !!}</div></div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ==================== HABILIDADES TÉCNICAS ==================== -->
    @if(($habilidadesTecnicasFrontend ?? collect())->count() > 0 || ($habilidadesTecnicasBackend ?? collect())->count() > 0)
    <div class="section" id="section-tecnicas">
        <h2><i class="fas fa-code"></i> Habilidades técnicas</h2>
        <div id="tecnicas-grid">
            @if(($habilidadesFrontendRecientes ?? collect())->count() > 0)
                <div class="tech-category-title">Frontend</div>
                <div class="tech-skills-grid">
                    @foreach($habilidadesFrontendRecientes as $skill)
                        @php $nivel=$skill->nivel??'Intermedio'; $claseNivel=$nivel=='Avanzado'?'advanced':($nivel=='Intermedio'?'intermediate':'basic'); $hasProjects=isset($skill->proyectos)&&count($skill->proyectos)>0; @endphp
                        <div class="tech-skill-item">
                            <div class="tech-skill-header"><span class="tech-skill-name">{{ $skill->nombre }}</span><span class="tech-skill-level">{{ $nivel }}</span></div>
                            <div class="tech-skill-bar-bg"><div class="tech-skill-bar-fill {{ $claseNivel }}"></div></div>
                            @if($hasProjects)<div class="tech-skill-projects">@foreach($skill->proyectos as $p)<a href="javascript:void(0)" class="tech-skill-project-chip" onclick="abrirModalPorId({{ $p->id }});" title="{{ $p->nombre }}">{{ $p->nombre }}</a>@endforeach</div>@endif
                        </div>
                    @endforeach
                </div>
            @endif
            @if(($habilidadesBackendRecientes ?? collect())->count() > 0)
                <div class="tech-category-title">Backend</div>
                <div class="tech-skills-grid">
                    @foreach($habilidadesBackendRecientes as $skill)
                        @php $nivel=$skill->nivel??'Intermedio'; $claseNivel=$nivel=='Avanzado'?'advanced':($nivel=='Intermedio'?'intermediate':'basic'); $hasProjects=isset($skill->proyectos)&&count($skill->proyectos)>0; @endphp
                        <div class="tech-skill-item">
                            <div class="tech-skill-header"><span class="tech-skill-name">{{ $skill->nombre }}</span><span class="tech-skill-level">{{ $nivel }}</span></div>
                            <div class="tech-skill-bar-bg"><div class="tech-skill-bar-fill {{ $claseNivel }}"></div></div>
                            @if($hasProjects)<div class="tech-skill-projects">@foreach($skill->proyectos as $p)<a href="javascript:void(0)" class="tech-skill-project-chip" onclick="abrirModalPorId({{ $p->id }});" title="{{ $p->nombre }}">{{ $p->nombre }}</a>@endforeach</div>@endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        @if(($habilidadesFrontendRestantes->count() > 0) || ($habilidadesBackendRestantes->count() > 0))
        <div class="btn-ver-todos">
            <button onclick="abrirModalTecnicas()"><i class="fas fa-code"></i> Ver todas las habilidades técnicas</button>
        </div>
        @endif

        <div id="tecnicas-completas" style="display:none;">
            @if(($habilidadesTecnicasFrontend ?? collect())->count() > 0)
                <div class="tech-category-title">Frontend</div>
                <div class="tech-skills-grid">
                    @foreach($habilidadesTecnicasFrontend as $skill)
                    <div class="tech-skill-item">
                        <div class="tech-skill-header">
                            <span class="tech-skill-name">{{ $skill->nombre }}</span>
                            <span class="tech-skill-level">{{ $skill->nivel ?? 'Intermedio' }}</span>
                        </div>
                        <div class="tech-skill-bar-bg">
                            <div class="tech-skill-bar-fill @php echo ($skill->nivel ?? 'Intermedio') == 'Avanzado' ? 'advanced' : (($skill->nivel ?? 'Intermedio') == 'Intermedio' ? 'intermediate' : 'basic') @endphp"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
            @if(($habilidadesTecnicasBackend ?? collect())->count() > 0)
                <div class="tech-category-title">Backend</div>
                <div class="tech-skills-grid">
                    @foreach($habilidadesTecnicasBackend as $skill)
                    <div class="tech-skill-item">
                        <div class="tech-skill-header">
                            <span class="tech-skill-name">{{ $skill->nombre }}</span>
                            <span class="tech-skill-level">{{ $skill->nivel ?? 'Intermedio' }}</span>
                        </div>
                        <div class="tech-skill-bar-bg">
                            <div class="tech-skill-bar-fill @php echo ($skill->nivel ?? 'Intermedio') == 'Avanzado' ? 'advanced' : (($skill->nivel ?? 'Intermedio') == 'Intermedio' ? 'intermediate' : 'basic') @endphp"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- ==================== HABILIDADES BLANDAS ==================== -->
    @if($habilidadesBlandas->count() > 0)
    <div class="section" id="section-blandas">
        <h2><i class="fas fa-heart"></i> Habilidades blandas</h2>
        <div class="skills-container" id="blandas-grid">
            @foreach($habilidadesBlandasRecientes as $skill)
                <span class="soft-skill-tag"><i class="fas fa-star" style="color:#0abf9e;"></i> {{ $skill->nombre }}</span>
            @endforeach
        </div>
        @if($habilidadesBlandasRestantes->count() > 0)
        <div class="btn-ver-todos">
            <button onclick="abrirModalBlandas()"><i class="fas fa-heart"></i> Ver todas las habilidades blandas ({{ $habilidadesBlandas->count() }})</button>
        </div>
        @endif

        <div id="blandas-completas" style="display:none;">
            <div class="skills-container">
                @foreach($habilidadesBlandas as $skill)
                <span class="soft-skill-tag"><i class="fas fa-star"></i> {{ $skill->nombre }}</span>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ==================== IDIOMAS ==================== -->
    @if($idiomas->count() > 0)
    <div class="section" id="section-idiomas">
        <h2><i class="fas fa-language"></i> Idiomas</h2>
        <div class="idiomas-preview-grid" id="idiomas-grid">
            @foreach($idiomasRecientes as $index => $idioma)
            @php $banderaEmoji=$banderas[strtolower($idioma->nombre)]??null; $codigo=$codigos[strtolower($idioma->nombre)]??strtoupper(substr($idioma->nombre,0,2)); $colorFondo=$colorFondos[$index%count($colorFondos)]; $colorBarra=$coloresBarra[$index%count($coloresBarra)]; @endphp
            <div class="idioma-preview-card">
                <div class="idioma-preview-header">
                    <div class="idioma-preview-left">
                        @if($banderaEmoji)<span class="idioma-bandera">{{ $banderaEmoji }}</span>
                        @else<span class="idioma-flag-code-preview" style="background:{{ $colorFondo }};">{{ $codigo }}</span>@endif
                        <div class="idioma-preview-info">
                            <span class="idioma-preview-nombre">{{ $idioma->nombre }}</span>
                            <span class="idioma-preview-nivel">{{ $idioma->nivel_label }} — {{ $idioma->nivel_nombre }}</span>
                        </div>
                    </div>
                    @if($idioma->certificado)<a href="javascript:void(0)" onclick="abrirLightbox('{{ asset('storage/' . $idioma->certificado) }}')" class="idioma-cert-link"><i class="fas fa-certificate"></i> Cert.</a>@endif
                </div>
                <div class="idioma-barra-wrap">
                    <div class="idioma-barra-fill-custom" style="width:{{ $idioma->porcentaje }}%;background:{{ $colorBarra }};"></div>
                </div>
            </div>
            @endforeach
        </div>
        @if($idiomasRestantes->count() > 0)
        <div class="btn-ver-todos">
            <button onclick="abrirModalIdiomas()"><i class="fas fa-language"></i> Ver todos los idiomas ({{ $idiomas->count() }})</button>
        </div>
        @endif

        <div id="idiomas-completos" style="display:none;">
            <div class="idiomas-preview-grid">
                @foreach($idiomas as $index => $idioma)
                @php $banderaEmoji=$banderas[strtolower($idioma->nombre)]??null; $codigo=$codigos[strtolower($idioma->nombre)]??strtoupper(substr($idioma->nombre,0,2)); $colorFondo=$colorFondos[$index%count($colorFondos)]; $colorBarra=$coloresBarra[$index%count($coloresBarra)]; @endphp
                <div class="idioma-preview-card">
                    <div class="idioma-preview-header">
                        <div class="idioma-preview-left">
                            @if($banderaEmoji)<span class="idioma-bandera">{{ $banderaEmoji }}</span>
                            @else<span class="idioma-flag-code-preview" style="background:{{ $colorFondo }};">{{ $codigo }}</span>@endif
                            <div class="idioma-preview-info">
                                <span class="idioma-preview-nombre">{{ $idioma->nombre }}</span>
                                <span class="idioma-preview-nivel">{{ $idioma->nivel_label }} — {{ $idioma->nivel_nombre }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="idioma-barra-wrap"><div class="idioma-barra-fill-custom" style="width:{{ $idioma->porcentaje }}%;background:{{ $colorBarra }};"></div></div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ==================== PROYECTOS ==================== -->
    @if($proyectos->count() > 0)
    <div class="section" id="section-proyectos">
        <h2><i class="fas fa-project-diagram"></i> Proyectos</h2>
        <div class="cards-grid" id="proyectos-grid">
            @foreach($proyectosRecientes as $proyecto)
                @php
                    $modalProyectoPayload = ['nombre'=>$proyecto->nombre,'descripcion'=>strip_tags($proyecto->descripcion??'',$allowedHtmlTags),'fecha_inicio'=>optional($proyecto->fecha_inicio)->format('d/m/Y'),'fecha_fin'=>optional($proyecto->fecha_fin)->format('d/m/Y'),'estado'=>$proyecto->estado,'rol'=>$proyecto->rol,'cliente'=>$proyecto->cliente,'tecnologias'=>$proyecto->tecnologias,'evidencias'=>$proyecto->evidencias];
                @endphp
                <div class="card" id="project-card-{{ $proyecto->id }}" onclick='abrirModal(@json($modalProyectoPayload))'>
                    <h3 style="color:#1abc9c;font-size:1rem;margin-bottom:8px;">{{ $proyecto->nombre }}</h3>
                    @if(!empty($proyecto->descripcion))
                    <div class="description-wrapper">
                        <div class="description collapsed" id="desc-proy-{{ $loop->index }}">
                            <div class="ql-snow"><div class="ql-editor">{!! strip_tags($proyecto->descripcion, $allowedHtmlTags) !!}</div></div>
                        </div>
                        @if(mb_strlen(trim(strip_tags($proyecto->descripcion))) > 150)
                            <button type="button" class="ver-mas-btn" onclick="event.stopPropagation(); toggleDesc('desc-proy-{{ $loop->index }}', this)">Ver más</button>
                        @endif
                    </div>
                    @endif
                    <div class="date" style="font-size:0.7rem;">
                        {{ \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') }}
                        @if($proyecto->fecha_fin) — {{ \Carbon\Carbon::parse($proyecto->fecha_fin)->format('d/m/Y') }} @endif
                        | {{ $proyecto->estado ?? 'En progreso' }}
                    </div>
                    @if(!empty($proyecto->rol)||!empty($proyecto->cliente))
                    <div class="description" style="font-size:0.75rem;">
                        @if(!empty($proyecto->rol))Rol: {{ $proyecto->rol }}@endif
                        @if(!empty($proyecto->rol)&&!empty($proyecto->cliente)) | @endif
                        @if(!empty($proyecto->cliente))Cliente: {{ $proyecto->cliente }}@endif
                    </div>
                    @endif
                    @if(!empty($proyecto->tecnologias))
                        <div class="proyecto-tecnologias">
                            @foreach(array_slice($proyecto->tecnologias,0,3) as $tec)<span class="tec-badge">{{ $tec }}</span>@endforeach
                            @if(count($proyecto->tecnologias)>3)<span class="tec-badge">+{{ count($proyecto->tecnologias)-3 }}</span>@endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if($proyectosRestantes->count() > 0)
        <div class="btn-ver-todos">
            <button onclick="abrirModalTodosProyectos()">
                <i class="fas fa-th-large"></i> Ver todos los proyectos ({{ $proyectos->count() }})
            </button>
        </div>
        @endif

        <div id="proyectos-completos" style="display:none;">
            <div class="cards-grid">
                @foreach($proyectos as $proyecto)
                <div class="card">
                    <h3 style="color:#1abc9c;font-size:1rem;margin-bottom:8px;">{{ $proyecto->nombre }}</h3>
                    @if(!empty($proyecto->descripcion))
                    <div class="description-wrapper">
                        <div class="description expanded">
                            <div class="ql-snow"><div class="ql-editor">{!! strip_tags($proyecto->descripcion, $allowedHtmlTags) !!}</div></div>
                        </div>
                    </div>
                    @endif
                    <div class="date" style="font-size:0.7rem;">
                        {{ \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') }}
                        @if($proyecto->fecha_fin) — {{ \Carbon\Carbon::parse($proyecto->fecha_fin)->format('d/m/Y') }} @endif
                        | {{ $proyecto->estado ?? 'En progreso' }}
                    </div>
                    @if(!empty($proyecto->rol)||!empty($proyecto->cliente))
                    <div class="description" style="font-size:0.75rem;">
                        @if(!empty($proyecto->rol))Rol: {{ $proyecto->rol }}@endif
                        @if(!empty($proyecto->rol)&&!empty($proyecto->cliente)) | @endif
                        @if(!empty($proyecto->cliente))Cliente: {{ $proyecto->cliente }}@endif
                    </div>
                    @endif
                    @if(!empty($proyecto->tecnologias))
                        <div class="proyecto-tecnologias">
                            @foreach($proyecto->tecnologias as $tec)<span class="tec-badge">{{ $tec }}</span>@endforeach
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ==================== DATOS JS ==================== -->
    <script>
    window.previewExperienciasData = {!! json_encode($experiencias->map(function($exp) use ($allowedHtmlTags) { return ['empresa'=>$exp->empresa,'cargo'=>$exp->cargo,'ubicacion'=>$exp->ubicacion,'fecha_inicio'=>\Carbon\Carbon::parse($exp->fecha_inicio)->format('d/m/Y'),'fecha_fin'=>$exp->fecha_fin?\Carbon\Carbon::parse($exp->fecha_fin)->format('d/m/Y'):null,'trabajo_actual'=>$exp->trabajo_actual??false,'descripcion'=>strip_tags($exp->descripcion??'',$allowedHtmlTags)]; })->values()) !!};

    window.previewAcademicasData = {!! json_encode($academicas->map(function($aca) use ($allowedHtmlTags) { $evidencias=[]; if(isset($aca->evidence_url)&&$aca->evidence_url){$evidencias=is_array($aca->evidence_url)?$aca->evidence_url:(json_decode($aca->evidence_url,true)??[]);} return ['institucion'=>$aca->institucion,'titulo'=>$aca->titulo,'specialty'=>$aca->specialty??null,'fecha_inicio'=>\Carbon\Carbon::parse($aca->fecha_inicio)->format('d/m/Y'),'fecha_fin'=>$aca->fecha_fin?\Carbon\Carbon::parse($aca->fecha_fin)->format('d/m/Y'):null,'estudio_actual'=>$aca->estudio_actual??false,'descripcion'=>strip_tags($aca->descripcion??'',$allowedHtmlTags),'evidencias'=>array_map(function($e){return['url'=>asset('storage/'.$e),'ext'=>pathinfo($e,PATHINFO_EXTENSION),'nombre'=>basename($e)];}, $evidencias)]; })->values()) !!};

    window.previewProjectsById = {!! json_encode(collect($proyectos)->keyBy('id')->map(function($p) use ($allowedHtmlTags) { return ['id'=>$p->id,'nombre'=>$p->nombre,'descripcion'=>strip_tags($p->descripcion??'',$allowedHtmlTags),'fecha_inicio'=>optional($p->fecha_inicio)->format('d/m/Y'),'fecha_fin'=>optional($p->fecha_fin)->format('d/m/Y'),'estado'=>$p->estado,'rol'=>$p->rol,'cliente'=>$p->cliente,'tecnologias'=>$p->tecnologias,'evidencias'=>$p->evidencias]; })) !!};
    </script>

    <!-- ==================== MODAL PROYECTOS — FOLDER CARDS ==================== -->
    @if($proyectos->count() > 0)
    <div id="modal-todos-proyectos" class="modal-overlay">
        <div class="modal-box" style="max-width:1000px;">
            <div class="modal-header">
                <h2>
                    <i class="fas fa-folder-open"></i>
                    Todos los proyectos
                    <span class="modal-count-badge">{{ $proyectos->count() }}</span>
                </h2>
                <button class="modal-close-btn" onclick="cerrarModalTodosProyectos()">✕</button>
            </div>
            <div class="modal-search-bar">
                <input type="text" id="search-proyectos" placeholder="🔍 Buscar por nombre, tecnología o estado..." oninput="filtrarProyectos(this.value)">
            </div>
            <div class="projects-modal-body">
                <div class="projects-folder-grid" id="folder-grid-proyectos">
                    @foreach($proyectos as $idx => $proyecto)
                        @php
                            $color = $folderColors[$idx % count($folderColors)];
                            $estadoClass = $proyecto->estado == 'Completado' ? 'background:#d1fae5;color:#065f46;' : ($proyecto->estado == 'En curso' ? 'background:#fef3c7;color:#92400e;' : 'background:#f1f5f9;color:#64748b;');
                            $modalProyectoPayload = ['nombre'=>$proyecto->nombre,'descripcion'=>strip_tags($proyecto->descripcion??'',$allowedHtmlTags),'fecha_inicio'=>optional($proyecto->fecha_inicio)->format('d/m/Y'),'fecha_fin'=>optional($proyecto->fecha_fin)->format('d/m/Y'),'estado'=>$proyecto->estado,'rol'=>$proyecto->rol,'cliente'=>$proyecto->cliente,'tecnologias'=>$proyecto->tecnologias,'evidencias'=>$proyecto->evidencias];
                            $techs = $proyecto->tecnologias ?? [];
                            $descCorta = strip_tags($proyecto->descripcion ?? '');
                        @endphp
                        <div class="folder-card"
                             data-nombre="{{ strtolower($proyecto->nombre) }}"
                             data-techs="{{ strtolower(implode(' ', $techs)) }}"
                             data-estado="{{ strtolower($proyecto->estado ?? '') }}"
                             onclick='abrirModal(@json($modalProyectoPayload)); cerrarModalTodosProyectos();'>
                            <div class="folder-card-top" style="background:{{ $color['bg'] }};">
                                <div class="folder-card-tab" style="background:{{ $color['tab'] }};"></div>
                                <div class="folder-card-icon">
                                    <i class="fas fa-code-branch"></i>
                                </div>
                                <span class="folder-card-status" style="{{ $estadoClass }}">
                                    {{ $proyecto->estado ?? 'En progreso' }}
                                </span>
                            </div>
                            <div class="folder-card-body">
                                <h4>{{ $proyecto->nombre }}</h4>
                                <div class="folder-card-meta">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') }}
                                    @if($proyecto->fecha_fin) — {{ \Carbon\Carbon::parse($proyecto->fecha_fin)->format('d/m/Y') }} @endif
                                </div>
                                @if(!empty($descCorta))
                                <div class="folder-card-desc">{{ $descCorta }}</div>
                                @endif
                                @if(!empty($techs))
                                <div class="folder-card-chips">
                                    @foreach(array_slice($techs, 0, 3) as $tec)
                                        <span class="folder-chip">{{ $tec }}</span>
                                    @endforeach
                                    @if(count($techs) > 3)
                                        <span class="folder-chip-more">+{{ count($techs)-3 }}</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="folder-card-footer">
                                <div class="folder-card-rol">
                                    @if(!empty($proyecto->rol))
                                        <i class="fas fa-user-check"></i> {{ $proyecto->rol }}
                                    @elseif(!empty($proyecto->cliente))
                                        <i class="fas fa-building"></i> {{ $proyecto->cliente }}
                                    @else
                                        <i class="fas fa-folder"></i> Ver detalle
                                    @endif
                                </div>
                                <div class="folder-card-arrow"><i class="fas fa-arrow-right"></i></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="no-results-proyectos" class="empty-state" style="display:none;">
                    <i class="fas fa-search"></i>
                    No se encontraron proyectos con ese criterio.
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ==================== MODAL EXPERIENCIAS — TIMELINE ==================== -->
    @if($experiencias->count() > 0)
    <div id="modal-todos-experiencias" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2>
                    <i class="fas fa-briefcase"></i>
                    Experiencia laboral
                    <span class="modal-count-badge">{{ $experiencias->count() }}</span>
                </h2>
                <button class="modal-close-btn" onclick="cerrarModalExperiencias()">✕</button>
            </div>
            <div class="timeline-container">
                <div class="timeline-track">
                    @foreach($experiencias as $i => $exp)
                    <div class="timeline-item">
                        <div class="timeline-dot"><div class="timeline-dot-inner"></div></div>
                        <div class="timeline-card" onclick="cerrarModalExperiencias(); abrirDetalleExpDirecto({{ $i }});">
                            <div class="timeline-card-header">
                                <div>
                                    <div class="timeline-card-title">{{ $exp->empresa }}</div>
                                    <div class="timeline-card-sub">{{ $exp->cargo }}</div>
                                </div>
                                <div class="timeline-card-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($exp->fecha_inicio)->format('M Y') }}
                                    @if($exp->fecha_fin) — {{ \Carbon\Carbon::parse($exp->fecha_fin)->format('M Y') }}
                                    @elseif($exp->trabajo_actual) — Actualidad @endif
                                </div>
                            </div>
                            @if(!empty($exp->ubicacion))
                            <div class="timeline-card-loc"><i class="fas fa-map-marker-alt"></i> {{ $exp->ubicacion }}</div>
                            @endif
                            @if(!empty($exp->descripcion))
                            <div class="timeline-card-desc-preview">{{ strip_tags($exp->descripcion) }}</div>
                            @endif
                            <div class="timeline-card-hint">
                                <i class="fas fa-mouse-pointer"></i> Clic para ver detalle completo
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ==================== MODAL ACADÉMICAS — TIMELINE ==================== -->
    @if($academicas->count() > 0)
    <div id="modal-todos-academicas" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2>
                    <i class="fas fa-graduation-cap"></i>
                    Formación académica
                    <span class="modal-count-badge">{{ $academicas->count() }}</span>
                </h2>
                <button class="modal-close-btn" onclick="cerrarModalAcademicas()">✕</button>
            </div>
            <div class="timeline-container">
                <div class="timeline-track">
                    @foreach($academicas as $i => $aca)
                    @php
                        $hasEv = false;
                        if(isset($aca->evidence_url)&&$aca->evidence_url){$evArr=is_array($aca->evidence_url)?$aca->evidence_url:(json_decode($aca->evidence_url,true)??[]);$hasEv=!empty($evArr);}
                    @endphp
                    <div class="timeline-item">
                        <div class="timeline-dot"><div class="timeline-dot-inner"></div></div>
                        <div class="timeline-card" onclick="cerrarModalAcademicas(); abrirDetalleAcaDirecto({{ $i }});">
                            <div class="timeline-card-header">
                                <div>
                                    <div class="timeline-card-title">{{ $aca->institucion }}</div>
                                    <div class="timeline-card-sub">{{ $aca->titulo }}</div>
                                </div>
                                <div class="timeline-card-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($aca->fecha_inicio)->format('M Y') }}
                                    @if($aca->fecha_fin) — {{ \Carbon\Carbon::parse($aca->fecha_fin)->format('M Y') }}
                                    @elseif($aca->estudio_actual??false) — Actualidad @endif
                                </div>
                            </div>
                            @if(!empty($aca->specialty??''))
                            <div class="timeline-card-loc"><i class="fas fa-tag"></i> {{ $aca->specialty }}</div>
                            @endif
                            @if(!empty($aca->descripcion))
                            <div class="timeline-card-desc-preview">{{ strip_tags($aca->descripcion) }}</div>
                            @endif
                            <div class="timeline-card-hint">
                                <i class="fas fa-mouse-pointer"></i>
                                Clic para ver detalle completo{{ $hasEv ? ' + certificado' : '' }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ==================== MODAL DETALLE EXPERIENCIA ==================== -->
    <div id="modal-detalle-exp" class="detail-modal-overlay">
        <div class="detail-modal-box">
            <div class="detail-modal-hero">
                <button class="detail-modal-close" onclick="cerrarDetalleExp()">✕</button>
                <div class="detail-modal-hero-badge"><i class="fas fa-briefcase"></i> Experiencia laboral</div>
                <h3 id="det-exp-empresa"></h3>
                <div class="sub" id="det-exp-cargo"></div>
                <div class="detail-modal-meta-row">
                    <div class="detail-meta-pill" id="det-exp-fecha-pill"><i class="fas fa-calendar-alt"></i> <span id="det-exp-fecha-txt"></span></div>
                    <div class="detail-meta-pill" id="det-exp-ubicacion-pill" style="display:none;"><i class="fas fa-map-marker-alt"></i> <span id="det-exp-ubicacion-txt"></span></div>
                </div>
            </div>
            <div class="detail-modal-body">
                <div id="det-exp-desc-section">
                    <div class="detail-section-label"><i class="fas fa-align-left"></i> Descripción</div>
                    <div class="detail-desc-wrap ql-snow">
                        <div class="ql-editor" id="det-exp-desc-inner" style="padding:0;min-height:0;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL DETALLE ACADÉMICA ==================== -->
    <div id="modal-detalle-aca" class="detail-modal-overlay">
        <div class="detail-modal-box">
            <div class="detail-modal-hero">
                <button class="detail-modal-close" onclick="cerrarDetalleAca()">✕</button>
                <div class="detail-modal-hero-badge"><i class="fas fa-graduation-cap"></i> Formación académica</div>
                <h3 id="det-aca-inst"></h3>
                <div class="sub" id="det-aca-titulo"></div>
                <div class="detail-modal-meta-row">
                    <div class="detail-meta-pill" id="det-aca-fecha-pill"><i class="fas fa-calendar-alt"></i> <span id="det-aca-fecha-txt"></span></div>
                    <div class="detail-meta-pill" id="det-aca-specialty-pill" style="display:none;"><i class="fas fa-tag"></i> <span id="det-aca-specialty-txt"></span></div>
                </div>
            </div>
            <div class="detail-modal-body">
                <div id="det-aca-desc-section">
                    <div class="detail-section-label"><i class="fas fa-align-left"></i> Descripción</div>
                    <div class="detail-desc-wrap ql-snow">
                        <div class="ql-editor" id="det-aca-desc" style="padding:0;min-height:0;"></div>
                    </div>
                </div>
                <div id="det-aca-certs" class="detail-certs-wrap" style="display:none;"></div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL HABILIDADES TÉCNICAS ==================== -->
    @if(($habilidadesTecnicasFrontend ?? collect())->count() > 0 || ($habilidadesTecnicasBackend ?? collect())->count() > 0)
    <div id="modal-todos-tecnicas" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2><i class="fas fa-code"></i> Todas las habilidades técnicas</h2>
                <button class="modal-close-btn" onclick="cerrarModalTecnicas()">✕</button>
            </div>
            <div style="padding:24px;display:flex;flex-direction:column;gap:20px;">
                @if(($habilidadesTecnicasFrontend??collect())->count()>0)
                <div>
                    <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.12em;color:#7a8298;margin-bottom:12px;"><i class="fas fa-palette" style="color:#0abf9e;margin-right:6px;"></i>Frontend</div>
                    <div style="display:flex;flex-wrap:wrap;gap:12px;">
                        @foreach($habilidadesTecnicasFrontend as $skill)
                        @php $nv=$skill->nivel??'Intermedio'; $cl=$nv=='Avanzado'?'advanced':($nv=='Intermedio'?'intermediate':'basic'); @endphp
                        <div class="tech-skill-item" style="width:200px;">
                            <div class="tech-skill-header"><span class="tech-skill-name">{{ $skill->nombre }}</span><span class="tech-skill-level">{{ $nv }}</span></div>
                            <div class="tech-skill-bar-bg"><div class="tech-skill-bar-fill {{ $cl }}"></div></div>
                            @if(isset($skill->proyectos)&&count($skill->proyectos)>0)
                            <div class="tech-skill-projects">@foreach($skill->proyectos as $p)<a href="javascript:void(0)" class="tech-skill-project-chip" onclick="cerrarModalTecnicas();abrirModalPorId({{ $p->id }});">{{ $p->nombre }}</a>@endforeach</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @if(($habilidadesTecnicasBackend??collect())->count()>0)
                <div>
                    <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.12em;color:#7a8298;margin-bottom:12px;"><i class="fas fa-server" style="color:#0abf9e;margin-right:6px;"></i>Backend</div>
                    <div style="display:flex;flex-wrap:wrap;gap:12px;">
                        @foreach($habilidadesTecnicasBackend as $skill)
                        @php $nv=$skill->nivel??'Intermedio'; $cl=$nv=='Avanzado'?'advanced':($nv=='Intermedio'?'intermediate':'basic'); @endphp
                        <div class="tech-skill-item" style="width:200px;">
                            <div class="tech-skill-header"><span class="tech-skill-name">{{ $skill->nombre }}</span><span class="tech-skill-level">{{ $nv }}</span></div>
                            <div class="tech-skill-bar-bg"><div class="tech-skill-bar-fill {{ $cl }}"></div></div>
                            @if(isset($skill->proyectos)&&count($skill->proyectos)>0)
                            <div class="tech-skill-projects">@foreach($skill->proyectos as $p)<a href="javascript:void(0)" class="tech-skill-project-chip" onclick="cerrarModalTecnicas();abrirModalPorId({{ $p->id }});">{{ $p->nombre }}</a>@endforeach</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- ==================== MODAL HABILIDADES BLANDAS ==================== -->
    @if($habilidadesBlandas->count() > 0)
    <div id="modal-todos-blandas" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2><i class="fas fa-heart"></i> Habilidades blandas <span class="modal-count-badge">{{ $habilidadesBlandas->count() }}</span></h2>
                <button class="modal-close-btn" onclick="cerrarModalBlandas()">✕</button>
            </div>
            <div style="padding:24px;display:flex;flex-wrap:wrap;gap:10px;">
                @foreach($habilidadesBlandas as $skill)
                    <span class="soft-skill-tag" style="font-size:14px;padding:8px 18px;"><i class="fas fa-star" style="color:#0abf9e;margin-right:6px;"></i>{{ $skill->nombre }}</span>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ==================== MODAL IDIOMAS - CON BOTÓN VER CERTIFICADO BIEN ALINEADO ==================== -->
    @if($idiomas->count() > 0)
    <div id="modal-todos-idiomas" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2><i class="fas fa-language"></i> Todos los idiomas <span class="modal-count-badge">{{ $idiomas->count() }}</span></h2>
                <button class="modal-close-btn" onclick="cerrarModalIdiomas()">✕</button>
            </div>
            <div class="idiomas-modal-grid">
                @foreach($idiomas as $index => $idioma)
                @php
                    $banderaEmoji = $banderas[strtolower($idioma->nombre)] ?? null;
                    $codigo = $codigos[strtolower($idioma->nombre)] ?? strtoupper(substr($idioma->nombre, 0, 2));
                    $colorFondo = $colorFondos[$index % count($colorFondos)];
                    $colorBarra = $coloresBarra[$index % count($coloresBarra)];
                @endphp
                <div class="idioma-modal-card">
                    <div class="idioma-modal-header">
                        @if($banderaEmoji)
                            <div class="idioma-modal-flag">{{ $banderaEmoji }}</div>
                        @else
                            <div class="idioma-modal-code" style="background: {{ $colorFondo }};">{{ $codigo }}</div>
                        @endif
                        <div class="idioma-modal-info">
                            <div class="idioma-modal-name">{{ $idioma->nombre }}</div>
                            <div class="idioma-modal-level">
                                <i class="fas fa-chart-line"></i>
                                {{ $idioma->nivel_label }} — {{ $idioma->nivel_nombre }}
                            </div>
                        </div>
                    </div>
                    <div class="idioma-modal-bar">
                        <div class="idioma-barra-wrap">
                            <div class="idioma-barra-fill-custom" style="width: {{ $idioma->porcentaje }}%; background: {{ $colorBarra }};"></div>
                        </div>
                    </div>
                    @if($idioma->certificado)
                    <div class="idioma-modal-footer">
                        <button onclick="event.stopPropagation(); abrirLightbox('{{ asset('storage/' . $idioma->certificado) }}')" class="cert-btn">
                            <i class="fas fa-certificate"></i> Ver certificado
                        </button>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ==================== MODAL COMPARTIR ==================== -->
    <div id="modal-compartir" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(2px);">
        <div style="background:#fff;border-radius:12px;width:450px;max-width:90%;position:relative;padding:24px;box-shadow:0 10px 25px rgba(0,0,0,0.1);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <h2 style="margin:0;font-size:20px;font-weight:600;color:#333;">Compartir</h2>
                <button onclick="cerrarModalCompartir()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#888;">✕</button>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:24px;text-align:center;">
                <a href="javascript:void(0)" style="text-decoration:none;color:#333;" onclick="shareTo('whatsapp')"><div style="width:55px;height:55px;border-radius:50%;background:#25D366;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;color:white;font-size:26px;"><i class="fab fa-whatsapp"></i></div><span style="font-size:12px;font-weight:500;">WhatsApp</span></a>
                <a href="javascript:void(0)" style="text-decoration:none;color:#333;" onclick="shareTo('facebook')"><div style="width:55px;height:55px;border-radius:50%;background:#1877F2;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;color:white;font-size:26px;"><i class="fab fa-facebook-f"></i></div><span style="font-size:12px;font-weight:500;">Facebook</span></a>
                <a href="javascript:void(0)" style="text-decoration:none;color:#333;" onclick="shareTo('twitter')"><div style="width:55px;height:55px;border-radius:50%;background:#000;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;color:white;font-size:26px;"><i class="fab fa-x-twitter"></i></div><span style="font-size:12px;font-weight:500;">X</span></a>
                <a href="javascript:void(0)" style="text-decoration:none;color:#333;" onclick="shareTo('email')"><div style="width:55px;height:55px;border-radius:50%;background:#7f8c8d;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;color:white;font-size:26px;"><i class="fas fa-envelope"></i></div><span style="font-size:12px;font-weight:500;">Correo</span></a>
            </div>
            <div style="display:flex;border:1px solid #e0e0e0;border-radius:8px;padding:6px;background:#f9f9f9;align-items:center;">
                <input type="text" id="share-link-input" readonly value="{{ $user->portfolio ? url('/portafolio/' . $user->portfolio->slug) : '' }}" style="flex:1;border:none;background:transparent;padding:8px 12px;outline:none;color:#555;font-size:14px;text-overflow:ellipsis;">
                <button onclick="copiarLinkPortafolio()" id="btn-copiar-link" style="background:white;border:1px solid #e0e0e0;border-radius:20px;padding:6px 18px;cursor:pointer;font-weight:600;font-size:14px;color:#333;">Copiar</button>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL PROYECTO DETALLE ==================== -->
    <div id="modal-proyecto" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:12px;max-width:680px;width:90%;max-height:88vh;overflow-y:auto;position:relative;">
            <div style="padding:24px 28px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:flex-start;">
                <div>
                    <h2 id="modal-nombre" style="color:#1a0a2e;font-size:22px;margin:0 0 10px;"></h2>
                    <div id="modal-badges" style="display:flex;flex-wrap:wrap;gap:6px;"></div>
                </div>
                <button onclick="cerrarModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#888;">✕</button>
            </div>
            <div id="modal-fechas" style="padding:12px 28px;background:#f9f9f9;border-bottom:1px solid #f0f0f0;font-size:13px;color:#666;display:flex;gap:20px;"></div>
            <div style="padding:24px 28px;">
                <div style="margin-bottom:20px;">
                    <div style="font-size:11px;font-weight:600;color:#888;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px;">Descripción</div>
                    <div class="ql-snow" style="border:none;padding:0;margin:0;"><div id="modal-descripcion" class="ql-editor" style="padding:0;min-height:0;color:#444;font-size:14px;line-height:1.6;"></div></div>
                </div>
                <div id="modal-tec-section" style="margin-bottom:20px;">
                    <div style="font-size:11px;font-weight:600;color:#888;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px;">Stack Tecnológico</div>
                    <div id="modal-tecnologias" style="display:flex;flex-wrap:wrap;gap:6px;"></div>
                </div>
                <div id="modal-evidencias" style="display:none;">
                    <div style="font-size:11px;font-weight:600;color:#888;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:12px;padding-top:16px;border-top:1px solid #f0f0f0;">Evidencias <span id="modal-ev-count" style="color:#1abc9c;"></span></div>
                    <div id="modal-evidencias-lista" style="display:flex;flex-direction:column;gap:12px;"></div>
                </div>
            </div>
        </div>
    </div>

</div><!-- fin .preview-container -->

<script>
// ============================================================
// MENÚ FLOTANTE
// ============================================================
function toggleFabMenuTop() {
    var menu = document.getElementById('fabMenuTop'), icon = document.getElementById('fabIconTop');
    var isOpen = menu.classList.contains('open');
    menu.classList.toggle('open', !isOpen);
    icon.className = isOpen ? 'fas fa-ellipsis-h' : 'fas fa-times';
}
document.addEventListener('click', function(e) {
    var container = document.getElementById('fabContainerTop');
    if (container && !container.contains(e.target)) {
        document.getElementById('fabMenuTop').classList.remove('open');
        document.getElementById('fabIconTop').className = 'fas fa-ellipsis-h';
    }
});
document.querySelectorAll('.fab-item-top').forEach(function(item) { item.addEventListener('click', function(e) { e.stopPropagation(); }); });

// ============================================================
// VER MÁS / VER MENOS
// ============================================================
function toggleDesc(id, btn) {
    var el = document.getElementById(id);
    if (el.classList.contains('collapsed')) { el.classList.replace('collapsed','expanded'); btn.textContent = 'Ver menos'; }
    else { el.classList.replace('expanded','collapsed'); btn.textContent = 'Ver más'; }
}

// ============================================================
// LIGHTBOX
// ============================================================
function abrirLightbox(imagenSrc) {
    var lb = document.getElementById('lightbox-modal');
    if (!lb) {
        lb = document.createElement('div');
        lb.id = 'lightbox-modal';
        lb.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,0.9);z-index:20000;align-items:center;justify-content:center;cursor:pointer;';
        lb.innerHTML = '<div style="position:relative;max-width:90vw;max-height:90vh;"><img id="lightbox-img" style="max-width:100%;max-height:90vh;object-fit:contain;border-radius:8px;"><button id="lightbox-close" style="position:absolute;top:-40px;right:0;background:rgba(0,0,0,0.5);border:none;color:white;font-size:28px;cursor:pointer;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;">✕</button></div>';
        document.body.appendChild(lb);
        lb.addEventListener('click', function(e) { if (e.target === lb || e.target.id === 'lightbox-close') lb.style.display = 'none'; });
    }
    document.getElementById('lightbox-img').src = imagenSrc;
    lb.style.display = 'flex';
}

// ============================================================
// BUSCAR PROYECTOS EN MODAL
// ============================================================
function filtrarProyectos(q) {
    q = q.toLowerCase().trim();
    var cards = document.querySelectorAll('#folder-grid-proyectos .folder-card');
    var visible = 0;
    cards.forEach(function(card) {
        var nombre = card.dataset.nombre || '';
        var techs  = card.dataset.techs  || '';
        var estado = card.dataset.estado || '';
        var match  = !q || nombre.includes(q) || techs.includes(q) || estado.includes(q);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    var noRes = document.getElementById('no-results-proyectos');
    if (noRes) noRes.style.display = visible === 0 ? 'block' : 'none';
}

// ============================================================
// MODAL DETALLE EXPERIENCIA
// ============================================================
function abrirDetalleExpDirecto(i) {
    var d = window.previewExperienciasData[i];
    if (!d) return;
    document.getElementById('det-exp-empresa').textContent = d.empresa;
    document.getElementById('det-exp-cargo').textContent = d.cargo;
    var fecha = d.fecha_inicio;
    if (d.fecha_fin) fecha += ' — ' + d.fecha_fin;
    else if (d.trabajo_actual) fecha += ' — Actualidad';
    document.getElementById('det-exp-fecha-txt').textContent = fecha;
    var ubicPill = document.getElementById('det-exp-ubicacion-pill');
    if (d.ubicacion) { document.getElementById('det-exp-ubicacion-txt').textContent = d.ubicacion; ubicPill.style.display = 'inline-flex'; }
    else { ubicPill.style.display = 'none'; }
    document.getElementById('det-exp-desc-inner').innerHTML = d.descripcion || '<em style="color:#94a3b8;">Sin descripción disponible.</em>';
    document.getElementById('modal-detalle-exp').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarDetalleExp() { document.getElementById('modal-detalle-exp').style.display = 'none'; document.body.style.overflow = ''; }
document.getElementById('modal-detalle-exp').addEventListener('click', function(e) { if (e.target === this) cerrarDetalleExp(); });

// ============================================================
// MODAL DETALLE ACADÉMICA
// ============================================================
function abrirDetalleAcaDirecto(i) {
    var d = window.previewAcademicasData[i];
    if (!d) return;
    document.getElementById('det-aca-inst').textContent = d.institucion;
    document.getElementById('det-aca-titulo').textContent = d.titulo;
    var fecha = d.fecha_inicio;
    if (d.fecha_fin) fecha += ' — ' + d.fecha_fin;
    else if (d.estudio_actual) fecha += ' — Actualidad';
    document.getElementById('det-aca-fecha-txt').textContent = fecha;
    var spPill = document.getElementById('det-aca-specialty-pill');
    if (d.specialty) { document.getElementById('det-aca-specialty-txt').textContent = d.specialty; spPill.style.display = 'inline-flex'; }
    else { spPill.style.display = 'none'; }
    document.getElementById('det-aca-desc').innerHTML = d.descripcion || '<em style="color:#94a3b8;">Sin descripción disponible.</em>';
    var certsDiv = document.getElementById('det-aca-certs');
    certsDiv.innerHTML = '';
    if (d.evidencias && d.evidencias.length) {
        certsDiv.style.display = 'flex';
        d.evidencias.forEach(function(ev) {
            if (ev.ext === 'pdf') {
                certsDiv.innerHTML += '<a href="' + ev.url + '" target="_blank" class="cert-btn" onmouseover="this.style.background=\'#d1fae5\'" onmouseout="this.style.background=\'#f0fdf9\'"><i class="fas fa-file-pdf"></i> Ver PDF</a>';
            } else {
                certsDiv.innerHTML += '<button onclick="abrirLightbox(\'' + ev.url + '\')" class="cert-btn" onmouseover="this.style.background=\'#d1fae5\'" onmouseout="this.style.background=\'#f0fdf9\'"><i class="fas fa-certificate"></i> Ver certificado</button>';
            }
        });
    } else { certsDiv.style.display = 'none'; }
    document.getElementById('modal-detalle-aca').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarDetalleAca() { document.getElementById('modal-detalle-aca').style.display = 'none'; document.body.style.overflow = ''; }
document.getElementById('modal-detalle-aca').addEventListener('click', function(e) { if (e.target === this) cerrarDetalleAca(); });

// ============================================================
// MODAL PROYECTOS
// ============================================================
function previewEscapeHtml(s) {
    if (s == null) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function abrirModalPorId(projectId) {
    var data = (window.previewProjectsById || {})[projectId];
    if (!data) return;
    var target = document.getElementById('project-card-' + projectId);
    if (target) target.scrollIntoView({ behavior: 'smooth', block: 'center' });
    abrirModal(data);
}

function abrirModal(data) {
    document.getElementById('modal-nombre').textContent = data.nombre;
    var badgesDiv = document.getElementById('modal-badges');
    badgesDiv.innerHTML = '';
    var badge = function(t,bg,c){ return '<span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:'+bg+';color:'+c+';">'+t+'</span>'; };
    var badgeIcon = function(ic,t,bg,c){ return '<span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:'+bg+';color:'+c+';"><i class="'+ic+'" style="font-size:10px;"></i>'+t+'</span>'; };
    if (data.estado) { var bg=data.estado==='Completado'?'#d1fae5':data.estado==='En curso'?'#fef3c7':'#f1f5f9'; var c=data.estado==='Completado'?'#065f46':data.estado==='En curso'?'#92400e':'#64748b'; badgesDiv.innerHTML+=badge(previewEscapeHtml(data.estado),bg,c); }
    if (data.rol)    badgesDiv.innerHTML+=badgeIcon('fas fa-user-check',previewEscapeHtml(data.rol),'#ede9fe','#5b21b6');
    if (data.cliente)badgesDiv.innerHTML+=badgeIcon('fas fa-building',previewEscapeHtml(data.cliente),'#f1f5f9','#475569');
    var fechasDiv = document.getElementById('modal-fechas');
    fechasDiv.innerHTML = '';
    if (data.fecha_inicio) fechasDiv.innerHTML+='<span><i class="far fa-calendar-alt" style="color:#94a3b8;margin-right:4px;"></i>Inicio: <strong>'+previewEscapeHtml(data.fecha_inicio)+'</strong></span>';
    if (data.fecha_fin)    fechasDiv.innerHTML+='<span><i class="far fa-calendar-alt" style="color:#94a3b8;margin-right:4px;"></i>Fin: <strong>'+previewEscapeHtml(data.fecha_fin)+'</strong></span>';
    document.getElementById('modal-descripcion').innerHTML = data.descripcion || '';
    var tecDiv=document.getElementById('modal-tecnologias'), tecSection=document.getElementById('modal-tec-section');
    tecDiv.innerHTML='';
    if (data.tecnologias&&data.tecnologias.length) { tecSection.style.display='block'; data.tecnologias.forEach(function(t){tecDiv.innerHTML+='<span class="tec-badge">'+previewEscapeHtml(t)+'</span>';}); }
    else { tecSection.style.display='none'; }
    var evDiv=document.getElementById('modal-evidencias-lista'), evSec=document.getElementById('modal-evidencias');
    evDiv.innerHTML='';
    if (data.evidencias&&data.evidencias.length) {
        evSec.style.display='block';
        document.getElementById('modal-ev-count').textContent='('+data.evidencias.length+')';
        data.evidencias.forEach(function(ev) {
            var item=document.createElement('div');
            if (ev.tipo==='imagen'&&ev.imagen) {
                item.style.cssText='margin-bottom:12px;background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;';
                item.innerHTML='<div style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:#f8fafc;border-bottom:1px solid #e2e8f0;"><div style="width:36px;height:36px;background:#0abf9e15;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image" style="color:#0abf9e;font-size:18px;"></i></div><strong style="font-size:14px;color:#0f172a;flex:1;">Imagen del proyecto</strong><button onclick="abrirLightbox(\''+ev.imagen+'\')" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:40px;border:1.5px solid #0abf9e;background:#f0fdf9;color:#0abf9e;font-size:12px;font-weight:600;cursor:pointer;" onmouseover="this.style.background=\'#0abf9e\';this.style.color=\'white\';" onmouseout="this.style.background=\'#f0fdf9\';this.style.color=\'#0abf9e\';"><i class="fas fa-eye"></i> Ver</button></div><div style="padding:16px;position:relative;cursor:pointer;" onclick="abrirLightbox(\''+ev.imagen+'\')"><img src="'+ev.imagen+'" style="width:100%;max-height:220px;object-fit:cover;border-radius:12px;" onmouseover="this.style.opacity=\'0.85\'" onmouseout="this.style.opacity=\'1\'"></div>';
            } else if (ev.tipo==='enlace'&&ev.url) {
                item.style.cssText='margin-bottom:12px;background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;';
                item.innerHTML='<div style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:#f8fafc;border-bottom:1px solid #e2e8f0;"><div style="width:36px;height:36px;background:#0abf9e15;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-link" style="color:#0abf9e;font-size:18px;"></i></div><strong style="font-size:14px;color:#0f172a;flex:1;">'+previewEscapeHtml(ev.titulo||'Enlace')+'</strong></div><div style="padding:16px;display:flex;align-items:center;justify-content:space-between;gap:10px;"><span style="font-size:12px;color:#94a3b8;word-break:break-all;flex:1;">'+previewEscapeHtml(ev.url)+'</span><a href="'+ev.url+'" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:40px;background:#0abf9e;color:white;font-size:12px;font-weight:600;text-decoration:none;white-space:nowrap;" onmouseover="this.style.background=\'#07866e\';" onmouseout="this.style.background=\'#0abf9e\';"><i class="fas fa-eye"></i> Ver enlace</a></div>';
            } else if (ev.tipo==='repositorio'&&ev.url) {
                var icon=ev.plataforma==='GitLab'?'fa-gitlab':ev.plataforma==='Bitbucket'?'fa-bitbucket':'fa-github';
                item.style.cssText='margin-bottom:12px;background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;';
                item.innerHTML='<div style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:#f8fafc;border-bottom:1px solid #e2e8f0;"><div style="width:36px;height:36px;background:#0abf9e15;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fab '+icon+'" style="color:#0abf9e;font-size:18px;"></i></div><strong style="font-size:14px;color:#0f172a;flex:1;">'+previewEscapeHtml(ev.titulo||'Repositorio')+'</strong></div><div style="padding:16px;display:flex;align-items:center;justify-content:space-between;gap:10px;"><span style="font-size:12px;color:#94a3b8;word-break:break-all;flex:1;">'+previewEscapeHtml(ev.url)+'</span><a href="'+ev.url+'" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:40px;background:#0abf9e;color:white;font-size:12px;font-weight:600;text-decoration:none;white-space:nowrap;" onmouseover="this.style.background=\'#07866e\';" onmouseout="this.style.background=\'#0abf9e\';"><i class="fas fa-eye"></i> Ver repositorio</a></div>';
            }
            evDiv.appendChild(item);
        });
    } else { evSec.style.display='none'; }
    document.getElementById('modal-proyecto').style.display='flex';
    document.body.style.overflow='hidden';
}

function cerrarModal() {
    document.getElementById('modal-proyecto').style.display='none';
    document.body.style.overflow='';
}
document.getElementById('modal-proyecto').addEventListener('click', function(e) { if (e.target===this) cerrarModal(); });

// ============================================================
// CONTROLES DE MODALES
// ============================================================
function abrirModalTodosProyectos() {
    var el=document.getElementById('modal-todos-proyectos');
    if(el){el.style.display='flex';document.body.style.overflow='hidden';}
}
function cerrarModalTodosProyectos() {
    var el=document.getElementById('modal-todos-proyectos');
    if(el){el.style.display='none';document.body.style.overflow='';}
    var inp=document.getElementById('search-proyectos'); if(inp)inp.value='';
    filtrarProyectos('');
}
function abrirModalExperiencias() {
    var el=document.getElementById('modal-todos-experiencias');
    if(el){el.style.display='flex';document.body.style.overflow='hidden';}
}
function cerrarModalExperiencias() {
    var el=document.getElementById('modal-todos-experiencias');
    if(el){el.style.display='none';document.body.style.overflow='';}
}
function abrirModalAcademicas() {
    var el=document.getElementById('modal-todos-academicas');
    if(el){el.style.display='flex';document.body.style.overflow='hidden';}
}
function cerrarModalAcademicas() {
    var el=document.getElementById('modal-todos-academicas');
    if(el){el.style.display='none';document.body.style.overflow='';}
}
function abrirModalTecnicas() { var el=document.getElementById('modal-todos-tecnicas'); if(el){el.style.display='flex';document.body.style.overflow='hidden';} }
function cerrarModalTecnicas() { var el=document.getElementById('modal-todos-tecnicas'); if(el){el.style.display='none';document.body.style.overflow='';} }
function abrirModalBlandas() { var el=document.getElementById('modal-todos-blandas'); if(el){el.style.display='flex';document.body.style.overflow='hidden';} }
function cerrarModalBlandas() { var el=document.getElementById('modal-todos-blandas'); if(el){el.style.display='none';document.body.style.overflow='';} }
function abrirModalIdiomas() { var el=document.getElementById('modal-todos-idiomas'); if(el){el.style.display='flex';document.body.style.overflow='hidden';} }
function cerrarModalIdiomas() { var el=document.getElementById('modal-todos-idiomas'); if(el){el.style.display='none';document.body.style.overflow='';} }
function abrirModalCompartir() { document.getElementById('modal-compartir').style.display='flex'; }
function cerrarModalCompartir() { document.getElementById('modal-compartir').style.display='none'; document.getElementById('btn-copiar-link').textContent='Copiar'; }
document.getElementById('modal-compartir').addEventListener('click', function(e) { if(e.target===this)cerrarModalCompartir(); });

// Cerrar overlays al clic en fondo
['modal-todos-proyectos','modal-todos-experiencias','modal-todos-academicas','modal-todos-tecnicas','modal-todos-blandas','modal-todos-idiomas'].forEach(function(id) {
    var el=document.getElementById(id);
    if(el) el.addEventListener('click', function(e){ if(e.target===this){this.style.display='none';document.body.style.overflow='';} });
});

function copiarLinkPortafolio() {
    var input=document.getElementById('share-link-input'); input.select(); input.setSelectionRange(0,99999);
    navigator.clipboard.writeText(input.value).then(function(){ var btn=document.getElementById('btn-copiar-link'); btn.textContent='¡Copiado!'; setTimeout(function(){btn.textContent='Copiar';},2000); });
}
function shareTo(platform) {
    var link=encodeURIComponent(document.getElementById('share-link-input').value);
    var text=encodeURIComponent('¡Mira mi portafolio profesional en DevFolio!');
    var urls={whatsapp:'https://api.whatsapp.com/send?text='+text+' '+link,facebook:'https://www.facebook.com/sharer/sharer.php?u='+link,twitter:'https://twitter.com/intent/tweet?text='+text+'&url='+link,email:'mailto:?subject='+text+'&body=Puedes ver mi portafolio aquí: '+link};
    if(urls[platform])window.open(urls[platform],'_blank','width=600,height=400');
}

// ============================================================
// PDF / IMAGEN - VERSIÓN COMPLETA SIN EVIDENCIAS NI CERTIFICADOS
// ============================================================
function crearCopiaCompleta() {
    var original = document.querySelector('.preview-container');
    var clone = original.cloneNode(true);
    
    // Expandir descripciones colapsadas
    clone.querySelectorAll('.description.collapsed, .proyecto-desc-wrap.collapsed').forEach(function(el) {
        el.classList.remove('collapsed');
        el.classList.add('expanded');
        el.style.maxHeight = 'none';
        el.style.overflow = 'visible';
    });
    
    // Eliminar botones "Ver más"
    clone.querySelectorAll('.ver-mas-btn').forEach(function(btn) { btn.remove(); });
    clone.querySelectorAll('.btn-ver-todos').forEach(function(btn) { btn.style.display = 'none'; });
    
    // INYECTAR TODOS LOS DATOS COMPLETOS DESDE DIVS OCULTOS (sin certificados/evidencias)
    var seccionesCompletas = [
        { source: '#proyectos-completos', target: '#proyectos-grid', inner: '.cards-grid' },
        { source: '#experiencias-completas', target: '#experiencias-grid', inner: '.cards-grid' },
        { source: '#academicas-completas', target: '#academicas-grid', inner: '.cards-grid' },
        { source: '#tecnicas-completas', target: '#tecnicas-grid', inner: '' },
        { source: '#blandas-completas', target: '#blandas-grid', inner: '.skills-container' },
        { source: '#idiomas-completos', target: '#idiomas-grid', inner: '.idiomas-preview-grid' }
    ];
    
    seccionesCompletas.forEach(function(sec) {
        var sourceDiv = document.querySelector(sec.source);
        if (sourceDiv) {
            var targetDiv = clone.querySelector(sec.target);
            if (targetDiv) {
                var content = sec.inner ? sourceDiv.querySelector(sec.inner) : sourceDiv;
                if (content) {
                    targetDiv.innerHTML = content.innerHTML;
                }
            }
        }
    });
    
    // Ocultar elementos flotantes
    ['fab-container-top', 'btn-volver-flotante'].forEach(function(cls) {
        var el = clone.querySelector('.' + cls);
        if (el) el.style.display = 'none';
    });
    
    // ELIMINAR COMPLETAMENTE cualquier botón o enlace de certificado/evidencia
    clone.querySelectorAll('.cert-btn, .idioma-cert-link, .idioma-modal-footer, .detail-certs-wrap').forEach(function(el) {
        el.remove();
    });
    
    // Eliminar cualquier enlace que pueda ser un certificado
    clone.querySelectorAll('a').forEach(function(a) {
        var text = a.textContent || '';
        if (text.indexOf('certificado') !== -1 || text.indexOf('PDF') !== -1 || 
            text.indexOf('Cert') !== -1 ||
            a.querySelector('.fa-certificate') || a.querySelector('.fa-file-pdf')) {
            a.remove();
        }
    });
    
    return clone;
}

async function descargarPDF() {
    document.getElementById('fabMenuTop').classList.remove('open');
    document.getElementById('fabIconTop').className='fas fa-ellipsis-h';
    Swal.fire({title:'Generando PDF completo...',text:'Por favor espera...',allowOutsideClick:false,showConfirmButton:false,didOpen:function(){Swal.showLoading();}});
    try {
        var clone = crearCopiaCompleta();
        var tempDiv = document.createElement('div');
        Object.assign(tempDiv.style, {position:'absolute', left:'-9999px', top:'-9999px', width:'1200px', backgroundColor:'white'});
        tempDiv.appendChild(clone);
        document.body.appendChild(tempDiv);
        clone.style.cssText = 'max-width:1200px;margin:0 auto;';
        
        setTimeout(async function() {
            try {
                var canvas = await html2canvas(tempDiv, {scale: 2.5, useCORS: true, backgroundColor: '#ffffff', logging: false, windowWidth: tempDiv.scrollWidth, windowHeight: tempDiv.scrollHeight});
                var jsPDF = window.jspdf.jsPDF;
                var imgData = canvas.toDataURL('image/png');
                var pdf = new jsPDF({unit: 'mm', format: 'a4', orientation: 'portrait'});
                var pw = pdf.internal.pageSize.getWidth(), ph = pdf.internal.pageSize.getHeight();
                var iw = pw - 20, ih = (canvas.height * iw) / canvas.width;
                var pos = 10, left = ih - (ph - 20), page = 1;
                pdf.addImage(imgData, 'PNG', 10, pos, iw, ih);
                while (left > 0) {
                    pdf.addPage();
                    pos = 10 - (page * (ph - 20));
                    pdf.addImage(imgData, 'PNG', 10, pos, iw, ih);
                    left -= (ph - 20);
                    page++;
                }
                pdf.save('portafolio_completo.pdf');
                document.body.removeChild(tempDiv);
                Swal.fire({icon:'success',title:'¡PDF descargado!',text:'Se ha generado el portafolio completo',toast:true,position:'top-end',showConfirmButton:false,timer:3000});
            } catch(err) {
                document.body.removeChild(tempDiv);
                throw err;
            }
        }, 800);
    } catch(e) {
        console.error(e);
        Swal.fire({icon:'error',title:'Error',text:'No se pudo generar el PDF completo.'});
    }
}

async function descargarImagen() {
    document.getElementById('fabMenuTop').classList.remove('open');
    document.getElementById('fabIconTop').className='fas fa-ellipsis-h';
    Swal.fire({title:'Generando imagen completa...',text:'Por favor espera...',allowOutsideClick:false,showConfirmButton:false,didOpen:function(){Swal.showLoading();}});
    try {
        var clone = crearCopiaCompleta();
        var tempDiv = document.createElement('div');
        Object.assign(tempDiv.style, {position:'absolute', left:'-9999px', top:'-9999px', width:'1200px', backgroundColor:'white'});
        tempDiv.appendChild(clone);
        document.body.appendChild(tempDiv);
        clone.style.cssText = 'max-width:1200px;margin:0 auto;';
        
        setTimeout(async function() {
            try {
                var canvas = await html2canvas(tempDiv, {scale: 2.5, useCORS: true, backgroundColor: '#ffffff', logging: false, windowWidth: tempDiv.scrollWidth, windowHeight: tempDiv.scrollHeight});
                var link = document.createElement('a');
                link.download = 'portafolio_completo.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                document.body.removeChild(tempDiv);
                Swal.fire({icon:'success',title:'¡Imagen descargada!',text:'Se ha generado la imagen completa',toast:true,position:'top-end',showConfirmButton:false,timer:3000});
            } catch(err) {
                document.body.removeChild(tempDiv);
                throw err;
            }
        }, 800);
    } catch(e) {
        console.error(e);
        Swal.fire({icon:'error',title:'Error',text:'No se pudo generar la imagen completa.'});
    }
}

// Tecla ESC para cerrar modales
document.addEventListener('keydown', function(e) {
    if (e.key !== 'Escape') return;
    ['modal-todos-proyectos','modal-todos-experiencias','modal-todos-academicas','modal-todos-tecnicas','modal-todos-blandas','modal-todos-idiomas'].forEach(function(id){
        var el=document.getElementById(id); if(el&&el.style.display==='flex'){el.style.display='none';document.body.style.overflow='';}
    });
    if(document.getElementById('modal-detalle-exp').style.display==='flex') cerrarDetalleExp();
    if(document.getElementById('modal-detalle-aca').style.display==='flex') cerrarDetalleAca();
    if(document.getElementById('modal-proyecto').style.display==='flex') cerrarModal();
});
</script>

</body>
</html>