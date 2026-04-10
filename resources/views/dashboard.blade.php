<x-app-layout> 
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h2 class="font-semibold text-xl leading-tight" style="color: #2d0a1e;">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <style>
        /* Fondo */
        .main-content {
            background: linear-gradient(135deg, #e0faf5, #b2f0e8, #e8f9ff);
            min-height: 100vh;
        }

        /* Tarjeta con borde mágico */
        .magic-card {
            position: relative;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 24px;
            overflow: hidden;
        }

        .magic-card::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 18px;
            background: linear-gradient(90deg, #0abf9e, #1de8c0, #2d0a1e, #0abf9e);
            background-size: 300% 300%;
            animation: borderGlow 3s linear infinite;
            z-index: -1;
        }

        .magic-card::after {
            content: '';
            position: absolute;
            inset: 2px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.85);
            z-index: -1;
        }

        @keyframes borderGlow {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .welcome-text {
            color: #2d0a1e;
            font-size: 16px;
            font-weight: 500;
        }

        /* Botón cerrar sesión */
        .logout-btn {
            display: inline-block;
            padding: 10px 18px;
            background: linear-gradient(90deg, #2d0a1e, #0abf9e);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            opacity: 0.95;
        }

        /* Mariposas en esquina inferior derecha - 400px */
        .mariposas {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 400px;
            z-index: 10;
        }

        .mariposas img {
            width: 100%;
            animation: flotar 3s ease-in-out infinite;
        }

        @keyframes flotar {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>

    <div class="main-content py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="magic-card">
                <p class="welcome-text">
                    {{ __("¡Qué bueno verte, Bienvenido!") }}
                </p>
            </div>
        </div>
    </div>

    <!-- Mariposas en esquina -->
    <div class="mariposas">
        <img src="{{ asset('imagenes/mariposas.gif') }}" 
             alt="Mariposas animadas">
    </div>
</x-app-layout>