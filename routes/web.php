<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\AdminNotificacionController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\AdminBitacoraController;
use App\Http\Controllers\CasillaController; // <--- ¡IMPORTANTE! Agregado
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

// 3. DASHBOARD Y PERFIL (Redireccionamiento inteligente)
Route::get('/dashboard', function () {
    // Redireccionar según rol
    if(auth()->user()->is_admin) {
        return redirect()->route('admin.peticiones'); // O admin.index según prefieras
    }
    return redirect()->route('casilla.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// 4. CIUDADANO: Casilla Electrónica (NUEVO SISTEMA TIPO GMAIL)
// He eliminado el bloque antiguo de 'NotificacionController' porque este lo reemplaza.
Route::middleware(['auth'])->group(function () {
    Route::get('/casilla', [CasillaController::class, 'index'])->name('casilla.index');
    Route::post('/casilla/etiqueta', [CasillaController::class, 'crearEtiqueta'])->name('casilla.etiqueta.store');
    Route::post('/casilla/mover/{id}', [CasillaController::class, 'mover'])->name('casilla.mover');
    Route::post('/casilla/destacar/{id}', [CasillaController::class, 'toggleDestacado'])->name('casilla.destacar');
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

    Route::get('/cargo/{id}', [AdminNotificacionController::class, 'descargarCargo'])->name('admin.cargo');
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

    // Registrar lectura si es el dueño quien descarga (Opcional, pero recomendado)
    if (auth()->id() === $notificacion->user_id && is_null($notificacion->fecha_lectura)) {
        $notificacion->update([
            'fecha_lectura' => now(),
            'ip_lectura' => request()->ip()
        ]);
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