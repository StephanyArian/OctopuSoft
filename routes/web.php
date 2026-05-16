<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\InformacionAcademicaController;
use App\Http\Controllers\ExperienciaLaboralController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\RedContactoController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\EvidenciaController;
use App\Models\Portfolio;


// ============================================
// RUTA PRINCIPAL (HOME) - CON PORTAFOLIOS
// ============================================
Route::get('/', function () {
    $portfolios = Portfolio::where('is_public', true)
        ->with(['user.skills', 'user.profession'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    return view('welcome', compact('portfolios'));
})->name('home');

Route::get('/dashboard', function () {
    return response()
        ->view('dashboard')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
})->middleware(['auth', 'verified'])->name('dashboard');

// RESET PASSWORD
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => request()->email,
    ]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')->name('password.store');

// ============================================
// RUTAS PÚBLICAS - PORTAFOLIO (HU-16)
// ============================================
Route::get('/portafolio/{slug}', [App\Http\Controllers\PreviewController::class, 'publicShow'])
    ->name('portafolio.public');

// ============================================
// RUTAS PROTEGIDAS (requieren autenticación)
// ============================================
Route::middleware('auth')->group(function () {

    // PERFIL
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');

    Route::post('/logout-others', [SessionController::class, 'logoutOtherDevices'])->name('logout.others');

    Route::get('/cerrar-sesion', function () {
        return response()
            ->view('auth.cerrar-sesion')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    })->name('cerrar.sesion');

    // INFORMACIÓN ACADÉMICA
    Route::get('/informacion-academica',         [InformacionAcademicaController::class, 'index'])->name('informacion.academica');
    Route::post('/informacion-academica',        [InformacionAcademicaController::class, 'store'])->name('informacion.academica.store');
    Route::post('/informacion-academica/{id}',   [InformacionAcademicaController::class, 'update']);
    Route::put('/informacion-academica/{id}',    [InformacionAcademicaController::class, 'update'])->name('informacion.academica.update');
    Route::delete('/informacion-academica/{id}', [InformacionAcademicaController::class, 'destroy'])->name('informacion.academica.destroy');

    // EXPERIENCIA LABORAL
    Route::get('/experiencia-laboral',          [ExperienciaLaboralController::class, 'index'])->name('experiencia.laboral');
    Route::post('/experiencia-laboral',         [ExperienciaLaboralController::class, 'store'])->name('experiencia.laboral.store');
    Route::put('/experiencia-laboral/{id}',     [ExperienciaLaboralController::class, 'update'])->name('experiencia.laboral.update');
    Route::delete('/experiencia-laboral/{id}',  [ExperienciaLaboralController::class, 'destroy'])->name('experiencia.laboral.destroy');

    // PERFIL (CREAR)
    Route::get('/perfil',  [ProfileController::class, 'create'])->name('profile.create');
    Route::post('/perfil', [ProfileController::class, 'store'])->name('profile.store');

    // SKILLS
    Route::get('/habilidades-tecnicas', [SkillController::class, 'tecnicas'])->name('skills.tecnicas');
    Route::get('/habilidades-blandas',  [SkillController::class, 'blandas'])->name('skills.blandas');
    Route::post('/skills',              [SkillController::class, 'store'])->name('skills.store');
    Route::put('/skills/{skill}',       [SkillController::class, 'update'])->name('skills.update');
    Route::delete('/skills/{skill}',    [SkillController::class, 'destroy'])->name('skills.destroy');
    //Evidencia de los skill con proyectos 
    Route::post('/skills/{skill}/projects',           [SkillController::class, 'attachProject'])->name('skills.projects.attach');
    Route::delete('/skills/{skill}/projects/{project}', [SkillController::class, 'detachProject'])->name('skills.projects.detach');

    // REDES
    Route::get('/redes-contacto',  [RedContactoController::class, 'index'])->name('redes.index');
    Route::post('/redes-contacto', [RedContactoController::class, 'store'])->name('redes.store');

    // PROYECTOS (VISTA)
    Route::get('/mis-proyectos', function () {
        return view('proyectos');
    })->name('proyectos');

    // PROYECTOS (API)
    Route::get('/proyectos',                          [ProyectoController::class, 'index'])->name('proyectos.index');
    Route::post('/proyectos',                         [ProyectoController::class, 'store'])->name('proyectos.store');
    Route::put('/proyectos/{id}',                     [ProyectoController::class, 'update'])->name('proyectos.update');
    Route::patch('/proyectos/{id}/visibilidad',       [ProyectoController::class, 'toggleVisibilidad'])->name('proyectos.toggleVisibilidad');
    Route::delete('/proyectos/{id}',                  [ProyectoController::class, 'destroy'])->name('proyectos.destroy');

    // EVIDENCIAS
    Route::get('/mis-evidencias', function () {
        return view('evidencia');
    })->name('evidencias');

    Route::get('/proyectos/{proyectoId}/evidencias',  [EvidenciaController::class, 'index'])->name('evidencias.index');
    Route::post('/proyectos/{proyectoId}/evidencias', [EvidenciaController::class, 'store'])->name('evidencias.store');
    Route::delete('/evidencias/{id}',                 [EvidenciaController::class, 'destroy'])->name('evidencias.destroy');

    // VISTA Preview
    Route::get('/preview', [App\Http\Controllers\PreviewController::class, 'preview'])
    ->middleware(['auth'])
    ->name('preview');

    Route::post('/perfil/publicar', [ProfileController::class, 'publish'])->name('perfil.publicar');
});

require __DIR__.'/auth.php';