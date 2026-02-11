<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MtcCallbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas para integración con MTC (Casilla Electrónica)
// Según documentación técnica del MTC (Capítulo 5)

// 1. Constancia de Depósito (El MTC nos entrega la notificación y nosotros devolvemos el cargo firmado)
Route::post('/constancia-deposito', [MtcCallbackController::class, 'firmarDeposito']);

// 2. Constancia de Lectura (Similar al depósito, pero confirma el acto de leer)
Route::post('/constancia-lectura', [MtcCallbackController::class, 'firmarLectura']);

// 3. Acuse de Lectura (Solo actualización de estado)
Route::post('/acuse-lectura', [MtcCallbackController::class, 'acuseLectura']);