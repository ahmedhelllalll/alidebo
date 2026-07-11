<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$procs = DB::select('SHOW PROCESSLIST');
$myId = DB::select('SELECT CONNECTION_ID() as id')[0]->id;

foreach ($procs as $p) {
    if ($p->Id != $myId && $p->User != 'system user') {
        echo "Killing connection {$p->Id} ({$p->Command}: {$p->Info})\n";
        try {
            DB::statement("KILL {$p->Id}");
        } catch(Exception $e) {}
    }
}
echo "Killed all other connections.\n";
