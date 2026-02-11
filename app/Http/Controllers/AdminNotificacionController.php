<?php

namespace App\Http\Controllers;

use App\Mail\NotificacionRecibida;
use App\Mail\CuentaActivada; // Importante
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash; // Importante
use Illuminate\Support\Str; // Importante
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Storage;
use App\Services\MtcService;

class AdminNotificacionController extends Controller
{
    /**
     * 1. Dashboard con estadísticas reales
     */
    public function index()
    {
        $notificaciones = Notificacion::with('user')->orderBy('created_at', 'desc')->get();
        
        $totalNotificaciones = Notificacion::count();
        $totalCiudadanos = User::where('is_admin', false)->count();
        $totalPeticiones = User::where('status', 0)->where('is_admin', false)->count();

        return view('admin.index', compact(
            'notificaciones', 
            'totalNotificaciones', 
            'totalCiudadanos', 
            'totalPeticiones'
        ));
    }

    /**
     * 2. Listar peticiones de acceso (status 0)
     */
    public function peticiones()
    {
        $solicitudes = User::where('status', 0)
                           ->where('is_admin', false)
                           ->orderBy('created_at', 'desc')
                           ->get();

        return view('admin.peticiones', compact('solicitudes'));
    }

    /**
     * 3. Formulario para enviar nuevas notificaciones
     */
    public function create(Request $request)
    {
        $search = $request->input('search');

        $usuarios = User::where('is_admin', false)
            ->where('status', 1) 
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                             ->orWhere('dni', 'LIKE', "%{$search}%")
                             ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        return view('admin.crear', compact('usuarios', 'search'));
    }

    /**
     * 4. Procesar el envío de la notificación
     */
    public function store(Request $request, MtcService $mtcService)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'asunto'  => 'required|string|max:255',
            'archivo' => 'required|file|mimes:pdf|max:10240',
        ]);

        $ruta = $request->file('archivo')->store('notificaciones');

        $notificacion = Notificacion::create([
            'user_id'          => $request->user_id,
            'asunto'           => $request->asunto,
            'mensaje'          => $request->mensaje,
            'ruta_archivo_pdf' => $ruta,
        ]);

        $usuario = User::find($request->user_id);
        
        // Integración opcional con servicio externo MTC
        $resultadoMtc = $mtcService->enviarNotificacionExterna($usuario, $request->asunto, $request->mensaje, $ruta);

        if ($resultadoMtc->successful()) {
            $datos = $resultadoMtc->json();
            $notificacion->update([
                'mtc_id' => $datos['data']['idNotificacion'] ?? null
            ]);
        }

        Mail::to($usuario->email)->send(new NotificacionRecibida($notificacion));

        return redirect()->route('admin.crear')->with('success', 'Notificación enviada con éxito.');
    }

    /**
     * 5. Mostrar formulario público de solicitud
     */
    public function createSolicitud()
    {
        return view('auth.solicitar-acceso');
    }

    /**
     * 6. Guardar la solicitud del ciudadano (Status 0)
     */
    public function storeSolicitud(Request $request)
    {
        $request->validate([
            'dni'   => 'required|string|max:8|unique:users,dni',
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
        ]);

        User::create([
            'dni'      => $request->dni,
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt(Str::random(12)), // Password temporal aleatorio
            'status'   => 0, 
            'is_admin' => false,
        ]);

        return redirect()->route('login')->with('success', 'Su solicitud ha sido enviada. El administrador revisará sus datos pronto.');
    }

    /**
     * 7. Aprobar usuario, generar clave y enviar correo
     */
    public function aprobarUsuario($id)
    {
        $usuario = User::findOrFail($id);

        // Generamos contraseña aleatoria
        $passwordAleatorio = Str::random(10);

        // Actualizamos a status 1 y guardamos clave cifrada
        $usuario->update([
            'status'   => 1,
            'password' => Hash::make($passwordAleatorio)
        ]);

        // Enviamos el correo con la clave en texto plano
        Mail::to($usuario->email)->send(new CuentaActivada($usuario, $passwordAleatorio));

        return back()->with('success', 'Cuenta activada. Credenciales enviadas a: ' . $usuario->email);
    }
}