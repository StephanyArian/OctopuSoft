<section class="password-section">
    <h2 class="password-title">Cambiar contraseña</h2>

    <form method="post" action="{{ route('password.update') }}" class="password-form" id="passwordForm">
        @csrf
        @method('put')

        @if (session('status') === 'password-updated')
            <div class="alert-success">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Contraseña actualizada correctamente.
            </div>
        @endif

        {{-- Mostrar errores generales del backend --}}
        @if ($errors->updatePassword->any() && !$errors->updatePassword->has('current_password'))
            <div class="alert-error">
                @foreach ($errors->updatePassword->all() as $error)
                    <p style="margin: 0;">❌ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- CONTRASEÑA ACTUAL --}}
        <div class="field-group" id="group-current">
            <label class="field-label">Contraseña actual <span class="required">*</span></label>
            <div class="input-wrapper">
                <input id="current_password" name="current_password" type="password" maxlength="20"
                    class="field-input @error('current_password', 'updatePassword') input-error-state @enderror"
                    placeholder="Ingresa tu contraseña actual" autocomplete="current-password">
                <button type="button" class="eye-btn" onclick="togglePassword('current_password', this)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            <span class="field-error" id="error-current" style="display: none;"></span>
            @error('current_password', 'updatePassword')
                <span class="field-error" id="error-current-backend" style="display: block;">{{ $message }}</span>
            @enderror
        </div>

        {{-- NUEVA CONTRASEÑA --}}
        <div class="field-group" id="group-new">
            <label class="field-label">Nueva contraseña <span class="required">*</span></label>
            <div class="input-wrapper">
                <input id="new_password" name="password" type="password" maxlength="20"
                    class="field-input @error('password', 'updatePassword') input-error-state @enderror"
                    placeholder="Mínimo 8, máximo 20 caracteres" autocomplete="new-password"
                    oninput="checkStrength(this.value); checkMatch()">
                <button type="button" class="eye-btn" onclick="togglePassword('new_password', this)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            <span class="field-error" id="error-new" style="display: none;"></span>
            @error('password', 'updatePassword')
                <span class="field-error">{{ $message }}</span>
            @enderror

            <div class="strength-bar">
                <div class="strength-track"><div id="strengthFill" class="strength-fill"></div></div>
                <span id="strengthLabel" class="strength-label">Escribe una contraseña para ver su fortaleza</span>
            </div>

            <div class="requirements">
                <div class="req-item" id="req-length"><span class="req-dot"></span> 8 a 20 caracteres</div>
                <div class="req-item" id="req-upper"><span class="req-dot"></span> Una mayúscula</div>
                <div class="req-item" id="req-lower"><span class="req-dot"></span> Una minúscula</div>
                <div class="req-item" id="req-number"><span class="req-dot"></span> Un número</div>
                <div class="req-item" id="req-special"><span class="req-dot"></span> Un carácter especial</div>
            </div>
        </div>

        {{-- CONFIRMAR CONTRASEÑA --}}
        <div class="field-group" id="group-confirm">
            <label class="field-label">Confirmar nueva contraseña <span class="required">*</span></label>
            <div class="input-wrapper">
                <input id="confirm_password" name="password_confirmation" type="password" maxlength="20"
                    class="field-input @error('password_confirmation', 'updatePassword') input-error-state @enderror"
                    placeholder="Repite tu nueva contraseña" autocomplete="new-password"
                    oninput="checkMatch()">
                <button type="button" class="eye-btn" onclick="togglePassword('confirm_password', this)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            <span id="error-confirm" class="field-error" style="display: none;"></span>
            @error('password_confirmation', 'updatePassword')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <button type="submit" class="btn-save">Guardar contraseña</button>
            <button type="button" class="btn-cancel" onclick="resetForm()">Cancelar</button>
        </div>
    </form>
</section>

<style>
.field-group.error .field-input {
    border-color: #ef4444 !important;
    background-color: #fff5f5 !important;
}
.field-group.error .field-input:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}
.alert-error {
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}
</style>

<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
}

function checkStrength(val) {
    const fill = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    
    const checks = {
        length: val.length >= 8 && val.length <= 20,
        upper: /[A-Z]/.test(val),
        lower: /[a-z]/.test(val),
        number: /[0-9]/.test(val),
        special: /[^A-Za-z0-9]/.test(val)
    };
    
    let score = Object.values(checks).filter(Boolean).length;
    
    document.getElementById('req-length').classList.toggle('met', checks.length);
    document.getElementById('req-upper').classList.toggle('met', checks.upper);
    document.getElementById('req-lower').classList.toggle('met', checks.lower);
    document.getElementById('req-number').classList.toggle('met', checks.number);
    document.getElementById('req-special').classList.toggle('met', checks.special);
    
    if (!val) {
        fill.style.width = '0%';
        fill.removeAttribute('data-level');
        label.textContent = 'Escribe una contraseña para ver su fortaleza';
        return;
    }
    
    const levels = ['very-weak', 'weak', 'fair', 'good', 'strong'];
    const texts = ['Muy débil', 'Débil', 'Regular', 'Buena', 'Muy fuerte'];
    const idx = Math.min(score - 1, 4);
    const pct = (score / 5) * 100;
    
    fill.style.width = pct + '%';
    fill.setAttribute('data-level', levels[idx]);
    label.textContent = texts[idx];
}

function checkMatch() {
    const newPw = document.getElementById('new_password').value;
    const confirmPw = document.getElementById('confirm_password').value;
    const errorSpan = document.getElementById('error-confirm');
    
    if (confirmPw.length > 0 && newPw !== confirmPw) {
        errorSpan.textContent = 'Las contraseñas no coinciden.';
        errorSpan.style.display = 'block';
        document.getElementById('group-confirm').classList.add('error');
        return false;
    } else {
        errorSpan.style.display = 'none';
        document.getElementById('group-confirm').classList.remove('error');
        return true;
    }
}

function resetForm() {
    document.getElementById('passwordForm').reset();
    document.getElementById('strengthFill').style.width = '0%';
    document.getElementById('strengthFill').removeAttribute('data-level');
    document.getElementById('strengthLabel').textContent = 'Escribe una contraseña para ver su fortaleza';
    document.getElementById('error-current').style.display = 'none';
    document.getElementById('error-new').style.display = 'none';
    document.getElementById('error-confirm').style.display = 'none';
    document.getElementById('group-current').classList.remove('error');
    document.getElementById('group-new').classList.remove('error');
    document.getElementById('group-confirm').classList.remove('error');
    ['req-length','req-upper','req-lower','req-number','req-special'].forEach(id => {
        document.getElementById(id).classList.remove('met');
    });
}

// Validación de campos vacíos al enviar
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Validar contraseña actual (solo vacío, el backend valida si es correcta)
    const currentPw = document.getElementById('current_password').value.trim();
    const currentGroup = document.getElementById('group-current');
    const currentError = document.getElementById('error-current');
    
    if (currentPw === '') {
        currentError.textContent = 'La contraseña actual es obligatoria.';
        currentError.style.display = 'block';
        currentGroup.classList.add('error');
        isValid = false;
    } else {
        currentError.style.display = 'none';
        currentGroup.classList.remove('error');
    }
    
    // Validar nueva contraseña
    const newPw = document.getElementById('new_password').value;
    const newGroup = document.getElementById('group-new');
    const newErrorSpan = document.getElementById('error-new');
    let newError = '';
    
    if (newPw === '') {
        newError = 'La nueva contraseña es obligatoria.';
        isValid = false;
    } else if (newPw.length < 8) {
        newError = 'La contraseña debe tener al menos 8 caracteres.';
        isValid = false;
    } else if (newPw.length > 20) {
        newError = 'La contraseña no puede tener más de 20 caracteres.';
        isValid = false;
    } else if (!/[A-Z]/.test(newPw)) {
        newError = 'Debe incluir al menos una mayúscula.';
        isValid = false;
    } else if (!/[a-z]/.test(newPw)) {
        newError = 'Debe incluir al menos una minúscula.';
        isValid = false;
    } else if (!/[0-9]/.test(newPw)) {
        newError = 'Debe incluir al menos un número.';
        isValid = false;
    } else if (!/[^A-Za-z0-9]/.test(newPw)) {
        newError = 'Debe incluir al menos un carácter especial (!@#$%^&*).';
        isValid = false;
    }
    
    if (newError) {
        newErrorSpan.textContent = newError;
        newErrorSpan.style.display = 'block';
        newGroup.classList.add('error');
        isValid = false;
    } else {
        newErrorSpan.style.display = 'none';
        newGroup.classList.remove('error');
    }
    
    // Validar confirmación
    const confirmPw = document.getElementById('confirm_password').value;
    const confirmGroup = document.getElementById('group-confirm');
    const confirmErrorSpan = document.getElementById('error-confirm');
    
    if (confirmPw === '') {
        confirmErrorSpan.textContent = 'Debes confirmar tu nueva contraseña.';
        confirmErrorSpan.style.display = 'block';
        confirmGroup.classList.add('error');
        isValid = false;
    } else if (newPw !== confirmPw) {
        confirmErrorSpan.textContent = 'Las contraseñas no coinciden.';
        confirmErrorSpan.style.display = 'block';
        confirmGroup.classList.add('error');
        isValid = false;
    } else {
        confirmErrorSpan.style.display = 'none';
        confirmGroup.classList.remove('error');
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});
</script>