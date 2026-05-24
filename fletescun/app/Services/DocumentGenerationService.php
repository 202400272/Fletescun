<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CotizacionGenerada;

/**
 * DocumentGenerationService
 * 
 * Orquestador central que coordina:
 * 1. Cálculo de precios finales
 * 2. Generación de Carta Porte Word
 * 3. Generación de Anexo Fotográfico PDF
 * 4. Registro en BD (documentos_generados)
 * 5. Envío por correo a gerencia
 * 6. Registro de notificaciones (log)
 */
class DocumentGenerationService
{
    protected PricingService $pricingService;
    protected CartaPorteService $cartaPorteService;
    protected AnexoFotograficoService $anexoService;

    public function __construct(
        PricingService $pricingService,
        CartaPorteService $cartaPorteService,
        AnexoFotograficoService $anexoService
    ) {
        $this->pricingService = $pricingService;
        $this->cartaPorteService = $cartaPorteService;
        $this->anexoService = $anexoService;
    }

    /**
     * Genera todos los documentos para una cotización
     * 
     * Pasos:
     * 1. Calcula precio final con PricingService
     * 2. Genera Carta Porte Word con CartaPorteService
     * 3. Genera Anexo PDF con AnexoFotograficoService
     * 4. Registra archivos en tabla documentos_generados
     * 5. Envía correo a gerencia con adjuntos
     * 6. Registra en notificaciones_correo
     * 
     * @return array Resultado de la operación:
     *   - success: bool
     *   - message: string
     *   - precios: array con desglose
     *   - documentos: array con info de archivos generados
     *   - email_enviado: bool
     */
    public function generarTodo(string $cotizacionId): array
    {
        try {
            // 1. Calcular precio final
            $precios = $this->pricingService->calcularPrecioFinal($cotizacionId);

            // Actualizar cotización con precio final
            DB::table('cotizaciones')
                ->where('id', $cotizacionId)
                ->update([
                    'precio_estimado_min' => $precios['total_final'],
                    'precio_estimado_max' => $precios['total_final'],
                    'precio_final'        => $precios['total_final'],
                    'actualizado_en'      => now(),
                ]);

            // 2. Generar Carta Porte
            $cartaPorte = $this->cartaPorteService->generar($cotizacionId);
            $this->registrarDocumento($cotizacionId, 'Carta Porte Word', $cartaPorte);

            // 3. Generar Anexo Fotográfico
            $anexo = $this->anexoService->generar($cotizacionId);
            $this->registrarDocumento($cotizacionId, 'Anexo Fotográfico PDF', $anexo);

            // 4. Enviar por correo
            $emailEnviado = $this->enviarCorreo($cotizacionId, $cartaPorte, $anexo);

            return [
                'success'       => true,
                'message'       => 'Documentos generados y enviados exitosamente',
                'precios'       => $precios,
                'documentos'    => [
                    'carta_porte' => $cartaPorte,
                    'anexo'       => $anexo,
                ],
                'email_enviado' => $emailEnviado,
            ];

        } catch (\Exception $e) {
            $this->registrarError($cotizacionId, $e->getMessage());

            return [
                'success'   => false,
                'message'   => 'Error al generar documentos: ' . $e->getMessage(),
                'error'     => $e,
            ];
        }
    }

    /**
     * Registra un documento en la tabla documentos_generados
     */
    private function registrarDocumento(
        string $cotizacionId,
        string $tipo,
        array $documento
    ): void {
        DB::table('documentos_generados')->insert([
            'cotizacion_id'   => $cotizacionId,
            'tipo'            => $tipo,
            'nombre_archivo'  => $documento['nombre_archivo'],
            'ruta_relativa'   => $documento['ruta_relativa'],
            'tamanio_bytes'   => $documento['tamanio_bytes'],
            'enviado_al_jefe' => 0,  // Se marcará como 1 después de enviar
            'creado_en'       => now(),
        ]);
    }

    /**
     * Envía correo a gerencia con los documentos adjuntos
     */
    private function enviarCorreo(
        string $cotizacionId,
        array $cartaPorte,
        array $anexo
    ): bool {
        try {
            // Obtener datos de cotización para el correo
            $cotizacion = DB::table('cotizaciones')->where('id', $cotizacionId)->first();
            $cliente = DB::table('clientes')->where('id', $cotizacion->cliente_id)->first();

            // Enviar Mailable
            Mail::to(config('mail.from.address'))  // A gerencia
                ->send(new CotizacionGenerada($cotizacion, $cliente, $cartaPorte, $anexo));

            // Marcar documentos como enviados
            DB::table('documentos_generados')
                ->where('cotizacion_id', $cotizacionId)
                ->update(['enviado_al_jefe' => 1]);

            // Registrar en log de notificaciones
            DB::table('notificaciones_correo')->insert([
                'cotizacion_id'   => $cotizacionId,
                'destinatario'    => config('mail.from.address'),
                'asunto'          => "Cotización Generada - Folio {$cotizacion->folio}",
                'tipo'            => 'Nuevo Prospecto',
                'estatus'         => 'Enviado',
                'creado_en'       => now(),
            ]);

            return true;

        } catch (\Exception $e) {
            // Registrar fallo
            DB::table('notificaciones_correo')->insert([
                'cotizacion_id'   => $cotizacionId,
                'destinatario'    => config('mail.from.address'),
                'asunto'          => "Error al enviar cotización - Folio",
                'tipo'            => 'Otro',
                'estatus'         => 'Fallido',
                'error_msg'       => $e->getMessage(),
                'creado_en'       => now(),
            ]);

            \Log::error("Error enviando correo para cotización {$cotizacionId}: " . $e->getMessage());

            return false;
        }
    }

    /**
     * Registra un error en el log
     */
    private function registrarError(string $cotizacionId, string $error): void
    {
        DB::table('notificaciones_correo')->insert([
            'cotizacion_id'   => $cotizacionId,
            'destinatario'    => 'sistema@fletescun.local',
            'asunto'          => 'Error en generación de documentos',
            'tipo'            => 'Otro',
            'estatus'         => 'Fallido',
            'error_msg'       => $error,
            'creado_en'       => now(),
        ]);

        \Log::error("DocumentGenerationService error para {$cotizacionId}: {$error}");
    }
}
