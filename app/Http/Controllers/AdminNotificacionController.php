<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\NotificacionRecibida; // Asegúrate de tener este Mailable
use TCPDF; 
use App\Http\Controllers\Controller;

class AdminNotificacionController extends Controller
{
    /**
     * 1. BANDEJA DE SALIDA (Historial de notificaciones enviadas)
     */
    public function index()
    {
        $enviadas = Notificacion::count();
        $leidas = Notificacion::whereNotNull('fecha_lectura')->count();
        $pendientes = $enviadas - $leidas;

        $notificaciones = Notificacion::with('user')
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);

        return view('admin.index', compact('notificaciones', 'enviadas', 'leidas', 'pendientes'));
    }

    /**
     * 2. PETICIONES DE ACCESO (Usuarios nuevos esperando aprobación)
     */
    public function peticiones()
    {
        // CORRECCIÓN: Usamos $solicitudes porque así lo espera tu vista 'admin.peticiones'
        $solicitudes = User::where('status', 0)
                           ->where('is_admin', 0)
                           ->orderBy('created_at', 'desc')
                           ->get();

        return view('admin.peticiones', compact('solicitudes'));
    }

    /**
     * 3. FORMULARIO DE REDACCIÓN
     */
    public function create(Request $request)
    {
        $search = $request->input('search');

        $usuarios = User::where('is_admin', false)
            ->where('status', 1) // Solo usuarios aprobados
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                             ->orWhere('dni', 'LIKE', "%{$search}%")
                             ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->limit(5)
            ->get();

        return view('admin.crear', compact('usuarios', 'search'));
    }

    /**
     * 4. PROCESAR ENVÍO (Guardar Notificación y Archivo)
     * ¡AQUÍ ESTABA EL ERROR! Esto debe crear una Notificación, no un Usuario.
     */
    /**
     * 4. PROCESAR ENVÍO (CORREGIDO)
     * Este método recibe el archivo y lo guarda en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validación de los datos del formulario "Redactar Notificación"
        $request->validate([
            'user_id' => 'required|exists:users,id',      // El destinatario debe existir
            'asunto'  => 'required|string|max:255',
            'mensaje' => 'nullable|string',
            'archivo' => 'required|file|mimes:pdf|max:20480', // Solo PDF, máx 20MB
        ]);

        try {
            // 2. Subir el Archivo PDF de forma segura (carpeta 'local' para que no sea pública)
            // Se guardará en storage/app/documentos
            $rutaArchivo = $request->file('archivo')->store('documentos', 'local'); 

            // 3. Guardar el registro en la tabla 'notificaciones'
            Notificacion::create([
                'user_id'          => $request->user_id,
                'asunto'           => $request->asunto,
                'mensaje'          => $request->mensaje,
                'ruta_archivo_pdf' => $rutaArchivo,
                'fecha_lectura'    => null, // Aún no lo lee
            ]);

            // 4. (Opcional) Aquí podrías enviar un correo avisando al usuario
            // $usuario = User::find($request->user_id);
            // Mail::to($usuario->email)->send(new NotificacionRecibida(...));

            return redirect()->route('admin.crear')->with('success', '¡Documento enviado correctamente al ciudadano!');

        } catch (\Exception $e) {
            // En caso de error, volvemos atrás con el mensaje
            return back()->withInput()->with('error', 'Error al procesar el envío: ' . $e->getMessage());
        }
    }
    
    /**
     * 5. APROBAR Y GENERAR PDF (Esto sí estaba bien)
     */
    public function aprobarYGenerarPdf($id)
    {
        try {
            $usuario = User::findOrFail($id);
            $passwordPlano = Str::random(8); 

            // Actualizamos estado y asignamos contraseña
            $usuario->update([
                'status' => 1, // 1 = Activo
                'password' => Hash::make($passwordPlano)
            ]);

            // Generación del PDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator('GORE PASCO');
            $pdf->SetTitle('Credenciales de Acceso');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(25, 25, 25);
            $pdf->AddPage();
            
            // Estilos y contenido
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
            <p>Se recomienda cambiar su contraseña al primer ingreso.</p>
            <br>
            <p style='text-align:center'>______________________________________<br>Oficina de TI - GORE PASCO</p>
            ";

            $pdf->writeHTML($html, true, false, true, false, '');

            return $pdf->Output('Credenciales_' . $usuario->dni . '.pdf', 'D');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar credenciales: ' . $e->getMessage());
        }
    }
}