<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class NotificacionGeneral extends Mailable
{
    public $mensaje;

    public function __construct($mensaje)
    {
        $this->mensaje = $mensaje;
    }

    public function build()
    {
        return $this->subject('Notificación del sistema BgaGO')
                    ->view('emails.notificacion-general');
    }
}
