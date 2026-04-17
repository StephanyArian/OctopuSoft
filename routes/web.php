<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return response()
        ->view('dashboard')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => request()->email,
    ]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');


Route::middleware('auth')->group(function () {
    // ========== RUTAS PARA CREAR PERFIL (HU-05) ==========
    Route::get('/profile/create', [ProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile/store', [ProfileController::class, 'store'])->name('profile.store');
    // ====================================================
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/logout-others', [SessionController::class, 'logoutOtherDevices'])
        ->name('logout.others');

    Route::get('/cerrar-sesion', function () {
        return response()
            ->view('auth.cerrar-sesion')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    })->name('cerrar.sesion');

    Route::get('/informacion-academica', function () {
        return view('informacion-academica');
    })->middleware(['auth'])->name('informacion.academica');
});

require __DIR__.'/auth.php';