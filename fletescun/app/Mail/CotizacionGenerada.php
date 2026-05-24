<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

/**
 * CotizacionGenerada
 * 
 * Mailable que envía a gerencia la notificación de una nueva cotización
 * con los documentos adjuntos (Carta Porte + Anexo Fotográfico).
 */
class CotizacionGenerada extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $cotizacion;
    protected $cliente;
    protected $cartaPorte;
    protected $anexo;

    /**
     * Create a new message instance.
     */
    public function __construct($cotizacion, $cliente, $cartaPorte, $anexo)
    {
        $this->cotizacion = $cotizacion;
        $this->cliente = $cliente;
        $this->cartaPorte = $cartaPorte;
        $this->anexo = $anexo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Cotización Generada - Folio {$this->cotizacion->folio}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.cotizacion-generada',
            with: [
                'cotizacion' => $this->cotizacion,
                'cliente'    => $this->cliente,
                'folio'      => $this->cotizacion->folio,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->cartaPorte['ruta_absoluta'])
                ->as($this->cartaPorte['nombre_archivo'])
                ->withMime('application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            
            Attachment::fromPath($this->anexo['ruta_absoluta'])
                ->as($this->anexo['nombre_archivo'])
                ->withMime('application/pdf'),
        ];
    }
}
