<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestMtcApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-mtc-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    // app/Console/Commands/TestMtcApi.php
public function handle()
{
    $baseUrl = "https://dvwscasilla.mtc.gob.pe/ms-notific/v2/servicios-publicos"; // Ambiente de Desarrollo [cite: 15]
    $clientId = env('MTC_CLIENT_ID'); 
    $clientSecret = env('MTC_CLIENT_SECRET');

    $this->info("1. Probando obtención de Token...");
    
    // 4.1. Obtener Token [cite: 19, 20]
    $response = \Illuminate\Support\Facades\Http::get("{$baseUrl}/notificaciones/token", [
        'grantType' => 'authorization_code',
        'clientId' => $clientId,
        'clientSecret' => $clientSecret,
    ]);

    if ($response->successful()) {
        $token = $response->json()['accessToken']; // 
        $this->info("¡Éxito! Token recibido.");

        $this->info("2. Probando verificación de casilla...");
        
        // 4.2. Verificar estado casilla [cite: 31, 41]
        $resCasilla = \Illuminate\Support\Facades\Http::withToken($token)
            ->get("{$baseUrl}/verificar-estado-casilla", [
                'codTipoPersona' => '00001', // Persona Natural 
                'codTipoDocumento' => '00002', // DNI 
                'nroDocumento' => 'TU_DNI_AQUI' // Usa tu DNI para la prueba
            ]);

        if ($resCasilla->successful()) {
            $this->info("Respuesta del MTC: " . $resCasilla->body()); // [cite: 54, 71]
        } else {
            $this->error("Error al verificar casilla: " . $resCasilla->status());
        }
    } else {
        $this->error("Error al obtener token: " . $response->status());
        $this->error($response->body());
    }
}
}
