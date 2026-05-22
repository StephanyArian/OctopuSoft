<nav x-data="{ open: false }" style="background-color: #2d0a1e; border-bottom: 1px solid #4a1030; position: sticky; top: 0; z-index: 1000;">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
<div class="shrink-0 flex items-center">
    <span style="font-weight:800;font-size:1.4rem;font-family:'Figtree',sans-serif;background:linear-gradient(135deg,#0abf9e,#00ff88);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:-0.5px;">DevFolio</span>
</div>

            <!-- Settings Dropdown -->
            <div class="sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
<x-slot name="trigger">
    <div style="display:flex;align-items:center;gap:8px;">
        
        <!-- Casita FUERA del botón dropdown -->
        <a href="{{ url('/') }}" style="text-decoration:none;" onclick="event.stopPropagation();">
            <div style="width:32px;height:32px;background:linear-gradient(135deg,#0abf9e,#1de8c0,#00ff88);border-radius:8px;display:flex;align-items:center;justify-content:center;box-shadow:0 0 10px rgba(10,191,158,0.5);">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#2d0a1e" viewBox="0 0 16 16">
                    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                    <path d="M13 7.293l-5-5-5 5V14a1 1 0 0 0 1 1h3v-3h2v3h3a1 1 0 0 0 1-1z"/>
                </svg>
            </div>
        </a>

        <!-- Botón dropdown solo con foto/abreviatura -->
        <button style="background:transparent;border:none;padding:0;cursor:pointer;display:flex;align-items:center;gap:6px;"
                class="inline-flex items-center focus:outline-none transition ease-in-out duration-150">
            <div style="position:relative;">
                @if(Auth::user()->photo_base64)
                    <img src="{{ Auth::user()->photo_base64 }}"
                         style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid #0abf9e;box-shadow:0 0 10px rgba(10,191,158,0.5);">
                @else
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0abf9e,#1de8c0,#00ff88);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:0.9rem;color:#2d0a1e;box-shadow:0 0 10px rgba(10,191,158,0.5);">
                        {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                @endif
                <div style="position:absolute;bottom:0;right:0;width:10px;height:10px;background:#00ff88;border-radius:50%;border:2px solid #2d0a1e;"></div>
            </div>
            <svg class="fill-current h-4 w-4" style="color:#0abf9e;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

    </div>
</x-slot>


                    <x-slot name="content">
                        <div style="border: 2px solid #0abf9e; border-radius: 12px; overflow: hidden; background: white; min-width: 160px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            
                            <a href="{{ url('/')}}" 
                            style="display: block; padding: 12px 20px; color: #2d0a1e; text-decoration: none; transition: all 0.3s ease; font-size: 14px; display:flex; align-items:center; gap:8px;"
                            onmouseover="this.style.backgroundColor='#e0faf5'; this.style.paddingLeft='25px';" 
                            onmouseout="this.style.backgroundColor='transparent'; this.style.paddingLeft='20px';">
                                <i class="bi bi-house-fill"></i> Inicio
                            </a>

                            <a href="{{ route('informacion.academica') }}" 
                            style="display: block; padding: 12px 20px; color: #2d0a1e; text-decoration: none; border-top: 1px solid #e5e7eb; transition: all 0.3s ease; font-size: 14px; display:flex; align-items:center; gap:8px;"
                            onmouseover="this.style.backgroundColor='#e0faf5'; this.style.paddingLeft='25px';" 
                            onmouseout="this.style.backgroundColor='transparent'; this.style.paddingLeft='20px';">
                                <i class="bi bi-pencil-fill"></i> Completar
                            </a>

                            <a href="{{ route('preview') }}" 
                            style="display: block; padding: 12px 20px; color: #2d0a1e; text-decoration: none; border-top: 1px solid #e5e7eb; transition: all 0.3s ease; font-size: 14px; display:flex; align-items:center; gap:8px;"
                            onmouseover="this.style.backgroundColor='#e0faf5'; this.style.paddingLeft='25px';" 
                            onmouseout="this.style.backgroundColor='transparent'; this.style.paddingLeft='20px';">
                                <i class="bi bi-eye-fill"></i> Ver perfil
                            </a>

                            <a href="{{ route('profile.edit') }}" 
                            style="display: block; padding: 12px 20px; color: #2d0a1e; text-decoration: none; border-top: 1px solid #e5e7eb; transition: all 0.3s ease; font-size: 14px; display:flex; align-items:center; gap:8px;"
                            onmouseover="this.style.backgroundColor='#e0faf5'; this.style.paddingLeft='25px';" 
                            onmouseout="this.style.backgroundColor='transparent'; this.style.paddingLeft='20px';">
                                <i class="bi bi-gear-fill"></i> Configuracion
                            </a>

                            <a href="{{ route('cerrar.sesion') }}" 
                            style="display: block; padding: 12px 20px; color: #2d0a1e; text-decoration: none; border-top: 1px solid #e5e7eb; transition: all 0.3s ease; font-size: 14px; display:flex; align-items:center; gap:8px;"
                            onmouseover="this.style.backgroundColor='#e0faf5'; this.style.paddingLeft='25px';" 
                            onmouseout="this.style.backgroundColor='transparent'; this.style.paddingLeft='20px';">
                                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                            </a>

                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" style="color: #0abf9e;" class="inline-flex items-center justify-center p-2 rounded-md focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 20 20">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menú móvil: overlay fijo que NO empuja el contenido -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        style="
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            background-color: #2d0a1e;
            z-index: 49;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            max-height: calc(100vh - 64px);
            overflow-y: auto;
            border-bottom: 2px solid #0abf9e;
        "
        class="sm:hidden"
    >
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" style="color: #ffffff;">
                <i class="bi bi-house-fill"></i> {{ __('DevFolio') }}
            </x-responsive-nav-link>
            <div style="border-top: 1px solid rgba(255,255,255,0.1); margin: 8px 16px;"></div>
            <x-responsive-nav-link :href="route('profile.create')" style="color: #1de8c0;">
                <i class="bi bi-person-fill"></i> {{ __('Personal') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('experiencia.laboral')" style="color: #1de8c0;">
                <i class="bi bi-briefcase-fill"></i> {{ __('Experiencia laboral') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('informacion.academica')" style="color: #1de8c0;">
                <i class="bi bi-mortarboard-fill"></i> {{ __('Información académica') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('skills.tecnicas')" style="color: #1de8c0;">
                <i class="bi bi-cpu-fill"></i> {{ __('Habilidades técnicas') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('skills.blandas')" style="color: #1de8c0;">
                <i class="bi bi-heart-fill"></i> {{ __('Habilidades blandas') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('proyectos')" style="color: #1de8c0;">
                <i class="bi bi-kanban-fill"></i> {{ __('Proyectos') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('idiomas.index')" style="color: #1de8c0;">
                <i class="bi bi-translate"></i> {{ __('Idiomas') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('redes.index')" style="color: #1de8c0;">
                <i class="bi bi-globe"></i> {{ __('Redes profesionales y contacto') }}
            </x-responsive-nav-link>
        </div>
    </div>

    <!-- Fondo semitransparente: al tocar fuera cierra el menú -->
    <div
        x-show="open"
        @click="open = false"
        style="position: fixed; inset: 0; top: 64px; background: rgba(0,0,0,0.4); z-index: 48;"
        class="sm:hidden"
    ></div>
</nav>