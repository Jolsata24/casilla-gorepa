<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NotificacionController extends Controller
{
    // 1. Mostrar la Bandeja de Entrada
    public function index()
    {
        // Obtenemos solo las notificaciones del usuario logueado
        // ordenadas de la más reciente a la más antigua
        $notificaciones = Notificacion::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('casilla.index', compact('notificaciones'));
    }

    // 2. Descargar el PDF y Registrar la Lectura (Legal)
    public function descargar($id)
    {
        $notificacion = Notificacion::findOrFail($id);

        // SEGURIDAD: Verificar que la notificación sea del usuario actual
        if ($notificacion->user_id !== Auth::id()) {
            abort(403, 'Acceso denegado a este expediente.');
        }

        // LÓGICA LEGAL: Si es la primera vez que lo abre, registramos fecha e IP
        if (is_null($notificacion->fecha_lectura)) {
            $notificacion->update([
                'fecha_lectura' => now(),
                'ip_lectura' => request()->ip()
            ]);
        }

        // Verificar si el archivo realmente existe en el servidor
        if (!Storage::exists($notificacion->ruta_archivo_pdf)) {
            return back()->with('error', 'El archivo físico no se encuentra.');
        }

        // Descargar el archivo
        return Storage::download($notificacion->ruta_archivo_pdf);
    }
}