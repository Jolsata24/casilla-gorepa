<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MtcService
{
    /**
     * Envía la notificación a la Casilla Electrónica del MTC
     */
    public function enviarNotificacionExterna($usuario, $asunto, $mensaje, $rutaArchivo)
    {
        try {
            // 1. Validar que el archivo físico exista antes de llamar a nadie
            if (!Storage::exists($rutaArchivo)) {
                Log::error("MTC Error: El archivo no existe en disco: " . $rutaArchivo);
                return (object)['successful' => false, 'json' => ['mensaje' => 'Archivo no encontrado localmente']];
            }

            // 2. Obtener Token (Página 1 del PDF)
            $authResponse = Http::asForm()->post('https://dvwscasilla.mtc.gob.pe/ms-notific/v2/servicios-publicos/notificaciones/token', [
                'grant_type' => 'authorization_code', // Ojo: a veces las APIs piden 'client_credentials' o 'password', verificar PDF. Usualmente es 'password' o 'client_credentials' para servicios backend.
                // Si el PDF dice explícitamente 'authorization_code', requiere un flujo de navegador. 
                // Asumiremos que es 'client_credentials' o similar para server-to-server, pero dejamos tu config.
                // NOTA: Revisa si tu PDF pide 'grant_type' o 'grantType'.
                'grantType' => 'authorization_code', 
                'clientId' => config('services.mtc.client_id'),
                'clientSecret' => config('services.mtc.client_secret'),
            ]);

            if (!$authResponse->successful()) {
                Log::error("MTC Error Auth: " . $authResponse->body());
                return $authResponse;
            }

            $token = $authResponse->json()['accessToken'] ?? null;
            if (!$token) {
                Log::error("MTC Error: No se recibió accessToken en la respuesta.");
                return $authResponse;
            }

            // 3. Generar URL Pública
            // Usamos APP_URL del .env para asegurar que no sea 'localhost'
            $urlPublica = config('app.url') . '/storage/' . $rutaArchivo;
            
            // Log para depuración
            Log::info("MTC Enviando archivo: " . $urlPublica);

            // 4. Enviar la Notificación (Página 3 del PDF)
            $response = Http::withToken($token)
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
                            'url' => $urlPublica,
                            'nombreArchivo' => basename($rutaArchivo)
                        ]
                    ]
                ]);

            if (!$response->successful()) {
                Log::error("MTC Error Envio: " . $response->body());
            }

            return $response;

        } catch (\Exception $e) {
            Log::critical("MTC Excepción Crítica: " . $e->getMessage());
            // Devolvemos un objeto fake para que el controlador no rompa
            return (object)[
                'successful' => function() { return false; },
                'json' => function() { return ['error' => 'Error interno del servidor']; }
            ];
        }
    }
}