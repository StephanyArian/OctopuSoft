{{-- ============================================================
     RecupCont.blade.php — Restablecer Contraseña
     Ruta esperada: /reset-password/{token}
     ============================================================ --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Restablecer contraseña — Portafolio</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- CSS compartido del flujo de recuperación --}}
    <link rel="stylesheet" href="{{ asset('css/Recupcont.css') }}">
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

        {{-- ── Indicador de pasos — estamos en el paso 3 ── --}}
        <div class="steps" id="stepsIndicator">
            <div class="step-dot done"  id="dot1"></div>
            <div class="step-line done" id="line1"></div>
            <div class="step-dot done"  id="dot2"></div>
            <div class="step-line"      id="line2"></div>
            <div class="step-dot active" id="dot3"></div>
        </div>

        {{-- ─────────────────────────────────────────────────
             PANEL — Formulario nueva contraseña
        ───────────────────────────────────────────────── --}}
        <div id="panelForm">

            {{-- Ícono --}}
            <div class="icon-wrapper">
                <div class="icon-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        <line x1="12" y1="15" x2="12" y2="17"/>
                    </svg>
                </div>
            </div>

            <h1 class="forgot-title">Nueva contraseña</h1>
            <p class="forgot-subtitle">
                Crea una contraseña <strong>segura y nueva</strong> para tu cuenta.
            </p>

            {{-- Errores de sesión --}}
            @if ($errors->any())
                <div class="alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Formulario --}}
            <form class="forgot-form" id="resetForm" method="POST" action="{{ route('password.store') }}" novalidate>
                @csrf
                

                {{-- Token oculto --}}
                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email oculto (requerido por Laravel) --}}
                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                {{-- ── Campo: Nueva contraseña ── --}}
                <div class="field-group">
                    <label class="field-label" for="password">
                        Nueva contraseña <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="field-input @error('password') is-invalid @enderror"
                            placeholder="Mínimo 8 caracteres"
                            autocomplete="new-password"
                            autofocus
                            required
                        >
                        <button type="button" class="toggle-pw" data-target="password" aria-label="Mostrar contraseña">
                            <svg class="eye-icon eye-show" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-icon eye-hide" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>

                    @error('password')
                        <span class="field-error">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror

                    <span class="field-error" id="pwError" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <span id="pwErrorMsg">La contraseña no cumple los requisitos.</span>
                    </span>

                    {{-- Indicador de fortaleza --}}
                    <div class="strength-bar-wrapper" id="strengthWrapper" style="display:none;">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="strength-label" id="strengthLabel"></span>
                    </div>

                    {{-- Checklist de requisitos --}}
                    <ul class="req-list" id="reqList">
                        <li class="req-item" id="req-len">
                            <span class="req-dot"></span> Mínimo 8 caracteres
                        </li>
                        <li class="req-item" id="req-upper">
                            <span class="req-dot"></span> Al menos una mayúscula
                        </li>
                        <li class="req-item" id="req-lower">
                            <span class="req-dot"></span> Al menos una minúscula
                        </li>
                        <li class="req-item" id="req-num">
                            <span class="req-dot"></span> Al menos un número
                        </li>
                        <li class="req-item" id="req-special">
                            <span class="req-dot"></span> Al menos un carácter especial (!@#$%^&*)
                        </li>
                    </ul>
                </div>

                {{-- ── Campo: Confirmar contraseña ── --}}
                <div class="field-group">
                    <label class="field-label" for="password_confirmation">
                        Confirmar contraseña <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="field-input"
                            placeholder="Repite tu nueva contraseña"
                            autocomplete="new-password"
                            required
                        >
                        <button type="button" class="toggle-pw" data-target="password_confirmation" aria-label="Mostrar contraseña">
                            <svg class="eye-icon eye-show" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-icon eye-hide" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>

                    <span class="field-error" id="confirmError" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        Las contraseñas no coinciden.
                    </span>

                    <span class="field-ok" id="confirmOk" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Las contraseñas coinciden.
                    </span>
                </div>

                {{-- Botón --}}
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span class="btn-text">
                        <span class="spinner" id="spinner"></span>
                        <span id="btnLabel">Restablecer contraseña</span>
                    </span>
                </button>
            </form>
        </div>

        {{-- ─────────────────────────────────────────────────
             PANEL — Éxito: contraseña cambiada
        ───────────────────────────────────────────────── --}}
        <div class="success-panel" id="panelSuccess">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24"
                     fill="none" stroke="#00e5cc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>

            <h2 class="success-title">¡Contraseña actualizada!</h2>
            <p class="success-msg">
                Tu contraseña fue restablecida correctamente.<br>
                Ya puedes iniciar sesión con tu nueva contraseña.
            </p>

            <a href="{{ route('login') }}" class="btn-submit" style="
                display:inline-block; text-align:center;
                text-decoration:none; margin-top:8px;
            ">
                Ir a iniciar sesión
            </a>
        </div>

    </div>
</div>

{{-- ─────────────────────────────────────────────────────────
     JavaScript
───────────────────────────────────────────────────────── --}}
<script>
(function () {

    const form        = document.getElementById('resetForm');
    const pwInp       = document.getElementById('password');
    const confirmInp  = document.getElementById('password_confirmation');
    const pwError     = document.getElementById('pwError');
    const pwErrorMsg  = document.getElementById('pwErrorMsg');
    const confirmErr  = document.getElementById('confirmError');
    const confirmOk   = document.getElementById('confirmOk');
    const btnSubmit   = document.getElementById('btnSubmit');
    const btnLabel    = document.getElementById('btnLabel');
    const spinner     = document.getElementById('spinner');
    const panelForm   = document.getElementById('panelForm');
    const panelOk     = document.getElementById('panelSuccess');
    const strengthW   = document.getElementById('strengthWrapper');
    const strengthF   = document.getElementById('strengthFill');
    const strengthL   = document.getElementById('strengthLabel');

    /* Requisitos individuales */
    const reqs = {
        'req-len':     v => v.length >= 8,
        'req-upper':   v => /[A-Z]/.test(v),
        'req-lower':   v => /[a-z]/.test(v),
        'req-num':     v => /[0-9]/.test(v),
        'req-special': v => /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(v),
    };

    function allPass(v) {
        return Object.values(reqs).every(fn => fn(v));
    }

    /* ── Toggle mostrar/ocultar contraseña ── */
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', function () {
            const inp  = document.getElementById(this.dataset.target);
            const show = this.querySelector('.eye-show');
            const hide = this.querySelector('.eye-hide');
            if (inp.type === 'password') {
                inp.type = 'text';
                show.style.display = 'none';
                hide.style.display = 'block';
            } else {
                inp.type = 'password';
                show.style.display = 'block';
                hide.style.display = 'none';
            }
        });
    });

    /* ── Validación en tiempo real: contraseña ── */
    pwInp.addEventListener('input', function () {
        const v = this.value;
        strengthW.style.display = v.length ? 'flex' : 'none';

        /* Checklist */
        let passed = 0;
        for (const [id, fn] of Object.entries(reqs)) {
            const li = document.getElementById(id);
            if (fn(v)) {
                li.classList.add('req-ok');
                passed++;
            } else {
                li.classList.remove('req-ok');
            }
        }

        /* Barra de fortaleza */
        const pct = (passed / 5) * 100;
        strengthF.style.width = pct + '%';
        strengthF.className = 'strength-fill';
        if (passed <= 2) {
            strengthF.classList.add('weak');
            strengthL.textContent = 'Débil';
        } else if (passed <= 3) {
            strengthF.classList.add('fair');
            strengthL.textContent = 'Regular';
        } else if (passed === 4) {
            strengthF.classList.add('good');
            strengthL.textContent = 'Buena';
        } else {
            strengthF.classList.add('strong');
            strengthL.textContent = 'Fuerte';
        }

        /* Ocultar error si ya es válida */
        if (allPass(v)) pwError.style.display = 'none';

        /* Re-validar confirmación si ya fue tocada */
        if (confirmInp.value) validateConfirm();
    });

    /* ── Validación en tiempo real: confirmación ── */
    confirmInp.addEventListener('input', validateConfirm);

    function validateConfirm() {
        const match = pwInp.value === confirmInp.value && confirmInp.value !== '';
        confirmErr.style.display = match ? 'none' : (confirmInp.value ? 'flex' : 'none');
        confirmOk.style.display  = match ? 'flex' : 'none';
        confirmInp.style.borderColor = confirmInp.value
            ? (match ? 'rgba(0,229,204,0.6)' : '#ff6b8a')
            : '';
    }

    /* ── Submit ── */
    form.addEventListener('submit', function (e) {
        let valid = true;

        /* Validar contraseña */
        if (!allPass(pwInp.value)) {
            e.preventDefault();
            pwError.style.display = 'flex';
            pwErrorMsg.textContent = 'La contraseña no cumple los requisitos de seguridad.';
            pwInp.style.borderColor = '#ff6b8a';
            pwInp.focus();
            valid = false;
        }

        /* Validar coincidencia */
        if (pwInp.value !== confirmInp.value) {
            e.preventDefault();
            confirmErr.style.display = 'flex';
            confirmInp.style.borderColor = '#ff6b8a';
            if (valid) confirmInp.focus();
            valid = false;
        }

        if (valid) {
            btnSubmit.classList.add('loading');
            btnLabel.textContent = 'Guardando…';
            spinner.style.display = 'block';
        }
    });

    /* Si Laravel redirige con status de éxito, mostrar panel de éxito */
    @if (session('status') === 'passwords.reset')
        showSuccess();
    @endif

    function showSuccess() {
        panelForm.style.display = 'none';
        panelOk.classList.add('visible');
        document.getElementById('dot3').classList.add('done');
        document.getElementById('line2').classList.add('done');
    }

})();
</script>

</body>
</html>