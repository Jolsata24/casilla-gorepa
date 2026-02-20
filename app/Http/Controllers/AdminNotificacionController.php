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
    /**
     * Permite al administrador descargar la constancia de lectura (Cargo)
     */
    /**
     * Permite al administrador descargar la constancia de lectura (Cargo)
     */
    public function descargarCargo($id)
    {
        $notificacion = \App\Models\Notificacion::findOrFail($id);

        // 1. Validar que el documento realmente haya sido leído
        if (!$notificacion->fecha_lectura) {
            return back()->with('error', 'El destinatario aún no ha leído este documento.');
        }

        // 2. Identificar si es ciudadano (DNI) o empresa (RUC) para el nombre del archivo
        $user = $notificacion->user;
        $esRUC = $user->tipo_documento === 'RUC';
        $numDoc = $esRUC ? $user->ruc : $user->dni;

        $nombreArchivo = "cargo_recepcion_{$notificacion->id}_{$numDoc}.pdf";
        $path = "cargos/{$nombreArchivo}";

        // 3. CASO A: El archivo YA EXISTE FÍSICAMENTE (Flujo normal)
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            return \Illuminate\Support\Facades\Storage::disk('local')->download($path, $nombreArchivo);
        }

        // 4. CASO B: EL ARCHIVO NO EXISTE (Lo generamos al vuelo)
        try {
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            $pdf->SetCreator('GORE PASCO - Casilla Electrónica');
            $pdf->SetAuthor('Sistema de Notificaciones');
            $pdf->SetTitle('Cargo de Recepción - Notificación #' . $notificacion->id);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(25, 25, 25);
            $pdf->AddPage();

            $nombreCompleto = mb_strtoupper($esRUC ? $user->razon_social : "{$user->name} {$user->apellido_paterno} {$user->apellido_materno}");
            $tipoDocLabel = $esRUC ? 'RUC' : 'DNI';
            
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'GOBIERNO REGIONAL DE PASCO', 0, 1, 'C');
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'CONSTANCIA DE NOTIFICACIÓN ELECTRÓNICA', 0, 1, 'C');
            $pdf->Ln(10);

            $pdf->SetFont('helvetica', '', 11);
            $fechaLectura = \Carbon\Carbon::parse($notificacion->fecha_lectura)->format('d/m/Y H:i:s');

            $html = "
            <p style=\"text-align: justify;\">
                Por medio del presente documento, el <strong>GOBIERNO REGIONAL DE PASCO</strong> deja constancia que el destinatario:
            </p>
            <br>
            <table border=\"1\" cellpadding=\"8\">
                <tr>
                    <td width=\"150\" bgcolor=\"#f0f0f0\"><strong>Destinatario:</strong></td>
                    <td>$nombreCompleto</td>
                </tr>
                <tr>
                    <td bgcolor=\"#f0f0f0\"><strong>$tipoDocLabel:</strong></td>
                    <td>$numDoc</td>
                </tr>
                <tr>
                    <td bgcolor=\"#f0f0f0\"><strong>Domicilio:</strong></td>
                    <td>{$user->direccion} - {$user->distrito}</td>
                </tr>
            </table>
            <br>
            <p style=\"text-align: justify;\">
                Ha accedido conforme a ley a su <strong>CASILLA ELECTRÓNICA</strong>, dándose por <strong>NOTIFICADO VÁLIDAMENTE</strong> del siguiente acto administrativo:
            </p>
            <br>
            <table border=\"1\" cellpadding=\"8\">
                <tr>
                    <td width=\"150\" bgcolor=\"#e6f7ff\"><strong>Asunto:</strong></td>
                    <td>{$notificacion->asunto}</td>
                </tr>
                <tr>
                    <td bgcolor=\"#e6f7ff\"><strong>Documento ID:</strong></td>
                    <td>{$notificacion->id}</td>
                </tr>
                <tr>
                    <td bgcolor=\"#e6f7ff\"><strong>Fecha de Lectura:</strong></td>
                    <td>$fechaLectura</td>
                </tr>
                <tr>
                    <td bgcolor=\"#e6f7ff\"><strong>IP de Acceso:</strong></td>
                    <td>{$notificacion->ip_lectura}</td>
                </tr>
            </table>
            <br>
            <p style=\"font-size: 9pt; color: #555;\">
                <i>Base Legal: TUO de la Ley N° 27444, Ley de Procedimiento Administrativo General. La notificación electrónica surte efectos legales desde el momento en que el ciudadano/entidad accede al documento en su casilla.</i>
            </p>
            <br><br><br>
            <p style=\"text-align:center\">______________________________________<br>SISTEMA DE GESTIÓN DOCUMENTAL<br>GORE PASCO</p>
            <p style=\"text-align:center; font-size: 8pt;\">Generado automáticamente (Registro en base de datos: $fechaLectura)</p>
            ";

            $pdf->writeHTML($html, true, false, true, false, '');

            // ¡EL CAMBIO CRÍTICO ESTÁ AQUÍ!
            // 1. Obtenemos el PDF como una cadena de texto cruda ('S' = String)
            $pdfContent = $pdf->Output($nombreArchivo, 'S');

            // 2. Guardamos usando el disco 'local' de Laravel. Esto crea las carpetas automáticamente sin dar error de permisos.
            \Illuminate\Support\Facades\Storage::disk('local')->put($path, $pdfContent);

            // 3. Forzamos la descarga del archivo que acabamos de crear
            return \Illuminate\Support\Facades\Storage::disk('local')->download($path, $nombreArchivo);

        } catch (\Exception $e) {
            // En lugar de refrescar silenciosamente, detendrá la pantalla y te mostrará el error exacto
            dd("Error crítico al generar el PDF: " . $e->getMessage() . " en la línea " . $e->getLine());
        }
    }
}