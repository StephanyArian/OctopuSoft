<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/updateContra.css') }}">
    @endpush

    <div class="profile-page">
        <div class="profile-container">
            <div class="profile-page-title">Configuración</div>
            <p class="profile-page-subtitle">Administra tu información de cuenta y seguridad.</p>

            <div class="profile-card">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="profile-card">
                @include('profile.partials.update-password-form')
            </div>

            <div class="profile-card profile-card-danger">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
</x-app-layout>