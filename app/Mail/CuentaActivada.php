<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CuentaActivada extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $password; // Aquí guardamos la clave plana

    public function __construct(User $usuario, $password)
    {
        $this->usuario = $usuario;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Sus Credenciales de Acceso - GORE Pasco')
                    ->view('emails.cuenta-activada');
    }
}