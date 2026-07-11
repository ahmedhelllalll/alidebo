<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$cols = DB::select('SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.columns WHERE COLUMN_NAME = "country_id" AND TABLE_SCHEMA = DATABASE()');
foreach ($cols as $col) {
    echo $col->TABLE_NAME . ' ' . $col->COLUMN_NAME . "\n";
}
