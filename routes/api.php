<?php

use App\Http\Controllers\Api\MtcCallbackController; // 

// ... ruta de DNI ...

// Rutas obligatorias según el PDF (Capítulo 5) [cite: 326, 390]
Route::post('/constancia-deposito', [MtcCallbackController::class, 'firmarDeposito']); // 
Route::post('/constancia-lectura', [MtcCallbackController::class, 'firmarDeposito']);   // 
Route::post('/acuse-lectura', [MtcCallbackController::class, 'acuseLectura']);         //