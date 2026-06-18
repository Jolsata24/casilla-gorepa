<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Tcpdf\Fpdi;

class FirmaDigitalService
{
    /**
     * Recibe el contenido binario del PDF del MTC, lo lee y devuelve el binario firmado.
     */
    public function firmarDocumento(string $pdfContent)
    {
        // 1. Guardar temporalmente el PDF del MTC para que FPDI pueda leerlo
        $tempPath = storage_path('app/temp_mtc_' . uniqid() . '.pdf');
        file_put_contents($tempPath, $pdfContent);

        try {
            // 2. Instanciar FPDI (que extiende de TCPDF)
            $pdf = new Fpdi(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // 3. Importar todas las páginas del PDF original del MTC
            $pageCount = $pdf->setSourceFile($tempPath);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                
                // Respetar la orientación original de cada página
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }

            // 4. FIRMA DIGITAL CRIPTOGRÁFICA
            $certPath = storage_path('app/certificados/certificado.crt');
            $keyPath  = storage_path('app/certificados/llave_privada.key');
            $password = env('FIRMA_PASSWORD', ''); 

            if (file_exists($certPath) && file_exists($keyPath)) {
                $info = array(
                    'Name' => 'Gobierno Regional de Pasco',
                    'Location' => 'Cerro de Pasco',
                    'Reason' => 'Recepción y validación de Notificación MTC',
                    'ContactInfo' => 'informatica@regionpasco.gob.pe',
                );

                // Aplicar firma invisible al documento
                $pdf->setSignature('file://'.$certPath, 'file://'.$keyPath, $password, '', 2, $info);
                Log::info("FirmaDigitalService: Firma criptográfica aplicada al documento del MTC.");
            } else {
                Log::warning("FirmaDigitalService: Faltan certificados. Se devuelve el PDF del MTC sin firma criptográfica válida.");
            }

            // 5. Generar el PDF final en memoria
            $pdfFirmado = $pdf->Output('cargo_firmado.pdf', 'S');

            // Limpiar archivo temporal
            unlink($tempPath);

            return $pdfFirmado;

        } catch (\Exception $e) {
            Log::error("Error al firmar PDF con FPDI: " . $e->getMessage());
            if (file_exists($tempPath)) { unlink($tempPath); }
            
            // Si falla, devolvemos el original para no interrumpir el flujo del MTC
            return $pdfContent;
        }
    }
}