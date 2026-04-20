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

    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
   

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
                        <div class="sidebar-item active">Personal</div>
                        <div class="sidebar-item">Experiencia laboral</div>
                        <a href="{{ route('informacion.academica') }}" class="sidebar-item">Información académica</a>
                        <a href="{{ route('skills.tecnicas') }}" class="sidebar-item">Habilidades técnicas</a>
                        <a href="{{ route('skills.blandas') }}" class="sidebar-item">Habilidades blandas</a>
                        <div class="sidebar-item">Proyectos</div>
                        <div class="sidebar-item">Redes profesionales y contacto</div>
                    </div>
                    <div class="main">
                        <div class="page-title">Perfil Personal</div>
                        <div class="section-card">
                            <div class="section-subtitle">
                                Crea tu perfil con nombre, foto de perfil y biografía para presentarte profesionalmente.
                            </div>
                            
                            <form id="profileForm" action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-grid">
                                    <div class="form-row">
                                        <div class="photo-box" onclick="document.getElementById('photoInput').click()">
                                            <div class="photo-icon" id="photoIcon">📷</div>
                                            <div class="photo-label" id="photoLabel">Subir foto<br>de perfil</div>
                                            <img id="photoPreview" src="#" alt="Preview" style="display: none;">
                                        </div>
                                        <div style="flex:1;display:flex;flex-direction:column;gap:16px">
                                            <div class="form-group">
                                                <label class="form-label">Nombre completo <span class="required">*</span></label>
                                                <input class="form-input" type="text" id="name" name="name" placeholder="Ej. Juan Pérez García" value="{{ old('name') }}">
                                                <div id="nameError" class="error-message hidden">El nombre es obligatorio</div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Título profesional</label>
                                                <input class="form-input" type="text" id="title" name="title" placeholder="Ej. Desarrollador de Software" value="{{ old('title') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Ubicación</label>
                                        <input class="form-input" type="text" id="location" name="location" placeholder="Ciudad, País" value="{{ old('location') }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Biografía profesional</label>
                                        <textarea class="form-textarea" id="bio" name="bio" placeholder="Escribe una breve presentación sobre ti, tu experiencia y lo que te apasiona profesionalmente...">{{ old('bio') }}</textarea>
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
                    photoIcon.style.display = 'none';
                    photoLabel.style.display = 'none';
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
    </script>
</x-app-layout>