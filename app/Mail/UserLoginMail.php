<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class UserLoginMail extends Mailable implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable, SerializesModels;

    public Authenticatable $user;

    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Inicio de sesión');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.user-login');
    }
}