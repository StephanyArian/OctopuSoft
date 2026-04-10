<x-guest-layout>
    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    @endpush

    <div class="register-container">
        <h2 class="register-title">Crear Cuenta</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="row-fields">
                <div class="form-group">
                    <label for="first_name" class="form-label">Nombre</label>
                    <input id="first_name" class="form-input" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus>
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>

                <div class="form-group">
                    <label for="last_name" class="form-label">Apellido</label>
                    <input id="last_name" class="form-input" type="text" name="last_name" value="{{ old('last_name') }}" required>
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Correo</label>
                <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Contraseña con ojito -->
            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <div class="password-wrapper">
                    <input id="password" class="form-input" type="password" name="password" required>
                    <button class="toggle-password" type="button" onclick="togglePw('password', this)">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <small class="password-hint">Mínimo 8 caracteres, 1 mayúscula y 1 número</small>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirmar contraseña con ojito -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                <div class="password-wrapper">
                    <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required>
                    <button class="toggle-password" type="button" onclick="togglePw('password_confirmation', this)">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <div class="terms-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms" required>
                    <span>Acepto los términos y condiciones</span>
                </label>
            </div>

            <button type="submit" class="register-btn">Registrarse</button>

            <div class="login-link">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
            </div>
        </form>
    </div>

    <script>
    function togglePw(id, btn) {
        const inp = document.getElementById(id);
        const isPassword = inp.type === 'password';
        inp.type = isPassword ? 'text' : 'password';
        btn.innerHTML = isPassword
            ? `<svg viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                   <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                   <line x1="1" y1="1" x2="23" y2="23"/>
               </svg>`
            : `<svg viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                   <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                   <circle cx="12" cy="12" r="3"/>
               </svg>`;
    }
    </script>

</x-guest-layout>