<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\FirmaDigitalService;

class MtcCallbackController extends Controller
{
    protected $firmaService;

    public function __construct(FirmaDigitalService $firmaService)
    {
        $this->firmaService = $firmaService;
    }

    /**
     * 5.1 Firma de Constancia de Depósito
     * El MTC envía un documento (Notificación) y espera recibirlo firmado como cargo de recepción.
     */
    public function firmarDeposito(Request $request) 
    {
        Log::info("MTC: Solicitud de firma de depósito recibida.");

        // Validar que venga el archivo
        if (!$request->has('archivoBase64')) {
            return response()->json(['success' => false, 'message' => 'Falta archivoBase64'], 400);
        }

        try {
            // 1. Decodificar Base64 entrante
            $pdfBinario = base64_decode($request->archivoBase64);

            // 2. Procesar firma (o sello de recepción)
            $pdfFirmadoBinario = $this->firmaService->firmarDocumento($pdfBinario);

            // 3. Codificar de nuevo a Base64 para responder
            $pdfFirmadoBase64 = base64_encode($pdfFirmadoBinario);

            return response()->json([
                'success' => true,
                'archivoBase64' => $pdfFirmadoBase64
            ]);

        } catch (\Exception $e) {
            Log::error("MTC Error en firmarDeposito: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno al firmar'], 500);
        }
    }

    /**
     * 5.2 Constancia de Lectura (Opcional según flujo)
     * A veces el MTC usa el mismo endpoint, pero si lo separan:
     */
    public function firmarLectura(Request $request)
    {
        Log::info("MTC: Solicitud de constancia de lectura.");
        // Reutilizamos la lógica de firma
        return $this->firmarDeposito($request);
    }

    /**
     * 5.3 Acuse de Lectura 
     * Solo confirma que el usuario abrió la notificación en tu sistema.
     */
    public function acuseLectura(Request $request)
    {
        $idNotificacionMtc = $request->input('IdNotificacion'); 
        
        if (!$idNotificacionMtc) {
            return response()->json(['success' => false, 'message' => 'Falta IdNotificacion'], 400);
        }

        // Buscar la notificación por el ID del MTC y marcarla como leída 
        $affected = \App\Models\Notificacion::where('mtc_id', $idNotificacionMtc)
            ->update(['leido_en_mtc' => true]);

        if ($affected) {
            Log::info("MTC: Confirmada lectura de notificación MTC ID: " . $idNotificacionMtc);
        } else {
            Log::warning("MTC: Intento de acuse de lectura para ID no encontrado: " . $idNotificacionMtc);
        }

        return response()->json(['success' => true]); 
    }

    
}