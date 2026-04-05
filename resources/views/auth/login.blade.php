<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión | Portafolio</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <a href="/" class="back-link">← Volver</a>
        <h2>Inicio de sesión</h2>
        <div class="subtitle">Accede a tu portafolio profesional</div>

        @if($errors->any())
            <div class="error-message">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="server-error" id="serverError">
            Correo o contraseña incorrectos
        </div>

        <div class="blocked-box" id="blockedBox">
            <strong>Acceso bloqueado</strong>
            Demasiados intentos fallidos. Intenta en: <span class="timer" id="timer">04:32</span>
        </div>

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <div class="form-group">
                <label>Correo electrónico <span class="required">*</span></label>
                <div class="input-wrapper" id="emailWrapper">
                    <span class="input-icon">✉</span>
                    <input type="email" name="email" id="email"
                        value="{{ old('email') }}" placeholder="tu@email.com">
                </div>
                <div class="field-error" id="emailError"></div>
            </div>

            <div class="form-group">
                <label>Contraseña <span class="required">*</span></label>
                <div class="input-wrapper" id="passwordWrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" name="password" id="password" placeholder="••••••••">
                    <button type="button" class="eye-btn" id="eyeBtn">👁</button>
                </div>
                <div class="field-error" id="passwordError"></div>
                <div class="forgot-link">
                    <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                </div>
            </div>

            <button type="submit" class="btn-login" id="btnLogin">Ingresar</button>

            <div class="register-link">
                ¿No tienes cuenta? <a href="{{ route('register') }}">Registrarse</a>
            </div>
        </form>
    </div>

    <script>
        let attempts = 0;
        let blocked = false;

        // Mostrar/ocultar contraseña
        document.getElementById('eyeBtn').addEventListener('click', function() {
            const input = document.getElementById('password');
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            this.textContent = isText ? '👁' : '🙈';
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            if (blocked) { e.preventDefault(); return; }

            let isValid = true;
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');
            const emailWrapper = document.getElementById('emailWrapper');
            const passwordWrapper = document.getElementById('passwordWrapper');

            // Limpiar errores
            emailError.textContent = '';
            emailError.classList.remove('visible');
            passwordError.textContent = '';
            passwordError.classList.remove('visible');
            emailWrapper.classList.remove('error');
            passwordWrapper.classList.remove('error');

            // T2: Validar campos vacíos
            if (email === '') {
                emailError.textContent = '✕ El correo es obligatorio';
                emailError.classList.add('visible');
                emailWrapper.classList.add('error');
                isValid = false;
            } else {
                // T3: Validar formato email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    emailError.textContent = '✕ Ingresa un correo electrónico válido';
                    emailError.classList.add('visible');
                    emailWrapper.classList.add('error');
                    isValid = false;
                }
            }

            if (password === '') {
                passwordError.textContent = '✕ La contraseña es obligatoria';
                passwordError.classList.add('visible');
                passwordWrapper.classList.add('error');
                isValid = false;
            }

            if (!isValid) { e.preventDefault(); }
        });

        // Mostrar error de servidor si Laravel devuelve error
        @if($errors->has('email') || $errors->has('password'))
            attempts = {{ session('login_attempts', 0) }};
            if (attempts >= 3) {
                startBlock();
            } else {
                document.getElementById('serverError').classList.add('visible');
            }
        @endif

        function startBlock() {
            blocked = true;
            document.getElementById('blockedBox').classList.add('visible');
            document.getElementById('serverError').classList.remove('visible');
            document.getElementById('btnLogin').disabled = true;

            let seconds = 4 * 60 + 32;
            const timerEl = document.getElementById('timer');

            const interval = setInterval(() => {
                seconds--;
                const m = String(Math.floor(seconds / 60)).padStart(2, '0');
                const s = String(seconds % 60).padStart(2, '0');
                timerEl.textContent = `${m}:${s}`;
                if (seconds <= 0) {
                    clearInterval(interval);
                    blocked = false;
                    attempts = 0;
                    document.getElementById('blockedBox').classList.remove('visible');
                    document.getElementById('btnLogin').disabled = false;
                }
            }, 1000);
        }
    </script>
</body>
</html>