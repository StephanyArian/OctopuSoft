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

        @if($errors->has('email') && !str_contains($errors->first('email'), 'segundos'))
            <div class="error-message">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="server-error" id="serverError" style="display:none;">
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
                        value="{{ old('email') }}" placeholder="tu@email.com"  maxlength="30">
                </div>


                <div class="field-error" id="emailError"></div>
            </div>

            <div class="form-group">
                <label>Contraseña <span class="required">*</span></label>
                <div class="input-wrapper" id="passwordWrapper">
                    <span class="input-icon">🔒</span>
                        

                    <input type="password" name="password" id="password" placeholder="••••••••"  maxlength="50">
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

            // ========== ✅ NUEVO: CONTADOR DE CARACTERES EN VIVO ==========
            function setupCharCounter(inputId, counterId, maxLength) {
            const input = document.getElementById(inputId);
            const counter = document.getElementById(counterId);
            
            if (!input || !counter) return;
            
            function updateCounter() {
                const current = input.value.length;
                counter.textContent = `${current} / ${maxLength} caracteres`;
                
                // Cambiar color según se acerque al límite
                counter.classList.remove('warning', 'danger');
                if (current >= maxLength) {
                    counter.classList.add('danger');
                } else if (current >= maxLength - 10) {
                    counter.classList.add('warning');
                }
            }
            
            input.addEventListener('input', updateCounter);
            updateCounter(); // Inicializar
        }
        
        
        
        
        document.querySelectorAll('.input-wrapper input').forEach(input => {
            
            input.addEventListener('input', function() {
                this.scrollLeft = this.scrollWidth;
            });
            
            
            input.addEventListener('focus', function() {
                this.scrollLeft = this.scrollWidth;
            });
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