<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\AdminNotificacionController;
use Illuminate\Support\Facades\Route;

// Página de bienvenida
Route::get('/', function () {
    return view('welcome');
});

// Dashboard general de Breeze
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil de usuario
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// --- SECCIÓN: CIUDADANO (Casilla Electrónica) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/casilla', [NotificacionController::class, 'index'])
        ->name('casilla.index');

    Route::get('/notificacion/descargar/{id}', [NotificacionController::class, 'descargar'])
        ->name('casilla.descargar');
});

// --- SECCIÓN: ADMINISTRADOR (Envío GOREPA) ---
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/notificaciones', [AdminNotificacionController::class, 'index'])
        ->name('admin.index');
    Route::get('/admin/enviar', [AdminNotificacionController::class, 'create'])
        ->name('admin.crear');

    Route::post('/admin/enviar', [AdminNotificacionController::class, 'store'])
        ->name('admin.store');
});

use Illuminate\Support\Facades\Http;

Route::get('/consulta-dni/{dni}', function ($dni) {
    // 1. Token de prueba (Cámbialo por el tuyo propio cuando te registres)
    // Te recomiendo registrarte en apisperu.com o apidni.com para obtener uno GRATIS.
    $token = 'TU_TOKEN_AQUI'; 

    // 2. Conexión a la API (Ejemplo con ApisPeru, ajusta la URL si usas otro)
    $response = Http::withToken($token)
                    ->get("https://api.apis.net.pe/v1/dni?numero={$dni}"); 
                    // OJO: La URL cambia según el proveedor. Revisa su documentación.

    if ($response->successful()) {
        $data = $response->json();
        
        // 3. Devolvemos solo lo que nos interesa
        // Ajustamos los campos según lo que devuelve ApisPeru
        return response()->json([
            'success' => true,
            'nombre_completo' => $data['nombres'] . ' ' . $data['apellidoPaterno'] . ' ' . $data['apellidoMaterno']
        ]);
    }

    return response()->json(['success' => false, 'message' => 'DNI no encontrado'], 404);
});