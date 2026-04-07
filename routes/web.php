<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
     ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
     ->name('password.email');
// Si usas Laravel Breeze o Fortify, la ruta ya existe automáticamente.
// Si la manejas manualmente:
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.RecupCont', ['token' => $token]);
})->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->name('password.update');
    });
  
require __DIR__.'/auth.php';
