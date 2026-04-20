<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\InformacionAcademicaController;
use App\Http\Controllers\SkillController;

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

    // Informacion academica -
    
    Route::get('/informacion-academica', [InformacionAcademicaController::class, 'index'])->name('informacion.academica');
    Route::post('/informacion-academica', [InformacionAcademicaController::class, 'store'])->name('informacion.academica.store');
    Route::put('/informacion-academica/{id}', [InformacionAcademicaController::class, 'update'])->name('informacion.academica.update');
    Route::delete('/informacion-academica/{id}', [InformacionAcademicaController::class, 'destroy'])->name('informacion.academica.destroy');
    

     //  Perfil personal (dashboard) 
    Route::get('/perfil', [ProfileController::class, 'create'])->name('profile.create'); 
    Route::post('/perfil', [ProfileController::class, 'store'])->name('profile.store');    

    // Skill tecnicas y blandas
    Route::get('/habilidades-tecnicas', [SkillController::class, 'tecnicas'])->name('skills.tecnicas');
    Route::get('/habilidades-blandas',  [SkillController::class, 'blandas'])->name('skills.blandas');
    Route::post('/skills',              [SkillController::class, 'store'])->name('skills.store');
    Route::put('/skills/{skill}',       [SkillController::class, 'update'])->name('skills.update');
    Route::delete('/skills/{skill}',    [SkillController::class, 'destroy'])->name('skills.destroy');

});

require __DIR__.'/auth.php';