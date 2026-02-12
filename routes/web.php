<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\AdminNotificacionController;
use App\Http\Controllers\SolicitudController;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AdminBitacoraController;

// 1. PÁGINA DE BIENVENIDA
Route::get('/', function () {
    return view('welcome');
});

// 2. SOLICITUD DE ACCESO (PÚBLICO)
Route::get('/solicitar-acceso', [SolicitudController::class, 'create'])->name('solicitud.create');
Route::post('/solicitar-acceso', [SolicitudController::class, 'store'])->name('solicitud.store');

// 3. DASHBOARD Y PERFIL (Auth)
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

// 4. CIUDADANO: Casilla
Route::middleware(['auth'])->group(function () {
    Route::get('/casilla', [NotificacionController::class, 'index'])->name('casilla.index');
    Route::get('/notificacion/descargar/{id}', [NotificacionController::class, 'descargar'])->name('casilla.descargar');
});

// ... otras rutas ...

// 5. SECCIÓN: ADMINISTRADOR (Gestión Completa)
// ...
// <--- 1. ASEGÚRATE DE QUE ESTO ESTÉ ARRIBA

// ...

// 5. SECCIÓN: ADMINISTRADOR
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // BANDEJA (Historial de Notificaciones)
    Route::get('/notificaciones', [AdminNotificacionController::class, 'index'])->name('admin.index');
    
    // Enviar Notificación
    Route::get('/enviar', [AdminNotificacionController::class, 'create'])->name('admin.crear');
    Route::post('/enviar', [AdminNotificacionController::class, 'store'])->name('admin.store');

    // Peticiones
    Route::get('/peticiones', [AdminNotificacionController::class, 'peticiones'])->name('admin.peticiones');
    Route::post('/peticiones/aprobar-pdf/{id}', [AdminNotificacionController::class, 'aprobarYGenerarPdf'])->name('admin.aprobar.pdf');

    // AUDITORÍA (Esta es la que te faltaba o estaba mal)
    Route::get('/bitacora', [AdminBitacoraController::class, 'index'])->name('admin.bitacora');
});


// 8. DESCARGA SEGURA DE DOCUMENTOS (Nueva funcionalidad)
// Esta ruta sirve los archivos desde storage/app/notificaciones (privado)
Route::get('/documento/seguro/{id}', function ($id) {
    
    // Usamos el modelo importado arriba
    $notificacion = Notificacion::findOrFail($id);

    // 1. SEGURIDAD: Verificar que el usuario sea el dueño O sea Admin
    if (auth()->id() !== $notificacion->user_id && !auth()->user()->is_admin) {
        abort(403, 'No tiene permiso para ver este documento.');
    }

    // 2. Servir el archivo privado
    // IMPORTANTE: Asegúrate de que el archivo exista en 'storage/app/notificaciones/...'
    if (!Storage::disk('local')->exists($notificacion->ruta_archivo_pdf)) {
        abort(404, 'El archivo no se encuentra en el servidor.');
    }

    return Storage::disk('local')->download($notificacion->ruta_archivo_pdf);

})->middleware(['auth'])->name('documento.seguro');