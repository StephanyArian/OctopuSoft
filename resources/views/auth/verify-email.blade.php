<h2>Verifica tu correo 📧</h2>

<p>Revisa tu correo y haz clic en el enlace de verificación.</p>

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit">Reenviar correo</button>
</form>

@if(session('success'))
    <p>{{ session('success') }}</p>
@endif