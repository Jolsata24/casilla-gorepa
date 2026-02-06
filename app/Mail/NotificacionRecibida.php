<?php

namespace App\Mail;

use App\Models\Notificacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionRecibida extends Mailable
{
    use Queueable, SerializesModels;

    public $notificacion;

    public function __construct(Notificacion $notificacion)
    {
        $this->notificacion = $notificacion;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva Notificación Electrónica - GOREPA',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notificacion',
        );
    }
}