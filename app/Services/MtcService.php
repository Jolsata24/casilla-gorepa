namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MtcService
{
    public function enviarNotificacionExterna($usuario, $asunto, $mensaje, $rutaArchivo)
    {
        $clientId = env('MTC_CLIENT_ID');
        
        // MOCK/SIMULACIÓN: Si no hay credenciales, simulamos éxito
        if (!$clientId || $clientId === 'TU_TOKEN_AQUI') {
            Log::info("MTC API: Modo simulación activado para el usuario {$usuario->dni}");
            return (object) [
                'successful' => true,
                'json' => fn() => [
                    'success' => true,
                    'data' => [
                        'idNotificacion' => rand(100000, 999999),
                        'fechaNotificacion' => now()->toIso8601String()
                    ]
                ]
            ];
        }

        // ... Aquí iría el código real que vimos antes ...
    }
}