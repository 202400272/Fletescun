<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$failed = DB::table('failed_jobs')->orderBy('failed_at','desc')->limit(10)->get();
if ($failed->isEmpty()) {
    echo "No failed jobs.\n";
} else {
    foreach ($failed as $f) {
        echo "failed_id={$f->id} connection={$f->connection} queue={$f->queue} exception={$f->exception}\n";
    }
}

$jobs = DB::table('jobs')->orderBy('created_at','desc')->limit(10)->get();
if ($jobs->isEmpty()) {
    echo "No pending jobs.\n";
} else {
    echo "Pending jobs:\n";
    foreach ($jobs as $j) {
        echo "job_id={$j->id} queue={$j->queue} attempts={$j->attempts} payload_snippet=" . substr($j->payload,0,200) . "\n";
    }
}
