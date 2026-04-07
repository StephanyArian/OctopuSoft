<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Ruta de inicio
Route::get('/', function () {
    return view('home.index');
})->name('home');

// Registro
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])
         ->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

// Página que avisa al usuario que verifique su correo
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Procesa el clic en el enlace del correo
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('dashboard')
                     ->with('success', 'Correo verificado correctamente.');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Reenviar correo de verificación
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Correo de verificación reenviado.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Dashboard (protegido con auth)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
});