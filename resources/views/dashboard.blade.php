<h1>Dashboard 🎉</h1>

<p>Bienvenido {{ auth()->user()->name }}</p>

@if(session('success'))
    <p>{{ session('success') }}</p>
@endif