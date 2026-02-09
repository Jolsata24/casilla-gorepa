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

// ESTA ES LA RUTA QUE DEBE APARECER EN TU LISTA
Route::get('/dni/info/{dni}', function ($dni) {
    $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI0MDMzOCIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6ImNvbnN1bHRvciJ9.jizzTXiQo8kYYWzUA0uM2jVvTh0KbO5byEbwoRlyNZA'; 

    $response = Http::withToken($token)
                    ->get("https://api.factiliza.com/v1/dni/info/{$dni}");

    if ($response->successful()) {
        $result = $response->json();
        if (isset($result['data'])) {
            return response()->json([
                'success' => true,
                'data' => $result['data'] // Enviamos todo el objeto
            ]);
        }
    }
    return response()->json(['success' => false], 404);
});