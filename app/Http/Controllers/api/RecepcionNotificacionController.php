<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notificacion;

class RecepcionNotificacionController extends Controller
{
    public function recibir(Request $request)
    {
        // El SGD del GOREPA debe enviar estos datos
        $request->validate([
            'documento_destino' => 'required|string', // DNI o RUC del destinatario
            'asunto' => 'required|string',
            'mensaje' => 'nullable|string',
            'archivo_pdf' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        // 1. Buscar al usuario por DNI o RUC
        $usuario = User::where('dni', $request->documento_destino)
                       ->orWhere('ruc', $request->documento_destino)
                       ->first();

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no registrado en la Casilla'], 404);
        }

        // 2. Guardar el PDF en el servidor
        $rutaPdf = $request->file('archivo_pdf')->store('notificaciones/inbound', 'local');

        // 3. Crear la notificación en la BD
        $notificacion = Notificacion::create([
            'user_id' => $usuario->id,
            'asunto' => $request->asunto,
            'mensaje' => $request->mensaje,
            'ruta_archivo_pdf' => $rutaPdf,
        ]);

        return response()->json([
            'success' => true, 
            'mensaje' => 'Notificación depositada con éxito',
            'notificacion_id' => $notificacion->id
        ], 201);
    }
}
