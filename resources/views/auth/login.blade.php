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
        <a href="/" class="back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Volver
        </a>
        <h2>Inicio de sesión</h2>
        <div class="subtitle">Accede a tu portafolio profesional</div>

        @if($errors->has('email') && !str_contains($errors->first('email'), 'segundos'))
            <div class="error-message">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="server-error" id="serverError" style="display:none;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Correo o contraseña incorrectos
        </div>

        <div class="blocked-box" id="blockedBox">
            <strong>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Acceso bloqueado
            </strong>
            Demasiados intentos fallidos. Intenta en: <span class="timer" id="timer">04:32</span>
        </div>

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <div class="form-group">
                <label>Correo electrónico <span class="required">*</span></label>
                <div class="input-wrapper" id="emailWrapper">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="M22 7l-10 7L2 7"/>
                        </svg>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="tu@email.com" maxlength="30">
                </div>
                <div class="field-error" id="emailError"></div>
            </div>

            <div class="form-group">
                <label>Contraseña <span class="required">*</span></label>
                <div class="input-wrapper" id="passwordWrapper">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input type="password" name="password" id="password" placeholder="••••••••" maxlength="50">
                    <button type="button" class="eye-btn" id="eyeBtn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <div class="field-error" id="passwordError"></div>
                <div class="forgot-link">
                    <a href="{{ route('password.request') }}">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            </div>

            <button type="submit" class="btn-login" id="btnLogin">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
                    <path d="M15 3h6v6M14 10L21 3"/>
                    <path d="M10 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5"/>
                </svg>
                Ingresar
            </button>

            <div class="register-link">
                ¿No tienes cuenta? 
                <a href="{{ route('register') }}">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/>
                        <line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                    Registrarse
                </a>
            </div>
        </form>
    </div>

    <script>
        let attempts = 0;
        let blocked = false;

        // Mostrar/ocultar contraseña con SVG
        const eyeBtn = document.getElementById('eyeBtn');
        const passwordInput = document.getElementById('password');
        let isPasswordVisible = false;

        eyeBtn.addEventListener('click', function() {
            isPasswordVisible = !isPasswordVisible;
            passwordInput.type = isPasswordVisible ? 'text' : 'password';
            
            // Cambiar SVG
            eyeBtn.innerHTML = isPasswordVisible ? 
                `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                </svg>` :
                `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>`;
        });

        // Contador de caracteres
        function setupCharCounter(inputId, counterId, maxLength) {
            const input = document.getElementById(inputId);
            if (!input) return;
            
            function updateCounter() {
                const current = input.value.length;
                // Puedes crear un elemento contador si lo necesitas
            }
            input.addEventListener('input', updateCounter);
        }

        // Scroll automático para inputs
        document.querySelectorAll('.input-wrapper input').forEach(input => {
            input.addEventListener('input', function() {
                this.scrollLeft = this.scrollWidth;
            });
            input.addEventListener('focus', function() {
                this.scrollLeft = this.scrollWidth;
            });
        });

        // Validación del formulario
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            if (blocked) { e.preventDefault(); return; }

            let isValid = true;
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');
            const emailWrapper = document.getElementById('emailWrapper');
            const passwordWrapper = document.getElementById('passwordWrapper');

            emailError.textContent = '';
            emailError.classList.remove('visible');
            passwordError.textContent = '';
            passwordError.classList.remove('visible');
            emailWrapper.classList.remove('error');
            passwordWrapper.classList.remove('error');

            if (email === '') {
                emailError.textContent = '✕ El correo es obligatorio';
                emailError.classList.add('visible');
                emailWrapper.classList.add('error');
                isValid = false;
            } else {
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

        // Mostrar error de servidor
        @if($errors->has('email'))
            const mensaje = @json($errors->first('email'));
            const match = mensaje.match(/(\d+)\s*segundos/);
            if (match) {
                blocked = true;
                document.getElementById('blockedBox').classList.add('visible');
                document.getElementById('btnLogin').disabled = true;

                let remaining = parseInt(match[1]);
                const timerEl = document.getElementById('timer');

                const interval = setInterval(() => {
                    remaining--;
                    const m = String(Math.floor(remaining / 60)).padStart(2, '0');
                    const s = String(remaining % 60).padStart(2, '0');
                    timerEl.textContent = `${m}:${s}`;
                    if (remaining <= 0) {
                        clearInterval(interval);
                        blocked = false;
                        document.getElementById('blockedBox').classList.remove('visible');
                        document.getElementById('btnLogin').disabled = false;
                    }
                }, 1000);
            } else {
                document.getElementById('serverError').classList.add('visible');
            }
        @endif
    </script>
</body>
</html>