<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tu CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body class="bg-auth min-h-screen flex items-center justify-center">

<div class="w-full max-w-md p-8 rounded-2xl shadow-xl card-auth">

    <!-- TÍTULO -->
    <h2 class="text-2xl font-bold text-center mb-6 text-white">
        Crear Cuenta
    </h2>

    <!-- ERRORES GENERALES -->
    @if($errors->any())
        <div class="bg-red-400/20 text-red-200 p-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORMULARIO -->
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- NOMBRE -->
        <div>
            <label class="label-auth">Nombre</label>
            <input 
                type="text" 
                name="name" 
                value="{{ old('name') }}"
                class="input-auth @error('name') border-red-400 @enderror"
                placeholder="Tu nombre"
                required
            >
            @error('name')
                <p class="text-red-300 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- APELLIDO -->
        <div>
            <label class="label-auth">Apellido</label>
            <input 
                type="text" 
                name="last_name" 
                value="{{ old('last_name') }}"
                class="input-auth @error('last_name') border-red-400 @enderror"
                placeholder="Tu apellido"
                required
            >
            @error('last_name')
                <p class="text-red-300 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- EMAIL -->
        <div>
            <label class="label-auth">Correo</label>
            <input 
                type="email" 
                name="email" 
                value="{{ old('email') }}"
                class="input-auth @error('email') border-red-400 @enderror"
                placeholder="ejemplo@email.com"
                required
            >
            @error('email')
                <p class="text-red-300 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- PASSWORD -->
        <div>
            <label class="label-auth">Contraseña</label>
            <input 
                type="password" 
                name="password"
                class="input-auth @error('password') border-red-400 @enderror"
                placeholder="********"
                required
            >
            <p class="text-xs text-muted mt-1">
                Mínimo 8 caracteres, 1 mayúscula y 1 número
            </p>
            @error('password')
                <p class="text-red-300 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- CONFIRMAR PASSWORD -->
        <div>
            <label class="label-auth">Confirmar contraseña</label>
            <input 
                type="password" 
                name="password_confirmation"
                class="input-auth"
                placeholder="********"
                required
            >
        </div>

        <!-- TÉRMINOS -->
        <div class="flex items-center text-gray-300 text-sm">
            <input 
                type="checkbox" 
                name="terms" 
                class="mr-2 accent-teal-400"
                required
            >
            Acepto los términos y condiciones
        </div>

        @error('terms')
            <p class="text-red-300 text-sm">{{ $message }}</p>
        @enderror

        <!-- BOTÓN -->
        <button type="submit" class="w-full btn-auth">
            Registrarse
        </button>

    </form>

    <!-- LOGIN -->
    <p class="text-center text-sm mt-6 text-gray-300">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" class="text-teal-300 hover:underline">
            Inicia sesión
        </a>
    </p>

</div>

</body>
</html>