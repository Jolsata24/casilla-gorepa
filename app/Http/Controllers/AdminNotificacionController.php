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
use App\Models\Bitacora; // <--- AGREGAR ESTA (Soluciona "Undefined type Bitacora")
use Illuminate\Support\Facades\Auth;
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
        // 1. Validación
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'asunto'  => 'required|string|max:255',
            'mensaje' => 'nullable|string',
            'archivo' => 'required|file|mimes:pdf|max:20480', // Máx 20MB
        ]);

        try {
            // 2. Subir Archivo
            $rutaArchivo = $request->file('archivo')->store('documentos', 'local');

            // 3. Crear Notificación en BD
            $notificacion = Notificacion::create([
                'user_id'          => $request->user_id,
                'asunto'           => $request->asunto,
                'mensaje'          => $request->mensaje,
                'ruta_archivo_pdf' => $rutaArchivo,
                'fecha_lectura'    => null,
            ]);

            // 4. Enviar Correo y Registrar en Bitácora
            // Usamos un try-catch interno para que, si falla el correo, NO falle todo el proceso
            try {
                $usuario = User::find($request->user_id);
                
                // Enviar Correo
                Mail::to($usuario->email)->send(new NotificacionRecibida($request->asunto, $usuario->name));

                // Registrar en Bitácora (Auditoría)
                Bitacora::create([
                    'user_id' => Auth::id(),
                    'accion'  => 'ENVIO_NOTIFICACION',
                    'detalle' => "Enviado a {$usuario->dni} ({$usuario->email}) | Asunto: {$request->asunto}",
                    'ip'      => $request->ip()
                ]);

            } catch (\Exception $e) {
                // Si falla el correo, avisamos con 'warning' pero el documento SÍ se guardó
                return redirect()->route('admin.crear')
                    ->with('warning', 'Documento guardado correctamente, pero no se pudo enviar el correo de aviso: ' . $e->getMessage());
            }

            // 5. Retorno Exitoso (Todo salió bien)
            return redirect()->route('admin.crear')
                ->with('success', '¡Documento enviado y ciudadano notificado por correo!');

        } catch (\Exception $e) {
            // Error General (Falla al subir archivo o guardar en BD)
            return back()->withInput()
                ->with('error', 'Error crítico al procesar el envío: ' . $e->getMessage());
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

            // Determinar si es Empresa o Ciudadano
            $esRUC = $usuario->tipo_documento == 'RUC';
            $identificador = $esRUC ? $usuario->ruc : $usuario->dni;
            $tipoDocLabel = $esRUC ? 'RUC' : 'DNI';
            
            // Si es empresa, mostrar Razón Social, si no, Nombre Completo
            $nombreDestinatario = $esRUC 
                ? $usuario->razon_social 
                : "{$usuario->name} {$usuario->apellido_paterno} {$usuario->apellido_materno}";

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
            <p><strong>Estimado(a):</strong> $nombreDestinatario</p>
            <p><strong>$tipoDocLabel:</strong> $identificador</p>
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
                    <td bgcolor=\"#f0f0f0\"><strong>Usuario ($tipoDocLabel):</strong></td>
                    <td><b>$identificador</b></td>
                </tr>
                <tr>
                    <td bgcolor=\"#f0f0f0\"><strong>Correo Registrado:</strong></td>
                    <td>{$usuario->email}</td>
                </tr>
                <tr>
                    <td bgcolor=\"#f0f0f0\"><strong>Contraseña Temporal:</strong></td>
                    <td><b style=\"font-size:14pt; color:#1a56db;\">$passwordPlano</b></td>
                </tr>
            </table>
            <br>
            <p>Se recomienda ingresar y cambiar su contraseña inmediatamente por razones de seguridad.</p>
            <br>
            <p style='text-align:center'>______________________________________<br>Oficina de TI - GORE PASCO</p>
            ";

            $pdf->writeHTML($html, true, false, true, false, '');

            // Descargar con el nombre correcto (DNI o RUC)
            return $pdf->Output('Credenciales_' . $identificador . '.pdf', 'D');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar credenciales: ' . $e->getMessage());
        }
    }
    // app/Http/Controllers/AdminNotificacionController.php

    /**
     * Permite al administrador descargar la constancia de lectura (Cargo)
     */
    public function descargarCargo($id)
{
    $notificacion = Notificacion::findOrFail($id);

    if (!$notificacion->fecha_lectura) {
        return back()->with('error', 'El ciudadano aún no ha leído este documento.');
    }

    // 1. Determinar el número de documento correcto (RUC o DNI)
    $esRUC = $notificacion->user->tipo_documento === 'RUC';
    $numDoc = $esRUC ? $notificacion->user->ruc : $notificacion->user->dni;

    // 2. Reconstruir el nombre del archivo
    $nombreArchivo = "cargo_recepcion_{$notificacion->id}_{$numDoc}.pdf";
    $path = "cargos/{$nombreArchivo}";

    // 3. Verificar en el disco 'local' explícitamente
    if (!Storage::disk('local')->exists($path)) {
            return back()->with('error', 'El cargo no se encuentra físico. Nombre buscado: ' . $nombreArchivo);
    }

    return Storage::disk('local')->download($path, $nombreArchivo);
}
}