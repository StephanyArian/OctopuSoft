{{-- ============================================================
     CambContrasena.blade.php — Recuperación de Contraseña
     Ruta esperada: /forgot-password
     ============================================================ --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Recuperar contraseña — Portafolio</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- CSS propio --}}
    {{-- antes --}}
     <link rel="stylesheet" href="{{ asset('css/CambContrasena.css') }}">

     {{-- después (usa el CSS compartido) --}}
     <link rel="stylesheet" href="{{ asset('css/RecupCont.css') }}">
    {{-- ─── Estilos inline (fallback si el asset aún no está publicado) ─── --}}
    {{-- Borra este bloque cuando hayas colocado CambContrasena.css en public/css/ --}}
    <style>
        /* El archivo CambContrasena.css debe estar en public/css/CambContrasena.css */
    </style>
</head>
<body>

<div class="forgot-wrapper">
    <div class="forgot-card">

        {{-- ── Volver al login ── --}}
        <a href="{{ route('login') }}" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Volver
        </a>

        {{-- ── Indicador de pasos ── --}}
        <div class="steps" id="stepsIndicator">
            <div class="step-dot active" id="dot1"></div>
            <div class="step-line" id="line1"></div>
            <div class="step-dot" id="dot2"></div>
            <div class="step-line" id="line2"></div>
            <div class="step-dot" id="dot3"></div>
        </div>

        {{-- ─────────────────────────────────────────────────
             PANEL 1 — Ingresar correo
        ───────────────────────────────────────────────── --}}
        <div id="panelForm">
            {{-- Ícono --}}
            <div class="icon-wrapper">
                <div class="icon-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
            </div>

            <h1 class="forgot-title">Recuperar contraseña</h1>
            <p class="forgot-subtitle">
                Ingresa tu correo y te enviaremos un enlace para <strong>restablecer tu acceso</strong>.
            </p>

            {{-- Mensajes de sesión (Laravel) --}}
            @if (session('status'))
                <div style="
                    background: rgba(0,229,204,0.1);
                    border: 1px solid rgba(0,229,204,0.35);
                    border-radius: 10px;
                    padding: 12px 16px;
                    font-size: 0.875rem;
                    color: #00e5cc;
                    margin-bottom: 20px;
                    text-align: center;
                ">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Formulario --}}
            <form class="forgot-form" id="forgotForm" method="POST" action="{{ route('password.email') }}" novalidate>
                @csrf

                {{-- Campo correo --}}
                <div class="field-group">
                    <label class="field-label" for="email">
                        Correo electrónico <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="field-input @error('email') is-invalid @enderror"
                            placeholder="tu@email.com"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            autofocus
                            required
                        >
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </span>
                    </div>

                    @error('email')
                        <span class="field-error">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror

                    {{-- Error JS --}}
                    <span class="field-error" id="emailError" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        Ingresa un correo válido.
                    </span>
                </div>

                {{-- Botón --}}
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span class="btn-text">
                        <span class="spinner" id="spinner"></span>
                        <span id="btnLabel">Enviar enlace de recuperación</span>
                    </span>
                </button>
            </form>

            <div class="divider" style="margin-top:28px;">o</div>

            <p class="forgot-footer" style="margin-top:20px;">
                ¿Recuerdas tu contraseña?
                <a href="{{ route('login') }}">Iniciar sesión</a>
            </p>
        </div>

        {{-- ─────────────────────────────────────────────────
             PANEL 2 — Correo enviado (éxito)
        ───────────────────────────────────────────────── --}}
        <div class="success-panel" id="panelSuccess">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24"
                     fill="none" stroke="#00e5cc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 2L11 13M22 2L15 22l-4-9-9-4 20-7z"/>
                </svg>
            </div>

            <h2 class="success-title">¡Correo enviado!</h2>
            <p class="success-msg">
                Enviamos un enlace de recuperación a<br>
                <span id="sentEmail"></span><br><br>
                Revisa tu bandeja de entrada y sigue las instrucciones.
            </p>

            <div class="resend-row">
                <span>¿No llegó?</span>
                <button class="resend-btn" id="resendBtn" disabled>
                    Reenviar en <span id="countdown">60</span>s
                </button>
            </div>

            <a href="{{ route('login') }}" class="btn-submit" style="
                display:inline-block; text-align:center;
                text-decoration:none; margin-top:8px;
            ">
                Volver al inicio de sesión
            </a>
        </div>

    </div>
</div>

{{-- ─────────────────────────────────────────────────────────
     JavaScript
───────────────────────────────────────────────────────── --}}
<script>
(function () {

    /* ── Elementos ── */
    const form      = document.getElementById('forgotForm');
    const emailInp  = document.getElementById('email');
    const emailErr  = document.getElementById('emailError');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnLabel  = document.getElementById('btnLabel');
    const spinner   = document.getElementById('spinner');
    const panelForm = document.getElementById('panelForm');
    const panelOk   = document.getElementById('panelSuccess');
    const sentEmail = document.getElementById('sentEmail');
    const resendBtn = document.getElementById('resendBtn');
    const countdown = document.getElementById('countdown');

    /* ── Steps ── */
    const dot1  = document.getElementById('dot1');
    const dot2  = document.getElementById('dot2');
    const dot3  = document.getElementById('dot3');
    const line1 = document.getElementById('line1');
    const line2 = document.getElementById('line2');

    /* Si Laravel ya mostró un session('status'), pasar directo al panel de éxito */
    @if (session('status'))
        showSuccess('{{ old('email') ?? '' }}');
    @endif

    /* ── Validación de email ── */
    function isValidEmail(v) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim());
    }

    emailInp.addEventListener('input', function () {
        if (emailErr.style.display !== 'none') {
            if (isValidEmail(this.value)) {
                emailErr.style.display = 'none';
                this.style.borderColor = '';
            }
        }
    });

    /* ── Submit ── */
    form.addEventListener('submit', async function (e) {

        /* Validación client-side */
        if (!isValidEmail(emailInp.value)) {
            e.preventDefault();
            emailErr.style.display = 'flex';
            emailInp.style.borderColor = '#ff6b8a';
            emailInp.focus();
            return;
        }

        /* Mostrar loading */
        btnSubmit.classList.add('loading');
        btnLabel.textContent = 'Enviando…';
        spinner.style.display = 'block';

        /* Dejar que el form se envíe normalmente (POST Laravel).
           Si quieres modo AJAX, descomenta el bloque de abajo y
           pon e.preventDefault() arriba. */

        /*
        e.preventDefault();

        try {
            const fd  = new FormData(form);
            const res = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': fd.get('_token') },
                body: fd,
            });
            if (res.ok) {
                showSuccess(emailInp.value.trim());
            } else {
                const data = await res.json();
                emailErr.textContent = data.message ?? 'Error al enviar el correo.';
                emailErr.style.display = 'flex';
                btnSubmit.classList.remove('loading');
                btnLabel.textContent = 'Enviar enlace de recuperación';
                spinner.style.display = 'none';
            }
        } catch {
            emailErr.textContent = 'Sin conexión. Inténtalo de nuevo.';
            emailErr.style.display = 'flex';
            btnSubmit.classList.remove('loading');
            btnLabel.textContent = 'Enviar enlace de recuperación';
            spinner.style.display = 'none';
        }
        */
    });

    /* ── Mostrar panel éxito ── */
    function showSuccess(email) {
        panelForm.style.display = 'none';
        panelOk.classList.add('visible');
        sentEmail.textContent = email || '';

        /* Steps → estado 2 */
        dot2.classList.add('active');
        line1.classList.add('done');

        startCountdown();
    }

    /* ── Cuenta regresiva reenvío ── */
    function startCountdown() {
        let secs = 60;
        const tick = setInterval(function () {
            secs--;
            countdown.textContent = secs;
            if (secs <= 0) {
                clearInterval(tick);
                resendBtn.disabled = false;
                resendBtn.textContent = 'Reenviar enlace';
            }
        }, 1000);
    }

    resendBtn.addEventListener('click', function () {
        this.disabled = true;
        this.textContent = 'Enviando…';

        /* Resubmit del form */
        form.submit();
    });

})();
</script>

</body>
</html>