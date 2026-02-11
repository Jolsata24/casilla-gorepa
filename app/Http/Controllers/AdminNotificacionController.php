<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notificacion;
use App\Services\MtcService; // Asegúrate de que este servicio exista o quítalo si no lo usas
use App\Mail\NotificacionRecibida;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use TCPDF; 

class AdminNotificacionController extends Controller
{
    /**
     * 1. HISTORIAL DE NOTIFICACIONES (Bandeja de Salida)
     * Modificado para coincidir con la vista de "Ventanita de Notificaciones"
     */
    public function index()
    {
        // 1. Contadores para las tarjetas de resumen
        $enviadas = Notificacion::count();
        $leidas = Notificacion::whereNotNull('fecha_lectura')->count();
        $pendientes = $enviadas - $leidas;

        // 2. Lista de notificaciones para la tabla (Paginada de 10 en 10)
        $notificaciones = Notificacion::with('user')
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);

        // Enviamos las variables exactas que la vista espera
        return view('admin.index', compact('notificaciones', 'enviadas', 'leidas', 'pendientes'));
    }

    /**
     * 2. Peticiones de Acceso (Vista de Seguridad)
     */
    public function peticiones()
    {
        $solicitudes = User::where('status', 0)
                           ->where('is_admin', false)
                           ->orderBy('created_at', 'asc') 
                           ->get();

        return view('admin.peticiones', compact('solicitudes'));
    }

    /**
     * 3. Formulario para redactar (Vista tipo Email)
     */
    public function create(Request $request)
    {
        $search = $request->input('search');

        // Buscador de usuarios activos
        $usuarios = User::where('is_admin', false)
            ->where('status', 1) 
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                             ->orWhere('dni', 'LIKE', "%{$search}%")
                             ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->limit(5) // Limitamos a 5 para no saturar la vista
            ->get();

        return view('admin.crear', compact('usuarios', 'search'));
    }

    /**
     * 4. Procesar el envío
     */
    public function store(Request $request, MtcService $mtcService)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'asunto'  => 'required|string|max:255',
            'mensaje' => 'required|string',
            'archivo' => 'required|file|mimes:pdf|max:10240', 
        ]);

        $ruta = $request->file('archivo')->store('notificaciones', 'local');

        $notificacion = Notificacion::create([
            'user_id'          => $request->user_id,
            'asunto'           => $request->asunto,
            'mensaje'          => $request->mensaje,
            'ruta_archivo_pdf' => $ruta,
        ]);

        $usuario = User::find($request->user_id);
        
        // Intento de envío al MTC (Si falla, no detiene el flujo)
        try {
             $resultadoMtc = $mtcService->enviarNotificacionExterna($usuario, $request->asunto, $request->mensaje, $ruta);
             if ($resultadoMtc->successful()) {
                $datos = $resultadoMtc->json();
                $notificacion->update(['mtc_id' => $datos['data']['idNotificacion'] ?? null]);
             }
        } catch (\Exception $e) {
            // Log::error("Error MTC: " . $e->getMessage());
        }

        // Envío de correo
        try {
            Mail::to($usuario->email)->send(new NotificacionRecibida($notificacion));
        } catch (\Exception $e) {
            // Log::error("Fallo al enviar email: " . $e->getMessage());
        }

        return redirect()->route('admin.crear')->with('success', 'Notificación enviada correctamente.');
    }
    
    /**
     * 5. Aprobar y Generar PDF
     */
    public function aprobarYGenerarPdf($id)
    {
        $usuario = User::findOrFail($id);

        $passwordPlano = Str::random(8); 

        $usuario->update([
            'status' => 1, 
            'password' => Hash::make($passwordPlano)
        ]);

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetCreator('GORE PASCO');
        $pdf->SetTitle('Credenciales de Acceso');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(25, 25, 25);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'GOBIERNO REGIONAL DE PASCO', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'SISTEMA DE CASILLA ELECTRÓNICA', 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('helvetica', '', 11);
        $fecha = date('d/m/Y');
        $linkLogin = route('login');

        $html = "
        <p style='text-align:right'><strong>Fecha:</strong> $fecha</p>
        <br>
        <p><strong>Estimado(a):</strong> {$usuario->name} {$usuario->apellido_paterno} {$usuario->apellido_materno}</p>
        <p><strong>DNI:</strong> {$usuario->dni}</p>
        <br>
        <p>Su solicitud de acceso ha sido <strong>APROBADA</strong>.</p>
        <p>Credenciales de ingreso:</p>
        <br>
        <table border=\"1\" cellpadding=\"10\">
            <tr>
                <td width=\"150\" bgcolor=\"#f0f0f0\"><strong>Portal Web:</strong></td>
                <td>$linkLogin</td>
            </tr>
            <tr>
                <td bgcolor=\"#f0f0f0\"><strong>Usuario:</strong></td>
                <td>{$usuario->email}</td>
            </tr>
            <tr>
                <td bgcolor=\"#f0f0f0\"><strong>Contraseña:</strong></td>
                <td><b style=\"font-size:14pt\">$passwordPlano</b></td>
            </tr>
        </table>
        <br>
        <p style='text-align:center'>______________________________________<br>Oficina de TI - GORE PASCO</p>
        ";

        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('Credenciales_' . $usuario->dni . '.pdf', 'D');
    }
}