<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionRecibida extends Mailable
{
    use Queueable, SerializesModels;

    public $asuntoNotificacion;
    public $nombreCiudadano;

    public function __construct($asunto, $nombre)
    {
        $this->asuntoNotificacion = $asunto;
        $this->nombreCiudadano = $nombre;
    }

    public function build()
    {
        return $this->subject('🔔 Nuevo Documento - Casilla Electrónica GORE PASCO')
                    ->view('emails.notificacion');
    }
}