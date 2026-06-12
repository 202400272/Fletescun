<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('notificaciones_correo')->orderBy('creado_en','desc')->limit(20)->get();
foreach ($rows as $r) {
    echo "id={$r->id} cotizacion_id={$r->cotizacion_id} to={$r->destinatario} estatus={$r->estatus} asunto={$r->asunto} error={$r->error_msg}\n";
}
