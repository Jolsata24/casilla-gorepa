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
});

require __DIR__.'/auth.php';


use App\Http\Controllers\NotificacionController;

// --- INICIO: Rutas de la Casilla Electrónica GOREPA ---

// Agrupamos estas rutas con 'auth' para que SOLO los usuarios logueados puedan entrar.
// Si alguien intenta entrar sin loguearse, Laravel lo mandará al Login automáticamente.
Route::middleware(['auth'])->group(function () {

    // 1. Ruta para ver la Bandeja de Entrada
    // Cuando entren a "tusitio.com/casilla", ejecuta el método 'index'
    Route::get('/casilla', [NotificacionController::class, 'index'])
        ->name('casilla.index');

    // 2. Ruta para Descargar Documentos (Segura)
    // El {id} es el número de la notificación que quieren bajar
    Route::get('/notificacion/descargar/{id}', [NotificacionController::class, 'descargar'])
        ->name('casilla.descargar');

});
// --- FIN: Rutas de la Casilla Electrónica ---