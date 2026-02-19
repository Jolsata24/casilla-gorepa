<?php

// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MtcCallbackController;
use App\Http\Controllers\Api\RecepcionNotificacionController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// APLICAMOS EL MIDDLEWARE AQUÍ
Route::middleware(['mtc.auth'])->group(function () {
    
    // 1. Constancia de Depósito
    Route::post('/constancia-deposito', [MtcCallbackController::class, 'firmarDeposito']);

    // 2. Constancia de Lectura
    Route::post('/constancia-lectura', [MtcCallbackController::class, 'firmarLectura']);

    // 3. Acuse de Lectura
    Route::post('/acuse-lectura', [MtcCallbackController::class, 'acuseLectura']);

    Route::middleware('auth:sanctum')->post('/inbound/notificaciones', [RecepcionNotificacionController::class, 'recibir']);
});