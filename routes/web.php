<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\AdminNotificacionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

// 1. PÁGINA DE BIENVENIDA
Route::get('/', function () {
    return view('welcome');
});

// 2. DASHBOARD GENERAL (Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. PERFIL DE USUARIO (Protegido por Auth)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// 4. SECCIÓN: CIUDADANO (Casilla Electrónica GOREPA)
Route::middleware(['auth'])->group(function () {
    Route::get('/casilla', [NotificacionController::class, 'index'])
        ->name('casilla.index');

    Route::get('/notificacion/descargar/{id}', [NotificacionController::class, 'descargar'])
        ->name('casilla.descargar');
});

// 5. SECCIÓN PÚBLICA: SOLICITUD DE ACCESO
// Estas rutas permiten a ciudadanos sin cuenta enviar sus datos para revisión
// Nota: Requiere crear un SolicitudController o usar AdminNotificacionController
Route::get('/solicitar-acceso', [AdminNotificacionController::class, 'createSolicitud'])->name('solicitud.create');
Route::post('/solicitar-acceso', [AdminNotificacionController::class, 'storeSolicitud'])->name('solicitud.store');

// 6. SECCIÓN: ADMINISTRADOR (Gestión GOREPA)
// Usamos prefijo 'admin' para que todas las rutas empiecen con /admin/...
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // Gestión de Notificaciones
    Route::get('/notificaciones', [AdminNotificacionController::class, 'index'])->name('admin.index');
    Route::get('/enviar', [AdminNotificacionController::class, 'create'])->name('admin.crear');
    Route::post('/enviar', [AdminNotificacionController::class, 'store'])->name('admin.store');

    // Gestión de Peticiones de Acceso
    Route::get('/peticiones', [AdminNotificacionController::class, 'peticiones'])->name('admin.peticiones');
    
    // RUTA PARA APROBAR: Cambia el estado del usuario para que pueda entrar
    Route::post('/peticiones/aprobar/{id}', [AdminNotificacionController::class, 'aprobarUsuario'])->name('admin.aprobar');
});

// 7. UTILIDADES: CONSULTA DE DNI (API Factiliza)
Route::get('/dni/info/{dni}', function ($dni) {
    $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI0MDMzOCIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6ImNvbnN1bHRvciJ9.jizzTXiQo8kYYWzUA0uM2jVvTh0KbO5byEbwoRlyNZA'; 

    $response = Http::withToken($token)
                    ->get("https://api.factiliza.com/v1/dni/info/{$dni}");

    if ($response->successful()) {
        $result = $response->json();
        if (isset($result['data'])) {
            return response()->json([
                'success' => true,
                'data' => $result['data']
            ]);
        }
    }
    return response()->json(['success' => false], 404);
});
