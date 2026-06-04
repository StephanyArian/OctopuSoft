<style>
    .nav-hamburger { display: none; }
    .nav-mobile-menu { display: none; }
    @media (max-width: 768px) {
        .nav-hamburger { display: flex !important; align-items: center; }
    }
</style>

<nav style="background-color: #2d0a1e; border-bottom: 1px solid #4a1030; position: sticky; top: 0; z-index: 1000;">
    <div style="max-width:1280px; margin:0 auto; padding:0 1.5rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; height:72px;">

            {{-- LOGO --}}
            <div style="flex-shrink:0; display:flex; align-items:center;">
                <span style="font-weight:800;font-size:1.5rem;font-family:'Figtree',sans-serif;background:linear-gradient(135deg,#0abf9e,#00ff88);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">DevFolio</span>
            </div>

            {{-- DERECHA: casita + avatar + hamburger --}}
            <div style="display:flex; align-items:center; gap:12px;">

                {{-- CASITA --}}
                <a href="{{ url('/') }}" style="display:flex;flex-direction:column;align-items:center;gap:4px;text-decoration:none;">
                    <div style="width:44px;height:44px;background:linear-gradient(135deg,#0abf9e,#1de8c0,#00ff88);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 0 14px rgba(10,191,158,0.7),0 0 28px rgba(0,255,136,0.3);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 16 16">
                            <path fill="#2d0a1e" d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                            <path fill="#2d0a1e" d="M13 7.293l-5-5-5 5V14a1 1 0 0 0 1 1h3v-3h2v3h3a1 1 0 0 0 1-1z"/>
                        </svg>
                    </div>
                    <span style="font-size:0.58rem;font-weight:800;background:linear-gradient(135deg,#0abf9e,#00ff88);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:0.5px;">Inicio</span>
                </a>

                {{-- AVATAR + DROPDOWN --}}
                <div id="userDropdown" style="position:relative; display:flex; align-items:center;">
                    <button onclick="toggleUserMenu()" style="background:transparent;border:none;padding:0;cursor:pointer;display:flex;align-items:center;gap:6px;">
                        <div style="position:relative;">
                            @if(Auth::user()->photo_base64)
                                <img src="{{ Auth::user()->photo_base64 }}"
                                     style="width:46px;height:46px;border-radius:50%;object-fit:cover;border:2.5px solid #0abf9e;box-shadow:0 0 12px rgba(10,191,158,0.6),0 0 24px rgba(0,255,136,0.25);">
                            @else
                                <div style="width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,#0abf9e,#1de8c0,#00ff88);display:flex;align-items:center;justify-content:center;border:2.5px solid #0abf9e;box-shadow:0 0 12px rgba(10,191,158,0.6);font-weight:800;font-size:1.1rem;color:#2d0a1e;">
                                    {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <div style="position:absolute;bottom:1px;right:1px;width:12px;height:12px;background:#00ff88;border-radius:50%;border:2px solid #2d0a1e;"></div>
                        </div>
                        <i class="bi bi-chevron-down" id="dropdownChevron" style="color:#0abf9e;font-size:12px;transition:transform 0.2s;"></i>
                    </button>

                

                {{-- DROPDOWN MENU --}}
                <div id="userMenu" style="display:none;position:absolute;top:calc(100% + 12px);right:0;background:white;border-radius:16px;border:2px solid #a855f7;box-shadow:0 0 20px rgba(168,85,247,0.25);min-width:200px;padding:8px;flex-direction:column;gap:6px;z-index:9999;">

                    <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:12px;padding:10px 12px;text-decoration:none;color:#2d0a1e;font-size:14px;font-weight:500;border-radius:10px;border:1.5px solid transparent;background:linear-gradient(white,white) padding-box,linear-gradient(135deg,#a855f7,#7c3aed) border-box;box-shadow:0 2px 8px rgba(168,85,247,0.15);">
                        <div style="width:30px;height:30px;border-radius:7px;background:linear-gradient(135deg,#0abf9e,#1de8c0);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-display-fill" style="color:#2d0a1e;"></i>
                        </div>
                        Mi espacio
                    </a>

                    <a href="{{ route('preview') }}" style="display:flex;align-items:center;gap:12px;padding:10px 12px;text-decoration:none;color:#2d0a1e;font-size:14px;font-weight:500;border-radius:10px;border:1.5px solid transparent;background:linear-gradient(white,white) padding-box,linear-gradient(135deg,#a855f7,#7c3aed) border-box;box-shadow:0 2px 8px rgba(168,85,247,0.15);">
                        <div style="width:30px;height:30px;border-radius:7px;background:linear-gradient(135deg,#0abf9e,#1de8c0);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-eye-fill" style="color:#2d0a1e;"></i>
                        </div>
                        Ver perfil
                    </a>

                    <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:12px;padding:10px 12px;text-decoration:none;color:#2d0a1e;font-size:14px;font-weight:500;border-radius:10px;border:1.5px solid transparent;background:linear-gradient(white,white) padding-box,linear-gradient(135deg,#a855f7,#7c3aed) border-box;box-shadow:0 2px 8px rgba(168,85,247,0.15);">
                        <div style="width:30px;height:30px;border-radius:7px;background:linear-gradient(135deg,#0abf9e,#1de8c0);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-gear-fill" style="color:#2d0a1e;"></i>
                        </div>
                        Configuración
                    </a>

                    <a href="{{ route('cerrar.sesion') }}" style="display:flex;align-items:center;gap:12px;padding:10px 12px;text-decoration:none;color:#e53e3e;font-size:14px;font-weight:500;border-radius:10px;border:1.5px solid transparent;background:linear-gradient(white,white) padding-box,linear-gradient(135deg,#f87171,#dc2626) border-box;box-shadow:0 2px 8px rgba(229,62,62,0.15);">
                        <div style="width:30px;height:30px;border-radius:7px;background:linear-gradient(135deg,#f87171,#dc2626);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-box-arrow-right" style="color:white;"></i>
                        </div>
                        Cerrar sesión
                    </a>

                    
                </div>

                {{-- HAMBURGER: solo en móvil --}}
                <button class="nav-hamburger" onclick="toggleMobileMenu()"
                        style="color:#0abf9e;background:transparent;border:none;cursor:pointer;padding:4px;"
                        aria-label="Abrir menú">
                    <i id="hamburgerIcon" class="bi bi-list" style="font-size:28px;"></i>
                </button>

            </div>
        </div>
    </div>

    {{-- MENÚ MÓVIL --}}
    <div id="mobileMenu" class="nav-mobile-menu"
         style="position:fixed;top:72px;left:0;right:0;background-color:#2d0a1e;z-index:49;box-shadow:0 8px 32px rgba(0,0,0,0.4);border-bottom:2px solid #0abf9e;max-height:calc(100vh - 72px);overflow-y:auto;">
        <div style="padding:12px 16px; display:flex; flex-direction:column; gap:4px;">

            <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;text-decoration:none;color:#ffffff;font-size:15px;font-weight:600;border-bottom:1px solid rgba(255,255,255,0.1);">
                <i class="bi bi-display-fill" style="color:#0abf9e;font-size:18px;"></i> Mi espacio
            </a>
            <a href="{{ route('profile.create') }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;text-decoration:none;color:#1de8c0;font-size:14px;border-radius:10px;">
                <i class="bi bi-person-fill" style="font-size:16px;"></i> Personal
            </a>
            <a href="{{ route('experiencia.laboral') }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;text-decoration:none;color:#1de8c0;font-size:14px;border-radius:10px;">
                <i class="bi bi-briefcase-fill" style="font-size:16px;"></i> Experiencia laboral
            </a>
            <a href="{{ route('informacion.academica') }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;text-decoration:none;color:#1de8c0;font-size:14px;border-radius:10px;">
                <i class="bi bi-mortarboard-fill" style="font-size:16px;"></i> Información académica
            </a>
            <a href="{{ route('skills.tecnicas') }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;text-decoration:none;color:#1de8c0;font-size:14px;border-radius:10px;">
                <i class="bi bi-cpu-fill" style="font-size:16px;"></i> Habilidades técnicas
            </a>
            <a href="{{ route('skills.blandas') }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;text-decoration:none;color:#1de8c0;font-size:14px;border-radius:10px;">
                <i class="bi bi-heart-fill" style="font-size:16px;"></i> Habilidades blandas
            </a>
            <a href="{{ route('proyectos') }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;text-decoration:none;color:#1de8c0;font-size:14px;border-radius:10px;">
                <i class="bi bi-kanban-fill" style="font-size:16px;"></i> Proyectos
            </a>
            <a href="{{ route('idiomas.index') }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;text-decoration:none;color:#1de8c0;font-size:14px;border-radius:10px;">
                <i class="bi bi-translate" style="font-size:16px;"></i> Idiomas
            </a>
            <a href="{{ route('redes.index') }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;text-decoration:none;color:#1de8c0;font-size:14px;border-radius:10px;">
                <i class="bi bi-globe" style="font-size:16px;"></i> Redes profesionales y contacto
            </a>

        </div>
    </div>

    {{-- FONDO OSCURO MÓVIL --}}
    <div id="mobileOverlay"
         onclick="toggleMobileMenu()"
         style="display:none;position:fixed;inset:0;top:72px;background:rgba(0,0,0,0.4);z-index:48;">
    </div>

    <script>
        // MENÚ MÓVIL
        function toggleMobileMenu() {
            const menu    = document.getElementById('mobileMenu');
            const overlay = document.getElementById('mobileOverlay');
            const icon    = document.getElementById('hamburgerIcon');
            const isOpen  = menu.style.display === 'block';

            menu.style.display    = isOpen ? 'none' : 'block';
            overlay.style.display = isOpen ? 'none' : 'block';
            icon.className        = isOpen ? 'bi bi-list' : 'bi bi-x-lg';
        }

        // DROPDOWN AVATAR
        function toggleUserMenu() {
            const menu    = document.getElementById('userMenu');
            const chevron = document.getElementById('dropdownChevron');
            const isOpen  = menu.style.display === 'flex';
            menu.style.display       = isOpen ? 'none' : 'flex';
            menu.style.flexDirection = 'column';
            chevron.style.transform  = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        // CERRAR AL HACER CLIC FUERA
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            const menu     = document.getElementById('userMenu');
            if (dropdown && !dropdown.contains(e.target)) {
                menu.style.display = 'none';
                const chevron = document.getElementById('dropdownChevron');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        });
    </script>
</nav>