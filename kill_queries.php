<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$procs = DB::select('SHOW PROCESSLIST');
foreach ($procs as $p) {
    if (str_contains($p->Info ?? '', 'DELETE') || str_contains($p->Info ?? '', 'truncate')) {
        echo "Killing query {$p->Id}: {$p->Info}\n";
        DB::statement("KILL {$p->Id}");
    }
}
echo "Done checking queries.\n";
