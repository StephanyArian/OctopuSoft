<?php

use App\Http\Controllers\Auth\RegisterController;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');
