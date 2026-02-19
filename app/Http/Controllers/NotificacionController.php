<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // <--- Asegúrate de que esto esté aquí
use App\Models\Bitacora;
use TCPDF;
class NotificacionController extends Controller
{
    public function index()
    {
        // ... (tu código del index se queda igual) ...
        $nuevas = Notificacion::where('user_id', Auth::id())
                              ->whereNull('fecha_lectura')
                              ->orderBy('created_at', 'desc')
                              ->get();

        $historial = Notificacion::where('user_id', Auth::id())
                                 ->whereNotNull('fecha_lectura')
                                 ->orderBy('fecha_lectura', 'desc')
                                 ->paginate(10);

        return view('notificaciones.index', compact('nuevas', 'historial'));
    }

    public function descargar($id)
    {
        // Buscamos la notificación del usuario logueado
        $notificacion = Notificacion::where('user_id', Auth::id())->findOrFail($id);

        // 1. Verificar existencia física del documento original
        if (!Storage::disk('local')->exists($notificacion->ruta_archivo_pdf)) {
            Bitacora::registrar('ERROR_DESCARGA', "Archivo no encontrado para Notificación ID: $id");
            return back()->with('error', 'El archivo físico no se encuentra en el servidor.');
        }

        // 2. LÓGICA DE LECTURA Y GENERACIÓN DE CARGO
        // Solo generamos el cargo la PRIMERA vez que se lee.
        if ($notificacion->fecha_lectura == null) {
            
            // A) Actualizamos BD primero
            $notificacion->update([
                'fecha_lectura' => now(),
                'ip_lectura' => request()->ip()
            ]);

            // B) ¡AQUÍ LA MAGIA! Generamos el PDF de constancia
            // Esto crea un archivo "cargo_recepcion_XYZ.pdf" en storage/app/cargos
            $this->generarAcuseRecibo($notificacion);

            // C) Auditoría
            Bitacora::registrar('LECTURA_CONFIRMADA', "El usuario leyó la notificación ID: {$notificacion->id} y se generó el cargo.");
        }

        // 3. Descargar el archivo para el usuario
        return Storage::disk('local')->download($notificacion->ruta_archivo_pdf);
    }
    /**
     * Genera un PDF de "Cargo de Recepción" legal y lo guarda en el servidor.
     */
    private function generarAcuseRecibo($notificacion)
    {
        try {
            // 1. Crear instancia de TCPDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            // 2. Configuración del Documento
            $pdf->SetCreator('GORE PASCO - Casilla Electrónica');
            $pdf->SetAuthor('Sistema de Notificaciones');
            $pdf->SetTitle('Cargo de Recepción - Notificación #' . $notificacion->id);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(25, 25, 25);
            $pdf->AddPage();

            // 3. Obtener datos del Usuario
            $user = $notificacion->user;
            $nombreCompleto = mb_strtoupper("{$user->name} {$user->apellido_paterno} {$user->apellido_materno}");
            
            // 4. Contenido del Cargo (Estilo Formal)
            // Logos y Cabecera
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'GOBIERNO REGIONAL DE PASCO', 0, 1, 'C');
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'CONSTANCIA DE NOTIFICACIÓN ELECTRÓNICA', 0, 1, 'C');
            $pdf->Ln(10);

            // Cuerpo del mensaje
            $pdf->SetFont('helvetica', '', 11);
            
            $fechaLectura = $notificacion->fecha_lectura->format('d/m/Y H:i:s');
            $anio = date('Y');

            $html = "
            <p style=\"text-align: justify;\">
                Por medio del presente documento, el <strong>GOBIERNO REGIONAL DE PASCO</strong> deja constancia que el ciudadano:
            </p>
            <br>
            <table border=\"1\" cellpadding=\"8\">
                <tr>
                    <td width=\"150\" bgcolor=\"#f0f0f0\"><strong>Ciudadano:</strong></td>
                    <td>$nombreCompleto</td>
                </tr>
                <tr>
                    <td bgcolor=\"#f0f0f0\"><strong>DNI / RUC:</strong></td>
                    <td>{$user->dni}</td>
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
                <i>Base Legal: TUO de la Ley N° 27444, Ley de Procedimiento Administrativo General. La notificación electrónica surte efectos legales desde el momento en que el ciudadano accede al documento en su casilla.</i>
            </p>
            <br><br><br>
            <p style=\"text-align:center\">______________________________________<br>SISTEMA DE GESTIÓN DOCUMENTAL<br>GORE PASCO</p>
            <p style=\"text-align:center; font-size: 8pt;\">Generado automáticamente el $fechaLectura</p>
            ";

            $pdf->writeHTML($html, true, false, true, false, '');

            // 5. Guardar el archivo en el servidor (Carpeta 'cargos')
            // Asegúrate de crear la carpeta: storage/app/cargos
            $nombreArchivo = "cargo_recepcion_{$notificacion->id}_{$user->dni}.pdf";
            $rutaGuardado = storage_path("app/cargos/{$nombreArchivo}");
            
            // Verificar si existe directorio, si no, crearlo
            if (!file_exists(dirname($rutaGuardado))) {
                mkdir(dirname($rutaGuardado), 0755, true);
            }

            // 'F' = File Save (Guardar en disco)
            $pdf->Output($rutaGuardado, 'F'); 

            // Opcional: Si tienes un campo 'ruta_cargo' en la BD, guárdalo aquí.
            // $notificacion->update(['ruta_cargo' => "cargos/{$nombreArchivo}"]);

            return true;

        } catch (\Exception $e) {
            // Loguear error pero no detener la descarga del usuario
            \Illuminate\Support\Facades\Log::error("Error generando cargo PDF: " . $e->getMessage());
            return false;
        }
    }
    
}