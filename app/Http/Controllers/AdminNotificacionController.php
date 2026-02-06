<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Storage;

class AdminNotificacionController extends Controller
{
    // 1. Mostrar el formulario de envío
    public function create()
    {
        // Traemos a todos los usuarios que NO son administradores (ciudadanos)
        $usuarios = User::where('is_admin', false)->get();
        return view('admin.crear', compact('usuarios'));
    }

    // 2. Guardar la notificación y el archivo
    public function store(Request $request)
    {
        // Validamos que no suban cualquier cosa
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'asunto' => 'required|string|max:255',
            'archivo' => 'required|file|mimes:pdf|max:10240', // Solo PDF, máx 10MB
        ]);

        // Subir el archivo a la carpeta privada 'notificaciones'
        // Laravel le asignará un nombre único encriptado automáticamente
        $ruta = $request->file('archivo')->store('notificaciones');

        // Crear el registro en Base de Datos
        Notificacion::create([
            'user_id' => $request->user_id,
            'asunto' => $request->asunto,
            'mensaje' => $request->mensaje,
            'ruta_archivo_pdf' => $ruta,
            'fecha_lectura' => null // Nace sin leer
        ]);

        return redirect()->route('admin.crear')->with('success', '¡Notificación enviada correctamente!');
    }
}