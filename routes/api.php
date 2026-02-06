<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/consulta-dni/{dni}', function ($dni) {
    // Obtenemos el token desde el archivo .env
    $token = env('APIDNI_TOKEN');

    // Hacemos la petición de forma elegante (estilo Laravel)
    $response = Http::withToken($token)
                    ->get("https://apidni.com/api/v2/dni/{$dni}");

    if ($response->successful()) {
        $data = $response->json();
        
        // Ajustamos la respuesta según lo que devuelve apidni.com
        return response()->json([
            'success' => true,
            'nombre_completo' => $data['nombres'] . ' ' . $data['apellidoPaterno'] . ' ' . $data['apellidoMaterno']
        ]);
    }

    return response()->json(['success' => false, 'message' => 'DNI no encontrado'], 404);
}); 