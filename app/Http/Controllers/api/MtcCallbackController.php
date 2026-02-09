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
        // El MTC envía IdNotificacion y fechaAcuse 
        Log::info("MTC: El ciudadano leyó la notificación: " . $request->IdNotificacion);
        
        // Aquí buscarías en tu DB y marcarías como LEÍDO
        // Notificacion::where('mtc_id', $request->IdNotificacion)->update(['leido' => true]);

        return response()->json(['success' => true]);
    }
}