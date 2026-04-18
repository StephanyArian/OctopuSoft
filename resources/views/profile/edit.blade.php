<x-app-layout>
    <x-slot name="header">
        <h2 style="color: #1de8c0; font-weight: 700; font-size: 20px;">
            {{ __('Perfil') }}
        </h2>
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/updateContra.css') }}">
    @endpush

    <style>
        .magic-card {
            border-radius: 16px;
            border: 1px solid #0abf9e;
            background: linear-gradient(135deg, rgba(29, 232, 192, 0.25), rgba(125, 211, 252, 0.25));
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 28px;
            margin-bottom: 24px;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="magic-card">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="magic-card">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="magic-card">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>