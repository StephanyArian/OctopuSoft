<x-guest-layout>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: #c8ece8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-wrapper { width: 100%; max-width: 500px; padding: 20px; }

        .logout-card {
            background: #3b1730;
            border-radius: 20px;
            padding: 35px 30px;
            box-shadow: 0 18px 45px rgba(0,0,0,0.28);
            text-align: center;
            position: relative;
        }

        .back-link {
            position: absolute;
            left: 25px;
            top: 20px;
            text-decoration: none;
            color: #f7dfe9;
            font-size: 14px;
            font-weight: 500;
        }
        .back-link:hover { color: #fff; }

        .steps {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
            margin-bottom: 25px;
        }

        .step {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
        }
        .step.active { background: #18d5cf; }

        .icon-circle {
            width: 72px;
            height: 72px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        h1 {
            font-size: 28px;
            color: #fff;
            margin-bottom: 10px;
            font-weight: 700;
        }

        p {
            font-size: 14px;
            color: #f3d9e4;
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            color: #f3d9e4;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 11px 14px;
            border-radius: 9px;
            border: 1px solid rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.08);
            color: #fff;
            font-size: 14px;
            font-family: Arial, sans-serif;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input:focus { border-color: #18d5cf; }

        .form-group input::placeholder { color: rgba(255,255,255,0.3); }

        .form-group input[readonly] {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .error-msg {
            font-size: 12px;
            color: #f87a7a;
            margin-top: 5px;
        }

        .gradient-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(90deg, #12bdf0, #18d5b8);
            color: #111;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 8px 20px rgba(24,213,184,0.25);
            margin-top: 6px;
        }

        .gradient-btn:hover {
            transform: translateY(-2px);
            opacity: 0.96;
        }

        .divider {
            height: 1px;
            background: rgba(255,255,255,0.12);
            margin: 25px 0 20px;
        }

        .bottom-text {
            font-size: 14px;
            color: #f3d9e4;
        }

        .bottom-text a {
            color: #18d5cf;
            text-decoration: none;
            font-weight: 700;
        }

        .bottom-text a:hover { color: #fff; }
    </style>

    <div class="logout-wrapper">
        <div class="logout-card">

            <a href="{{ route('login') }}" class="back-link">← Volver</a>

            <div class="steps">
                <div class="step active"></div>
                <div class="step"></div>
                <div class="step"></div>
            </div>

            <div class="icon-circle">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                    stroke="#18d5cf" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="11" width="14" height="10" rx="2"/>
                    <path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                </svg>
            </div>

            <h1>Restablecer contraseña</h1>
            <p>Ingresa tu nueva contraseña para recuperar el acceso a tu cuenta.</p>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input id="email" type="email" name="email"
                        value="{{ old('email', $email) }}"
                        readonly />
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nueva contraseña -->
                <div class="form-group">
                    <label for="password">Nueva contraseña</label>
                    <input id="password" type="password" name="password"
                        placeholder="••••••••" required />
                    @error('password')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmar contraseña -->
                <div class="form-group">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <input id="password_confirmation" type="password"
                        name="password_confirmation"
                        placeholder="••••••••" required />
                    @error('password_confirmation')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="gradient-btn">
                    Restablecer contraseña
                </button>
            </form>

            <div class="divider"></div>
            <p class="bottom-text">
                ¿Recordaste tu contraseña?
                <a href="{{ route('login') }}">Iniciar sesión</a>
            </p>

        </div>
    </div>
</x-guest-layout>