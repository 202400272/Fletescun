<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

/**
 * CartaPorteService - Generador de Carta Porte en Word usando plantilla
 */
class CartaPorteService
{
    protected PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Genera el documento Word (Carta Porte y Contrato) a partir de plantilla.
     */
    public function generar(string $cotizacionId): array
    {
        $cotizacion = DB::table('cotizaciones')->where('id', $cotizacionId)->first();
        if (!$cotizacion) {
            throw new \Exception('Cotizacion no encontrada para generar Carta Porte.');
        }

        $cliente = DB::table('clientes')->where('id', $cotizacion->cliente_id)->first();
        if (!$cliente) {
            throw new \Exception('Cliente no encontrado para generar Carta Porte.');
        }

        $inventario = DB::table('inventario_articulos')
            ->where('cotizacion_id', $cotizacionId)
            ->orderBy('orden')
            ->get()
            ->toArray();

        $servicios = DB::table('servicios_adicionales')
            ->where('cotizacion_id', $cotizacionId)
            ->pluck('servicio')
            ->toArray();

        $precios = $this->pricingService->calcularPrecioFinal($cotizacionId);

        $templatePath = resource_path('templates/carta_porte_template.docx');
        if (!file_exists($templatePath)) {
            throw new \Exception('No se encontro la plantilla Carta Porte.');
        }

        $template = new TemplateProcessor($templatePath);

        $template->setValue('folio', $cotizacion->folio ?? '');
        $template->setValue('fecha_expedicion', now()->format('d/m/Y'));
        $template->setValue('proveedor_nombre', 'MUDANZA FLETESCUN TU EMPRESA');
        $template->setValue('ciudad_origen', $this->extractCiudad($cotizacion->direccion_origen ?? ''));
        $template->setValue('fecha_actual', now()->format('d/m/Y H:i'));
        $template->setValue('cliente_nombre', $cliente->nombre ?? '');
        $template->setValue('cliente_telefono', $cliente->telefono ?? '');
        $template->setValue('cliente_correo', $cliente->correo ?? '');
        $template->setValue('origen_direccion', $cotizacion->direccion_origen ?? '');
        $template->setValue('origen_piso', $this->formatPiso($cotizacion->piso_origen ?? '', $cotizacion->elevador_origen ?? 0));
        $template->setValue('destino_direccion', $cotizacion->direccion_destino ?? '');
        $template->setValue('destino_piso', $this->formatPiso($cotizacion->piso_destino ?? '', $cotizacion->elevador_destino ?? 0));
        $template->setValue('modalidad', $precios['modalidad'] ?? '');
        $template->setValue('fecha_ideal', $cotizacion->fecha_ideal ? date('d/m/Y', strtotime($cotizacion->fecha_ideal)) : '');

        $template->setValue('volumen_m3', (string) ($precios['volumen_m3'] ?? 0));
        $template->setValue('distancia_km', (string) ($precios['distancia_km'] ?? 0));
        $template->setValue('precio_volumen', $this->formatMoney($precios['costo_volumen'] ?? 0));
        $template->setValue('precio_distancia', $this->formatMoney($precios['costo_distancia'] ?? 0));
        $template->setValue('precio_piso', $this->formatMoney($precios['costo_piso'] ?? 0));
        $template->setValue('precio_fijos', $this->formatMoney($precios['costos_fijos'] ?? 0));
        $template->setValue('subtotal_base', $this->formatMoney($precios['subtotal_base'] ?? 0));
        $template->setValue('iva_pct', (string) ($precios['iva_pct'] ?? 16));
        $template->setValue('iva_monto', $this->formatMoney($precios['iva_monto'] ?? 0));
        $template->setValue('total_final', $this->formatMoney($precios['total_final'] ?? 0));

        $this->renderInventario($template, $inventario);
        $this->renderServicios($template, $servicios);

        return $this->guardarArchivo($template, $cotizacion->folio);
    }

    private function renderInventario(TemplateProcessor $template, array $inventario): void
    {
        if (empty($inventario)) {
            $template->cloneRow('inv_cantidad', 1);
            $template->setValue('inv_cantidad#1', '0');
            $template->setValue('inv_articulo#1', 'Sin inventario');
            $template->setValue('inv_observacion#1', '-');
            return;
        }

        $template->cloneRow('inv_cantidad', count($inventario));
        foreach ($inventario as $index => $item) {
            $row = $index + 1;
            $template->setValue("inv_cantidad#{$row}", (string) $item->cantidad);
            $template->setValue("inv_articulo#{$row}", (string) $item->nombre);
            $template->setValue("inv_observacion#{$row}", (string) ($item->observaciones ?? '-'));
        }
    }

    private function renderServicios(TemplateProcessor $template, array $servicios): void
    {
        if (empty($servicios)) {
            $template->cloneRow('servicio', 1);
            $template->setValue('servicio#1', 'Sin servicios adicionales');
            return;
        }

        $template->cloneRow('servicio', count($servicios));
        foreach ($servicios as $index => $servicio) {
            $row = $index + 1;
            $template->setValue("servicio#{$row}", $servicio);
        }
    }

    private function formatPiso(string $piso, int $elevador): string
    {
        if ($piso === '') {
            return $elevador ? 'Con elevador' : 'No especificado';
        }

        return $elevador ? "{$piso} (con elevador)" : $piso;
    }

    private function formatMoney($value): string
    {
        return '$' . number_format((float) $value, 2);
    }

    private function extractCiudad(string $direccion): string
    {
        if ($direccion === '') {
            return 'Ciudad';
        }

        $partes = array_values(array_filter(array_map('trim', explode(',', $direccion))));
        if (count($partes) >= 2) {
            return $partes[count($partes) - 2];
        }

        return $partes[0] ?? 'Ciudad';
    }

    private function guardarArchivo(TemplateProcessor $template, string $folio): array
    {
        $file = "CartaPorte_{$folio}.docx";
        $dir = storage_path('app/public/documentos');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $path = "{$dir}/{$file}";

        $template->saveAs($path);

        if (!file_exists($path) || filesize($path) <= 0) {
            throw new \Exception('No se pudo generar el Word de Carta Porte.');
        }

        return [
            'nombre_archivo' => $file,
            'ruta_relativa'  => "documentos/{$file}",
            'ruta_absoluta'  => $path,
            'tamanio_bytes'  => filesize($path),
        ];
    }
}
