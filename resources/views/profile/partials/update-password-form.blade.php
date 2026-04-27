<section class="password-section">
    <h2 class="password-title">Cambiar contraseña</h2>

    <form method="post" action="{{ route('password.update') }}" class="password-form" id="passwordForm">
        @csrf
        @method('put')

        @if (session('status') === 'password-updated')
            <div class="alert-success">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 15 4 10"/></svg>
                Contraseña actualizada correctamente.
            </div>
        @endif

        <div class="field-group">
            <label class="field-label">Contraseña actual <span class="required">*</span></label>
            <div class="input-wrapper">
                <input id="current_password" name="current_password" type="password"
                    class="field-input {{ $errors->updatePassword->has('current_password') ? 'input-error-state' : '' }}"
                    placeholder="Ingresa tu contraseña actual" autocomplete="current-password">
                <button type="button" class="eye-btn" onclick="togglePassword('current_password', this)">
                    <svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            @foreach($errors->updatePassword->get('current_password') as $error)
                <span class="field-error">{{ $error }}</span>
            @endforeach
        </div>

        <div class="field-group">
            <label class="field-label">Nueva contraseña <span class="required">*</span></label>
            <div class="input-wrapper">
                <input id="new_password" name="password" type="password"
                    class="field-input {{ $errors->updatePassword->has('password') ? 'input-error-state' : '' }}"
                    placeholder="Mínimo 8 caracteres" autocomplete="new-password"
                    oninput="checkStrength(this.value); checkMatch()">
                <button type="button" class="eye-btn" onclick="togglePassword('new_password', this)">
                    <svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <div class="strength-bar">
                <div class="strength-track"><div id="strengthFill" class="strength-fill"></div></div>
                <span id="strengthLabel" class="strength-label">Escribe una contraseña para ver su fortaleza</span>
            </div>
            <div class="requirements">
                <div class="req-item" id="req-length"><span class="req-dot"></span> Mínimo 8 caracteres</div>
                <div class="req-item" id="req-upper"><span class="req-dot"></span> Una mayúscula</div>
                <div class="req-item" id="req-lower"><span class="req-dot"></span> Una minúscula</div>
                <div class="req-item" id="req-number"><span class="req-dot"></span> Un número</div>
                <div class="req-item" id="req-special"><span class="req-dot"></span> Un carácter especial</div>
            </div>
            @foreach($errors->updatePassword->get('password') as $error)
                <span class="field-error">{{ $error }}</span>
            @endforeach
        </div>

        <div class="field-group">
            <label class="field-label">Confirmar nueva contraseña <span class="required">*</span></label>
            <div class="input-wrapper">
                <input id="confirm_password" name="password_confirmation" type="password"
                    class="field-input {{ $errors->updatePassword->has('password_confirmation') ? 'input-error-state' : '' }}"
                    placeholder="Repite tu nueva contraseña" autocomplete="new-password"
                    oninput="checkMatch()">
                <button type="button" class="eye-btn" onclick="togglePassword('confirm_password', this)">
                    <svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <span id="matchError" class="field-error hidden">Las contraseñas no coinciden.</span>
            @foreach($errors->updatePassword->get('password_confirmation') as $error)
                <span class="field-error">{{ $error }}</span>
            @endforeach
        </div>

        <button type="submit" class="btn-save">Guardar contraseña</button>
        <button type="button" class="btn-cancel" onclick="resetForm()">Cancelar</button>
    </form>
</section>

<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.querySelector('.eye-icon').style.opacity = isText ? '1' : '0.4';
}

function checkStrength(val) {
    const fill = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    const checks = {
        'req-length':  val.length >= 8,
        'req-upper':   /[A-Z]/.test(val),
        'req-lower':   /[a-z]/.test(val),
        'req-number':  /[0-9]/.test(val),
        'req-special': /[^A-Za-z0-9]/.test(val),
    };
    let score = Object.values(checks).filter(Boolean).length;
    Object.entries(checks).forEach(([id, met]) => {
        document.getElementById(id).classList.toggle('met', met);
    });
    if (!val) {
        fill.style.width = '0%';
        label.textContent = 'Escribe una contraseña para ver su fortaleza';
        label.removeAttribute('data-level');
        return;
    }
    const levels = ['very-weak', 'weak', 'fair', 'good', 'strong'];
    const texts  = ['Muy débil', 'Débil', 'Regular', 'Buena', 'Muy fuerte'];
    fill.style.width = (score * 20) + '%';
    fill.setAttribute('data-level', levels[score - 1]);
    label.textContent = texts[score - 1];
    label.setAttribute('data-level', levels[score - 1]);
}

function checkMatch() {
    const np  = document.getElementById('new_password').value;
    const cp  = document.getElementById('confirm_password').value;
    const err = document.getElementById('matchError');
    const inp = document.getElementById('confirm_password');
    if (cp.length > 0 && np !== cp)  {
        err.classList.remove('hidden');
        inp.classList.add('input-error-state');
    } else {
        err.classList.add('hidden');
        inp.classList.remove('input-error-state');
    }
}

function resetForm() {
    document.getElementById('passwordForm').reset();
    document.getElementById('strengthFill').style.width = '0%';
    document.getElementById('strengthFill').removeAttribute('data-level');
    document.getElementById('strengthLabel').textContent = 'Escribe una contraseña para ver su fortaleza';
    document.getElementById('strengthLabel').removeAttribute('data-level');
    document.getElementById('matchError').classList.add('hidden');
    ['req-length','req-upper','req-lower','req-number','req-special'].forEach(id => {
        document.getElementById(id).classList.remove('met');
    });
}
</script>