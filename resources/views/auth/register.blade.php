<x-guest-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    @endpush

    <div class="register-container">
        <h2 class="register-title">Crear Cuenta</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Nombre y Apellido (ahora en dos columnas) -->
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

            <!-- Correo -->
            <div class="form-group">
                <label for="email" class="form-label">Correo</label>
                <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Contraseña -->
            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" class="form-input" type="password" name="password" required>
                <small class="password-hint">Mínimo 8 caracteres, 1 mayúscula y 1 número</small>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirmar Contraseña -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required>
            </div>

            <!-- Términos -->
            <div class="terms-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms" required>
                    <span>Acepto los términos y condiciones</span>
                </label>
            </div>

            <!-- Botones -->
            <button type="submit" class="register-btn">Registrarse</button>

            <div class="login-link">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
            </div>
        </form>
    </div>
</x-guest-layout>