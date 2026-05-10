<nav x-data="{ open: false }" style="background-color: #2d0a1e; border-bottom: 1px solid #4a1030; position: relative; z-index: 50;">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current" style="color: #0abf9e;" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class=" space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" style="color:  #0abf9e; font-weight: 900;font-size: 16px; letter-spacing: 1px;font-family: 'Arial Black', sans-serif;">
                        {{ __('MI PORTAFOLIO') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button style="color: #1de8c0; background: transparent; border: 1px solid #0abf9e; border-radius: 50px; padding: 8px 16px; transition: all 0.3s ease;" 
                                onmouseover="this.style.backgroundColor='#0abf9e'; this.style.color='#2d0a1e';" 
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#1de8c0';"
                                class="inline-flex items-center px-3 py-2 text-sm leading-4 font-medium rounded-md focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->first_name ?? Auth::user()->name ?? 'Usuario' }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div style="border: 2px solid #0abf9e; border-radius: 12px; overflow: hidden; background: white; min-width: 160px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <a href="{{ route('profile.edit') }}" 
                               style="display: block; padding: 12px 20px; color: #2d0a1e; text-decoration: none; transition: all 0.3s ease; font-size: 14px;"
                               onmouseover="this.style.backgroundColor='#e0faf5'; this.style.paddingLeft='25px';" 
                               onmouseout="this.style.backgroundColor='transparent'; this.style.paddingLeft='20px';">
                                {{ __('Configuracion') }}
                            </a>
                            <a href="{{ route('cerrar.sesion') }}" 
                               style="display: block; padding: 12px 20px; color: #2d0a1e; text-decoration: none; border-top: 1px solid #e5e7eb; transition: all 0.3s ease; font-size: 14px;"
                               onmouseover="this.style.backgroundColor='#e0faf5'; this.style.paddingLeft='25px';" 
                               onmouseout="this.style.backgroundColor='transparent'; this.style.paddingLeft='20px';">
                                {{ __('Cerrar Sesión') }}
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
                <i class="bi bi-house-fill"></i> {{ __('Mi portafolio') }}
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