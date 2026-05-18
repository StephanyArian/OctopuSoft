<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrar sesión</title>
    <link rel="stylesheet" href="{{ asset('css/CierreSesion.css') }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>

    <div class="logout-wrapper">
        <div class="logout-card">

            {{-- Back link --}}
            <a href="{{ route('dashboard') }}" class="back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Volver
            </a>

            {{-- Steps indicator --}}
            <div class="steps">
                <span class="step active"></span>
                <span class="step"></span>
                <span class="step"></span>
                <span class="step"></span>
            </div>

            {{-- Ícono de candado SVG mejorado --}}
            <div class="icon-wrap">
                <svg class="lock-svg" viewBox="0 0 38 38" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    {{-- Cuerpo del candado --}}
                    <rect x="6" y="17" width="26" height="18" rx="5"
                          fill="none" stroke="#18d5bf" stroke-width="1.8"/>
                    {{-- Arco superior --}}
                    <path d="M12 17V12.5C12 8.91 14.91 6 18.5 6h1C23.09 6 26 8.91 26 12.5V17"
                          stroke="#18d5bf" stroke-width="1.8"
                          stroke-linecap="round" stroke-linejoin="round"/>
                    {{-- Punto central (ojo del candado) --}}
                    <circle cx="19" cy="26" r="2.4" fill="#18d5bf"/>
                    {{-- Línea vertical del ojo --}}
                    <line x1="19" y1="28.4" x2="19" y2="31"
                          stroke="#18d5bf" stroke-width="1.8"
                          stroke-linecap="round"/>
                    {{-- Brillo sutil --}}
                    <rect x="6" y="17" width="26" height="5" rx="5"
                          fill="url(#lockShine)" opacity="0.25"/>
                    <defs>
                        <linearGradient id="lockShine" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#ffffff"/>
                            <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            {{-- Badge de sesión segura --}}
            <div class="security-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Sesión protegida
            </div>

            {{-- Título y descripción --}}
            <h1>Cerrar sesión</h1>

            <p class="subtitle">
                ¿Deseas salir de forma segura?<br>
                Al continuar, se finalizará el acceso a tu
                <strong>portafolio personal</strong>.
            </p>

            {{-- Formulario --}}
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="button" class="gradient-btn" onclick="cerrarSesion()">
                    Cerrar sesión
                </button>
            </form>

            <a href="{{ route('dashboard') }}" class="cancel-btn" style="display:block;text-decoration:none;text-align:center;">
                Cancelar
            </a>

            {{-- Divider decorativo --}}
            <div class="divider">
                <span class="divider-dot"></span>
            </div>

            {{-- Texto inferior --}}
            <p class="bottom-text">
                ¿Prefieres seguir en tu cuenta?
                <a href="{{ route('dashboard') }}">Volver al portafolio</a>
            </p>

        </div>
    </div>

    <script>
        function cerrarSesion() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('portfolioData');

            sessionStorage.removeItem('token');
            sessionStorage.removeItem('user');
            sessionStorage.removeItem('portfolioData');

            document.cookie.split(";").forEach(function(c) {
                document.cookie = c
                    .replace(/^ +/, "")
                    .replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
            });

            document.getElementById('logout-form').submit();
        }

        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>

</body>
</html>