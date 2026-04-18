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

            <a href="{{ route('dashboard') }}" class="back-link">← Volver</a>

            <div class="steps">
                <span class="step active"></span>
                <span class="step"></span>
                <span class="step"></span>
            </div>

            <div class="icon-circle">
                <span class="lock-icon">🔒</span>
            </div>

            <h1>Cerrar sesión</h1>

            <p>
                ¿Deseas cerrar tu sesión de forma segura?
                Al continuar, se finalizará el acceso a tu portafolio.
            </p>

            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="button" class="gradient-btn" onclick="cerrarSesion()">
                    Cerrar sesión
                </button>
            </form>

            <div class="divider"></div>

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