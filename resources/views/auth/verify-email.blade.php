<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Gracias por registrarte. Antes de continuar, verifica tu correo electrónico haciendo clic en el enlace que te enviamos.
        Si no recibiste el correo, puedes solicitar uno nuevo.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm text-green-600">
            Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit">
                Reenviar correo de verificación
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-guest-layout>