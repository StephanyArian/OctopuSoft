<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Preview | {{ $user->first_name ?? 'Portafolio' }} {{ $user->last_name ?? '' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/preview.css') }}?v={{ time() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        .empty-message-preview {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            font-style: italic;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px dashed #cbd5e1;
            grid-column: 1 / -1;
        }
        .empty-message-preview i { color: #0abf9e; margin-right: 8px; }

        .btn-volver-flotante {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 999;
            background: var(--burg-mid, #4a1030);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 40px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.2s;
            font-family: inherit;
            text-decoration: none;
        }
        .btn-volver-flotante:hover { transform: translateY(-2px); background: var(--burg-deep, #2d0a1e); }
        .theme-sunset .btn-volver-flotante  { background: #f97316; }
        .theme-sunset .btn-volver-flotante:hover  { background: #ea580c; }
        .theme-emerald .btn-volver-flotante { background: #10b981; }
        .theme-emerald .btn-volver-flotante:hover { background: #047857; }
        .theme-midnight .btn-volver-flotante { background: #a855f7; }
        .theme-midnight .btn-volver-flotante:hover { background: #7e22ce; }
        .theme-ocean .btn-volver-flotante  { background: #00b4d8; }
        .theme-ocean .btn-volver-flotante:hover  { background: #0077b6; }
        .theme-sakura .btn-volver-flotante { background: #ec4899; }
        .theme-sakura .btn-volver-flotante:hover { background: #db2777; }

        .fab-container {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .fab-menu {
            background: white;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.18);
            border: 1px solid #edf0f4;
            min-width: 200px;
            overflow: hidden;
            opacity: 0;
            transform: translateY(10px) scale(0.97);
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
            margin-bottom: 10px;
        }
        .fab-menu.open { opacity: 1; transform: translateY(0) scale(1); pointer-events: all; }
        .fab-item {
            display: flex; align-items: center; gap: 12px;
            width: 100%; padding: 12px 18px;
            background: none; border: none; font-family: inherit;
            font-size: 13px; font-weight: 600; color: #2d0a1e;
            cursor: pointer; transition: background 0.15s, color 0.15s;
        }
        .fab-item:hover { background: #f5f6f8; color: #07866e; }
        .fab-item i { width: 20px; text-align: center; font-size: 14px; color: #0abf9e; flex-shrink: 0; }
        .fab-divider { height: 1px; background: #edf0f4; margin: 4px 0; }
        .fab-button {
            background: #0abf9e; color: white; border: none;
            padding: 12px 22px; border-radius: 40px; cursor: pointer;
            font-size: 14px; font-weight: 700; display: flex;
            align-items: center; gap: 10px;
            box-shadow: 0 4px 16px rgba(10,191,158,0.35);
            transition: all 0.2s; font-family: inherit;
        }
        .fab-button:hover { background: #07866e; transform: translateY(-2px); }

        .preview-bottom-bar {
            background: white; border-top: 1px solid #e2e8f0;
            padding: 16px 24px; display: flex;
            justify-content: center; margin-top: 40px;
        }
        .btn-publicar-main {
            background: #0abf9e; color: white; border: none;
            padding: 12px 40px; border-radius: 40px;
            font-size: 15px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; gap: 10px;
            transition: all 0.2s; font-family: inherit;
            box-shadow: 0 2px 8px rgba(10,191,158,0.3);
        }
        .btn-publicar-main:hover { background: #07866e; transform: translateY(-2px); }

        @media (max-width: 768px) {
            .btn-volver-flotante { top: 10px; left: 10px; padding: 8px 16px; font-size: 12px; }
            .fab-container { bottom: 20px; left: 20px; }
        }

        .btn-ver-todos {
            display: flex; justify-content: center; margin-top: 30px;
        }
        .btn-ver-todos button {
            background: #0abf9e; color: white; border: none;
            padding: 10px 24px; border-radius: 40px; font-size: 13px;
            font-weight: 600; cursor: pointer; display: flex;
            align-items: center; gap: 8px; transition: all 0.2s;
        }
        .btn-ver-todos button:hover { background: #07866e; transform: translateY(-2px); }

        .modal-todos-proyectos {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.7); z-index: 10000;
            align-items: center; justify-content: center;
            backdrop-filter: blur(3px);
        }
        .modal-todos-content {
            background: white; border-radius: 20px;
            max-width: 950px; width: 90%; max-height: 85vh;
            overflow-y: auto; position: relative;
            animation: modalFadeIn 0.2s ease;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to   { opacity: 1; transform: scale(1); }
        }
        .modal-todos-header {
            padding: 18px 24px; border-bottom: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; background: white; z-index: 10;
        }
        .modal-todos-header h2 { font-size: 1.2rem; font-weight: 700; color: #0f172a; margin: 0; }
        .modal-todos-header h2 i { color: #0abf9e; margin-right: 8px; }
        .close-todos-modal {
            background: none; border: none; font-size: 22px;
            cursor: pointer; color: #94a3b8; transition: all 0.2s;
        }
        .close-todos-modal:hover { color: #ef4444; }
        .todos-proyectos-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px; padding: 24px;
        }
        .proyecto-card-modal {
            background: #fff; border-radius: 16px; padding: 16px;
            cursor: pointer; transition: all 0.2s;
            border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .proyecto-card-modal:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); border-color: #0abf9e; }
        .proyecto-card-modal h4 { font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        .proyecto-card-modal .proyecto-fecha { font-size: 0.7rem; color: #94a3b8; margin: 8px 0; }
        .estado-badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 0.65rem; font-weight: 600; }
        .estado-completado { background: #d1fae5; color: #065f46; }
        .estado-curso      { background: #fef3c7; color: #92400e; }
        .estado-default    { background: #f1f5f9; color: #64748b; }

        .card .description.collapsed {
            display: -webkit-box; -webkit-line-clamp: 3;
            -webkit-box-orient: vertical; overflow: hidden;
        }
        .card .description.expanded { display: block; }

        #modal-proyecto {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 9999;
            align-items: center; justify-content: center;
        }
        .modal-proyecto-inner {
            background: #fff; border-radius: 12px;
            max-width: 680px; width: 90%; max-height: 88vh;
            overflow-y: auto; position: relative;
        }
        .modal-proyecto-head {
            padding: 24px 28px; border-bottom: 1px solid #f0f0f0;
            display: flex; justify-content: space-between; align-items: flex-start;
        }
        .modal-proyecto-head h2 { color: #1a0a2e; font-size: 22px; margin: 0 0 10px; }
        .modal-proyecto-fechas {
            padding: 12px 28px; background: #f9f9f9;
            border-bottom: 1px solid #f0f0f0; font-size: 13px;
            color: #666; display: flex; gap: 20px;
        }
        .modal-proyecto-body { padding: 24px 28px; }
        .modal-proyecto-section-label {
            font-size: 11px; font-weight: 600; color: #888;
            text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px;
        }

        .idioma-barra-wrap { height: 8px; background: #edf0f4; border-radius: 10px; overflow: hidden; margin-top: 10px; }
        .idioma-barra-fill-custom { height: 100%; border-radius: 10px; transition: width 0.8s ease; }

        @media (max-width: 768px) {
            .todos-proyectos-grid { grid-template-columns: 1fr; padding: 16px; }
            .top-actions-bar { padding: 10px 20px !important; }
        }

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
        .top-actions-bar .btn-volver-flotante i {
            color: #475569;
            transition: color 0.2s ease;
        }
        .top-actions-bar .btn-volver-flotante:hover i {
            color: #0abf9e;
        }
        
        .theme-sunset.top-actions-bar .btn-volver-flotante:hover { background: #fff7ed; color: #f97316; border-color: rgba(249, 115, 22, 0.3); }
        .theme-sunset.top-actions-bar .btn-volver-flotante:hover i { color: #f97316; }
        
        .theme-emerald.top-actions-bar .btn-volver-flotante:hover { background: #ecfdf5; color: #10b981; border-color: rgba(16, 185, 129, 0.3); }
        .theme-emerald.top-actions-bar .btn-volver-flotante:hover i { color: #10b981; }
        
        .theme-midnight.top-actions-bar .btn-volver-flotante:hover { background: #fdf4ff; color: #a855f7; border-color: rgba(168, 85, 247, 0.3); }
        .theme-midnight.top-actions-bar .btn-volver-flotante:hover i { color: #a855f7; }
        
        .theme-ocean.top-actions-bar .btn-volver-flotante:hover { background: #f0fdfa; color: #00b4d8; border-color: rgba(0, 180, 216, 0.3); }
        .theme-ocean.top-actions-bar .btn-volver-flotante:hover i { color: #00b4d8; }
        
        .theme-sakura.top-actions-bar .btn-volver-flotante:hover { background: #fdf2f8; color: #ec4899; border-color: rgba(236, 72, 153, 0.3); }
        .theme-sakura.top-actions-bar .btn-volver-flotante:hover i { color: #ec4899; }

        .top-actions-bar .fab-container {
            position: relative;
            bottom: auto;
            left: auto;
            z-index: auto;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        .top-actions-bar .fab-button {
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
        .top-actions-bar .fab-button:hover {
            background: #f0fdf9;
            color: #0abf9e;
            border-color: rgba(10, 191, 158, 0.3);
            transform: translateY(-1px);
        }
        
        .theme-sunset.top-actions-bar .fab-button:hover { background: #fff7ed; color: #f97316; border-color: rgba(249, 115, 22, 0.3); }
        .theme-emerald.top-actions-bar .fab-button:hover { background: #ecfdf5; color: #10b981; border-color: rgba(16, 185, 129, 0.3); }
        .theme-midnight.top-actions-bar .fab-button:hover { background: #fdf4ff; color: #a855f7; border-color: rgba(168, 85, 247, 0.3); }
        .theme-ocean.top-actions-bar .fab-button:hover { background: #f0fdfa; color: #00b4d8; border-color: rgba(0, 180, 216, 0.3); }
        .theme-sakura.top-actions-bar .fab-button:hover { background: #fdf2f8; color: #ec4899; border-color: rgba(236, 72, 153, 0.3); }

        .top-actions-bar .fab-menu {
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
        .top-actions-bar .fab-menu.open {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: all;
        }
        
    .btn-volver-flotante {
        position: fixed !important;
        top: 20px !important;
        left: 20px !important;
        z-index: 1100 !important;
        background: var(--burg-mid, #4a1030) !important;
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
        box-shadow: 0 4px 12px rgba(0,0,0,0.25) !important;
        transition: all 0.2s !important;
        font-family: inherit !important;
        text-decoration: none !important;
    }
    .btn-volver-flotante:hover {
        transform: translateY(-2px) !important;
        background: var(--burg-deep, #2d0a1e) !important;
    }
    .theme-sunset .btn-volver-flotante  { background: #f97316 !important; }
    .theme-sunset .btn-volver-flotante:hover  { background: #ea580c !important; }
    .theme-emerald .btn-volver-flotante { background: #10b981 !important; }
    .theme-emerald .btn-volver-flotante:hover { background: #047857 !important; }
    .theme-midnight .btn-volver-flotante { background: #a855f7 !important; }
    .theme-midnight .btn-volver-flotante:hover { background: #7e22ce !important; }
    .theme-ocean .btn-volver-flotante  { background: #00b4d8 !important; }
    .theme-ocean .btn-volver-flotante:hover  { background: #0077b6 !important; }
    .theme-sakura .btn-volver-flotante { background: #ec4899 !important; }
    .theme-sakura .btn-volver-flotante:hover { background: #db2777 !important; }

    .fab-container {
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
    .fab-menu {
        position: relative !important;
        bottom: auto !important;
        left: auto !important;
        margin-bottom: 10px !important;
    }
    .fab-button {
        background: #0abf9e !important;
        color: white !important;
        border: none !important;
        padding: 12px 22px !important;
        border-radius: 40px !important;
        cursor: pointer !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        box-shadow: 0 4px 16px rgba(10,191,158,0.45) !important;
        transition: all 0.2s !important;
        font-family: inherit !important;
    }
    .fab-button:hover { background: #07866e !important; transform: translateY(-2px) !important; }

    .top-actions-bar { display: none !important; }

    @media (max-width: 768px) {
        .btn-volver-flotante { top: 12px !important; left: 12px !important; padding: 8px 16px !important; font-size: 12px !important; }
        .fab-container { bottom: 20px !important; left: 20px !important; }
    }
        
    </style>
</head>
<body>

@if($errors->has('publish'))
<div style="position:fixed;top:80px;left:50%;transform:translateX(-50%);z-index:99999;width:90%;max-width:500px;">
    <div style="background:#fff3cd;border:2px solid #ffc107;border-radius:16px;padding:20px 24px;box-shadow:0 12px 40px rgba(0,0,0,0.15);animation:slideDown 0.3s ease;">
        <div style="display:flex;align-items:flex-start;gap:14px;">
            <div style="background:#ffc107;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-exclamation-triangle" style="color:#856404;font-size:20px;"></i>
            </div>
            <div>
                <h4 style="margin:0 0 8px;font-size:16px;color:#856404;font-weight:700;">Portafolio incompleto</h4>
                <p style="margin:0 0 12px;font-size:14px;color:#856404;">
                    {{ $errors->first('publish') }}
                </p>

                @if(session('publish_missing'))
                    <div style="background:#fff;border-radius:10px;padding:12px 16px;border:1px solid #ffc107;">
                        <strong style="font-size:12px;color:#856404;display:block;margin-bottom:8px;">
                            📋 Debes completar:
                        </strong>
                        <ul style="margin:0;padding-left:20px;list-style:disc;">
                            @foreach(session('publish_missing') as $campo)
                                <li style="font-size:13px;color:#856404;margin-bottom:4px;">{{ $campo }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button onclick="this.closest('div[style*=\"position:fixed\"]').remove()" 
                        style="margin-top:12px;background:rgba(255,193,7,0.3);border:1px solid #ffc107;color:#856404;padding:6px 18px;border-radius:30px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;">
                    <i class="fas fa-times"></i> Entendido
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes slideDown {
        from { opacity: 0; transform: translateX(-50%) translateY(-30px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }
</style>
@endif

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'success',
            title: '✅ ¡Publicado!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#0abf9e',
            timer: 3000,
            timerProgressBar: true
        });
    });
</script>
@endif

@php
    $allowedHtmlTags = '<p><br><strong><b><em><i><u><s><strike><del><sup><sub><ul><ol><li><a><span><h1><h2><h3><blockquote><pre><div>';

    $proyectosRecientes  = $proyectos->take(2);
    $proyectosRestantes  = $proyectos->skip(2);
    $limiteMostrar = 2;

    $experienciasRecientes  = $experiencias->take($limiteMostrar);
    $experienciasRestantes  = $experiencias->skip($limiteMostrar);

    $academicasRecientes = $academicas->take($limiteMostrar);
    $academicasRestantes = $academicas->skip($limiteMostrar);

    $habilidadesFrontendRecientes = ($habilidadesTecnicasFrontend ?? collect())->take($limiteMostrar);
    $habilidadesFrontendRestantes = ($habilidadesTecnicasFrontend ?? collect())->skip($limiteMostrar);
    $habilidadesBackendRecientes  = ($habilidadesTecnicasBackend  ?? collect())->take($limiteMostrar);
    $habilidadesBackendRestantes  = ($habilidadesTecnicasBackend  ?? collect())->skip($limiteMostrar);

    $habilidadesBlandasRecientes = $habilidadesBlandas->take($limiteMostrar);
    $habilidadesBlandasRestantes = $habilidadesBlandas->skip($limiteMostrar);

    $idiomasRecientes = $idiomas->take($limiteMostrar);
    $idiomasRestantes = $idiomas->skip($limiteMostrar);

    $banderas = [
        'inglés'=>'🇬🇧','ingles'=>'🇬🇧','español'=>'🇧🇴','espanol'=>'🇧🇴',
        'portugués'=>'🇧🇷','portugues'=>'🇧🇷','francés'=>'🇫🇷','frances'=>'🇫🇷',
        'alemán'=>'🇩🇪','aleman'=>'🇩🇪','italiano'=>'🇮🇹','chino'=>'🇨🇳',
        'japonés'=>'🇯🇵','japones'=>'🇯🇵','coreano'=>'🇰🇷','árabe'=>'🇸🇦','arabe'=>'🇸🇦',
        'ruso'=>'🇷🇺','hindi'=>'🇮🇳','hindú'=>'🇮🇳','indu'=>'🇮🇳',
        'holandés'=>'🇳🇱','holandes'=>'🇳🇱','sueco'=>'🇸🇪','noruego'=>'🇳🇴',
        'danés'=>'🇩🇰','danes'=>'🇩🇰','polaco'=>'🇵🇱','turco'=>'🇹🇷',
        'griego'=>'🇬🇷','hebreo'=>'🇮🇱','tailandés'=>'🇹🇭','tailandes'=>'🇹🇭',
        'vietnamita'=>'🇻🇳','indonesio'=>'🇮🇩','catalán'=>'🏳️','catalan'=>'🏳️',
        'mandarin'=>'🇨🇳','mandarín'=>'🇨🇳',
    ];
    $codigos = [
        'inglés'=>'EN','ingles'=>'EN','español'=>'ES','espanol'=>'ES',
        'francés'=>'FR','frances'=>'FR','alemán'=>'DE','aleman'=>'DE',
        'portugués'=>'PT','portugues'=>'PT','italiano'=>'IT','chino'=>'ZH',
        'japonés'=>'JP','japones'=>'JP','coreano'=>'KO','árabe'=>'AR','arabe'=>'AR',
        'ruso'=>'RU','hindi'=>'HI','indu'=>'HI','mandarin'=>'ZH','mandarín'=>'ZH',
    ];
    $colorFondos = ['#0abf9e','#3b82f6','#a855f7','#f59e0b','#f43f5e','#22d3ee','#4ade80','#fb923c'];
    $coloresBarra = [
        'linear-gradient(90deg,#07866e,#0abf9e)',
        'linear-gradient(90deg,#1d4ed8,#3b82f6)',
        'linear-gradient(90deg,#7c3aed,#a855f7)',
        'linear-gradient(90deg,#b45309,#f59e0b)',
        'linear-gradient(90deg,#be123c,#f43f5e)',
        'linear-gradient(90deg,#0e7490,#22d3ee)',
        'linear-gradient(90deg,#15803d,#4ade80)',
        'linear-gradient(90deg,#9a3412,#fb923c)',
    ];
    $temaActual = $user->portfolio->color_theme ?? 'default';
    $claseTema  = $temaActual !== 'default' ? 'theme-' . $temaActual : '';
@endphp

<a href="{{ route('dashboard') }}" class="btn-volver-flotante">
    <i class="fas fa-arrow-left"></i>
    <span>Volver</span>
</a>

<div class="fab-container" id="fabContainer">
    <div class="fab-menu" id="fabMenu">
        @if($tieneContenido ?? false)
            <button class="fab-item" onclick="descargarPDF()">
                <i class="fas fa-file-pdf"></i><span>Descargar PDF</span>
            </button>
            <button class="fab-item" onclick="descargarImagen()">
                <i class="fas fa-image"></i><span>Descargar imagen</span>
            </button>
        @else
            <button class="fab-item" style="opacity:0.5;cursor:not-allowed;" onclick="mostrarAlertaIncompleto()">
                <i class="fas fa-file-pdf"></i><span>Descargar PDF</span>
            </button>
            <button class="fab-item" style="opacity:0.5;cursor:not-allowed;" onclick="mostrarAlertaIncompleto()">
                <i class="fas fa-image"></i><span>Descargar imagen</span>
            </button>
        @endif
        
        <div class="fab-divider"></div>
        <button class="fab-item" onclick="toggleVibeSidebar(true); document.getElementById('fabMenu').classList.remove('open');">
            <i class="fas fa-palette"></i><span>Elegir Vibe</span>
        </button>
    </div>
    <button class="fab-button" id="fabButton" onclick="toggleFabMenu()">
        <i class="fas fa-ellipsis-h" id="fabIcon"></i><span class="fab-label">Más opciones</span>
    </button>
</div>

<div class="preview-container {{ $claseTema }}" id="previewContainer">

    <div class="profile-header">
        <div class="profile-info">
            <h1>{{ $user->first_name ?? 'Usuario' }} {{ $user->last_name ?? '' }}</h1>
            @if(!empty($user->profession->name))
                <div class="title">{{ $user->profession->name }}</div>
            @else
                <div class="title" style="color:#94a3b8;font-style:italic;">Sin profesión definida</div>
            @endif

            <div class="profile-contact-list">
                @if($user->city || $user->country)
                    <div class="contact-row"><i class="fas fa-map-marker-alt"></i><span>{{ $user->city ?? '' }}{{ $user->country ? ', '.$user->country : '' }}</span></div>
                @else
                    <div class="contact-row" style="color:#94a3b8;"><i class="fas fa-map-marker-alt"></i><span>Sin ubicación registrada</span></div>
                @endif

                @if(!empty($redes['correo']))
                    <div class="contact-row"><i class="fas fa-envelope"></i><span>{{ $redes['correo'] }}</span></div>
                @else
                    <div class="contact-row" style="color:#94a3b8;"><i class="fas fa-envelope"></i><span>Sin correo registrado</span></div>
                @endif

                @if(!empty($redes['whatsapp']))
                    <div class="contact-row"><i class="fab fa-whatsapp"></i><span>{{ $redes['whatsapp'] }}</span></div>
                @endif

                @if(!empty($user->biography))
                    <div class="contact-row" style="align-items:flex-start;">
                        <i class="fas fa-quote-left" style="margin-top:4px;"></i>
                        <div class="ql-snow" style="width:100%;">
                            <div class="ql-editor" style="padding:0;min-height:0;">{!! strip_tags($user->biography, $allowedHtmlTags) !!}</div>
                        </div>
                    </div>
                @else
                    <div class="contact-row" style="color:#94a3b8;"><i class="fas fa-quote-left"></i><span>Sin biografía registrada</span></div>
                @endif
            </div>

            <div class="profile-social-icons">
                @if(!empty($redes['linkedin']))   <a href="{{ $redes['linkedin'] }}"   target="_blank"><i class="fab fa-linkedin-in"></i></a>   @endif
                @if(!empty($redes['github']))     <a href="{{ $redes['github'] }}"     target="_blank"><i class="fab fa-github"></i></a>         @endif
                @if(!empty($redes['maps_url']))   <a href="{{ $redes['maps_url'] }}"   target="_blank"><i class="fas fa-map-marker-alt"></i></a> @endif
                @if(!empty($redes['whatsapp']))   <a href="https://wa.me/{{ preg_replace('/[^0-9]/','', $redes['whatsapp']) }}" target="_blank"><i class="fab fa-whatsapp"></i></a> @endif
                @if($user->portfolio && $user->portfolio->is_public) <a href="javascript:void(0)" onclick="abrirModalCompartir()"><i class="fas fa-share-nodes"></i></a> @endif
                @if(!empty($redes['correo']))     <a href="mailto:{{ $redes['correo'] }}"><i class="fas fa-envelope"></i></a>                   @endif
                @if(!empty($redes['otros']))      <a href="{{ $redes['otros'] }}" target="_blank"><i class="fas fa-globe"></i></a>               @endif
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

    <div class="section" id="section-experiencias">
        <h2><i class="fas fa-briefcase"></i> Experiencia laboral</h2>
        <div class="cards-grid" id="experiencias-grid">
    @foreach($experienciasRecientes as $i => $exp)
        <div class="card" onclick="abrirDetalleExp({{ $i }})">
            <h3>{{ $exp->empresa }}</h3>
            @if(!empty($exp->ubicacion))<div class="subtitle">{{ $exp->ubicacion }}</div>@endif
            @if(str_contains($exp->cargo, ' / '))
                <ul class="roles-list">
                    @foreach(explode(' / ', $exp->cargo) as $rol)<li>{{ $rol }}</li>@endforeach
                </ul>
            @else
                <div class="role-single">{{ $exp->cargo }}</div>
            @endif
            <div class="date">
                {{ \Carbon\Carbon::parse($exp->fecha_inicio)->format('d F Y') }}
                @if($exp->fecha_fin) — {{ \Carbon\Carbon::parse($exp->fecha_fin)->format('d F Y') }}
                @elseif($exp->trabajo_actual) — Actualidad
                @endif
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
                <button onclick="abrirModalExperiencias()"><i class="fas fa-briefcase"></i> Ver todas las experiencias ({{ $experiencias->count() }})</button>
            </div>
        @endif
        <div id="experiencias-completas" style="display:none;">
            <div class="cards-grid">
                @foreach($experiencias as $exp)
                <div class="card">
                    <h3>{{ $exp->empresa }}</h3>
                    <div class="role-single">{{ $exp->cargo }}</div>
                    <div class="date">
                        {{ \Carbon\Carbon::parse($exp->fecha_inicio)->format('d/m/Y') }}
                        @if($exp->fecha_fin) — {{ \Carbon\Carbon::parse($exp->fecha_fin)->format('d/m/Y') }}
                        @elseif($exp->trabajo_actual) — Actualidad
                        @endif
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

    <div class="section" id="section-academicas">
        <h2><i class="fas fa-graduation-cap"></i> Información académica</h2>
        <div class="cards-grid" id="academicas-grid">
    @foreach($academicasRecientes as $i => $aca)
        <div class="card" onclick="event.stopPropagation(); abrirDetalleAca({{ $i }})">
            <h3>{{ $aca->institucion }}</h3>
            <div class="subtitle">{{ $aca->titulo }}</div>
            @if(!empty($aca->specialty))<div class="specialty-badge"><i class="fas fa-tag"></i> {{ $aca->specialty }}</div>@endif
            <div class="date">
                {{ \Carbon\Carbon::parse($aca->fecha_inicio)->format('F Y') }}
                @if($aca->fecha_fin) — {{ \Carbon\Carbon::parse($aca->fecha_fin)->format('F Y') }}
                @elseif($aca->estudio_actual) — Actualidad
                @endif
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
            @php
                $hasEvAca = false;
                if (isset($aca->evidence_url) && $aca->evidence_url) {
                    $evArr = is_array($aca->evidence_url) ? $aca->evidence_url : (json_decode($aca->evidence_url, true) ?? []);
                    $hasEvAca = !empty($evArr);
                }
            @endphp
            <div style="font-size:11px;color:#0abf9e;margin-top:10px;display:flex;align-items:center;gap:4px;font-weight:600;">
                <i class="fas fa-expand-alt" style="font-size:9px;"></i> Clic para ver detalle completo{{ $hasEvAca ? ' + certificado' : '' }}
            </div>
        </div>
        @endforeach
    </div>

        @if($academicasRestantes->count() > 0)
            <div class="btn-ver-todos">
                <button onclick="abrirModalAcademicas()"><i class="fas fa-graduation-cap"></i> Ver toda la formación ({{ $academicas->count() }})</button>
            </div>
        @endif
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
                        @elseif($aca->estudio_actual) — Actualidad
                        @endif
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

    @if(($habilidadesTecnicasFrontend ?? collect())->count() > 0 || ($habilidadesTecnicasBackend ?? collect())->count() > 0)
    <div class="section" id="section-tecnicas">
        <h2><i class="fas fa-code"></i> Habilidades técnicas</h2>
        <div id="tecnicas-grid">
            @if(($habilidadesFrontendRecientes ?? collect())->count() > 0)
                <div class="tech-category-title">Frontend</div>
                <div class="tech-skills-grid">
                    @foreach($habilidadesFrontendRecientes as $skill)
                        @php $nivel = $skill->nivel ?? 'Intermedio'; $claseNivel = $nivel=='Avanzado'?'advanced':($nivel=='Intermedio'?'intermediate':'basic'); $hasProjects = isset($skill->proyectos) && count($skill->proyectos) > 0; @endphp
                        <div class="tech-skill-item">
                            <div class="tech-skill-header"><span class="tech-skill-name">{{ $skill->nombre }}</span><span class="tech-skill-level">{{ $nivel }}</span></div>
                            <div class="tech-skill-bar-bg"><div class="tech-skill-bar-fill {{ $claseNivel }}"></div></div>
                            @if($hasProjects)
                                <div class="tech-skill-projects">
                                    @foreach($skill->proyectos as $p)
                                        <a href="javascript:void(0)" class="tech-skill-project-chip" onclick="abrirModalPorId({{ $p->id }});" title="{{ $p->nombre }}">{{ $p->nombre }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
            @if(($habilidadesBackendRecientes ?? collect())->count() > 0)
                <div class="tech-category-title">Backend</div>
                <div class="tech-skills-grid">
                    @foreach($habilidadesBackendRecientes as $skill)
                        @php $nivel = $skill->nivel ?? 'Intermedio'; $claseNivel = $nivel=='Avanzado'?'advanced':($nivel=='Intermedio'?'intermediate':'basic'); $hasProjects = isset($skill->proyectos) && count($skill->proyectos) > 0; @endphp
                        <div class="tech-skill-item">
                            <div class="tech-skill-header"><span class="tech-skill-name">{{ $skill->nombre }}</span><span class="tech-skill-level">{{ $nivel }}</span></div>
                            <div class="tech-skill-bar-bg"><div class="tech-skill-bar-fill {{ $claseNivel }}"></div></div>
                            @if($hasProjects)
                                <div class="tech-skill-projects">
                                    @foreach($skill->proyectos as $p)
                                        <a href="javascript:void(0)" class="tech-skill-project-chip" onclick="abrirModalPorId({{ $p->id }});" title="{{ $p->nombre }}">{{ $p->nombre }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        @if($habilidadesFrontendRestantes->count() > 0 || $habilidadesBackendRestantes->count() > 0)
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
                        <div class="tech-skill-header"><span class="tech-skill-name">{{ $skill->nombre }}</span><span class="tech-skill-level">{{ $skill->nivel ?? 'Intermedio' }}</span></div>
                        <div class="tech-skill-bar-bg"><div class="tech-skill-bar-fill @php echo ($skill->nivel??'')=='Avanzado'?'advanced':(($skill->nivel??'')=='Intermedio'?'intermediate':'basic') @endphp"></div></div>
                    </div>
                    @endforeach
                </div>
            @endif
            @if(($habilidadesTecnicasBackend ?? collect())->count() > 0)
                <div class="tech-category-title">Backend</div>
                <div class="tech-skills-grid">
                    @foreach($habilidadesTecnicasBackend as $skill)
                    <div class="tech-skill-item">
                        <div class="tech-skill-header"><span class="tech-skill-name">{{ $skill->nombre }}</span><span class="tech-skill-level">{{ $skill->nivel ?? 'Intermedio' }}</span></div>
                        <div class="tech-skill-bar-bg"><div class="tech-skill-bar-fill @php echo ($skill->nivel??'')=='Avanzado'?'advanced':(($skill->nivel??'')=='Intermedio'?'intermediate':'basic') @endphp"></div></div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endif

    @if($habilidadesBlandas->count() > 0)
    <div class="section" id="section-blandas">
        <h2><i class="fas fa-heart"></i> Habilidades blandas</h2>
        <div class="skills-container" id="blandas-grid">
            @forelse($habilidadesBlandasRecientes as $skill)
                <span class="soft-skill-tag"><i class="fas fa-star" style="color:var(--teal);"></i> {{ $skill->nombre }}</span>
            @empty
                <div class="empty-message-preview"><i class="fas fa-info-circle"></i> No hay habilidades blandas registradas.</div>
            @endforelse
        </div>
        @if($habilidadesBlandasRestantes->count() > 0)
            <div class="btn-ver-todos">
                <button onclick="abrirModalBlandas()"><i class="fas fa-heart"></i> Ver todas ({{ $habilidadesBlandas->count() }})</button>
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

    @if($idiomas->count() > 0)
    <div class="section" id="section-idiomas">
        <h2><i class="fas fa-language"></i> Idiomas</h2>
        <div class="idiomas-preview-grid" id="idiomas-grid">
            @forelse($idiomasRecientes as $index => $idioma)
            @php
                $banderaEmoji = $banderas[strtolower($idioma->nombre)] ?? null;
                $codigo       = $codigos[strtolower($idioma->nombre)]  ?? strtoupper(substr($idioma->nombre,0,2));
                $colorFondo   = $colorFondos[$index % count($colorFondos)];
                $colorBarra   = $coloresBarra[$index % count($coloresBarra)];
            @endphp
            <div class="idioma-preview-card">
                <div class="idioma-preview-header">
                    <div class="idioma-preview-left">
                        @if($banderaEmoji)
                            <span class="idioma-bandera">{{ $banderaEmoji }}</span>
                        @else
                            <span class="idioma-flag-code-preview" style="background:{{ $colorFondo }};">{{ $codigo }}</span>
                        @endif
                        <div class="idioma-preview-info">
                            <span class="idioma-preview-nombre">{{ $idioma->nombre }}</span>
                            <span class="idioma-preview-nivel">{{ $idioma->nivel_label }} — {{ $idioma->nivel_nombre }}</span>
                        </div>
                    </div>
                    @if(!empty($idioma->certificado))
                    <a href="javascript:void(0)" onclick="abrirLightbox('{{ asset('storage/' . $idioma->certificado) }}')" class="idioma-cert-link">
                        <i class="fas fa-certificate"></i> Cert.
                    </a>
                    @endif
                </div>
                <div class="idioma-barra-wrap">
                    <div class="idioma-barra-fill-custom" style="width:{{ $idioma->porcentaje }}%;background:{{ $colorBarra }};"></div>
                </div>
            </div>
            @empty
            <div class="empty-message-preview" style="width:100%;"><i class="fas fa-info-circle"></i> No hay idiomas registrados.</div>
            @endforelse
        </div>
        @if($idiomasRestantes->count() > 0)
            <div class="btn-ver-todos">
                <button onclick="abrirModalIdiomas()"><i class="fas fa-language"></i> Ver todos los idiomas ({{ $idiomas->count() }})</button>
            </div>
        @endif
        <div id="idiomas-completos" style="display:none;">
            <div class="idiomas-preview-grid">
                @foreach($idiomas as $index => $idioma)
                @php
                    $banderaEmoji = $banderas[strtolower($idioma->nombre)] ?? null;
                    $codigo       = $codigos[strtolower($idioma->nombre)]  ?? strtoupper(substr($idioma->nombre,0,2));
                    $colorFondo   = $colorFondos[$index % count($colorFondos)];
                    $colorBarra   = $coloresBarra[$index % count($coloresBarra)];
                @endphp
                <div class="idioma-preview-card">
                    <div class="idioma-preview-header">
                        <div class="idioma-preview-left">
                            @if($banderaEmoji)
                                <span class="idioma-bandera">{{ $banderaEmoji }}</span>
                            @else
                                <span class="idioma-flag-code-preview" style="background:{{ $colorFondo }};">{{ $codigo }}</span>
                            @endif
                            <div class="idioma-preview-info">
                                <span class="idioma-preview-nombre">{{ $idioma->nombre }}</span>
                                <span class="idioma-preview-nivel">{{ $idioma->nivel_label }} — {{ $idioma->nivel_nombre }}</span>
                            </div>
                        </div>
                        @if(!empty($idioma->certificado))
                        <a href="javascript:void(0)" onclick="abrirLightbox('{{ asset('storage/' . $idioma->certificado) }}')" class="idioma-cert-link">
                            <i class="fas fa-certificate"></i> Cert.
                        </a>
                        @endif
                    </div>
                    <div class="idioma-barra-wrap">
                        <div class="idioma-barra-fill-custom" style="width:{{ $idioma->porcentaje }}%;background:{{ $colorBarra }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($proyectos->count() > 0)
    <div class="section" id="section-proyectos">
        <h2><i class="fas fa-project-diagram"></i> Proyectos</h2>
        <div class="cards-grid" id="proyectos-grid">
            @forelse($proyectosRecientes as $proyecto)
                @php
                    $modalProyectoPayload = [
                        'nombre'      => $proyecto->nombre,
                        'descripcion' => strip_tags($proyecto->descripcion ?? '', $allowedHtmlTags),
                        'fecha_inicio'=> optional($proyecto->fecha_inicio)->format('d/m/Y'),
                        'fecha_fin'   => optional($proyecto->fecha_fin)->format('d/m/Y'),
                        'estado'      => $proyecto->estado,
                        'rol'         => $proyecto->rol,
                        'cliente'     => $proyecto->cliente,
                        'tecnologias' => $proyecto->tecnologias,
                        'evidencias'  => $proyecto->evidencias,
                    ];
                @endphp
                <div class="card" id="project-card-{{ $proyecto->id }}" onclick='abrirModal(@json($modalProyectoPayload))'>
                    <h3 style="color:#1abc9c;font-size:1rem;margin-bottom:8px;">{{ $proyecto->nombre }}</h3>
                    @if(!empty($proyecto->descripcion))
                    <div class="description-wrapper">
                        <div class="description collapsed proyecto-desc-wrap" id="desc-proy-{{ $loop->index }}">
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
                    @if(!empty($proyecto->rol) || !empty($proyecto->cliente))
                    <div class="description" style="font-size:0.75rem;">
                        @if(!empty($proyecto->rol))Rol: {{ $proyecto->rol }}@endif
                        @if(!empty($proyecto->rol) && !empty($proyecto->cliente)) | @endif
                        @if(!empty($proyecto->cliente))Cliente: {{ $proyecto->cliente }}@endif
                    </div>
                    @endif
                    @if(!empty($proyecto->tecnologias))
                        <div class="proyecto-tecnologias">
                            @foreach(array_slice($proyecto->tecnologias, 0, 3) as $tec)
                                <span class="tec-badge">{{ $tec }}</span>
                            @endforeach
                            @if(count($proyecto->tecnologias) > 3)
                                <span class="tec-badge">+{{ count($proyecto->tecnologias) - 3 }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
            <div class="empty-message-preview" style="grid-column:1/-1;"><i class="fas fa-info-circle"></i> No hay proyectos registrados.</div>
            @endforelse
        </div>

        @if($proyectosRestantes->count() > 0)
            <div class="btn-ver-todos">
                <button onclick="abrirModalTodosProyectos()"><i class="fas fa-th-large"></i> Ver todos los proyectos ({{ $proyectos->count() }})</button>
            </div>
        @endif
        <div id="proyectos-completos" style="display:none;">
            <div class="cards-grid">
                @foreach($proyectos as $proyecto)
                <div class="card">
                    <h3 style="color:#1abc9c;font-size:1rem;">{{ $proyecto->nombre }}</h3>
                    @if(!empty($proyecto->descripcion))
                    <div class="description expanded">
                        <div class="ql-snow"><div class="ql-editor">{!! strip_tags($proyecto->descripcion, $allowedHtmlTags) !!}</div></div>
                    </div>
                    @endif
                    <div class="date" style="font-size:0.7rem;">
                        {{ \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') }}
                        @if($proyecto->fecha_fin) — {{ \Carbon\Carbon::parse($proyecto->fecha_fin)->format('d/m/Y') }} @endif
                        | {{ $proyecto->estado ?? 'En progreso' }}
                    </div>
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

    {{-- ==================== MODAL COMPARTIR (CON COPIA DE QR) ==================== --}}
    <div id="modal-compartir" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
        <div style="background:#fff;border-radius:16px;width:520px;max-width:92%;padding:28px 30px;box-shadow:0 20px 60px rgba(0,0,0,0.2);position:relative;">

            <button onclick="cerrarModalCompartir()" 
                    style="position:absolute;top:14px;right:18px;background:#f1f5f9;border:none;width:32px;height:32px;border-radius:50%;font-size:16px;cursor:pointer;color:#64748b;transition:all 0.2s;display:flex;align-items:center;justify-content:center;"
                    onmouseover="this.style.background='#fee2e2';this.style.color='#ef4444';" 
                    onmouseout="this.style.background='#f1f5f9';this.style.color='#64748b';">
                ✕
            </button>

            <div style="display:flex;align-items:center;gap:10px;margin-bottom:22px;">
                <div style="width:38px;height:38px;border-radius:50%;background:#f0fdf9;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-share-alt" style="color:#0abf9e;font-size:16px;"></i>
                </div>
                <h2 style="margin:0;font-size:18px;font-weight:700;color:#0f172a;">Compartir portafolio</h2>
            </div>

            <div style="display:flex;gap:20px;align-items:stretch;">

                <div style="flex:1;min-width:0;display:flex;flex-direction:column;">
                    <label style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:8px;">
                        <i class="fas fa-link" style="color:#0abf9e;margin-right:6px;"></i> Enlace público
                    </label>
                    <div style="display:flex;border:2px solid #e2e8f0;border-radius:10px;padding:3px;background:#fafbfc;align-items:center;transition:border-color 0.2s;flex:1;" 
                         onmouseover="this.style.borderColor='#0abf9e';" 
                         onmouseout="this.style.borderColor='#e2e8f0';">
                        <input type="text" id="share-link-input" readonly 
                               value="{{ $user->portfolio ? url('/portafolio/'.$user->portfolio->slug) : '' }}" 
                               style="flex:1;border:none;background:transparent;padding:8px 10px;outline:none;color:#1e293b;font-size:13px;font-family:monospace;min-width:0;">
                        <button onclick="copiarLinkPortafolio()" id="btn-copiar-link" 
                                style="background:#0abf9e;border:none;border-radius:6px;padding:7px 16px;cursor:pointer;font-weight:600;font-size:12px;color:white;transition:all 0.2s;white-space:nowrap;"
                                onmouseover="this.style.background='#07866e';" 
                                onmouseout="this.style.background='#0abf9e';">
                            <i class="fas fa-copy"></i> <span id="btn-copiar-texto">Copiar</span>
                        </button>
                    </div>
                    <p style="font-size:11px;color:#94a3b8;margin-top:8px;display:flex;align-items:center;gap:6px;">
                        <i class="fas fa-info-circle" style="color:#0abf9e;font-size:12px;"></i>
                        Comparte este enlace con quien quieras
                    </p>
                </div>

                <div style="flex-shrink:0;text-align:center;background:#fafbfc;border-radius:12px;padding:12px;border:1px solid #eef2f6;display:flex;flex-direction:column;align-items:center;justify-content:center;min-width:120px;">
                    <div id="qrCodeContainer" style="width:100px;height:100px;display:flex;align-items:center;justify-content:center;"></div>
                    <button onclick="copiarQR()" 
                            style="margin-top:8px;background:#f0fdf9;border:1px solid #0abf9e;border-radius:20px;padding:4px 14px;cursor:pointer;font-size:10px;font-weight:600;color:#0abf9e;transition:all 0.2s;display:flex;align-items:center;gap:4px;font-family:inherit;"
                            onmouseover="this.style.background='#0abf9e';this.style.color='white';" 
                            onmouseout="this.style.background='#f0fdf9';this.style.color='#0abf9e';">
                        <i class="fas fa-copy" style="font-size:10px;"></i> Copiar QR
                    </button>
                    <span style="font-size:9px;color:#94a3b8;display:block;margin-top:4px;">Escanea o copia</span>
                </div>

            </div>

        </div>
    </div>

    {{-- ==================== MODALES "VER TODOS" ==================== --}}
    @if($proyectos->count() > 0)
    @php
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
    @endphp
    <div id="modal-todos-proyectos" class="modal-todos-proyectos">
        <div class="modal-todos-content" style="max-width:1000px;">
            <div class="modal-todos-header">
                <h2><i class="fas fa-folder-open"></i> Todos los proyectos <span style="background:#f0fdf9;color:#0abf9e;border:1px solid #d1fae5;font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px;margin-left:8px;">{{ $proyectos->count() }}</span></h2>
                <button class="close-todos-modal" onclick="cerrarModalTodosProyectos()">✕</button>
            </div>
            <div style="padding:0 24px 16px;">
                <input type="text" id="search-proyectos-prev" placeholder="🔍 Buscar por nombre, tecnología o estado..." oninput="filtrarProyectosPreview(this.value)" style="width:100%;padding:10px 16px;border:1.5px solid #e2e8f0;border-radius:40px;font-size:13px;outline:none;font-family:inherit;box-sizing:border-box;">
            </div>
            <div style="padding:0 24px 24px;display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;" id="folder-grid-preview">
                @foreach($proyectos as $idx => $proyecto)
                @php
                    $color = $folderColors[$idx % count($folderColors)];
                    $estadoStyle = $proyecto->estado=='Completado' ? 'background:#d1fae5;color:#065f46;' : ($proyecto->estado=='En curso' ? 'background:#fef3c7;color:#92400e;' : 'background:#f1f5f9;color:#64748b;');
                    $modalPayload = ['nombre'=>$proyecto->nombre,'descripcion'=>strip_tags($proyecto->descripcion??'',$allowedHtmlTags),'fecha_inicio'=>optional($proyecto->fecha_inicio)->format('d/m/Y'),'fecha_fin'=>optional($proyecto->fecha_fin)->format('d/m/Y'),'estado'=>$proyecto->estado,'rol'=>$proyecto->rol,'cliente'=>$proyecto->cliente,'tecnologias'=>$proyecto->tecnologias,'evidencias'=>$proyecto->evidencias];
                    $techs = $proyecto->tecnologias ?? [];
                    $descCorta = strip_tags($proyecto->descripcion ?? '');
                @endphp
                <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:18px;overflow:hidden;cursor:pointer;transition:all 0.25s ease;box-shadow:0 2px 8px rgba(0,0,0,0.04);"
                     data-nombre="{{ strtolower($proyecto->nombre) }}"
                     data-techs="{{ strtolower(implode(' ', $techs)) }}"
                     data-estado="{{ strtolower($proyecto->estado ?? '') }}"
                     onclick='cerrarModalTodosProyectos(); abrirModal(@json($modalPayload));'
                     onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 16px 32px -8px rgba(10,191,158,0.2)';this.style.borderColor='#0abf9e';"
                     onmouseout="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)';this.style.borderColor='#e2e8f0';">
                    <div style="height:80px;background:{{ $color['bg'] }};position:relative;display:flex;align-items:flex-end;padding:0 18px 14px;">
                        <div style="position:absolute;top:0;left:18px;width:60px;height:20px;border-radius:8px 8px 0 0;background:{{ $color['tab'] }};"></div>
                        <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,0.25);display:flex;align-items:center;justify-content:center;font-size:20px;color:white;">
                            <i class="fas fa-code-branch"></i>
                        </div>
                        <span style="margin-left:auto;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;{{ $estadoStyle }}">
                            {{ $proyecto->estado ?? 'En progreso' }}
                        </span>
                    </div>
                    <div style="padding:16px 18px 12px;">
                        <h4 style="margin:0 0 6px;font-size:0.95rem;font-weight:700;color:#0f172a;">{{ $proyecto->nombre }}</h4>
                        <div style="font-size:11px;color:#94a3b8;display:flex;align-items:center;gap:6px;margin-bottom:10px;">
                            <i class="fas fa-calendar-alt" style="color:#0abf9e;font-size:10px;"></i>
                            {{ \Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y') }}
                            @if($proyecto->fecha_fin) — {{ \Carbon\Carbon::parse($proyecto->fecha_fin)->format('d/m/Y') }} @endif
                        </div>
                        @if(!empty($descCorta))
                        <div style="font-size:12px;color:#64748b;line-height:1.55;margin-bottom:12px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $descCorta }}</div>
                        @endif
                        @if(!empty($techs))
                        <div style="display:flex;flex-wrap:wrap;gap:5px;">
                            @foreach(array_slice($techs,0,3) as $tec)
                                <span style="background:#f0fdf9;color:#0abf9e;border:1px solid #d1fae5;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;">{{ $tec }}</span>
                            @endforeach
                            @if(count($techs)>3)
                                <span style="background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;">+{{ count($techs)-3 }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                    <div style="padding:12px 18px;border-top:1px solid #f0f4f8;display:flex;align-items:center;justify-content:space-between;background:#fafbfc;">
                        <div style="font-size:11px;color:#64748b;display:flex;align-items:center;gap:5px;">
                            @if(!empty($proyecto->rol))
                                <i class="fas fa-user-check" style="color:#0abf9e;font-size:10px;"></i> {{ $proyecto->rol }}
                            @elseif(!empty($proyecto->cliente))
                                <i class="fas fa-building" style="color:#0abf9e;font-size:10px;"></i> {{ $proyecto->cliente }}
                            @else
                                <i class="fas fa-folder" style="color:#0abf9e;font-size:10px;"></i> Ver detalle
                            @endif
                        </div>
                        <div style="width:28px;height:28px;border-radius:50%;background:#f0fdf9;border:1px solid #d1fae5;display:flex;align-items:center;justify-content:center;color:#0abf9e;font-size:11px;transition:all 0.2s;">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div id="no-results-preview" style="display:none;text-align:center;padding:40px;color:#94a3b8;font-size:14px;">
                <i class="fas fa-search" style="font-size:32px;margin-bottom:10px;display:block;"></i>
                No se encontraron proyectos con ese criterio.
            </div>
        </div>
    </div>
    @endif

    @if($experiencias->count() > 0)
    <div id="modal-todos-experiencias" class="modal-todos-proyectos">
        <div class="modal-todos-content" style="max-width:700px;">
            <div class="modal-todos-header">
                <h2>
                    <i class="fas fa-briefcase"></i> Experiencia laboral
                    <span style="background:#f0fdf9;color:#0abf9e;border:1px solid #d1fae5;font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px;margin-left:8px;">{{ $experiencias->count() }}</span>
                </h2>
                <button class="close-todos-modal" onclick="cerrarModalExperiencias()">✕</button>
            </div>
            <div style="padding:32px 28px;">
                <div style="position:relative;padding-left:48px;">
                    <div style="position:absolute;left:18px;top:0;bottom:0;width:2px;background:linear-gradient(180deg,#0abf9e 0%,#e2e8f0 100%);"></div>
                    @foreach($experiencias->values() as $i => $exp)
                    <div style="position:relative;margin-bottom:24px;">
                        <div style="position:absolute;left:-39px;top:18px;width:20px;height:20px;border-radius:50%;background:#fff;border:3px solid #0abf9e;display:flex;align-items:center;justify-content:center;z-index:2;">
                            <div style="width:8px;height:8px;border-radius:50%;background:#0abf9e;"></div>
                        </div>
                        <div onclick="cerrarModalExperiencias(); abrirDetalleExp({{ $i }});"
                             style="background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;padding:18px 20px;cursor:pointer;transition:all 0.22s ease;box-shadow:0 2px 8px rgba(0,0,0,0.04);"
                             onmouseover="this.style.borderColor='#0abf9e';this.style.transform='translateX(4px)';this.style.boxShadow='0 8px 24px -6px rgba(10,191,158,0.2)';"
                             onmouseout="this.style.borderColor='#e2e8f0';this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)';">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:8px;">
                                <div>
                                    <div style="font-size:0.95rem;font-weight:700;color:#0f172a;margin-bottom:3px;">{{ $exp->empresa }}</div>
                                    <div style="font-size:12px;color:#0abf9e;font-weight:600;">{{ $exp->cargo }}</div>
                                </div>
                                <div style="font-size:11px;color:#94a3b8;white-space:nowrap;background:#f8fafc;padding:3px 10px;border-radius:20px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:4px;flex-shrink:0;">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($exp->fecha_inicio)->format('M Y') }}
                                    @if($exp->fecha_fin) — {{ \Carbon\Carbon::parse($exp->fecha_fin)->format('M Y') }}
                                    @elseif($exp->trabajo_actual ?? false) — Actualidad @endif
                                </div>
                            </div>
                            @if(!empty($exp->ubicacion))
                            <div style="font-size:11px;color:#64748b;display:flex;align-items:center;gap:5px;margin-bottom:8px;">
                                <i class="fas fa-map-marker-alt" style="color:#0abf9e;font-size:10px;"></i> {{ $exp->ubicacion }}
                            </div>
                            @endif
                            @if(!empty($exp->descripcion))
                            <div style="font-size:12px;color:#64748b;line-height:1.55;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ strip_tags($exp->descripcion) }}
                            </div>
                            @endif
                            <div style="font-size:11px;color:#0abf9e;margin-top:8px;display:flex;align-items:center;gap:4px;font-weight:600;">
                                <i class="fas fa-mouse-pointer" style="font-size:9px;"></i>
                                Clic para ver detalle completo
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <div id="modal-todos-academicas" class="modal-todos-proyectos">
        <div class="modal-todos-content" style="max-width:700px;">
            <div class="modal-todos-header">
                <h2>
                    <i class="fas fa-graduation-cap"></i> Formación académica
                    <span style="background:#f0fdf9;color:#0abf9e;border:1px solid #d1fae5;font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px;margin-left:8px;">{{ $academicas->count() }}</span>
                </h2>
                <button class="close-todos-modal" onclick="cerrarModalAcademicas()">✕</button>
            </div>
            <div style="padding:32px 28px;">
                <div style="position:relative;padding-left:48px;">
                    <div style="position:absolute;left:18px;top:0;bottom:0;width:2px;background:linear-gradient(180deg,#0abf9e 0%,#e2e8f0 100%);"></div>
                    @foreach($academicas->values() as $i => $aca)
                    @php
                        $hasEv = false;
                        if (isset($aca->evidence_url) && $aca->evidence_url) {
                            $evArr = is_array($aca->evidence_url) ? $aca->evidence_url : json_decode($aca->evidence_url, true);
                            if (!is_array($evArr)) { $evArr = []; }
                            $hasEv = !empty($evArr);
                        }
                    @endphp
                    <div style="position:relative;margin-bottom:24px;">
                        <div style="position:absolute;left:-39px;top:18px;width:20px;height:20px;border-radius:50%;background:#fff;border:3px solid #0abf9e;display:flex;align-items:center;justify-content:center;z-index:2;">
                            <div style="width:8px;height:8px;border-radius:50%;background:#0abf9e;"></div>
                        </div>
                        <div onclick="cerrarModalAcademicas(); abrirDetalleAca({{ $i }});"
         style="background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;padding:18px 20px;cursor:pointer;transition:all 0.22s ease;box-shadow:0 2px 8px rgba(0,0,0,0.04);"
         onmouseover="this.style.borderColor='#0abf9e';this.style.transform='translateX(4px)';this.style.boxShadow='0 8px 24px -6px rgba(10,191,158,0.2)';"
         onmouseout="this.style.borderColor='#e2e8f0';this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)';">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:8px;">
                                <div>
                                    <div style="font-size:0.95rem;font-weight:700;color:#0f172a;margin-bottom:3px;">{{ $aca->institucion }}</div>
                                    <div style="font-size:12px;color:#0abf9e;font-weight:600;">{{ $aca->titulo }}</div>
                                </div>
                                <div style="font-size:11px;color:#94a3b8;white-space:nowrap;background:#f8fafc;padding:3px 10px;border-radius:20px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:4px;flex-shrink:0;">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($aca->fecha_inicio)->format('M Y') }}
                                    @if($aca->fecha_fin) — {{ \Carbon\Carbon::parse($aca->fecha_fin)->format('M Y') }}
                                    @elseif($aca->estudio_actual??false) — Actualidad @endif
                                </div>
                            </div>
                            @if(!empty($aca->specialty??''))
                            <div style="font-size:11px;color:#64748b;display:flex;align-items:center;gap:5px;margin-bottom:8px;">
                                <i class="fas fa-tag" style="color:#0abf9e;font-size:10px;"></i> {{ $aca->specialty }}
                            </div>
                            @endif
                            @if(!empty($aca->descripcion))
                            <div style="font-size:12px;color:#64748b;line-height:1.55;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ strip_tags($aca->descripcion) }}
                            </div>
                            @endif
                            <div style="font-size:11px;color:#0abf9e;margin-top:8px;display:flex;align-items:center;gap:4px;font-weight:600;">
                                <i class="fas fa-mouse-pointer" style="font-size:9px;"></i>
                                Clic para ver detalle completo{{ $hasEv ? ' + certificado' : '' }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if(($habilidadesTecnicasFrontend ?? collect())->count() > 0 || ($habilidadesTecnicasBackend ?? collect())->count() > 0)
    <div id="modal-todos-tecnicas" class="modal-todos-proyectos">
        <div class="modal-todos-content">
            <div class="modal-todos-header">
                <h2><i class="fas fa-code"></i> Todas las habilidades técnicas</h2>
                <button class="close-todos-modal" onclick="cerrarModalTecnicas()">✕</button>
            </div>
            <div style="padding:24px;display:flex;flex-direction:column;gap:20px;">
                @if(($habilidadesTecnicasFrontend ?? collect())->count() > 0)
                    <div>
                        <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.12em;color:#7a8298;margin-bottom:12px;"><i class="fas fa-palette" style="color:#0abf9e;margin-right:6px;"></i>Frontend</div>
                        <div style="display:flex;flex-wrap:wrap;gap:12px;">
                            @foreach($habilidadesTecnicasFrontend as $skill)
                            @php $nv=$skill->nivel??'Intermedio';$cl=$nv=='Avanzado'?'advanced':($nv=='Intermedio'?'intermediate':'basic'); @endphp
                            <div class="tech-skill-item" style="width:200px;">
                                <div class="tech-skill-header"><span class="tech-skill-name">{{ $skill->nombre }}</span><span class="tech-skill-level">{{ $nv }}</span></div>
                                <div class="tech-skill-bar-bg"><div class="tech-skill-bar-fill {{ $cl }}"></div></div>
                                @if(isset($skill->proyectos) && count($skill->proyectos) > 0)
                                    <div class="tech-skill-projects">
                                        @foreach($skill->proyectos as $p)
                                            <a href="javascript:void(0)" class="tech-skill-project-chip" onclick="cerrarModalTecnicas(); abrirModalPorId({{ $p->id }});" title="{{ $p->nombre }}">{{ $p->nombre }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if(($habilidadesTecnicasBackend ?? collect())->count() > 0)
                    <div>
                        <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.12em;color:#7a8298;margin-bottom:12px;"><i class="fas fa-server" style="color:#0abf9e;margin-right:6px;"></i>Backend</div>
                        <div style="display:flex;flex-wrap:wrap;gap:12px;">
                            @foreach($habilidadesTecnicasBackend as $skill)
                            @php $nv=$skill->nivel??'Intermedio';$cl=$nv=='Avanzado'?'advanced':($nv=='Intermedio'?'intermediate':'basic'); @endphp
                            <div class="tech-skill-item" style="width:200px;">
                                <div class="tech-skill-header"><span class="tech-skill-name">{{ $skill->nombre }}</span><span class="tech-skill-level">{{ $nv }}</span></div>
                                <div class="tech-skill-bar-bg"><div class="tech-skill-bar-fill {{ $cl }}"></div></div>
                                @if(isset($skill->proyectos) && count($skill->proyectos) > 0)
                                    <div class="tech-skill-projects">
                                        @foreach($skill->proyectos as $p)
                                            <a href="javascript:void(0)" class="tech-skill-project-chip" onclick="cerrarModalTecnicas(); abrirModalPorId({{ $p->id }});" title="{{ $p->nombre }}">{{ $p->nombre }}</a>
                                        @endforeach
                                    </div>
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

    @if($habilidadesBlandas->count() > 0)
    <div id="modal-todos-blandas" class="modal-todos-proyectos">
        <div class="modal-todos-content">
            <div class="modal-todos-header">
                <h2><i class="fas fa-heart"></i> Todas las habilidades blandas ({{ $habilidadesBlandas->count() }})</h2>
                <button class="close-todos-modal" onclick="cerrarModalBlandas()">✕</button>
            </div>
            <div class="todos-proyectos-grid">
                @foreach($habilidadesBlandas as $skill)
                    <div class="proyecto-card-modal"><h4><i class="fas fa-star" style="color:#0abf9e;"></i> {{ $skill->nombre }}</h4></div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($idiomas->count() > 0)
    <div id="modal-todos-idiomas" class="modal-todos-proyectos">
        <div class="modal-todos-content">
            <div class="modal-todos-header">
                <h2><i class="fas fa-language"></i> Todos los idiomas ({{ $idiomas->count() }})</h2>
                <button class="close-todos-modal" onclick="cerrarModalIdiomas()">✕</button>
            </div>
            <div class="todos-proyectos-grid">
                @foreach($idiomas as $index => $idioma)
                @php
                    $banderaEmoji = $banderas[strtolower($idioma->nombre)] ?? null;
                    $codigo       = $codigos[strtolower($idioma->nombre)]  ?? strtoupper(substr($idioma->nombre,0,2));
                    $colorFondo   = $colorFondos[$index % count($colorFondos)];
                    $colorBarra   = $coloresBarra[$index % count($coloresBarra)];
                @endphp
                <div class="proyecto-card-modal">
                    <h4>
                        @if($banderaEmoji){{ $banderaEmoji }}
                        @else<span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;background:{{ $colorFondo }};color:white;font-size:11px;font-weight:700;margin-right:8px;">{{ $codigo }}</span>@endif
                        {{ $idioma->nombre }}
                    </h4>
                    <div class="proyecto-fecha" style="margin-top:4px;">{{ $idioma->nivel_label }} — {{ $idioma->nivel_nombre }}</div>
                    <div class="idioma-barra-wrap" style="margin-top:10px;">
                        <div style="height:8px;background:#edf0f4;border-radius:10px;overflow:hidden;">
                            <div style="width:{{ $idioma->porcentaje }}%;height:100%;background:{{ $colorBarra }};border-radius:10px;transition:width 0.8s ease;"></div>
                        </div>
                    </div>
                    @if(!empty($idioma->certificado))
                        <a href="javascript:void(0)"
                           onclick="event.stopPropagation(); abrirLightbox('{{ asset('storage/' . $idioma->certificado) }}')"
                           style="margin-top:10px;display:inline-flex;align-items:center;gap:6px;color:#0abf9e;font-size:12px;font-weight:600;text-decoration:none;padding:6px 14px;border-radius:20px;background:#f0fdf9;border:1px solid #d1fae5;transition:all 0.2s;"
                           onmouseover="this.style.background='#d1fae5';" onmouseout="this.style.background='#f0fdf9';">
                            <i class="fas fa-certificate"></i> Ver certificado
                        </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ==================== MODAL DETALLE EXPERIENCIA ==================== --}}
    <div id="modal-detalle-exp" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:12000;align-items:center;justify-content:center;backdrop-filter:blur(2px);">
            <div style="background:#fff;border-radius:20px;max-width:640px;width:92%;max-height:88vh;overflow-y:auto;animation:modalFadeIn 0.2s ease;">
                <div style="padding:24px 28px 20px;border-bottom:1px solid #e2e8f0;position:relative;">
                    <button onclick="cerrarDetalleExp()" style="position:absolute;top:20px;right:20px;background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8;transition:color 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">✕</button>
                    <div style="display:inline-flex;align-items:center;gap:6px;background:#f0fdf9;color:#0abf9e;border:1px solid #d1fae5;font-size:11px;font-weight:700;padding:3px 12px;border-radius:20px;margin-bottom:10px;">
                        <i class="fas fa-briefcase"></i> Experiencia laboral
                    </div>
                    <h3 id="det-exp-empresa" style="margin:0 0 4px;font-size:1.2rem;font-weight:700;color:#0f172a;"></h3>
                    <div id="det-exp-cargo" style="font-size:14px;color:#0abf9e;font-weight:600;margin-bottom:12px;"></div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;">
                        <div id="det-exp-fecha-pill" style="display:inline-flex;align-items:center;gap:5px;background:#f8fafc;border:1px solid #e2e8f0;color:#64748b;font-size:11px;font-weight:600;padding:4px 12px;border-radius:20px;">
                            <i class="fas fa-calendar-alt" style="color:#0abf9e;font-size:10px;"></i>
                            <span id="det-exp-fecha-txt"></span>
                        </div>
                        <div id="det-exp-ubicacion-pill" style="display:none;align-items:center;gap:5px;background:#f8fafc;border:1px solid #e2e8f0;color:#64748b;font-size:11px;font-weight:600;padding:4px 12px;border-radius:20px;">
                            <i class="fas fa-map-marker-alt" style="color:#0abf9e;font-size:10px;"></i>
                            <span id="det-exp-ubicacion-txt"></span>
                        </div>
                    </div>
                </div>
                <div style="padding:24px 28px;display:flex;flex-direction:column;gap:20px;">
                    <div>
                        <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#94a3b8;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
                            <i class="fas fa-align-left" style="color:#0abf9e;"></i> Descripción
                        </div>
                        <div class="ql-snow" style="border:none;">
                            <div class="ql-editor" id="det-exp-desc-inner" style="padding:0;min-height:0;font-size:13px;color:#475569;line-height:1.75;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- ==================== MODAL DETALLE ACADÉMICA ==================== --}}
    <div id="modal-detalle-aca" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.8);z-index:99999;align-items:center;justify-content:center;">
            <div style="background:#fff;border-radius:20px;max-width:640px;width:90%;max-height:85vh;overflow-y:auto;margin:auto;position:relative;">
                <div style="padding:24px 28px 20px;border-bottom:1px solid #e2e8f0;position:relative;">
                    <button onclick="cerrarDetalleAca()" style="position:absolute;top:15px;right:20px;background:none;border:none;font-size:24px;cursor:pointer;">✕</button>
                    <div style="background:#f0fdf9;color:#0abf9e;display:inline-block;padding:3px 12px;border-radius:20px;font-size:11px;margin-bottom:10px;">
                        <i class="fas fa-graduation-cap"></i> Formación académica
                    </div>
                    <h3 id="det-aca-inst" style="margin:0;font-size:1.2rem;"></h3>
                    <div id="det-aca-titulo" style="color:#0abf9e;font-weight:600;margin:5px 0;"></div>
                    <div style="margin-top:10px;">
                        <span style="display:inline-flex;align-items:center;gap:5px;background:#f8fafc;padding:4px 12px;border-radius:20px;font-size:11px;">
                            <i class="fas fa-calendar-alt"></i> <span id="det-aca-fecha-txt"></span>
                        </span>
                        <span id="det-aca-specialty-pill" style="display:none;margin-left:8px;display:inline-flex;align-items:center;gap:5px;background:#f8fafc;padding:4px 12px;border-radius:20px;font-size:11px;">
                            <i class="fas fa-tag"></i> <span id="det-aca-specialty-txt"></span>
                        </span>
                    </div>
                </div>
                <div style="padding:24px 28px;">
                    <div style="font-size:11px;font-weight:600;color:#888;margin-bottom:8px;">Descripción</div>
                    <div id="det-aca-desc" style="font-size:13px;line-height:1.6;color:#475569;"></div>
                    <div id="det-aca-certs" style="margin-top:20px;display:none;"></div>
                </div>
            </div>
        </div>

    {{-- ==================== MODAL PROYECTO DETALLE ==================== --}}
    <div id="modal-proyecto">
        <div class="modal-proyecto-inner">
            <div class="modal-proyecto-head">
                <div>
                    <h2 id="modal-nombre"></h2>
                    <div id="modal-badges" style="display:flex;flex-wrap:wrap;gap:6px;"></div>
                </div>
                <button onclick="cerrarModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#888;transition:color 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#888'">✕</button>
            </div>
            <div id="modal-fechas" class="modal-proyecto-fechas"></div>
            <div class="modal-proyecto-body">
                <div style="margin-bottom:20px;">
                    <div class="modal-proyecto-section-label">Descripción</div>
                    <div class="ql-snow" style="border:none;padding:0;margin:0;">
                        <div id="modal-descripcion" class="ql-editor" style="padding:0;min-height:0;color:#444;font-size:14px;line-height:1.6;"></div>
                    </div>
                </div>
                <div id="modal-tec-section" style="margin-bottom:20px;">
                    <div class="modal-proyecto-section-label">Stack Tecnológico</div>
                    <div id="modal-tecnologias" style="display:flex;flex-wrap:wrap;gap:6px;"></div>
                </div>
                <div id="modal-evidencias" style="display:none;">
                    <div class="modal-proyecto-section-label" style="padding-top:16px;border-top:1px solid #f0f0f0;">
                        Evidencias <span id="modal-ev-count" style="color:#1abc9c;"></span>
                    </div>
                    <div id="modal-evidencias-lista" style="display:flex;flex-direction:column;gap:12px;margin-top:8px;"></div>
                </div>
            </div>
        </div>
    </div>

{{-- ==================== BARRA PUBLICAR ==================== --}}
<div class="preview-bottom-bar" id="previewBottomBar">
    @php
        $tieneExperiencia = $experiencias->count() > 0;
        $tieneAcademica = $academicas->count() > 0;
        $tieneProyecto = $proyectos->count() > 0;
        $tieneHabilidadTecnica = $habilidadesTecnicasFrontend->count() > 0 || $habilidadesTecnicasBackend->count() > 0;
        $tieneIdioma = $idiomas->count() > 0;
        $tieneBlanda = $habilidadesBlandas->count() > 0;
        $tieneBiografia = !empty($user->biography);
        $tieneProfesion = !empty($user->profession_id);
        
        $seccionesCompletas = 0;
        if ($tieneExperiencia) $seccionesCompletas++;
        if ($tieneAcademica) $seccionesCompletas++;
        if ($tieneProyecto) $seccionesCompletas++;
        if ($tieneHabilidadTecnica) $seccionesCompletas++;
        if ($tieneIdioma) $seccionesCompletas++;
        if ($tieneBlanda) $seccionesCompletas++;
        if ($tieneBiografia) $seccionesCompletas++;
        if ($tieneProfesion) $seccionesCompletas++;
        
        $totalItems = $experiencias->count() + $academicas->count() + $proyectos->count() 
                    + $habilidadesTecnicasFrontend->count() + $habilidadesTecnicasBackend->count() 
                    + $idiomas->count() + $habilidadesBlandas->count();
        
        $puedePublicar = $seccionesCompletas >= 2 && $totalItems >= 2;
    @endphp

    <form action="{{ route('perfil.publicar') }}" method="POST" style="margin:0;" id="formPublicar">
        @csrf
        <button type="submit" 
                class="btn-publicar-main" 
                id="btnPublicar"
                @if(!$puedePublicar) 
                    disabled 
                    style="opacity:0.5;cursor:not-allowed;background:#94a3b8;"
                @endif>
            <i class="fas fa-globe"></i> 
            {{ $puedePublicar ? 'Publicar perfil' : 'Completa tu perfil para publicar' }}
        </button>
        @if(!$puedePublicar)
            <div style="font-size:12px;color:#94a3b8;margin-top:8px;text-align:center;width:100%;">
                <i class="fas fa-info-circle"></i> 
                Necesitas al menos 2 secciones completas y 2 items en total para publicar
            </div>
        @endif
    </form>
</div>

</div><!-- fin .preview-container -->

{{-- Vibe Sidebar --}}
<div class="vibe-sidebar-backdrop" id="vibeBackdrop" onclick="toggleVibeSidebar(false)"></div>
<div class="vibe-sidebar" id="vibeSidebar">
    <div class="vibe-sidebar-header"><h3><i class="fas fa-palette"></i> Personalizar Vibe</h3><button class="vibe-sidebar-close" onclick="toggleVibeSidebar(false)">✕</button></div>
    <div class="vibe-sidebar-body">
        <div class="vibe-card {{ $temaActual === 'default'   ? 'active' : '' }}" data-theme="default"   onclick="selectVibe(this)"><div class="vibe-card-title">Clásico (Predeterminado) @if($temaActual==='default')<i class="fas fa-check-circle" style="color:var(--teal);"></i>@endif</div><div class="vibe-palette"><div class="vibe-color-box" style="background:#2d0a1e;"></div><div class="vibe-color-box" style="background:#4a1030;"></div><div class="vibe-color-box" style="background:#6b1f45;"></div><div class="vibe-color-box" style="background:#0abf9e;"></div></div></div>
        <div class="vibe-card {{ $temaActual === 'sunset'    ? 'active' : '' }}" data-theme="sunset"    onclick="selectVibe(this)"><div class="vibe-card-title">Sunset Glow @if($temaActual==='sunset')<i class="fas fa-check-circle" style="color:#f97316;"></i>@endif</div><div class="vibe-palette"><div class="vibe-color-box" style="background:#1e1b4b;"></div><div class="vibe-color-box" style="background:#312e81;"></div><div class="vibe-color-box" style="background:#4338ca;"></div><div class="vibe-color-box" style="background:#f97316;"></div></div></div>
        <div class="vibe-card {{ $temaActual === 'emerald'   ? 'active' : '' }}" data-theme="emerald"   onclick="selectVibe(this)"><div class="vibe-card-title">Emerald Mint @if($temaActual==='emerald')<i class="fas fa-check-circle" style="color:#10b981;"></i>@endif</div><div class="vibe-palette"><div class="vibe-color-box" style="background:#022c22;"></div><div class="vibe-color-box" style="background:#064e3b;"></div><div class="vibe-color-box" style="background:#0f766e;"></div><div class="vibe-color-box" style="background:#10b981;"></div></div></div>
        <div class="vibe-card {{ $temaActual === 'midnight'  ? 'active' : '' }}" data-theme="midnight"  onclick="selectVibe(this)"><div class="vibe-card-title">Midnight Neon @if($temaActual==='midnight')<i class="fas fa-check-circle" style="color:#a855f7;"></i>@endif</div><div class="vibe-palette"><div class="vibe-color-box" style="background:#0f172a;"></div><div class="vibe-color-box" style="background:#1e293b;"></div><div class="vibe-color-box" style="background:#334155;"></div><div class="vibe-color-box" style="background:#a855f7;"></div></div></div>
        <div class="vibe-card {{ $temaActual === 'ocean'     ? 'active' : '' }}" data-theme="ocean"     onclick="selectVibe(this)"><div class="vibe-card-title">Ocean Breeze @if($temaActual==='ocean')<i class="fas fa-check-circle" style="color:#00b4d8;"></i>@endif</div><div class="vibe-palette"><div class="vibe-color-box" style="background:#0b132b;"></div><div class="vibe-color-box" style="background:#1c2541;"></div><div class="vibe-color-box" style="background:#3a506b;"></div><div class="vibe-color-box" style="background:#00b4d8;"></div></div></div>
        <div class="vibe-card {{ $temaActual === 'sakura'    ? 'active' : '' }}" data-theme="sakura"    onclick="selectVibe(this)"><div class="vibe-card-title">Sakura Dream @if($temaActual==='sakura')<i class="fas fa-check-circle" style="color:#ec4899;"></i>@endif</div><div class="vibe-palette"><div class="vibe-color-box" style="background:#3b0764;"></div><div class="vibe-color-box" style="background:#581c87;"></div><div class="vibe-color-box" style="background:#701a75;"></div><div class="vibe-color-box" style="background:#ec4899;"></div></div></div>
    </div>
    <div class="vibe-sidebar-footer"><button class="vibe-save-btn" id="saveVibeBtn" onclick="saveVibeTheme()"><i class="fas fa-save"></i> Guardar Vibra</button></div>
</div>

<script>
// ============================================================
// FUNCIONES GENERALES
// ============================================================
function toggleDesc(id, btn) {
    var el = document.getElementById(id);
    if (el.classList.contains('collapsed')) {
        el.classList.replace('collapsed', 'expanded');
        btn.textContent = 'Ver menos';
    } else {
        el.classList.replace('expanded', 'collapsed');
        btn.textContent = 'Ver más';
    }
}

function toggleFabMenu() {
    var menu = document.getElementById('fabMenu');
    var icon = document.getElementById('fabIcon');
    var isOpen = menu.classList.contains('open');
    menu.classList.toggle('open', !isOpen);
    icon.className = isOpen ? 'fas fa-ellipsis-h' : 'fas fa-times';
}
document.addEventListener('click', function(e) {
    var container = document.getElementById('fabContainer');
    if (container && !container.contains(e.target)) {
        document.getElementById('fabMenu').classList.remove('open');
        document.getElementById('fabIcon').className = 'fas fa-ellipsis-h';
    }
});

// ============================================================
// LIGHTBOX
// ============================================================
function abrirLightbox(src) {
    var lb = document.getElementById('lightbox-modal');
    if (!lb) {
        lb = document.createElement('div');
        lb.id = 'lightbox-modal';
        lb.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,0.9);z-index:20000;align-items:center;justify-content:center;cursor:pointer;';
        lb.innerHTML = '<div style="position:relative;max-width:90vw;max-height:90vh;"><img id="lightbox-img" style="max-width:100%;max-height:90vh;object-fit:contain;border-radius:8px;"><button id="lightbox-close" style="position:absolute;top:-40px;right:0;background:rgba(0,0,0,0.5);border:none;color:white;font-size:28px;cursor:pointer;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;">✕</button></div>';
        document.body.appendChild(lb);
        lb.addEventListener('click', function(e) { if (e.target === lb || e.target.id === 'lightbox-close') lb.style.display = 'none'; });
    }
    document.getElementById('lightbox-img').src = src;
    lb.style.display = 'flex';
}

// ============================================================
// MODAL DETALLE EXPERIENCIA
// ============================================================
function abrirDetalleExp(i) {
    var d = window.previewExperienciasData[i];
    if (!d) return;
    document.getElementById('det-exp-empresa').textContent = d.empresa;
    document.getElementById('det-exp-cargo').textContent = d.cargo;
    var fecha = d.fecha_inicio;
    if (d.fecha_fin) fecha += ' — ' + d.fecha_fin;
    else if (d.trabajo_actual) fecha += ' — Actualidad';
    document.getElementById('det-exp-fecha-txt').textContent = fecha;
    var ubicPill = document.getElementById('det-exp-ubicacion-pill');
    if (d.ubicacion) {
        document.getElementById('det-exp-ubicacion-txt').textContent = d.ubicacion;
        ubicPill.style.display = 'inline-flex';
    } else {
        ubicPill.style.display = 'none';
    }
    document.getElementById('det-exp-desc-inner').innerHTML = d.descripcion || '<em style="color:#94a3b8;">Sin descripción disponible.</em>';
    document.getElementById('modal-detalle-exp').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarDetalleExp() {
    document.getElementById('modal-detalle-exp').style.display = 'none';
    document.body.style.overflow = '';
}
// ============================================================
// MODAL DETALLE ACADÉMICA
// ============================================================
function abrirDetalleAca(i) {
    var d = window.previewAcademicasData[i];
    if (!d) return;
    document.getElementById('det-aca-inst').textContent = d.institucion;
    document.getElementById('det-aca-titulo').textContent = d.titulo;
    var fecha = d.fecha_inicio;
    if (d.fecha_fin) fecha += ' — ' + d.fecha_fin;
    else if (d.estudio_actual) fecha += ' — Actualidad';
    document.getElementById('det-aca-fecha-txt').textContent = fecha;
    var spPill = document.getElementById('det-aca-specialty-pill');
    if (d.specialty) {
        document.getElementById('det-aca-specialty-txt').textContent = d.specialty;
        spPill.style.display = 'inline-flex';
    } else {
        spPill.style.display = 'none';
    }
    document.getElementById('det-aca-desc').innerHTML = d.descripcion || '<em style="color:#94a3b8;">Sin descripción disponible.</em>';
    var certsDiv = document.getElementById('det-aca-certs');
    certsDiv.innerHTML = '';
    if (d.evidencias && d.evidencias.length) {
        certsDiv.style.display = 'flex';
        d.evidencias.forEach(function(ev) {
            if (ev.ext === 'pdf') {
                certsDiv.innerHTML += '<a href="' + ev.url + '" target="_blank" style="display:inline-flex;align-items:center;gap:6px;color:#0abf9e;font-size:12px;font-weight:600;text-decoration:none;padding:6px 14px;border-radius:20px;background:#f0fdf9;border:1px solid #d1fae5;" onmouseover="this.style.background=\'#d1fae5\'" onmouseout="this.style.background=\'#f0fdf9\'"><i class="fas fa-file-pdf"></i> Ver PDF</a>';
            } else {
               certsDiv.innerHTML += '<button onclick="document.getElementById(\'modal-detalle-aca\').style.display=\'none\'; abrirLightbox(\'' + ev.url + '\')" style="display:inline-flex;align-items:center;gap:6px;color:#0abf9e;font-size:12px;font-weight:600;cursor:pointer;padding:6px 14px;border-radius:20px;background:#f0fdf9;border:1px solid #d1fae5;font-family:inherit;" onmouseover="this.style.background=\'#d1fae5\'" onmouseout="this.style.background=\'#f0fdf9\'"><i class="fas fa-certificate"></i> Ver certificado</button>';
            }
        });
    } else {
        certsDiv.style.display = 'none';
    }
    document.getElementById('modal-detalle-aca').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function cerrarDetalleAca() {
    document.getElementById('modal-detalle-aca').style.display = 'none';
    document.body.style.overflow = '';
}

// ============================================================
// MODAL PROYECTO
// ============================================================
function previewEscapeHtml(s) {
    if (s == null) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function abrirModal(data) {
    document.getElementById('modal-nombre').textContent = data.nombre;

    var badgesDiv = document.getElementById('modal-badges');
    badgesDiv.innerHTML = '';
    var badge = function(t, bg, c) {
        return '<span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:'+bg+';color:'+c+';">'+t+'</span>';
    };
    var badgeIcon = function(ic, t, bg, c) {
        return '<span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:'+bg+';color:'+c+';"><i class="'+ic+'" style="font-size:10px;"></i>'+t+'</span>';
    };
    if (data.estado) {
        var bg = data.estado==='Completado'?'#d1fae5':data.estado==='En curso'?'#fef3c7':'#f1f5f9';
        var c  = data.estado==='Completado'?'#065f46':data.estado==='En curso'?'#92400e':'#64748b';
        badgesDiv.innerHTML += badge(previewEscapeHtml(data.estado), bg, c);
    }
    if (data.rol)    badgesDiv.innerHTML += badgeIcon('fas fa-user-check', previewEscapeHtml(data.rol),    '#ede9fe','#5b21b6');
    if (data.cliente) badgesDiv.innerHTML += badgeIcon('fas fa-building',  previewEscapeHtml(data.cliente),'#f1f5f9','#475569');

    var fechasDiv = document.getElementById('modal-fechas');
    fechasDiv.innerHTML = '';
    if (data.fecha_inicio) fechasDiv.innerHTML += '<span><i class="far fa-calendar-alt" style="color:#94a3b8;margin-right:4px;"></i>Inicio: <strong>'+previewEscapeHtml(data.fecha_inicio)+'</strong></span>';
    if (data.fecha_fin)    fechasDiv.innerHTML += '<span><i class="far fa-calendar-alt" style="color:#94a3b8;margin-right:4px;"></i>Fin: <strong>'+previewEscapeHtml(data.fecha_fin)+'</strong></span>';

    document.getElementById('modal-descripcion').innerHTML = data.descripcion || '';

    var tecDiv     = document.getElementById('modal-tecnologias');
    var tecSection = document.getElementById('modal-tec-section');
    tecDiv.innerHTML = '';
    if (data.tecnologias && data.tecnologias.length) {
        tecSection.style.display = 'block';
        data.tecnologias.forEach(function(t) { tecDiv.innerHTML += '<span class="tec-badge">'+previewEscapeHtml(t)+'</span>'; });
    } else { tecSection.style.display = 'none'; }

    var evDiv = document.getElementById('modal-evidencias-lista');
    var evSec = document.getElementById('modal-evidencias');
    evDiv.innerHTML = '';
    if (data.evidencias && data.evidencias.length) {
        evSec.style.display = 'block';
        document.getElementById('modal-ev-count').textContent = '(' + data.evidencias.length + ')';
        data.evidencias.forEach(function(ev) {
            var item = document.createElement('div');
            if (ev.tipo === 'imagen' && ev.imagen) {
                item.style.cssText = 'background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;';
                item.innerHTML =
                    '<div style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">' +
                        '<div style="width:36px;height:36px;background:#0abf9e15;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image" style="color:#0abf9e;font-size:18px;"></i></div>' +
                        '<strong style="font-size:14px;color:#0f172a;flex:1;">Imagen del proyecto</strong>' +
                        '<button onclick="abrirLightbox(\''+ev.imagen+'\')" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:40px;border:1.5px solid #0abf9e;background:#f0fdf9;color:#0abf9e;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background=\'#0abf9e\';this.style.color=\'white\';" onmouseout="this.style.background=\'#f0fdf9\';this.style.color=\'#0abf9e\';"><i class="fas fa-eye"></i> Ver</button>' +
                    '</div>' +
                    '<div style="padding:16px;cursor:pointer;" onclick="abrirLightbox(\''+ev.imagen+'\')">' +
                        '<img src="'+ev.imagen+'" style="width:100%;max-height:220px;object-fit:cover;border-radius:12px;" onmouseover="this.style.opacity=\'0.85\'" onmouseout="this.style.opacity=\'1\'">' +
                    '</div>';
            } else if (ev.tipo === 'enlace' && ev.url) {
                item.style.cssText = 'background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;';
                item.innerHTML =
                    '<div style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">' +
                        '<div style="width:36px;height:36px;background:#0abf9e15;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-link" style="color:#0abf9e;font-size:18px;"></i></div>' +
                        '<strong style="font-size:14px;color:#0f172a;flex:1;">'+previewEscapeHtml(ev.titulo||'Enlace')+'</strong>' +
                    '</div>' +
                    '<div style="padding:16px;display:flex;align-items:center;justify-content:space-between;gap:10px;">' +
                        '<span style="font-size:12px;color:#94a3b8;word-break:break-all;flex:1;">'+previewEscapeHtml(ev.url)+'</span>' +
                        '<a href="'+ev.url+'" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:40px;background:#0abf9e;color:white;font-size:12px;font-weight:600;text-decoration:none;white-space:nowrap;transition:background 0.2s;" onmouseover="this.style.background=\'#07866e\';" onmouseout="this.style.background=\'#0abf9e\';"><i class="fas fa-eye"></i> Ver enlace</a>' +
                    '</div>';
            } else if (ev.tipo === 'repositorio' && ev.url) {
                var icon = ev.plataforma==='GitLab'?'fa-gitlab':ev.plataforma==='Bitbucket'?'fa-bitbucket':'fa-github';
                item.style.cssText = 'background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;';
                item.innerHTML =
                    '<div style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">' +
                        '<div style="width:36px;height:36px;background:#0abf9e15;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fab '+icon+'" style="color:#0abf9e;font-size:18px;"></i></div>' +
                        '<strong style="font-size:14px;color:#0f172a;flex:1;">'+previewEscapeHtml(ev.titulo||'Repositorio')+'</strong>' +
                    '</div>' +
                    '<div style="padding:16px;display:flex;align-items:center;justify-content:space-between;gap:10px;">' +
                        '<span style="font-size:12px;color:#94a3b8;word-break:break-all;flex:1;">'+previewEscapeHtml(ev.url)+'</span>' +
                        '<a href="'+ev.url+'" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:40px;background:#0abf9e;color:white;font-size:12px;font-weight:600;text-decoration:none;white-space:nowrap;transition:background 0.2s;" onmouseover="this.style.background=\'#07866e\';" onmouseout="this.style.background=\'#0abf9e\';"><i class="fas fa-eye"></i> Ver repositorio</a>' +
                    '</div>';
            }
            evDiv.appendChild(item);
        });
    } else { evSec.style.display = 'none'; }

    document.getElementById('modal-proyecto').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarModal() { document.getElementById('modal-proyecto').style.display = 'none'; document.body.style.overflow = ''; }
function abrirModalPorId(projectId) { var d = (window.previewProjectsById||{})[projectId]; if (d) abrirModal(d); }

// ============================================================
// MODALES "VER TODOS"
// ============================================================
function abrirModalTodosProyectos()  { var el=document.getElementById('modal-todos-proyectos');   if(el){el.style.display='flex';document.body.style.overflow='hidden';} }
function cerrarModalTodosProyectos() { var el=document.getElementById('modal-todos-proyectos');   if(el){el.style.display='none';document.body.style.overflow='';} }
function abrirModalExperiencias()    { var el=document.getElementById('modal-todos-experiencias');if(el){el.style.display='flex';document.body.style.overflow='hidden';} }
function cerrarModalExperiencias()   { var el=document.getElementById('modal-todos-experiencias');if(el){el.style.display='none';document.body.style.overflow='';} }
function abrirModalAcademicas()      { var el=document.getElementById('modal-todos-academicas');  if(el){el.style.display='flex';document.body.style.overflow='hidden';} }
function cerrarModalAcademicas()     { var el=document.getElementById('modal-todos-academicas');  if(el){el.style.display='none';document.body.style.overflow='';} }
function abrirModalTecnicas()        { var el=document.getElementById('modal-todos-tecnicas');    if(el){el.style.display='flex';document.body.style.overflow='hidden';} }
function cerrarModalTecnicas()       { var el=document.getElementById('modal-todos-tecnicas');    if(el){el.style.display='none';document.body.style.overflow='';} }
function abrirModalBlandas()         { var el=document.getElementById('modal-todos-blandas');     if(el){el.style.display='flex';document.body.style.overflow='hidden';} }
function cerrarModalBlandas()        { var el=document.getElementById('modal-todos-blandas');     if(el){el.style.display='none';document.body.style.overflow='';} }
function abrirModalIdiomas()         { var el=document.getElementById('modal-todos-idiomas');     if(el){el.style.display='flex';document.body.style.overflow='hidden';} }
function cerrarModalIdiomas()        { var el=document.getElementById('modal-todos-idiomas');     if(el){el.style.display='none';document.body.style.overflow='';} }
function abrirModalCompartir()       { 
    document.getElementById('modal-compartir').style.display='flex';
    document.body.style.overflow='hidden';
    generarQR();
}
function cerrarModalCompartir()      { 
    document.getElementById('modal-compartir').style.display='none'; 
    document.body.style.overflow='';
    var btnTexto = document.getElementById('btn-copiar-texto');
    if (btnTexto) btnTexto.textContent = 'Copiar';
}

// Cerrar al clic fuera
['modal-todos-proyectos','modal-todos-experiencias','modal-todos-academicas','modal-todos-tecnicas','modal-todos-blandas','modal-todos-idiomas','modal-proyecto','modal-compartir','modal-detalle-exp','modal-detalle-aca'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('click', function(e) { if (e.target === this) { this.style.display='none'; document.body.style.overflow=''; } });
});

// ============================================================
// COPIAR ENLACE + QR
// ============================================================
function copiarLinkPortafolio() {
    var input = document.getElementById('share-link-input');
    if (!input) return;
    
    input.select();
    input.setSelectionRange(0, 99999);
    
    var link = input.value;
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(link).then(function() {
            var texto = document.getElementById('btn-copiar-texto');
            var btn = document.getElementById('btn-copiar-link');
            if (texto) texto.textContent = '¡Copiado!';
            if (btn) btn.style.background = '#10b981';
            
            setTimeout(function() {
                if (texto) texto.textContent = 'Copiar';
                if (btn) btn.style.background = '#0abf9e';
            }, 2000);
        });
    } else {
        document.execCommand('copy');
        Swal.fire({
            icon: 'success',
            title: 'Enlace copiado',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }
}

function generarQR() {
    var container = document.getElementById('qrCodeContainer');
    var input = document.getElementById('share-link-input');
    
    if (!container || !input) return;
    
    var link = input.value;
    if (!link) return;
    
    container.innerHTML = '';
    
    if (typeof QRCode !== 'undefined') {
        try {
            new QRCode(container, {
                text: link,
                width: 100,
                height: 100,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        } catch (e) {
            container.innerHTML = '<span style="font-size:10px;color:#94a3b8;">QR no disponible</span>';
        }
    } else {
        container.innerHTML = '<span style="font-size:10px;color:#94a3b8;">Cargando QR...</span>';
    }
}

function copiarQR() {
    var canvas = document.querySelector('#qrCodeContainer canvas');
    if (!canvas) {
        Swal.fire({
            icon: 'info',
            title: 'QR no disponible',
            text: 'Primero genera el código QR.',
            confirmButtonColor: '#0abf9e'
        });
        return;
    }

    canvas.toBlob(function(blob) {
        if (!blob) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo copiar el QR.',
                confirmButtonColor: '#0abf9e'
            });
            return;
        }

        if (navigator.clipboard && navigator.clipboard.write) {
            var item = new ClipboardItem({
                'image/png': blob
            });
            navigator.clipboard.write([item]).then(function() {
                Swal.fire({
                    icon: 'success',
                    title: '✅ QR copiado',
                    text: 'El código QR se ha copiado como imagen.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }).catch(function() {
                descargarQR();
            });
        } else {
            descargarQR();
        }
    }, 'image/png');
}

function descargarQR() {
    var canvas = document.querySelector('#qrCodeContainer canvas');
    if (!canvas) return;

    var link = document.createElement('a');
    link.download = 'codigo-qr-portafolio.png';
    link.href = canvas.toDataURL('image/png');
    link.click();

    Swal.fire({
        icon: 'success',
        title: '✅ QR descargado',
        text: 'El código QR se ha descargado como imagen.',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

// ============================================================
// PDF / IMAGEN
// ============================================================
function crearCopiaCompleta() {
    var original = document.getElementById('previewContainer');
    var clone = original.cloneNode(true);

    clone.querySelectorAll('.description.collapsed, .proyecto-desc-wrap.collapsed').forEach(function(el) {
        el.classList.remove('collapsed'); el.classList.add('expanded');
        el.style.maxHeight = 'none'; el.style.overflow = 'visible';
    });

    clone.querySelectorAll('.ver-mas-btn').forEach(function(btn) { btn.remove(); });
    clone.querySelectorAll('.btn-ver-todos').forEach(function(btn) { btn.style.display = 'none'; });

    [
        ['#experiencias-completas', '#experiencias-grid',   '.cards-grid'],
        ['#academicas-completas',   '#academicas-grid',     '.cards-grid'],
        ['#blandas-completas',      '#blandas-grid',        '.skills-container'],
        ['#idiomas-completos',      '#idiomas-grid',        '.idiomas-preview-grid'],
        ['#proyectos-completos',    '#proyectos-grid',      '.cards-grid'],
    ].forEach(function(pair) {
        var source = document.querySelector(pair[0]);
        if (!source) return;
        var target = clone.querySelector(pair[1]);
        if (!target) return;
        var inner = source.querySelector(pair[2]);
        if (inner) target.innerHTML = inner.innerHTML;
    });

    var tecSource = document.getElementById('tecnicas-completas');
    if (tecSource) { var tecTarget = clone.querySelector('#tecnicas-grid'); if (tecTarget) tecTarget.innerHTML = tecSource.innerHTML; }

    clone.querySelectorAll('.idioma-cert-link,.btn-certificado,a[onclick*="abrirLightbox"]').forEach(function(el) { el.remove(); });

    var seccionesARevisar = [
        { id: 'section-experiencias', selector: '.card:not(.empty-message-preview)' },
        { id: 'section-academicas',   selector: '.card:not(.empty-message-preview)' },
        { id: 'section-tecnicas',     selector: '.tech-skill-item' },
        { id: 'section-blandas',      selector: '.soft-skill-tag' },
        { id: 'section-idiomas',      selector: '.idioma-preview-card' },
        { id: 'section-proyectos',    selector: '.card:not(.empty-message-preview)' },
    ];
    seccionesARevisar.forEach(function(s) {
        var sec = clone.querySelector('#' + s.id);
        if (!sec) return;
        var tieneContenido = sec.querySelectorAll(s.selector).length > 0;
        var tieneVacio     = sec.querySelector('.empty-message-preview') !== null;
        if (!tieneContenido || tieneVacio) {
            sec.remove();
        }
    });

    clone.querySelectorAll('.empty-message-preview').forEach(function(el) { el.remove(); });

    var fabClone    = clone.querySelector('#fabContainer');       if (fabClone)    fabClone.style.display    = 'none';
    var barClone    = clone.querySelector('#previewBottomBar');   if (barClone)    barClone.style.display    = 'none';
    var volverClone = clone.querySelector('.btn-volver-flotante');if (volverClone) volverClone.style.display = 'none';
    var vibeSide    = clone.querySelector('#vibeSidebar');        if (vibeSide)    vibeSide.style.display    = 'none';
    var vibeBack    = clone.querySelector('#vibeBackdrop');       if (vibeBack)    vibeBack.style.display    = 'none';
    clone.style.paddingBottom = '0';

    return clone;
}

async function descargarPDF() {
    document.getElementById('fabMenu').classList.remove('open');
    Swal.fire({ title:'Generando PDF...', allowOutsideClick:false, showConfirmButton:false, didOpen:function(){ Swal.showLoading(); } });
    try {
        var clone = crearCopiaCompleta();
        var tempDiv = document.createElement('div');
        Object.assign(tempDiv.style, { position:'absolute', left:'-9999px', top:'-9999px', width:'1200px', backgroundColor:'white' });
        tempDiv.appendChild(clone); document.body.appendChild(tempDiv);
        clone.style.cssText = 'max-width:1200px;margin:0 auto;';
        setTimeout(async function() {
            try {
                var canvas = await html2canvas(tempDiv, { scale:2.5, useCORS:true, backgroundColor:'#ffffff', logging:false, windowWidth:tempDiv.scrollWidth, windowHeight:tempDiv.scrollHeight });
                var jsPDF = window.jspdf.jsPDF;
                var imgData = canvas.toDataURL('image/png');
                var pdf = new jsPDF({ unit:'mm', format:'a4', orientation:'portrait' });
                var pw = pdf.internal.pageSize.getWidth();
                var ph = pdf.internal.pageSize.getHeight();
                var iw = pw - 20;
                var ih = (canvas.height * iw) / canvas.width;
                var pos = 10, left = ih - (ph - 20), page = 1;
                pdf.addImage(imgData, 'PNG', 10, pos, iw, ih);
                while (left > 0) { pdf.addPage(); pos = 10 - (page * (ph - 20)); pdf.addImage(imgData, 'PNG', 10, pos, iw, ih); left -= (ph - 20); page++; }
                pdf.save('portafolio.pdf');
                document.body.removeChild(tempDiv);
                Swal.fire({ icon:'success', title:'¡PDF descargado!', toast:true, position:'top-end', showConfirmButton:false, timer:3000 });
            } catch(err) { document.body.removeChild(tempDiv); throw err; }
        }, 800);
    } catch(e) { Swal.fire({ icon:'error', title:'Error', text:'No se pudo generar el PDF.' }); }
}

async function descargarImagen() {
    document.getElementById('fabMenu').classList.remove('open');
    Swal.fire({ title:'Generando imagen...', allowOutsideClick:false, showConfirmButton:false, didOpen:function(){ Swal.showLoading(); } });
    try {
        var clone = crearCopiaCompleta();
        var tempDiv = document.createElement('div');
        Object.assign(tempDiv.style, { position:'absolute', left:'-9999px', top:'-9999px', width:'1200px', backgroundColor:'white' });
        tempDiv.appendChild(clone); document.body.appendChild(tempDiv);
        clone.style.cssText = 'max-width:1200px;margin:0 auto;';
        setTimeout(async function() {
            try {
                var canvas = await html2canvas(tempDiv, { scale:2.5, useCORS:true, backgroundColor:'#ffffff', logging:false, windowWidth:tempDiv.scrollWidth, windowHeight:tempDiv.scrollHeight });
                var link = document.createElement('a');
                link.download = 'portafolio.png'; link.href = canvas.toDataURL('image/png'); link.click();
                document.body.removeChild(tempDiv);
                Swal.fire({ icon:'success', title:'¡Imagen descargada!', toast:true, position:'top-end', showConfirmButton:false, timer:3000 });
            } catch(err) { document.body.removeChild(tempDiv); throw err; }
        }, 800);
    } catch(e) { Swal.fire({ icon:'error', title:'Error', text:'No se pudo generar la imagen.' }); }
}

// ============================================================
// PUBLICAR
// ============================================================
document.getElementById('formPublicar').addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
        title:'¿Publicar portafolio?', text:'Tu perfil será visible para todos los usuarios.',
        showCancelButton:true, confirmButtonColor:'#0abf9e', cancelButtonColor:'#6c757d',
        confirmButtonText:'Publicar', cancelButtonText:'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire({ title:'Publicando...', showConfirmButton:false, allowOutsideClick:false });
            document.getElementById('formPublicar').submit();
        }
    });
});

function filtrarProyectosPreview(q) {
    q = q.toLowerCase().trim();
    var cards = document.querySelectorAll('#folder-grid-preview [data-nombre]');
    var visible = 0;
    cards.forEach(function(card) {
        var match = !q || card.dataset.nombre.includes(q) || card.dataset.techs.includes(q) || card.dataset.estado.includes(q);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    var noRes = document.getElementById('no-results-preview');
    if (noRes) noRes.style.display = visible === 0 ? 'block' : 'none';
}

// ============================================================
// VIBE SELECTOR
// ============================================================
var selectedTheme = "{{ $temaActual }}";

function toggleVibeSidebar(show) {
    var sidebar  = document.getElementById('vibeSidebar');
    var backdrop = document.getElementById('vibeBackdrop');
    if (show) {
        sidebar.classList.add('open'); backdrop.classList.add('show');
        document.getElementById('fabMenu').classList.remove('open');
        document.getElementById('fabIcon').className = 'fas fa-ellipsis-h';
    } else {
        sidebar.classList.remove('open'); backdrop.classList.remove('show');
        applyThemeClass(selectedTheme);
        document.querySelectorAll('.vibe-card').forEach(function(c) { c.classList.toggle('active', c.dataset.theme === selectedTheme); });
    }
}
function selectVibe(card) {
    document.querySelectorAll('.vibe-card').forEach(function(c) { c.classList.remove('active'); });
    card.classList.add('active');
    applyThemeClass(card.dataset.theme);
}
function applyThemeClass(theme) {
    var container = document.getElementById('previewContainer');
    container.classList.remove('theme-sunset','theme-emerald','theme-midnight','theme-ocean','theme-sakura');
    if (theme !== 'default') container.classList.add('theme-' + theme);
}
function saveVibeTheme() {
    var activeCard = document.querySelector('.vibe-card.active');
    if (!activeCard) return;
    var theme = activeCard.dataset.theme;
    var saveBtn = document.getElementById('saveVibeBtn');
    saveBtn.disabled = true; saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    fetch("{{ route('portfolio.theme.update') }}", {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
        body: JSON.stringify({ theme: theme })
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
        saveBtn.disabled = false; saveBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Vibra';
        if (data.success) {
            selectedTheme = theme;
            Swal.fire({ icon:'success', title:'¡Vibra actualizada!', toast:true, position:'top-end', showConfirmButton:false, timer:3000 });
            toggleVibeSidebar(false);
        } else { Swal.fire({ icon:'error', title:'Error', text:'Ocurrió un error.' }); }
    })
    .catch(function(){
        saveBtn.disabled = false; saveBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Vibra';
        Swal.fire({ icon:'error', title:'Error', text:'No se pudo conectar.' });
    });
}

// ============================================================
// INICIALIZACIÓN
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    var shareInput = document.getElementById('share-link-input');
    if (shareInput) {
        var link = "{{ $user->portfolio ? url('/portafolio/'.$user->portfolio->slug) : '' }}";
        if (link) {
            shareInput.value = link;
        }
    }
});

function mostrarAlertaIncompleto() {
    document.getElementById('fabMenu').classList.remove('open');
    
    var seccionesCompletas = @json($seccionesCompletas ?? 0);
    var estadoSecciones = @json($estadoSecciones ?? []);
    
    var completas = Object.values(estadoSecciones).filter(function(s) { return s.completo; });
    var faltantes = Object.values(estadoSecciones).filter(function(s) { return !s.completo; });
    
    Swal.fire({
        icon: 'warning',
        title: '⚠️ Portafolio incompleto',
        html: `
            <div style="text-align: left;">
                <div style="background: #f0fdf4; border-left: 4px solid #0abf9e; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px;">
                    <p style="margin: 0; font-size: 14px; color: #065f46;">
                        <strong>Requisito:</strong> Mínimo 2 secciones completas
                    </p>
                    <p style="margin: 4px 0 0; font-size: 13px; color: #047857;">
                        Completadas: ${seccionesCompletas} de 2
                    </p>
                </div>
                
                ${faltantes.length > 0 ? `
                    <p style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;">
                        Secciones pendientes:
                    </p>
                    <ul style="margin: 0 0 16px 0; padding: 0; list-style: none;">
                        ${faltantes.map(function(item) {
                            return `<li style="font-size: 14px; color: #64748b; padding: 6px 0; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px;">
                                <span style="color: #94a3b8;">○</span>
                                ${item.icono} ${item.nombre}
                            </li>`;
                        }).join('')}
                    </ul>
                ` : ''}
                
                <div style="background: #f8fafc; border-radius: 6px; padding: 10px 14px; text-align: center;">
                    <span style="font-size: 13px; color: #64748b;">
                        💡 Complete las secciones pendientes para habilitar la descarga
                    </span>
                </div>
            </div>
        `,
        confirmButtonColor: '#0abf9e',
        confirmButtonText: 'Entendido',
        showCancelButton: false,
        width: 480,
    });
}
</script>

</body>
</html>