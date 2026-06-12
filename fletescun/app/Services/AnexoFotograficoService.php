<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * AnexoFotograficoService
 * 
 * Genera PDF con portada, resumen, fotos e inventario.
 */
class AnexoFotograficoService
{
    protected PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Genera el Anexo Fotografico PDF para una cotizacion.
     */
    public function generar(string $cotizacionId): array
    {
        $cotizacion = DB::table('cotizaciones')->where('id', $cotizacionId)->first();
        if (!$cotizacion) {
            throw new \Exception('Cotizacion no encontrada para generar anexo.');
        }
        $cliente = DB::table('clientes')->where('id', $cotizacion->cliente_id)->first();
        if (!$cliente) {
            throw new \Exception('Cliente no encontrado para generar anexo.');
        }

        $fotos = DB::table('fotos_anexo')
            ->where('cotizacion_id', $cotizacionId)
            ->orderBy('orden')
            ->get()
            ->toArray();

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

        $imagenes = $this->buildImagenes($fotos);

        $html = view('pdf.anexo-fotografico', [
            'cotizacion' => $cotizacion,
            'cliente' => $cliente,
            'inventario' => $inventario,
            'precios' => $precios,
            'imagenes' => $imagenes,
            'servicios' => $servicios,
            'fecha_actual' => now()->format('d/m/Y H:i'),
        ])->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

        return $this->guardarArchivo($pdf, $cotizacion->folio);
    }

    private function buildImagenes(array $fotos): array
    {
        $imagenes = [];
        $maxBytes = 1024 * 1024;
        $maxDimension = 1280;
        $jpegQuality = 70;
        $gdDisponible = function_exists('imagecreatefromstring');

        foreach ($fotos as $index => $foto) {
            $path = storage_path('app/public/' . $foto->ruta_relativa);
            if (!file_exists($path)) {
                \Log::warning("Foto no encontrada: {$path}");
                continue;
            }

            try {
                $raw = file_get_contents($path);
                if ($raw === false) {
                    continue;
                }

                $mime = $foto->tipo_mime ?: mime_content_type($path);
                $mime = $mime ?: 'image/jpeg';
                $needsCompress = (filesize($path) ?: 0) > $maxBytes;

                if ($gdDisponible) {
                    $image = @imagecreatefromstring($raw);
                } else {
                    $image = false;
                }

                if ($image !== false) {
                    $width = imagesx($image);
                    $height = imagesy($image);
                    if ($width > $maxDimension || $height > $maxDimension) {
                        $needsCompress = true;
                    }

                    if ($needsCompress) {
                        $ratio = min($maxDimension / $width, $maxDimension / $height, 1);
                        $newWidth = (int) round($width * $ratio);
                        $newHeight = (int) round($height * $ratio);
                        $canvas = imagecreatetruecolor($newWidth, $newHeight);
                        $white = imagecolorallocate($canvas, 255, 255, 255);
                        imagefill($canvas, 0, 0, $white);
                        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        ob_start();
                        imagejpeg($canvas, null, $jpegQuality);
                        $compressed = ob_get_clean();
                        imagedestroy($canvas);
                        imagedestroy($image);

                        if ($compressed !== false) {
                            $imagenes[] = [
                                'data_uri' => 'data:image/jpeg;base64,' . base64_encode($compressed),
                                'label' => 'Foto ' . ($index + 1),
                            ];
                            continue;
                        }
                    }

                    imagedestroy($image);
                }

                $imagenes[] = [
                    'data_uri' => "data:{$mime};base64," . base64_encode($raw),
                    'label' => 'Foto ' . ($index + 1),
                ];
            } catch (\Exception $e) {
                \Log::error("Error leyendo imagen {$path}: " . $e->getMessage());
            }
        }

        return $imagenes;
    }

    private function guardarArchivo($pdf, string $folio): array
    {
        $fileName = "AnexoFotografico_{$folio}.pdf";
        $relativePath = "documentos/{$fileName}";
        $destinationPath = storage_path("app/public/{$relativePath}");

        if (!is_dir(dirname($destinationPath))) {
            mkdir(dirname($destinationPath), 0755, true);
        }

        $pdf->save($destinationPath);

        if (!file_exists($destinationPath) || filesize($destinationPath) <= 0) {
            throw new \Exception('No se pudo generar el PDF de anexo fotografico.');
        }

        return [
            'nombre_archivo' => $fileName,
            'ruta_relativa'  => $relativePath,
            'ruta_absoluta'  => $destinationPath,
            'tamanio_bytes'  => filesize($destinationPath),
        ];
    }
}

