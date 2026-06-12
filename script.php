<?php
$path = 'c:/xampp/htdocs/Fletescun/fletescun/app/Services/AnexoFotograficoService.php';
$content = file_get_contents($path);

// Replace grid classes
$content = str_replace(
    '.galeria-fotos {
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
        }',
    '.galeria-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px;
            margin-bottom: 30px;
            page-break-inside: auto;
        }
        .galeria-table td {
            width: 33.33%;
            vertical-align: top;
            text-align: center;
            background: white;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            page-break-inside: avoid;
        }
        .foto-img {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 3px;
        }
        .foto-item-num {
            font-size: 0.8em;
            color: #666;
            margin-top: 5px;
        }',
    $content
);

// We find everything from "private function generarGaleriaFotos" up to "private function generarTablaInventario"
$start = strpos($content, 'private function generarGaleriaFotos');
$end = strpos($content, 'private function generarTablaInventario', $start);
$oldFunc = substr($content, $start, $end - $start);

$newFunc = <<<'EOD'
private function generarGaleriaFotos(array $fotos): string
    {
        if (empty($fotos)) {
            return '<p style="color: #999; text-align: center;">No hay fotos adjuntas.</p>';
        }

        $html = '<table class="galeria-table">';
        $columnas = 3;
        $i = 0;

        foreach ($fotos as $index => $foto) {
            if ($i % $columnas == 0) {
                $html .= '<tr>';
            }

            $numero = $index + 1;
            
            // Obtener ruta absoluta del archivo
            $rutaAbsoluta = storage_path('app/public/' . $foto->ruta_relativa);
            
            // Validar que el archivo existe
            if (!file_exists($rutaAbsoluta)) {
                \Log::warning("Foto no encontrada: {$rutaAbsoluta}");
                $html .= "
                    <td>
                        <div style='background: #f0f0f0; height: 180px; display: table-cell; vertical-align: middle; text-align: center;'>
                            <p style='color: #999; font-size: 0.8em; margin:0;'>Archivo no disponible</p>
                        </div>
                        <div class='foto-item-num'>Foto {$numero}</div>
                    </td>
                ";
            } else {
                // Resize/Load image
                try {
                    $imageData = base64_encode(file_get_contents($rutaAbsoluta));
                    $mimeType = $foto->tipo_mime ?? mime_content_type($rutaAbsoluta);
                    if (!$mimeType) $mimeType = 'image/jpeg';
                    $dataUrl = "data:{$mimeType};base64,{$imageData}";
                    
                    $html .= "
                        <td>
                            <img src='{$dataUrl}' alt='Foto {$numero}' class='foto-img'>
                            <div class='foto-item-num'>Foto {$numero}</div>
                        </td>
                    ";
                } catch (\Exception $e) {
                    \Log::error("Error leyendo imagen {$rutaAbsoluta}: " . $e->getMessage());
                    $html .= "<td>Error cargando imagen {$numero}</td>";
                }
            }

            $i++;
            if ($i % $columnas == 0) {
                $html .= '</tr>';
            }
        }

        if ($i % $columnas != 0) {
            $restantes = $columnas - ($i % $columnas);
            for ($j = 0; $j < $restantes; $j++) {
                $html .= '<td></td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>';
        return $html;
    }

    /**
     * Genera HTML de la tabla de inventario
     */
    
EOD;

$content = str_replace($oldFunc, $newFunc, $content);
file_put_contents($path, $content);
echo "REPLACED";
?>
