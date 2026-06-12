<?php

// Script temporal para eliminar jobs que referencian Mail antiguo
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$jobs = DB::table('jobs')->where('payload','like','%CotizacionGenerada%')->get();
echo "Found: " . $jobs->count() . "\n";
foreach ($jobs as $j) {
    echo "Deleting job id: {$j->id}\n";
}
$deleted = DB::table('jobs')->where('payload','like','%CotizacionGenerada%')->delete();
echo "Deleted: $deleted\n";

return 0;
