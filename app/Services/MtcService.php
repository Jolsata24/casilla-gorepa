<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MtcService
{
    public function enviarNotificacionExterna($usuario, $asunto, $mensaje, $rutaArchivo)
    {
        // 1. Obtener el Token (Página 1 del PDF)
        $authResponse = Http::get('https://dvwscasilla.mtc.gob.pe/ms-notific/v2/servicios-publicos/notificaciones/token', [
            'grantType' => 'authorization_code',
            'clientId' => config('services.mtc.client_id'),
            'clientSecret' => config('services.mtc.client_secret'),
        ]);

        if (!$authResponse->successful()) return $authResponse;

        $token = $authResponse->json()['accessToken'];

        // 2. Enviar la Notificación (Página 3 del PDF)
        return Http::withToken($token)
            ->post('https://dvwscasilla.mtc.gob.pe/ms-notific/v2/servicios-publicos/notificaciones-externas', [
                'codTipoPersona' => '00001', // Persona Natural
                'codTipoDocumento' => '00002', // DNI
                'nroDocumento' => $usuario->dni,
                'asunto' => $asunto,
                'mensaje' => $mensaje,
                'idCategoria' => 5, // Notificación
                'conSelloTiempo' => true,
                'adjuntos' => [
                    [
                        'url' => asset('storage/' . $rutaArchivo),
                        'nombreArchivo' => basename($rutaArchivo)
                    ]
                ]
            ]);
    }
}