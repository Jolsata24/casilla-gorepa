<?php

namespace App\Http\Controllers;
use App\Mail\NotificacionRecibida;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Storage;

class AdminNotificacionController extends Controller
{
    // 1. Mostrar el formulario de envío
    public function create(Request $request)
{
    $search = $request->input('search');

    $usuarios = User::where('is_admin', false)
        ->when($search, function ($query) use ($search) {
            return $query->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('dni', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
        })
        ->limit(10) // Solo mostramos los 10 mejores resultados para no saturar
        ->get();

    return view('admin.crear', compact('usuarios', 'search'));
}

    // 2. Guardar la notificación y el archivo
    public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'asunto' => 'required|string|max:255',
        'archivo' => 'required|file|mimes:pdf|max:10240',
    ]);

    $ruta = $request->file('archivo')->store('notificaciones');

    // 1. Guardamos en la BD
    $notificacion = Notificacion::create([
        'user_id' => $request->user_id,
        'asunto' => $request->asunto,
        'mensaje' => $request->mensaje,
        'ruta_archivo_pdf' => $ruta,
    ]);

    // 2. Buscamos al ciudadano y enviamos el correo
    $usuario = User::find($request->user_id);
    Mail::to($usuario->email)->send(new NotificacionRecibida($notificacion));

    return redirect()->route('admin.crear')->with('success', 'Notificación enviada y ciudadano avisado por correo.');
}
    // app/Http/Controllers/AdminNotificacionController.php

public function index()
{
    // Ahora 'user' ya existe como relación
    $notificaciones = Notificacion::with('user')->orderBy('created_at', 'desc')->get();
    
    return view('admin.index', compact('notificaciones'));
}
}

