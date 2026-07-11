<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

echo "Deleting cities...\n";
DB::table('cities')->delete();

echo "Running geo:process-source...\n";
Artisan::call('geo:process-source');
echo Artisan::output();

echo "Running inject_translations.php...\n";
exec('php inject_translations.php', $out);
echo implode("\n", $out) . "\n";

echo "Running geo:sync...\n";
Artisan::call('geo:sync');
echo Artisan::output();

echo "All done!\n";
