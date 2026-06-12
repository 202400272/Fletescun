<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cfg = config('mail.mailers.smtp');
echo json_encode($cfg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";