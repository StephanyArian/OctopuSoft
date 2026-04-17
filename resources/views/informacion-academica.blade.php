<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h2 class="font-semibold text-xl leading-tight" style="color: var(--burg-deep);">
                {{ __('Formulario de Perfil Profesional') }}
            </h2>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="logout-btn" style="background: linear-gradient(90deg, var(--burg-deep), var(--teal)); color: white; padding: 8px 20px; border-radius: 40px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; border: none; cursor: pointer;">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </x-slot>
 
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
 
        :root {
            --burg-deep: #2d0a1e;
            --burg-mid: #4a1030;
            --burg-soft: #6b1f45;
            --teal: #0abf9e;
            --teal-light: #1de8c0;
            --teal-dim: #07866e;
            --white: #ffffff;
            --off: #f5f6f8;
            --gray-100: #edf0f4;
            --gray-300: #c8cdd8;
            --gray-500: #7a8298;
            --gray-700: #3d4459;
            --dark: #111827;
        }
 
        .main-content {
            background: linear-gradient(135deg, var(--off) 0%, var(--gray-100) 50%, var(--white) 100%);
            min-height: 100vh;
            padding: 30px 20px;
            position: relative;
        }
 
        .main-content::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at 10% 20%, rgba(10, 191, 158, 0.03) 0%, transparent 50%);
            pointer-events: none;
        }
 
        .shell {
            display: flex;
            flex-direction: column;
            border: none;
            min-height: 600px;
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 1;
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
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            letter-spacing: .5px;
            transition: all 0.3s ease;
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
            background: rgba(255, 255, 255, 0.05);
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
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            text-decoration: none;
        }
 
        .sidebar-item:hover {
            background: var(--off);
            color: var(--teal);
        }
 
        .sidebar-item.active {
            background: linear-gradient(90deg, rgba(10, 191, 158, 0.08) 0%, transparent 100%);
            font-weight: 700;
            color: var(--teal);
            border-left: 3px solid var(--teal);
        }
 
        .main {
            flex: 1;
            padding: 32px 40px;
            background: var(--white);
        }
 
        .page-title {
            font-size: 28px;
            color: var(--burg-deep);
            font-weight: 800;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
            position: relative;
            display: inline-block;
        }
 
        .page-title::after {
            content: '';
            position: absolute;
            bottom: -8px; left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--teal), var(--teal-light));
            border-radius: 3px;
        }
 
        .section-card {
            border: 1px solid var(--gray-100);
            border-radius: 16px;
            padding: 28px 30px;
            background: var(--white);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
            margin-bottom: 28px;
        }
 
        .section-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
        }
 
        .section-subtitle {
            font-size: 14px;
            color: var(--gray-500);
            margin-bottom: 24px;
            line-height: 1.6;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--gray-100);
        }
 
        .form-grid {
            display: grid;
            gap: 20px;
        }
 
        .form-row {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }
 
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }
 
        .form-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--gray-700);
            text-transform: uppercase;
            letter-spacing: .8px;
        }
 
        .form-label .required {
            color: var(--teal);
            margin-left: 2px;
        }
 
        .form-input {
            border: 1.5px solid var(--gray-100);
            padding: 10px 14px;
            font-size: 14px;
            background: var(--white);
            border-radius: 12px;
            width: 100%;
            transition: all 0.3s ease;
            font-family: inherit;
        }
 
        .form-input:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(10, 191, 158, 0.1);
        }
 
        .form-textarea {
            border: 1.5px solid var(--gray-100);
            padding: 10px 14px;
            font-size: 14px;
            background: var(--white);
            border-radius: 12px;
            width: 100%;
            resize: vertical;
            height: 110px;
            transition: all 0.3s ease;
            font-family: inherit;
        }
 
        .form-textarea:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(10, 191, 158, 0.1);
        }
 
        .form-checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
        }
 
        .form-checkbox-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--teal);
            cursor: pointer;
        }
 
        .form-checkbox-row label {
            font-size: 13px;
            color: var(--gray-700);
            cursor: pointer;
            font-weight: 500;
        }
 
        .btn-row {
            display: flex;
            justify-content: flex-start;
            gap: 12px;
            margin-top: 28px;
            flex-wrap: wrap;
        }
 
        .btn {
            padding: 10px 28px;
            font-size: 13px;
            border: 1.5px solid var(--gray-100);
            background: var(--white);
            cursor: pointer;
            border-radius: 40px;
            font-weight: 600;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }
 
        .btn:hover {
            background: var(--off);
            transform: translateY(-1px);
        }
 
        .btn.primary {
            background: linear-gradient(135deg, var(--teal), var(--teal-dim));
            border-color: transparent;
            font-weight: 700;
            color: var(--white);
            box-shadow: 0 4px 12px rgba(10, 191, 158, 0.3);
        }
 
        .btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(10, 191, 158, 0.4);
            background: linear-gradient(135deg, var(--teal-light), var(--teal));
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
 
        .error-message {
            color: #e74c3c;
            font-size: 11px;
            margin-top: 5px;
            font-weight: 500;
        }
 
        .hidden { display: none; }
 
        .success-message {
            background: linear-gradient(135deg, var(--teal), var(--teal-dim));
            color: white;
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(10, 191, 158, 0.2);
        }
 
        /* ── Historial ── */
        .historial-divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }
 
        .historial-divider span {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            color: var(--gray-500);
            text-transform: uppercase;
            white-space: nowrap;
        }
 
        .historial-divider::before,
        .historial-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--gray-100);
        }
 
        .historial-card {
            border: 1px solid var(--gray-100);
            border-radius: 14px;
            padding: 18px 22px;
            background: var(--white);
            display: flex;
            flex-direction: column;
            gap: 8px;
            transition: all 0.3s ease;
            position: relative;
        }
 
        .historial-card:hover {
            box-shadow: 0 6px 24px rgba(0,0,0,0.06);
            border-color: var(--gray-300);
        }
 
        .historial-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
 
        .historial-index {
            font-size: 11px;
            font-weight: 700;
            color: var(--gray-500);
            background: var(--gray-100);
            border-radius: 6px;
            padding: 2px 8px;
        }
 
        .badge-actual {
            font-size: 11px;
            font-weight: 700;
            color: var(--teal-dim);
            background: rgba(10, 191, 158, 0.1);
            border-radius: 20px;
            padding: 3px 12px;
            letter-spacing: 0.3px;
        }
 
        .historial-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--burg-deep);
        }
 
        .historial-subtitle {
            font-size: 13px;
            color: var(--gray-500);
        }
 
        .historial-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }
 
        .tag {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
        }
 
        .tag-teal {
            background: rgba(10, 191, 158, 0.12);
            color: var(--teal-dim);
        }
 
        .tag-gray {
            background: var(--gray-100);
            color: var(--gray-500);
        }
 
        .historial-desc {
            font-size: 12px;
            color: var(--gray-500);
            font-weight: 500;
        }
 
        .historial-actions {
            display: flex;
            gap: 10px;
            margin-top: 4px;
        }
 
        .btn-sm {
            padding: 6px 16px;
            font-size: 12px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1.5px solid var(--gray-100);
            background: var(--white);
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 5px;
        }
 
        .btn-sm:hover {
            background: var(--off);
            border-color: var(--gray-300);
        }
 
        .btn-sm.danger {
            color: #e74c3c;
            border-color: rgba(231, 76, 60, 0.2);
        }
 
        .btn-sm.danger:hover {
            background: rgba(231, 76, 60, 0.05);
        }
 
        .logout-btn:hover {
            transform: translateY(-2px);
            opacity: 0.95;
            box-shadow: 0 4px 12px rgba(10, 191, 158, 0.3);
        }
 
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                min-width: 100%;
                border-right: none;
                border-bottom: 1px solid var(--gray-100);
                padding: 10px 0;
                display: flex;
                flex-wrap: wrap;
            }
 
            .sidebar-item { width: 50%; padding: 10px 16px; }
            .main { padding: 24px; }
            .section-card { padding: 20px; }
            .page-title { font-size: 24px; }
            .nav-tab { padding: 10px 16px; font-size: 11px; }
        }
 
        @media (max-width: 480px) {
            .btn { padding: 8px 20px; font-size: 12px; }
            .main { padding: 16px; }
        }
    </style>
 
    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
 
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif
 
            <div class="shell">
                <div class="header-bar">BIENVENIDO</div>
                <div class="navbar">
                    <div class="nav-tab active">COMPLETAR</div>
                    <div class="nav-tab muted">VER PERFIL</div>
                </div>
                <div class="body-row">
                    <div class="sidebar">
                        <a href="{{ route('profile.create') }}" class="sidebar-item">Personal</a>
                        <div class="sidebar-item">Experiencia laboral</div>
                        <a href="{{ route('informacion.academica') }}" class="sidebar-item active">Información académica</a>
                        <div class="sidebar-item">Habilidades técnicas</div>
                        <div class="sidebar-item">Habilidades blandas</div>
                        <div class="sidebar-item">Proyectos</div>
                        <div class="sidebar-item">Redes profesionales y contacto</div>
                    </div>
 
                    <div class="main">
                        <div class="page-title">Información académica</div>
 
                        <!-- Formulario -->
                        <div class="section-card">
                            <div class="section-subtitle">
                                Registra tus estudios con institución, título, período y descripción para enriquecer tu perfil.
                            </div>
 
                            <form id="academicForm" action="#"  method="POST">
                                @csrf
                                <div class="form-grid">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Institución <span class="required">*</span></label>
                                            <input class="form-input" type="text" name="institucion" id="institucion"
                                                placeholder="Ej. Universidad Mayor de San Simón" value="{{ old('institucion') }}">
                                            <div id="institucionError" class="error-message hidden">La institución es obligatoria</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Título obtenido <span class="required">*</span></label>
                                            <input class="form-input" type="text" name="titulo_obtenido" id="tituloObtenido"
                                                placeholder="Ej. Ingeniería de Sistemas" value="{{ old('titulo_obtenido') }}">
                                            <div id="tituloObtenidoError" class="error-message hidden">El título obtenido es obligatorio</div>
                                        </div>
                                    </div>
 
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Título</label>
                                            <input class="form-input" type="text" name="titulo" id="titulo"
                                                placeholder="Licenciatura, Maestría..." value="{{ old('titulo') }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Especialidad</label>
                                            <input class="form-input" type="text" name="especialidad" id="especialidad"
                                                placeholder="Campo de estudio" value="{{ old('especialidad') }}">
                                        </div>
                                    </div>
 
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Fecha de inicio <span class="required">*</span></label>
                                            <input class="form-input" type="month" name="fecha_inicio" id="fechaInicio"
                                                value="{{ old('fecha_inicio') }}">
                                            <div id="fechaInicioError" class="error-message hidden">La fecha de inicio es obligatoria</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Fecha de fin</label>
                                            <input class="form-input" type="month" name="fecha_fin" id="fechaFin"
                                                value="{{ old('fecha_fin') }}">
                                        </div>
                                    </div>
 
                                    <div class="form-checkbox-row">
                                        <input type="checkbox" name="estudio_actual" id="estudioActual" value="1"
                                            {{ old('estudio_actual') ? 'checked' : '' }}
                                            onchange="toggleFechaFin(this)">
                                        <label for="estudioActual">Estudio actual</label>
                                    </div>
 
                                    <div class="form-group">
                                        <label class="form-label">Descripción</label>
                                        <textarea class="form-textarea" name="descripcion" id="descripcion"
                                            placeholder="Describe brevemente tus logros, materias destacadas o proyectos en esta formación...">{{ old('descripcion') }}</textarea>
                                    </div>
                                </div>
 
                                <div class="btn-row">
                                    <button type="submit" class="btn primary">Guardar formación</button>
                                    <button type="button" class="btn" onclick="resetForm()">Cancelar</button>
                                </div>
                            </form>
                        </div>
 
                        <!-- Historial estático (reemplazar con BD luego) -->
<div class="historial-divider">
    <span>— HISTORIAL —</span>
</div>

<div style="display: flex; flex-direction: column; gap: 14px;">
    <div class="historial-card">
        <div class="historial-card-header">
            <span class="historial-index">01</span>
            <span class="badge-actual">Actual</span>
        </div>
        <div class="historial-title">Informática</div>
        <div class="historial-subtitle">Ingeniería en Software · Instituto Keral</div>
        <div class="historial-tags">
            <span class="tag tag-teal">Feb 2025 – Presente</span>
            <span class="tag tag-gray">En curso</span>
        </div>
        <div class="historial-desc">Certificado</div>
        <div class="historial-actions">
            <button class="btn-sm">  Editar</button>
            <button class="btn-sm danger"> Eliminar</button>
        </div>
    </div>
</div>


 
                    </div>{{-- /main --}}
                </div>{{-- /body-row --}}
            </div>{{-- /shell --}}
        </div>
    </div>
 
    <script>
        function toggleFechaFin(checkbox) {
            const fechaFin = document.getElementById('fechaFin');
            fechaFin.disabled = checkbox.checked;
            if (checkbox.checked) {
                fechaFin.value = '';
            }
        }
 
        function resetForm() {
            document.getElementById('academicForm').reset();
            document.getElementById('fechaFin').disabled = false;
        }
 
        document.getElementById('academicForm').addEventListener('submit', function(e) {
            let isValid = true;
 
            const institucion = document.getElementById('institucion').value.trim();
            const institucionError = document.getElementById('institucionError');
            if (!institucion) {
                institucionError.classList.remove('hidden');
                isValid = false;
            } else {
                institucionError.classList.add('hidden');
            }
 
            const tituloObtenido = document.getElementById('tituloObtenido').value.trim();
            const tituloObtenidoError = document.getElementById('tituloObtenidoError');
            if (!tituloObtenido) {
                tituloObtenidoError.classList.remove('hidden');
                isValid = false;
            } else {
                tituloObtenidoError.classList.add('hidden');
            }
 
            const fechaInicio = document.getElementById('fechaInicio').value;
            const fechaInicioError = document.getElementById('fechaInicioError');
            if (!fechaInicio) {
                fechaInicioError.classList.remove('hidden');
                isValid = false;
            } else {
                fechaInicioError.classList.add('hidden');
            }
 
            if (!isValid) e.preventDefault();
        });
    </script>
</x-app-layout>