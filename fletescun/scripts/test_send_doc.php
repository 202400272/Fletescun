<?php
// scripts/test_send_doc.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $cotizacionId = DB::table('cotizaciones')->orderBy('creado_en', 'desc')->value('id');
    if (! $cotizacionId) {
        echo "ERROR: No se encontró ninguna cotización en la BD.\n";
        exit(1);
    }

    echo "Usando cotizacion_id: {$cotizacionId}\n";

    $svc = app(\App\Services\DocumentGenerationService::class);
    $resultado = $svc->generarTodo($cotizacionId);

    echo "Resultado:\n";
    echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

} catch (Throwable $e) {
    echo "EXCEPCION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(2);
}
