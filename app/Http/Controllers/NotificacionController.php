<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // <--- Asegúrate de que esto esté aquí

class NotificacionController extends Controller
{
    public function index()
    {
        // ... (tu código del index se queda igual) ...
        $nuevas = Notificacion::where('user_id', Auth::id())
                              ->whereNull('fecha_lectura')
                              ->orderBy('created_at', 'desc')
                              ->get();

        $historial = Notificacion::where('user_id', Auth::id())
                                 ->whereNotNull('fecha_lectura')
                                 ->orderBy('fecha_lectura', 'desc')
                                 ->paginate(10);

        return view('notificaciones.index', compact('nuevas', 'historial'));
    }

    public function descargar($id)
    {
        // Buscamos la notificación asegurándonos que sea del usuario logueado
        $notificacion = Notificacion::where('user_id', Auth::id())->findOrFail($id);

        // 1. Verificar si el archivo existe físicamente
        if (!Storage::disk('local')->exists($notificacion->ruta_archivo_pdf)) {
            return back()->with('error', 'El archivo físico no se encuentra en el servidor.');
        }

        // 2. Marcar como leído (SI ES LA PRIMERA VEZ)
        if ($notificacion->fecha_lectura == null) {
            $notificacion->update([
                'fecha_lectura' => now(),
                'ip_lectura' => request()->ip() // <--- ¡AQUÍ ESTÁ LA LÍNEA QUE FALTABA!
            ]);
        }

        // 3. Descargar archivo
        return Storage::disk('local')->download($notificacion->ruta_archivo_pdf);
    }
}