<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MtcService
{
    /**
     * Función 1: Envía la notificación a la Casilla Electrónica del MTC
     */
    public function enviarNotificacionExterna($usuario, $asunto, $mensaje, $rutaArchivo)
    {
        try {
            // 1. Validar que el archivo físico exista
            if (!Storage::exists($rutaArchivo)) {
                Log::error("MTC Error: El archivo no existe en disco: " . $rutaArchivo);
                return (object)['successful' => false, 'json' => ['mensaje' => 'Archivo no encontrado localmente']];
            }

            // 2. Obtener Token
            $authResponse = Http::get('https://dvwscasilla.mtc.gob.pe/ms-notific/v2/servicios-publicos/notificaciones/token', [
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

            // 3. Verificar si la casilla del administrado está activa
            $estadoCasilla = $this->verificarEstadoCasilla($token, $usuario->dni);
            
            if (!isset($estadoCasilla['success']) || !$estadoCasilla['success'] || empty($estadoCasilla['data']['activo'])) {
                Log::warning("MTC Advertencia: La casilla del usuario {$usuario->dni} no está activa o no existe.");
                return (object)['successful' => false, 'json' => ['mensaje' => 'El usuario no tiene una casilla MTC activa.']];
            }

            // 4. Generar URL Pública para el archivo
            $urlPublica = config('app.url') . '/storage/' . $rutaArchivo;
            Log::info("MTC Enviando archivo: " . $urlPublica);

            // 5. Enviar la Notificación
            $response = Http::withToken($token)
                ->post('https://dvwscasilla.mtc.gob.pe/ms-notific/v2/servicios-publicos/notificaciones-externas', [
                    'codTipoPersona' => '00001', 
                    'codTipoDocumento' => '00002', 
                    'nroDocumento' => $usuario->dni,
                    'asunto' => $asunto,
                    'mensaje' => $mensaje,
                    'idCategoria' => 5, 
                    'conSelloTiempo' => true,
                    'tipoSelloTiempo' => 'PROPIO', // Usamos el agente propio para la firma
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
            return (object)[
                'successful' => function() { return false; },
                'json' => function() { return ['error' => 'Error interno del servidor']; }
            ];
        }
    }

    /**
     * Función 2: Verifica si la casilla electrónica del usuario está activa en el MTC
     */
    public function verificarEstadoCasilla($token, $dni)
    {
        $response = Http::withToken($token)->get('https://dvwscasilla.mtc.gob.pe/ms-notific/v2/servicios-publicos/verificar-estado-casilla', [
            'codTipoPersona' => '00001',   // 00001 = Persona Natural
            'codTipoDocumento' => '00002', // 00002 = DNI
            'nroDocumento' => $dni
        ]);
        
        return $response->json();
    }
}