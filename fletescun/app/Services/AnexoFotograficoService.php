<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * AnexoFotograficoService
 * 
 * Genera automáticamente un PDF que contiene:
 * - Portada con datos del folio
 * - Resumen del servicio
 * - Galería de fotografías subidas por el cliente
 * - Inventario declarado
 */
class AnexoFotograficoService
{
    protected PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Genera el Anexo Fotográfico PDF para una cotización
     * 
     * @return array Contiene:
     *   - nombre_archivo: nombre del archivo generado
     *   - ruta_relativa: ruta en storage/
     *   - ruta_absoluta: ruta completa del archivo
     */
    public function generar(string $cotizacionId): array
    {
        // Recuperar datos
        $cotizacion = DB::table('cotizaciones')->where('id', $cotizacionId)->first();
        $cliente = DB::table('clientes')->where('id', $cotizacion->cliente_id)->first();

        // Obtener fotos
        $fotos = DB::table('fotos_anexo')
            ->where('cotizacion_id', $cotizacionId)
            ->orderBy('orden')
            ->get()
            ->toArray();

        // Obtener inventario
        $inventario = DB::table('inventario_articulos')
            ->where('cotizacion_id', $cotizacionId)
            ->orderBy('orden')
            ->get()
            ->toArray();

        // Calcular precios
        $precios = $this->pricingService->calcularPrecioFinal($cotizacionId);

        // Generar HTML para PDF
        $html = $this->generarHTML(
            $cotizacion,
            $cliente,
            $fotos,
            $inventario,
            $precios
        );

        // Generar PDF desde HTML
        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

        return $this->guardarArchivo($pdf, $cotizacion->folio);
    }

    /**
     * Genera el HTML para el PDF
     */
    private function generarHTML(
        object $cotizacion,
        object $cliente,
        array $fotos,
        array $inventario,
        array $precios
    ): string {
        $fotosHTML = $this->generarGaleriaFotos($fotos);
        $inventarioHTML = $this->generarTablaInventario($inventario);
        $preciosHTML = $this->generarDesglosePrecio($precios);

        $fechaActual = now()->format('d/m/Y H:i');

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Anexo Fotográfico - {$cotizacion->folio}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
        }
        page { page-break-after: always; }
        .container { max-width: 210mm; margin: 0 auto; padding: 20px; }
        
        .portada {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            color: white;
            padding: 60px 40px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 40px;
        }
        .portada h1 { font-size: 2.5em; margin-bottom: 20px; }
        .portada .folio { font-size: 1.5em; font-weight: bold; margin: 20px 0; }
        .portada .info { font-size: 0.9em; margin: 10px 0; }
        
        .section-title {
            background: #2563EB;
            color: white;
            padding: 15px 20px;
            font-size: 1.3em;
            font-weight: bold;
            margin-top: 40px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .cliente-info {
            background: #f0f4f8;
            padding: 20px;
            border-left: 4px solid #2563EB;
            margin-bottom: 30px;
        }
        .cliente-info p { margin: 5px 0; }
        .cliente-info strong { color: #2563EB; }
        
        .galeria-fotos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .foto-item {
            background: white;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .foto-item img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 3px;
            margin-bottom: 5px;
        }
        .foto-item-num {
            font-size: 0.8em;
            color: #666;
        }
        
        .inventario-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .inventario-table th {
            background: #2563EB;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        .inventario-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
        }
        .inventario-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .precios-tabla {
            width: 100%;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .precio-row {
            display: flex;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .precio-concepto { flex: 1; }
        .precio-monto { width: 150px; text-align: right; font-weight: bold; }
        .precio-row.encabezado {
            background: #f0f4f8;
            font-weight: bold;
        }
        .precio-row.total {
            background: #2563EB;
            color: white;
            font-size: 1.2em;
            font-weight: bold;
        }
        
        .pie {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 0.9em;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- PORTADA -->
        <div class="portada">
            <h1>ANEXO FOTOGRÁFICO</h1>
            <p style="font-size: 1.1em; margin: 20px 0;">Mudanza Fletescun</p>
            <div class="folio">Folio: {$cotizacion->folio}</div>
            <div class="info">Cliente: {$cliente->nombre}</div>
            <div class="info">Teléfono: {$cliente->telefono}</div>
            <div class="info">Correo: {$cliente->correo}</div>
            <div class="info" style="margin-top: 30px; font-size: 0.9em;">Generado: {$fechaActual}</div>
        </div>

        <!-- INFORMACIÓN DEL CLIENTE -->
        <div class="section-title">INFORMACIÓN DEL CLIENTE</div>
        <div class="cliente-info">
            <p><strong>Nombre:</strong> {$cliente->nombre}</p>
            <p><strong>Teléfono:</strong> {$cliente->telefono}</p>
            <p><strong>Correo:</strong> {$cliente->correo}</p>
            <p><strong>Origen:</strong> {$cotizacion->direccion_origen}</p>
            <p><strong>Destino:</strong> {$cotizacion->direccion_destino}</p>
            <p><strong>Modalidad:</strong> {$cotizacion->tipo_servicio}</p>
        </div>

        <!-- GALERÍA DE FOTOS -->
        <div class="section-title">FOTOGRAFÍAS ADJUNTAS</div>
        {$fotosHTML}

        <!-- INVENTARIO -->
        <div class="section-title">INVENTARIO DECLARADO</div>
        {$inventarioHTML}

        <!-- DESGLOSE DE PRECIOS -->
        <div class="section-title">RESUMEN DE COTIZACIÓN</div>
        {$preciosHTML}

        <!-- PIE -->
        <div class="pie">
            <p>Este es un documento de referencia generado automáticamente por el sistema de cotización de FletesCun.</p>
            <p>Para información oficial, consultar la Carta Porte Word correspondiente.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Genera HTML de la galería de fotos
     * 
     * IMPORTANTE: DomPDF requiere rutas absolutas o URLs HTTP completas
     * No funciona con rutas relativas como /storage/...
     */
    private function generarGaleriaFotos(array $fotos): string
    {
        if (empty($fotos)) {
            return '<p style="color: #999; text-align: center;">No hay fotos adjuntas.</p>';
        }

        $html = '<div class="galeria-fotos">';

        foreach ($fotos as $index => $foto) {
            $numero = $index + 1;
            
            // Obtener ruta absoluta del archivo
            $rutaAbsoluta = storage_path('app/public/' . $foto->ruta_relativa);
            
            // Validar que el archivo existe
            if (!file_exists($rutaAbsoluta)) {
                \Log::warning("Foto no encontrada: {$rutaAbsoluta}");
                $html .= <<<HTML
                    <div class="foto-item">
                        <div style="background: #f0f0f0; height: 200px; display: flex; align-items: center; justify-content: center; border-radius: 3px;">
                            <p style="color: #999; font-size: 0.8em;">Archivo no disponible</p>
                        </div>
                        <div class="foto-item-num">Foto {$numero}</div>
                    </div>
                HTML;
                continue;
            }
            
            // Convertir la ruta absoluta a base64 data URL para máxima compatibilidad con DomPDF
            $imageData = base64_encode(file_get_contents($rutaAbsoluta));
            $mimeType = $foto->tipo_mime ?? 'image/jpeg';
            $dataUrl = "data:{$mimeType};base64,{$imageData}";
            
            $html .= <<<HTML
                <div class="foto-item">
                    <img src="{$dataUrl}" alt="Foto {$numero}" style="max-width: 100%; height: 200px; object-fit: cover; border-radius: 3px; margin-bottom: 5px;">
                    <div class="foto-item-num">Foto {$numero}</div>
                </div>
            HTML;
        }

        $html .= '</div>';
        
        \Log::info("Galería de fotos generada con " . count($fotos) . " imágenes");

        return $html;
    }

    /**
     * Genera HTML de la tabla de inventario
     */
    private function generarTablaInventario(array $inventario): string
    {
        if (empty($inventario)) {
            return '<p style="color: #999;">No hay inventario registrado.</p>';
        }

        $html = <<<HTML
            <table class="inventario-table">
                <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Artículo</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
        HTML;

        foreach ($inventario as $item) {
            $observacion = $item->observaciones ?? '—';
            $html .= <<<HTML
                    <tr>
                        <td>{$item->cantidad}</td>
                        <td>{$item->nombre}</td>
                        <td>{$observacion}</td>
                    </tr>
            HTML;
        }

        $html .= <<<HTML
                </tbody>
            </table>
        HTML;

        return $html;
    }

    /**
     * Genera HTML del desglose de precios
     */
    private function generarDesglosePrecio(array $precios): string
    {
        $html = '<div class="precios-tabla">';

        $html .= <<<HTML
            <div class="precio-row encabezado">
                <div class="precio-concepto">CONCEPTO</div>
                <div class="precio-monto">MONTO</div>
            </div>
        HTML;

        $html .= <<<HTML
            <div class="precio-row">
                <div class="precio-concepto">Volumen ({$precios['volumen_m3']} m³)</div>
                <div class="precio-monto">\${$precios['costo_volumen']}</div>
            </div>
            <div class="precio-row">
                <div class="precio-concepto">Distancia ({$precios['distancia_km']} km)</div>
                <div class="precio-monto">\${$precios['costo_distancia']}</div>
            </div>
            <div class="precio-row">
                <div class="precio-concepto">Maniobra por piso</div>
                <div class="precio-monto">\$0.00</div>
            </div>
            <div class="precio-row">
                <div class="precio-concepto">Subtotal</div>
                <div class="precio-monto">\${$precios['subtotal_con_modalidad']}</div>
            </div>
            <div class="precio-row">
                <div class="precio-concepto">IVA (16%)</div>
                <div class="precio-monto">\${$precios['iva_monto']}</div>
            </div>
            <div class="precio-row total">
                <div class="precio-concepto">TOTAL</div>
                <div class="precio-monto">\${$precios['total_final']}</div>
            </div>
        HTML;

        $html .= '</div>';

        return $html;
    }

    /**
     * Guarda el PDF en storage
     */
    private function guardarArchivo($pdf, string $folio): array
    {
        $fileName = "AnexoFotografico_{$folio}.pdf";
        $relativePath = "documentos/{$fileName}";
        $destinationPath = storage_path("app/public/{$relativePath}");

        // Crear directorio si no existe
        if (!is_dir(dirname($destinationPath))) {
            mkdir(dirname($destinationPath), 0755, true);
        }

        // Guardar PDF
        $pdf->save($destinationPath);

        return [
            'nombre_archivo' => $fileName,
            'ruta_relativa'  => $relativePath,
            'ruta_absoluta'  => $destinationPath,
            'tamanio_bytes'  => filesize($destinationPath),
        ];
    }
}
