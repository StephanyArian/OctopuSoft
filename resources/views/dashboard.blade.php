<x-app-layout>
    

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skills.css') }}">


    

    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="shell">
                
                <div class="navbar">
                    <div class="nav-tab active">COMPLETAR</div>
                    <a href="{{ url('/preview') }}" class="nav-tab muted" style="text-decoration: none; display: inline-block; color: inherit;">VER PERFIL</a>
                </div>
                <div class="body-row">
                    <div class="sidebar">
                     <a href="{{ route('profile.create') }}" class="sidebar-item active" id="personalLink" style="cursor: pointer; text-decoration: none;">Personal</a>
<a href="{{ route('experiencia.laboral') }}" class="sidebar-item" id="experienciaLink" style="cursor: pointer; text-decoration: none;">Experiencia laboral</a>
<a href="{{ route('informacion.academica') }}" class="sidebar-item" id="academicaLink" style="cursor: pointer; text-decoration: none;">Información académica</a>
<a href="{{ route('skills.tecnicas') }}" class="sidebar-item" id="habilidadesTecnicasLink" style="cursor: pointer; text-decoration: none;">Habilidades técnicas</a>
<a href="{{ route('skills.blandas') }}" class="sidebar-item" id="habilidadesBlandasLink" style="cursor: pointer; text-decoration: none;">Habilidades blandas</a>
<a href="{{ route('proyectos') }}" class="sidebar-item" style="text-decoration:none;">Proyectos</a>
<a href="{{ route('idiomas.index') }}" class="sidebar-item" id="idiomasLink" style="cursor: pointer; text-decoration: none;">Idiomas</a>
<a href="{{ route('redes.index') }}" class="sidebar-item" id="redesLink" style="cursor: pointer; text-decoration: none;">Redes profesionales y contacto</a>
                     
                    </div>
                    <div class="main" id="mainContent">
                        <!-- Contenido dinámico se cargará aquí -->
                        <div class="page-title">Perfil Personal</div>
                        <div class="section-card">
                            <div class="section-subtitle">
                                Crea tu perfil con nombre, foto de perfil y biografía para presentarte profesionalmente.
                            </div>
                            
                            @if(session('success'))
                                <div class="alert-skill success">
                                    <div class="alert-skill-icon">✓</div>
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert-skill error">
                                    <div class="alert-skill-icon">!</div>
                                    <span>Por favor, verifica que todos los campos estén correctamente llenados.</span>
                                </div>
                            @endif
                            
                            <form id="profileForm" action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-grid">
                                    <div class="form-row">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                            <div class="photo-box">
                                                @php $user = Auth::user(); @endphp
                                                @if($user->photo_base64)
                                                    <a id="photoLink" href="#" data-src="{{ $user->photo_base64 }}" onclick="openPhotoPreview(this.dataset.src); return false;" style="width: 100%; height: 100%; display: block; position: absolute; z-index: 10; border-radius: 50%;">
                                                        <img id="photoPreview" src="{{ $user->photo_base64 }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block;">
                                                    </a>
                                                    <div class="photo-icon" id="photoIcon" style="display: none;">📷</div>
                                                    <div class="photo-label" id="photoLabel" style="display: none;">Subir foto<br>de perfil</div>
                                                @else
                                                    <div onclick="document.getElementById('photoInput').click()" style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer;">
                                                        <div class="photo-icon" id="photoIcon">📷</div>
                                                        <div class="photo-label" id="photoLabel">Subir foto<br>de perfil</div>
                                                    </div>
                                                    <a id="photoLink" href="#" data-src="" onclick="openPhotoPreview(this.dataset.src); return false;" style="display: none; width: 100%; height: 100%; position: absolute; z-index: 10; border-radius: 50%;">
                                                        <img id="photoPreview" src="#" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block;">
                                                    </a>
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
                                                    <button type="button" onclick="openDeleteModal()" title="Eliminar foto" style="background: rgb(255, 8, 8); border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; color: white; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16" aria-hidden="true">
                                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div style="flex:1;display:flex;flex-direction:column;gap:16px">
                                            <div class="form-group">
                                                <label class="form-label">Nombre completo <span class="required">*</span></label>
                                                <input class="form-input" type="text" id="name" name="name" placeholder="Ej. Juan Pérez García" value="{{ old('name', $user->first_name . ' ' . $user->last_name) }}" maxlength="30" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                                                <div id="nameError" class="error-message hidden">El nombre es obligatorio</div>
                                                <span class="text-xs text-gray-400 mt-1">Máximo 30 caracteres - Solo letras</span>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Título profesional</label>
                                                <input class="form-input" type="text" id="title" name="title" placeholder="Ej. Desarrollador de Software" value="{{ old('title', $user->profession ? $user->profession->name : '') }}" maxlength="30" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                                                <span class="text-xs text-gray-400 mt-1">Máximo 30 caracteres - Solo letras</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Ubicación</label>
                                        <input class="form-input" type="text" id="location" name="location" placeholder="Ciudad, País" value="{{ old('location', $user->city . ($user->country ? ', ' . $user->country : '')) }}" maxlength="30">
                                        <span class="text-xs text-gray-400 mt-1">Máximo 30 caracteres</span>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Biografía profesional</label>
                                        <textarea class="form-textarea" id="bio" name="bio" placeholder="Escribe una breve presentación sobre ti, tu experiencia y lo que te apasiona profesionalmente..." maxlength="500">{{ old('bio', $user->biography) }}</textarea>
                                        <div id="bioError" class="error-message hidden">La biografía no puede exceder los 500 caracteres</div>
                                        <span class="text-xs text-gray-400 mt-1">Máximo 500 caracteres</span>
                                    </div>
                                </div>
                                
                                <input type="file" id="photoInput" name="photo" accept="image/jpeg,image/png" style="display: none;" onchange="previewPhoto(event)">
                                <div id="photoError" class="error-message hidden" style="margin-top: 12px;"></div>
                                
                                <div class="btn-row">
                                    <button type="button" class="btn" onclick="window.history.back()">Cancelar</button>
                                    <button type="submit" class="btn primary">Guardar</button>
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

    {{-- Modal eliminar foto --}}
    <div class="modal-backdrop" id="delete-modal">
        <div class="modal-box">
            <h3>Eliminar foto de perfil</h3>
            <p>¿Estás seguro de que deseas eliminar tu foto de perfil?<br>Esta acción no se puede deshacer.</p>
            <form id="deletePhotoFormModal" method="POST" action="{{ url('/profile/photo') }}">
                @csrf
                @method('DELETE')
                <div style="display:flex;gap:12px;justify-content:center;">
                    <button type="submit" class="btn-danger" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border: none; padding: 10px 28px; border-radius: 40px; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3); transition: all 0.3s;">Sí, eliminar</button>
                    <button type="button" class="btn" onclick="closeDeleteModal()" style="padding: 10px 28px; border-radius: 40px; font-weight: 700; font-size: 13px; cursor: pointer; background: transparent; border: 1.5px solid var(--gray-100); color: var(--gray-700);">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Lightbox para ver la foto --}}
    <div class="modal-backdrop" id="photo-lightbox" onclick="this.classList.remove('active')" style="cursor: pointer; z-index: 1000;">
        <div style="max-width: 90%; max-height: 90%; position: relative;">
            <button type="button" style="position: absolute; top: -15px; right: -15px; background: white; border: none; border-radius: 50%; width: 30px; height: 30px; font-size: 20px; font-weight: bold; cursor: pointer; color: #50081e; box-shadow: 0 2px 10px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; z-index: 10;">&times;</button>
            <img id="lightbox-img" src="" style="max-width: 100%; max-height: 90vh; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); object-fit: contain; position: relative;">
        </div>
    </div>

    <script src="{{ asset('js/dashboard.js') }}"></script>
   
</x-app-layout>