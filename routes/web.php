<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\AdminNotificacionController;
use App\Http\Controllers\SolicitudController;
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
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // Panel Principal y Notificaciones
    Route::get('/notificaciones', [AdminNotificacionController::class, 'index'])->name('admin.index');
    
    // Enviar Notificación (Formulario y Guardado)
    Route::get('/enviar', [AdminNotificacionController::class, 'create'])->name('admin.crear');
    Route::post('/enviar', [AdminNotificacionController::class, 'store'])->name('admin.store');

    // Gestión de Peticiones de Acceso
    Route::get('/peticiones', [AdminNotificacionController::class, 'peticiones'])->name('admin.peticiones');
    
    // ACCIÓN CLAVE: Aprobar y Generar PDF (Apunta a tu función 'aprobarYGenerarPdf')
    Route::post('/peticiones/aprobar-pdf/{id}', [AdminNotificacionController::class, 'aprobarYGenerarPdf'])
        ->name('admin.aprobar.pdf');
});

// ... Rutas de API y Documento Seguro se mantienen igual ...
// 7. UTILIDADES: CONSULTA DE DNI (API Factiliza)
Route::get('/dni/info/{dni}', function ($dni) {
    // Recuerda mover este token al .env en producción
    $token = env('FACTILIZA_TOKEN', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI0MDMzOCIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6ImNvbnN1bHRvciJ9.jizzTXiQo8kYYWzUA0uM2jVvTh0KbO5byEbwoRlyNZA'); 

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