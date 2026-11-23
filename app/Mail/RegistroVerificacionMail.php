<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistroVerificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public Usuario $usuario;
    public string $verifyUrl;

    public function __construct(Usuario $usuario, string $verifyUrl)
    {
        $this->usuario = $usuario;
        $this->verifyUrl = $verifyUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verifica tu correo electrónico - UniSpacePro',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registro_verificacion',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
