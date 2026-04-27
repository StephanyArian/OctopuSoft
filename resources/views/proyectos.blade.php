{{-- resources/views/proyectos.blade.php --}}
{{-- HU-10: Gestionar mis proyectos + HU-22: Agregar tecnologías --}}

<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/proyectos.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --burg-deep: #2d0a1e;
            --burg-mid:  #4a1030;
            --teal:      #0abf9e;
            --teal-dim:  #07866e;
            --teal-light:#1de8c0;
            --white:     #ffffff;
            --off:       #f5f6f8;
            --gray-100:  #edf0f4;
            --gray-300:  #c8cdd8;
            --gray-500:  #7a8298;
            --gray-700:  #3d4459;
        }

        .main-content {
            background: linear-gradient(135deg, var(--off) 0%, var(--gray-100) 50%, var(--white) 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .shell {
            display: flex;
            flex-direction: column;
            min-height: 600px;
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,.15);
        }

        .header-bar {
            background: linear-gradient(135deg, var(--burg-deep), var(--burg-mid));
            color: var(--white);
            padding: 10px 20px;
            font-size: 12px;
            letter-spacing: 1px;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
        }

        .navbar {
            display: flex;
            background: var(--burg-deep);
            color: var(--white);
            flex-wrap: wrap;
            padding: 0;
        }

        .nav-tab {
            padding: 14px 24px;
            font-size: 13px;
            font-weight: 600;
            border-right: 1px solid rgba(255,255,255,.1);
            cursor: pointer;
            letter-spacing: .5px;
            transition: all .3s ease;
        }

        .nav-tab.active {
            background: var(--white);
            color: var(--burg-deep);
            font-weight: 700;
            position: relative;
        }

        .nav-tab.active::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: var(--teal);
        }

        .nav-tab.muted {
            color: var(--gray-300);
            background: rgba(255,255,255,.05);
        }

        .body-row {
            display: flex;
            flex: 1;
            flex-wrap: wrap;
        }

        .sidebar {
            width: 220px;
            min-width: 220px;
            background: var(--white);
            border-right: 1px solid var(--gray-100);
            padding: 20px 0;
        }

        .sidebar-item {
            padding: 12px 20px;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--gray-700);
            transition: all .3s ease;
            font-weight: 500;
            text-decoration: none;
        }

        .sidebar-item:hover {
            background: var(--off);
            color: var(--teal);
        }

        .sidebar-item.active {
            background: linear-gradient(90deg, rgba(10,191,158,.08) 0%, transparent 100%);
            font-weight: 700;
            color: var(--teal);
            border-left: 3px solid var(--teal);
        }

        .main-panel {
            flex: 1;
            padding: 32px 40px;
            background: var(--white);
            overflow: auto;
        }

        /* ══ PROYECTOS — estilos originales intactos ══ */
        .proy-header {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 32px;
        }

        .proy-title-wrap h2 {
            font-size: 26px;
            font-weight: 800;
            color: var(--burg-deep);
            letter-spacing: -.5px;
            position: relative;
            display: inline-block;
            margin-bottom: 6px;
        }

        .proy-title-wrap h2::after {
            content: '';
            position: absolute;
            bottom: -6px; left: 0;
            width: 48px; height: 3px;
            background: linear-gradient(90deg, var(--teal), var(--teal-light));
            border-radius: 3px;
        }

        .proy-title-wrap p { font-size: 13px; color: #9ca3af; margin-top: 14px; }

        .proy-btn-nuevo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 40px;
            border: none;
            background: linear-gradient(135deg, var(--teal), var(--teal-dim));
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
        }

        .proy-btn-nuevo:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(10,191,158,.45); }

        .proy-form-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 32px;
            display: none;
        }

        .proy-form-card.open { display: block; animation: fadeIn .3s ease; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .proy-form-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--burg-deep);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--teal);
            display: inline-block;
        }

        .proy-form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .proy-field-full { grid-column: span 2; }
        .proy-field { display: flex; flex-direction: column; gap: 6px; }

        .proy-field label {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .proy-field label span { color: var(--teal); }

        .proy-inp, .proy-textarea, .proy-select {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 14px;
            font-family: inherit;
            background: white;
            width: 100%;
            transition: all .2s;
        }

        .proy-inp:focus, .proy-textarea:focus, .proy-select:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(10,191,158,.1);
        }

        .proy-textarea { resize: vertical; min-height: 80px; }

        .tec-section {
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border-radius: 20px;
            padding: 20px;
            margin-top: 16px;
            border: 1px solid rgba(10,191,158,.15);
            box-shadow: 0 4px 12px rgba(0,0,0,.03);
            transition: all .3s ease;
        }

        .tec-section:hover { border-color: rgba(10,191,158,.3); box-shadow: 0 6px 16px rgba(0,0,0,.05); }

        .tec-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .tec-header-left { display: flex; align-items: center; gap: 10px; }
        .tec-icon { font-size: 22px; }
        .tec-title { font-size: 16px; font-weight: 700; color: #1e293b; letter-spacing: -0.3px; }

        .tec-badge-count {
            background: linear-gradient(135deg, var(--teal), var(--teal-dim));
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 30px;
            box-shadow: 0 2px 6px rgba(10,191,158,.2);
        }

        .tec-subtitle { font-size: 12px; color: #64748b; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px dashed #e2e8f0; }

        .tec-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            min-height: 70px;
            background: #ffffff;
            border-radius: 16px;
            padding: 14px;
            border: 1px solid #e2e8f0;
        }

        .tec-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%);
            color: #1e293b;
            padding: 6px 12px 6px 10px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 600;
            transition: all .2s ease;
            animation: badgePop .25s ease-out;
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
        }

        @keyframes badgePop {
            0%   { transform: scale(.8); opacity: 0; }
            80%  { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }

        .tec-badge:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,.1); }
        .tec-badge-logo { display: inline-flex; align-items: center; justify-content: center; width: 20px; }
        .tec-badge-logo i { font-size: 14px; }

        .tec-badge-remove {
            cursor: pointer;
            color: #ef4444;
            font-weight: bold;
            font-size: 14px;
            margin-left: 4px;
            transition: all .2s;
            width: 18px; height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .tec-badge-remove:hover { background: rgba(239,68,68,.1); transform: scale(1.15); }

        .tec-empty-state { width: 100%; text-align: center; padding: 16px; }
        .tec-empty-icon  { font-size: 32px; margin-bottom: 8px; opacity: .5; }
        .tec-empty-text  { font-size: 13px; color: #94a3b8; font-weight: 500; }
        .tec-empty-hint  { font-size: 11px; color: #cbd5e1; margin-top: 4px; }

        .tec-input-wrapper { display: flex; gap: 12px; margin-bottom: 16px; }
        .tec-input-group   { flex: 1; position: relative; display: flex; align-items: center; }
        .tec-input-icon    { position: absolute; left: 14px; color: #94a3b8; display: flex; align-items: center; }

        .tec-input {
            width: 100%;
            padding: 12px 12px 12px 42px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 13px;
            font-family: inherit;
            background: white;
            transition: all .2s;
        }

        .tec-input:focus { outline: none; border-color: var(--teal); box-shadow: 0 0 0 3px rgba(10,191,158,.1); }

        .tec-btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--teal), var(--teal-dim));
            color: white;
            border: none;
            padding: 0 22px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 2px 6px rgba(10,191,158,.3);
        }

        .tec-btn-add:hover { transform: translateY(-2px); box-shadow: 0 6px 14px rgba(10,191,158,.4); }

        .tec-suggestions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-bottom: 16px; padding: 8px 0; }
        .tec-suggestions-label { font-size: 11px; color: #64748b; font-weight: 600; }

        .tec-suggestion-chip {
            background: #f1f5f9;
            border: none;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 500;
            color: #475569;
            cursor: pointer;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .tec-suggestion-chip i { font-size: 10px; }
        .tec-suggestion-chip:hover { background: linear-gradient(135deg, var(--teal), var(--teal-dim)); color: white; transform: translateY(-1px); }

        .tec-footer { display: flex; align-items: center; gap: 6px; padding-top: 12px; border-top: 1px solid #f1f5f9; font-size: 10px; color: #94a3b8; }

        .ev-trigger-wrap { margin-top: 14px; }

        .ev-trigger-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-radius: 16px;
            border: 1.5px dashed rgba(10,191,158,.4);
            background: rgba(10,191,158,.04);
            cursor: pointer;
            transition: all .2s;
            font-family: inherit;
            gap: 12px;
        }

        .ev-trigger-btn:hover { border-color: var(--teal); background: rgba(10,191,158,.09); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(10,191,158,.12); }
        .ev-trigger-left  { display: flex; align-items: center; gap: 12px; }

        .ev-trigger-icon-box {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: rgba(10,191,158,.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }

        .ev-trigger-texts strong { font-size: 14px; font-weight: 700; color: var(--burg-deep); display: block; margin-bottom: 2px; }
        .ev-trigger-texts span   { font-size: 12px; color: #94a3b8; }
        .ev-trigger-right        { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

        .ev-trigger-count {
            background: var(--teal);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            display: none;
        }

        .ev-trigger-count.visible { display: inline-flex; }
        .ev-trigger-arrow { color: #94a3b8; font-size: 13px; transition: transform .2s; }
        .ev-trigger-btn:hover .ev-trigger-arrow { color: var(--teal); transform: translateX(3px); }

        .proy-grid { display: grid; gap: 20px; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); }

        .proy-card { background: white; border: 1px solid #edf0f4; border-radius: 18px; overflow: hidden; transition: all .2s; }
        .proy-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,.08); }

        .proy-card-band { height: 4px; background: linear-gradient(90deg, var(--burg-deep), var(--teal)); }

        .proy-card-top { display: flex; justify-content: space-between; align-items: flex-start; padding: 16px 16px 0; }
        .proy-card-nombre { font-size: 16px; font-weight: 700; color: var(--burg-deep); }
        .proy-card-actions { display: flex; gap: 4px; }

        .proy-icon-btn { background: none; border: none; cursor: pointer; padding: 6px; border-radius: 8px; transition: background .2s; }
        .proy-icon-btn:hover { background: #f1f5f9; }

        .proy-card-body { padding: 12px 16px 16px; }

        .proy-card-desc {
            font-size: 13px; color: #64748b; line-height: 1.5; margin-bottom: 12px;
            display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
        }

        .proy-card-tec { display: flex; flex-wrap: wrap; gap: 5px; margin: 10px 0; }

        .tec-mini {
            background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 20px;
            font-size: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;
        }

        .tec-mini i { font-size: 10px; }

        .proy-card-footer { display: flex; justify-content: space-between; align-items: center; font-size: 11px; margin-top: 8px; }
        .proy-card-fecha  { color: #94a3b8; display: flex; align-items: center; gap: 4px; }

        .proy-badge { padding: 4px 12px; border-radius: 20px; font-weight: 600; }
        .proy-badge-completado { background: #dcfce7; color: #166534; }
        .proy-badge-en-curso   { background: #fef9c3; color: #854d0e; }
        .proy-badge-en-pausa   { background: #f1f5f9; color: #475569; }

        .proy-empty { text-align: center; padding: 60px 20px; background: #f8fafc; border-radius: 20px; border: 2px dashed #e2e8f0; }
        .proy-empty-icon { font-size: 48px; margin-bottom: 16px; }
        .proy-empty h3 { font-size: 18px; font-weight: 700; color: var(--burg-deep); margin-bottom: 8px; }
        .proy-empty p  { font-size: 13px; color: #94a3b8; margin-bottom: 20px; }

        .proy-empty-btn {
            background: linear-gradient(135deg, var(--teal), var(--teal-dim));
            color: white; border: none; padding: 10px 28px; border-radius: 40px; font-weight: 700; cursor: pointer; transition: all .2s;
        }

        .proy-empty-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(10,191,158,.4); }

        .proy-form-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0; }

        .proy-btn-cancel { padding: 8px 24px; border-radius: 40px; border: 1.5px solid #e2e8f0; background: white; font-weight: 600; cursor: pointer; transition: all .2s; }
        .proy-btn-cancel:hover { background: #f1f5f9; }

        .proy-btn-save {
            padding: 8px 28px; border-radius: 40px; border: none;
            background: linear-gradient(135deg, var(--teal), var(--teal-dim));
            color: white; font-weight: 700; cursor: pointer; transition: all .2s;
        }

        .proy-btn-save:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(10,191,158,.4); }

        .proy-err-msg { font-size: 11px; color: #ef4444; display: none; }
        .proy-err-msg.visible { display: block; }
        .proy-inp.proy-err, .proy-textarea.proy-err { border-color: #ef4444; }

        .proy-toast {
            position: fixed; bottom: 20px; right: 20px;
            background: #10b981; color: white; padding: 12px 20px;
            border-radius: 12px; font-weight: 500; z-index: 9999;
            animation: toastIn .3s ease forwards;
        }

        .proy-toast.error { background: #ef4444; }

        @keyframes toastIn {
            from { opacity: 0; transform: translateX(100px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* === AGrandar el contenido a toda la pantalla === */
        .max-w-7xl {
            max-width: 100% !important;
            padding: 0 !important;
        }

        .shell {
            border-radius: 0 !important;
            margin: 0 !important;
        }

        .main-panel {
            padding: 36px 48px !important;
        }

        @media (max-width: 768px) {
            .sidebar { width: 100%; min-width: 100%; border-right: none; border-bottom: 1px solid var(--gray-100); display: flex; flex-wrap: wrap; padding: 10px 0; }
            .sidebar-item { width: 50%; padding: 10px 16px; }
            .main-panel { padding: 24px; }
        }

        @media (max-width: 640px) {
            .proy-form-grid { grid-template-columns: 1fr; }
            .proy-field-full { grid-column: span 1; }
            .tec-input-wrapper { flex-direction: column; }
            .tec-btn-add { padding: 10px; justify-content: center; }
        }
    </style>

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