<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MtcCallbackController extends Controller
{
    // 5.1 Firma de Constancia de Depósito 
    public function firmarDeposito(Request $request) 
    {
        Log::info("MTC: Solicitud de firma de depósito recibida.");
        // Por ahora, devolvemos el mismo archivo (sin firmar realmente) para que la API no falle
        return response()->json([
            'success' => true,
            'archivoBase64' => $request->archivoBase64 
        ]);
    }

    // 5.3 Acuse de Lectura 
    public function acuseLectura(Request $request)
{
    $idNotificacionMtc = $request->input('IdNotificacion'); // [cite: 106, 387]
    
    // Buscar la notificación por el ID del MTC y marcarla como leída 
    \App\Models\Notificacion::where('mtc_id', $idNotificacionMtc)
        ->update(['leido_en_mtc' => true]);

    Log::info("MTC: Confirmada lectura de notificación MTC ID: " . $idNotificacionMtc);

    return response()->json(['success' => true]); // 
}
}