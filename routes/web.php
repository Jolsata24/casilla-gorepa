<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\AdminNotificacionController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\AdminBitacoraController; // Importante para la auditoría
use App\Models\Notificacion;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

// 1. PÁGINA DE BIENVENIDA
Route::get('/', function () {
    return view('welcome');
});

// 2. SOLICITUD DE ACCESO (PÚBLICO)
Route::get('/solicitar-acceso', [SolicitudController::class, 'create'])->name('solicitud.create');
Route::post('/solicitar-acceso', [SolicitudController::class, 'store'])->name('solicitud.store');

// 3. DASHBOARD Y PERFIL (Requiere Login)
Route::get('/dashboard', function () {
    // Redireccionar según rol
    if(auth()->user()->is_admin) {
        return redirect()->route('admin.peticiones');
    }
    return redirect()->route('casilla.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// 4. CIUDADANO: Casilla Electrónica
Route::middleware(['auth'])->group(function () {
    Route::get('/casilla', [NotificacionController::class, 'index'])->name('casilla.index');
    Route::get('/notificacion/descargar/{id}', [NotificacionController::class, 'descargar'])->name('casilla.descargar');
});

// 5. SECCIÓN: ADMINISTRADOR (Gestión Completa)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // BANDEJA (Historial de Notificaciones)
    Route::get('/notificaciones', [AdminNotificacionController::class, 'index'])->name('admin.index');
    
    // Enviar Notificación
    Route::get('/enviar', [AdminNotificacionController::class, 'create'])->name('admin.crear');
    Route::post('/enviar', [AdminNotificacionController::class, 'store'])->name('admin.store');

    // Peticiones de Acceso
    Route::get('/peticiones', [AdminNotificacionController::class, 'peticiones'])->name('admin.peticiones');
    Route::post('/peticiones/aprobar-pdf/{id}', [AdminNotificacionController::class, 'aprobarYGenerarPdf'])->name('admin.aprobar.pdf');

    // AUDITORÍA / LOGS (Bitácora)
    Route::get('/bitacora', [AdminBitacoraController::class, 'index'])->name('admin.bitacora');
});

// 6. DESCARGA SEGURA DE DOCUMENTOS (Ruta Privada)
Route::get('/documento/seguro/{id}', function ($id) {
    
    $notificacion = Notificacion::findOrFail($id);

    // Seguridad: Solo el dueño o el admin pueden ver el archivo
    if (auth()->id() !== $notificacion->user_id && !auth()->user()->is_admin) {
        abort(403, 'No tiene permiso para ver este documento.');
    }

    // Verificar existencia física
    if (!Storage::disk('local')->exists($notificacion->ruta_archivo_pdf)) {
        abort(404, 'El archivo no se encuentra en el servidor.');
    }

    return Storage::disk('local')->download($notificacion->ruta_archivo_pdf);

})->middleware(['auth'])->name('documento.seguro');

// 7. CONSULTA DNI (PÚBLICA PARA VALIDACIÓN)
Route::get('/dni/info/{dni}', function ($dni) {
    
    $token = env('FACTILIZA_TOKEN'); 

    if (!$token) {
        return response()->json(['success' => false, 'message' => 'Falta configurar el Token'], 500);
    }

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