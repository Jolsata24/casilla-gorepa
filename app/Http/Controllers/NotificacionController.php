<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NotificacionController extends Controller
{
    public function index()
    {
        // 1. Notificaciones NO LEÍDAS (Prioridad Alta)
        $nuevas = Notificacion::where('user_id', Auth::id())
                              ->whereNull('fecha_lectura')
                              ->orderBy('created_at', 'desc')
                              ->get();

        // 2. Historial (Ya leídas)
        $historial = Notificacion::where('user_id', Auth::id())
                                 ->whereNotNull('fecha_lectura')
                                 ->orderBy('fecha_lectura', 'desc')
                                 ->paginate(10);

        return view('notificaciones.index', compact('nuevas', 'historial'));
    }

    public function descargar($id)
    {
        $notificacion = Notificacion::where('user_id', Auth::id())->findOrFail($id);

        // 1. Marcar como leído si es la primera vez
        if ($notificacion->fecha_lectura == null) {
            $notificacion->update([
                'fecha_lectura' => now()
            ]);
        }

        // 2. Descargar archivo
        // Asegúrate que la ruta sea correcta (storage/app/...)
        return response()->download(storage_path('app/' . $notificacion->ruta_archivo_pdf));
    }
}