<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Esta es un área segura de la aplicación. Confirma tu contraseña antes de continuar.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <label for="password">Contraseña</label>

            <input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />

            @error('password')
                <div class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="flex justify-end mt-4">
            <button type="submit">
                Confirmar
            </button>
        </div>
    </form>
</x-guest-layout>