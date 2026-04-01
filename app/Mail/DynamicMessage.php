<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Clase Mailable para enviar mensajes dinámicos por correo electrónico.
 *
 * Esta clase se encarga de definir el asunto y el contenido del correo,
 * utilizando una vista Blade para el formato HTML.
 */
class DynamicMessage extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Contenido del mensaje.
     *
     * @var string
     */
    public $content;

    /**
     * Crea una nueva instancia del mensaje.
     *
     * @param  string  $content
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Obtiene el sobre del mensaje (Subject, To, etc.).
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva Notificación - msgs',
        );
    }

    /**
     * Obtiene la definición del contenido del mensaje.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.dynamic-message',
        );
    }

    /**
     * Obtiene los archivos adjuntos del mensaje.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
