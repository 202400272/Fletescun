<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CotizacionDocumentacionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly object $cotizacion,
        public readonly object $cliente,
        public readonly \Illuminate\Support\Collection $inventario,
        public readonly array $servicios,
        public readonly int $fotosCount,
        public readonly array $cartaPorte,
        public readonly array $anexo,
        public readonly string $logoUrl,
        public readonly ?string $waLink,
        public readonly ?string $telLink,
    ) {
    }

    public function envelope(): Envelope
    {
        $folio = $this->cotizacion->folio ?? 'SIN_FOLIO';

        return new Envelope(
            subject: "Documentación de cotización — Folio {$folio}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cotizacion-documentacion',
            with: [
                'cotizacion' => $this->cotizacion,
                'cliente' => $this->cliente,
                'inventario' => $this->inventario,
                'servicios' => $this->servicios,
                'fotosCount' => $this->fotosCount,
                'logoUrl' => $this->logoUrl,
                'waLink' => $this->waLink,
                'telLink' => $this->telLink,
                'fechaGeneracion' => now()->format('d/m/Y H:i'),
            ],
        );
    }

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
