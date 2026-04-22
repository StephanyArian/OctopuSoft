<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h2 class="font-semibold text-xl leading-tight" style="color: var(--burg-deep);">
                {{ __('Formulario de Perfil Profesional') }}
            </h2>
            <!-- Botón cerrar sesión -->
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

        /* Fondo elegante */
        .main-content {
            background: linear-gradient(135deg, var(--off) 0%, var(--gray-100) 50%, var(--white) 100%);
            min-height: 100vh;
            padding: 30px 20px;
            position: relative;
        }

        /* Decoración sutil de fondo */
        .main-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
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
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--teal);
        }

        .nav-tab.muted {
            color: var(--gray-300);
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--teal);
            display: inline-block;
            margin: auto 0 auto 12px;
            opacity: 0.5;
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
            transition: all 0.3s ease;
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
            bottom: -8px;
            left: 0;
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
            height: 100px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(10, 191, 158, 0.1);
        }

        /* Foto de perfil - Círculo elegante */
        .photo-box {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 3px solid var(--gray-100);
            background: linear-gradient(135deg, var(--off), var(--white));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .photo-box:hover {
            border-color: var(--teal);
            transform: scale(1.03);
            box-shadow: 0 12px 35px rgba(10, 191, 158, 0.2);
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            border-radius: 50%;
        }

        .photo-label {
            font-size: 11px;
            color: var(--gray-500);
            text-align: center;
            line-height: 1.4;
            position: relative;
            z-index: 1;
            font-weight: 500;
        }

        .photo-icon {
            font-size: 42px;
            color: var(--teal);
            position: relative;
            z-index: 1;
            margin-bottom: 5px;
        }

        .btn-row {
            display: flex;
            justify-content: flex-end;
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

        .hidden {
            display: none;
        }

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
            
            .sidebar-item {
                width: 50%;
                padding: 10px 16px;
            }
            
            .main {
                padding: 24px;
            }
            
            .section-card {
                padding: 20px;
            }
            
            .photo-box {
                width: 110px;
                height: 110px;
            }
            
            .page-title {
                font-size: 24px;
            }
            
            .nav-tab {
                padding: 10px 16px;
                font-size: 11px;
            }
        }
        
        @media (max-width: 480px) {
            .photo-box {
                width: 90px;
                height: 90px;
            }
            
            .photo-icon {
                font-size: 32px;
            }
            
            .btn {
                padding: 8px 20px;
                font-size: 12px;
            }
            
            .main {
                padding: 16px;
            }
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
                        <a href="{{ route('profile.create') }}" class="sidebar-item active" id="personalLink" style="cursor: pointer; text-decoration: none;">Personal</a>
                        <a href="{{ route('experiencia.laboral') }}" class="sidebar-item" id="experienciaLink" style="cursor: pointer; text-decoration: none;">Experiencia laboral</a>
                        <a href="{{ route('informacion.academica') }}" class="sidebar-item" id="academicaLink" style="cursor: pointer; text-decoration: none;">Información académica</a>
                        <a href="{{ route('skills.tecnicas') }}" class="sidebar-item" id="habilidadesTecnicasLink" style="cursor: pointer; text-decoration: none;">Habilidades técnicas</a>
                        <a href="{{ route('skills.blandas') }}" class="sidebar-item" id="habilidadesBlandasLink" style="cursor: pointer; text-decoration: none;">Habilidades blandas</a>
                        <div class="sidebar-item" id="proyectosLink" style="cursor: pointer;">📁 Proyectos</div>
                        <a href="{{ route('redes.index') }}" class="sidebar-item" id="redesLink" style="cursor: pointer; text-decoration: none;">Redes profesionales y contacto</a>
                    </div>
                    <div class="main" id="mainContent">
                        <!-- Contenido dinámico se cargará aquí -->
                        <div class="page-title">Perfil Personal</div>
                        <div class="section-card">
                            <div class="section-subtitle">
                                Crea tu perfil con nombre, foto de perfil y biografía para presentarte profesionalmente.
                            </div>
                            
                            <form id="profileForm" action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-grid">
                                    <div class="form-row">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                            <div class="photo-box" onclick="document.getElementById('photoInput').click()">
                                                @php $user = Auth::user(); @endphp
                                                @if($user->photo_base64)
                                                    <img id="photoPreview" src="{{ $user->photo_base64 }}" style="width: 100%; height: 100%; object-fit: cover; position: absolute; border-radius: 50%; display: block;">
                                                    <div class="photo-icon" id="photoIcon" style="display: none;">📷</div>
                                                    <div class="photo-label" id="photoLabel" style="display: none;">Subir foto<br>de perfil</div>
                                                @else
                                                    <div class="photo-icon" id="photoIcon">📷</div>
                                                    <div class="photo-label" id="photoLabel">Subir foto<br>de perfil</div>
                                                    <img id="photoPreview" src="#" alt="Preview" style="display: none;">
                                                @endif
                                            </div>
                                            
                                            <!-- Íconos pequeños debajo de la foto -->
                                            <div style="display: flex; gap: 8px; margin-top: 8px; justify-content: center;">
                                                <!-- Ícono para cambiar foto -->
                                                <button type="button" onclick="document.getElementById('photoInput').click()" style="background: var(--teal); border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; color: white; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill-add" viewBox="0 0 16 16">
  <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
  <path d="M2 13c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
</svg>
                                                </button>
                                                
                                                <!-- Ícono para eliminar foto (solo si existe foto) -->
                                                @if($user->photo_base64)
                                                    <button type="button" onclick="if(confirm('¿Eliminar foto?')) document.getElementById('deletePhotoForm').submit();" style="background: rgb(255, 8, 8); border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; color: white; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
  <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
</svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div style="flex:1;display:flex;flex-direction:column;gap:16px">
                                            <div class="form-group">
                                                <label class="form-label">Nombre completo <span class="required">*</span></label>
                                                <input class="form-input" type="text" id="name" name="name" placeholder="Ej. Juan Pérez García" value="{{ old('name', $user->first_name . ' ' . $user->last_name) }}">
                                                <div id="nameError" class="error-message hidden">El nombre es obligatorio</div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Título profesional</label>
                                                <input class="form-input" type="text" id="title" name="title" placeholder="Ej. Desarrollador de Software" value="{{ old('title', $user->profession ? $user->profession->name : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Ubicación</label>
                                        <input class="form-input" type="text" id="location" name="location" placeholder="Ciudad, País" value="{{ old('location', $user->city . ($user->country ? ', ' . $user->country : '')) }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Biografía profesional</label>
                                        <textarea class="form-textarea" id="bio" name="bio" placeholder="Escribe una breve presentación sobre ti, tu experiencia y lo que te apasiona profesionalmente...">{{ old('bio', $user->biography) }}</textarea>
                                        <div id="bioError" class="error-message hidden">La biografía no puede exceder los 500 caracteres</div>
                                        <span class="text-xs text-gray-400 mt-1">Máximo 500 caracteres</span>
                                    </div>
                                </div>
                                
                                <input type="file" id="photoInput" name="photo" accept="image/jpeg,image/png" style="display: none;" onchange="previewPhoto(event)">
                                <div id="photoError" class="error-message hidden" style="margin-top: 12px;"></div>
                                
                                <div class="btn-row">
                                    <button type="button" class="btn" onclick="window.history.back()">Cancelar</button>
                                    <button type="submit" class="btn primary">Guardar y continuar →</button>
                                </div>
                            </form>
                            <form id="deletePhotoForm" method="POST" action="{{ url('/profile/photo') }}" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewPhoto(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('photoPreview');
            const photoIcon = document.getElementById('photoIcon');
            const photoLabel = document.getElementById('photoLabel');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (photoIcon) photoIcon.style.display = 'none';
                    if (photoLabel) photoLabel.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('profileForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            const name = document.getElementById('name').value.trim();
            const nameError = document.getElementById('nameError');
            if (name === '') {
                nameError.classList.remove('hidden');
                isValid = false;
            } else {
                nameError.classList.add('hidden');
            }
            
            const bio = document.getElementById('bio').value;
            const bioError = document.getElementById('bioError');
            if (bio.length > 500) {
                bioError.classList.remove('hidden');
                isValid = false;
            } else {
                bioError.classList.add('hidden');
            }
            
            const photo = document.getElementById('photoInput').files[0];
            const photoError = document.getElementById('photoError');
            if (photo) {
                const validTypes = ['image/jpeg', 'image/png'];
                if (!validTypes.includes(photo.type)) {
                    photoError.classList.remove('hidden');
                    photoError.textContent = 'Formato no permitido. Use JPG o PNG';
                    isValid = false;
                } else if (photo.size > 2 * 1024 * 1024) {
                    photoError.classList.remove('hidden');
                    photoError.textContent = 'La imagen no puede superar los 2MB';
                    isValid = false;
                } else {
                    photoError.classList.add('hidden');
                }
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });

        // Función para cargar proyectos dinámicamente
        function cargarProyectos() {
            fetch('{{ url("/proyectos-content") }}')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('mainContent').innerHTML = html;
                    // Re-ejecutar scripts de la vista cargada
                    const scripts = document.querySelectorAll('#mainContent script');
                    scripts.forEach(script => {
                        const nuevoScript = document.createElement('script');
                        if (script.src) {
                            nuevoScript.src = script.src;
                        } else {
                            nuevoScript.textContent = script.textContent;
                        }
                        document.body.appendChild(nuevoScript);
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        // Función para cargar perfil personal (vista por defecto)
        function cargarPerfilPersonal() {
            // El perfil personal ya está cargado por defecto
            // Esta función se puede usar para recargar si es necesario
            location.reload();
        }

        // Evento click en Proyectos
        document.getElementById('proyectosLink')?.addEventListener('click', function(e) {
            e.preventDefault();
            cargarProyectos();
            
            // Actualizar clase activa en el sidebar
            document.querySelectorAll('.sidebar-item').forEach(item => {
                item.classList.remove('active');
            });
            this.classList.add('active');
        });

        // Evento click en Personal (recargar página)
        document.getElementById('personalLink')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '{{ url("/dashboard") }}';
        });
    </script>
</x-app-layout>