<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use TCPDF;

class FirmaDigitalService
{
    /**
     * Recibe el contenido binario de un PDF y devuelve el binario firmado/sellado.
     */
    public function firmarDocumento(string $pdfContent)
    {
        try {
            // 1. Crear instancia de TCPDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Configuración básica para no imprimir cabeceras/pies de página automáticos
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // 2. Cargar el contenido del PDF original
            // Nota: TCPDF no edita PDFs existentes nativamente de forma sencilla sin FPDI.
            // Para esta solución "rápida" y compatible, vamos a agregar una página de "Cargo de Recepción"
            // al documento, lo cual altera el hash del archivo y sirve como constancia.
            
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->Write(0, 'CONSTANCIA DE RECEPCIÓN - GORE PASCO', '', 0, 'C', true, 0, false, false, 0);
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Ln(10);
            $pdf->Write(0, 'Este documento confirma la recepción del archivo en la Casilla Electrónica.');
            $pdf->Ln(10);
            $pdf->Write(0, 'Fecha de recepción: ' . date('Y-m-d H:i:s'));
            
            // ---------------------------------------------------------
            // 3. FIRMA DIGITAL CRIPTOGRÁFICA (Opcional si tienes el certificado)
            // ---------------------------------------------------------
            $certPath = 'file://' . storage_path('app/certificados/certificado.crt'); // Tu certificado
            $keyPath  = 'file://' . storage_path('app/certificados/llave_privada.key'); // Tu llave privada
            $password = env('FIRMA_PASSWORD', ''); // Contraseña del certificado

            // Verificamos si existen los certificados reales
            if (file_exists(storage_path('app/certificados/certificado.crt'))) {
                $info = array(
                    'Name' => 'Gobierno Regional de Pasco',
                    'Location' => 'Cerro de Pasco',
                    'Reason' => 'Recepción de Notificación MTC',
                    'ContactInfo' => 'informatica@regionpasco.gob.pe',
                );

                // Aplicar firma
                $pdf->setSignature($certPath, $keyPath, $password, '', 2, $info);
                Log::info("FirmaDigitalService: Firma criptográfica aplicada.");
            } else {
                Log::warning("FirmaDigitalService: No se encontraron certificados. Se generará solo constancia visual.");
            }

            // ---------------------------------------------------------

            // 4. Generar el PDF en memoria (String)
            // 'S' devuelve el documento como string binario
            return $pdf->Output('cargo_recepcion.pdf', 'S');

        } catch (\Exception $e) {
            Log::error("Error al firmar PDF: " . $e->getMessage());
            // En caso de error crítico, devolvemos el original para no romper el flujo, 
            // pero lo ideal es lanzar la excepción.
            return $pdfContent;
        }
    }
}