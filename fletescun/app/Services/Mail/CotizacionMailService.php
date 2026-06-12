<?php

namespace App\Services\Mail;

use App\Mail\CotizacionDocumentacionMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class CotizacionMailService
{
    public function __construct() {
    }

    /**
     * Envía la documentación generada de una cotización (docx + pdf) por SMTP.
     */
    public function enviarDocumentacion(string $cotizacionId, array $cartaPorte, array $anexo): void
    {
        // 1) Recolectar data para el correo
        $cotizacion = DB::table('cotizaciones')->where('id', $cotizacionId)->first();
        if (!$cotizacion) return;

        $cliente = DB::table('clientes')->where('id', $cotizacion->cliente_id)->first();
        if (!$cliente) return;

        $inventario = DB::table('inventario_articulos')
            ->where('cotizacion_id', $cotizacionId)
            ->orderBy('orden')
            ->get();

        $servicios = DB::table('servicios_adicionales')
            ->where('cotizacion_id', $cotizacionId)
            ->pluck('servicio')
            ->toArray();

        $fotosCount = (int) DB::table('fotos_anexo')->where('cotizacion_id', $cotizacionId)->count();

        $telefonoDigits = preg_replace('/[^0-9]/', '', (string) ($cliente->telefono ?? ''));
        $waLink = $telefonoDigits ? ('https://wa.me/' . $telefonoDigits) : null;
        $telLink = $telefonoDigits ? ('tel:' . $telefonoDigits) : null;

        $logoUrl = url(config('cotizacion_mail.logo_path'));

        // Destinatario principal: correo del cliente. Copia a dirección interna de gerencia si está configurada.
        $toCliente = $cliente->correo ?? null;
        $toInterno = config('cotizacion_mail.to') ?: null;

        if (! $toCliente && ! $toInterno) {
            Log::warning('No hay destinatario configurado para enviar la documentación.', ['cotizacion_id' => $cotizacionId]);
            return;
        }

        $mailable = new CotizacionDocumentacionMail(
            cotizacion: $cotizacion,
            cliente: $cliente,
            inventario: $inventario,
            servicios: $servicios,
            fotosCount: $fotosCount,
            cartaPorte: $cartaPorte,
            anexo: $anexo,
            logoUrl: $logoUrl,
            waLink: $waLink,
            telLink: $telLink,
        );

        try {
            // Intentar envío síncrono primero para asegurar que el correo
            // se entregue al finalizar el flujo. Si falla (por ejemplo,
            // por un problema de transporte), se intentará encolar.
            $sender = Mail::to($toCliente ?: $toInterno);
            if ($toInterno && $toCliente) $sender = $sender->cc($toInterno);

            $sender->send($mailable);
            Log::info('Correo de documentación enviado (intentado en sync).', ['cotizacion_id' => $cotizacionId, 'to' => $toCliente, 'cc' => $toInterno]);
        } catch (\Throwable $e) {
            Log::warning('Envio síncrono falló, encolando como fallback: ' . $e->getMessage(), ['cotizacion_id' => $cotizacionId]);

            try {
                $sender = Mail::to($toCliente ?: $toInterno);
                if ($toInterno && $toCliente) $sender = $sender->cc($toInterno);
                $sender->queue($mailable);

                Log::info('Correo de documentación encolado para envío (fallback).', ['cotizacion_id' => $cotizacionId, 'to' => $toCliente, 'cc' => $toInterno]);
            } catch (\Throwable $e2) {
                Log::error('Error encolar correo de documentación: ' . $e2->getMessage(), ['cotizacion_id' => $cotizacionId]);
            }
        }
    }
}
