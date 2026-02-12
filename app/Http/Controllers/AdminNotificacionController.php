<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notificacion;
use App\Services\MtcService; 
use App\Mail\NotificacionRecibida;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use TCPDF; 
use App\Http\Controllers\Controller;

class AdminNotificacionController extends Controller
{
    /**
     * 1. BANDEJA DE SALIDA (Historial)
     */
    public function index()
    {
        // Contadores
        $enviadas = Notificacion::count();
        $leidas = Notificacion::whereNotNull('fecha_lectura')->count();
        $pendientes = $enviadas - $leidas;

        // Lista paginada
        $notificaciones = Notificacion::with('user')
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);

        // Retorna la vista de la bandeja (NO la bitácora)
        return view('admin.index', compact('notificaciones', 'enviadas', 'leidas', 'pendientes'));
    }

    /**
     * 2. PETICIONES DE ACCESO
     */
    public function peticiones()
{
    // Obtenemos SOLO los usuarios que tienen status 0 (Pendientes)
    // y que NO son administradores
    $solicitudes = \App\Models\User::where('status', 0)
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
            ->where('status', 1) 
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
     * 4. PROCESAR ENVÍO
     */
    public function store(Request $request)
    {
        // 1. Validación: Incluimos los campos de dirección como opcionales (nullable)
        $validated = $request->validate([
            'dni' => 'required|string|size:8|unique:users,dni',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'apellido_paterno' => 'required|string',
            'apellido_materno' => 'required|string',
            'celular' => 'required|string|max:15',
            // ESTOS SON LOS CAMPOS QUE FALTABAN:
            'departamento' => 'nullable|string',
            'provincia' => 'nullable|string',
            'distrito' => 'nullable|string',
            'direccion' => 'nullable|string',
        ]);

        try {
            // 2. Guardar en Base de Datos
            \App\Models\User::create([
                'dni' => $request->dni,
                'name' => $request->name,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'email' => $request->email,
                'celular' => $request->celular,
                // AGREGAMOS LA DIRECCIÓN AL CREAR EL USUARIO:
                'departamento' => $request->departamento,
                'provincia' => $request->provincia,
                'distrito' => $request->distrito,
                'direccion' => $request->direccion,
                
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(30)),
                'status' => 0,
                'is_admin' => 0,
            ]);

            return back()->with('status', '¡Solicitud enviada correctamente!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error interno: ' . $e->getMessage());
        }
    }
    
    /**
     * 5. APROBAR Y GENERAR PDF
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
        
        // Cabecera PDF
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'GOBIERNO REGIONAL DE PASCO', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'SISTEMA DE CASILLA ELECTRÓNICA', 0, 1, 'C');
        $pdf->Ln(10);

        // Cuerpo PDF
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