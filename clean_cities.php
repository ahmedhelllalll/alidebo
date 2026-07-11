<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\City;
use Illuminate\Support\Facades\DB;

echo "Finding duplicate cities...\n";
$duplicates = DB::table('cities')
    ->select('name_en', 'country_id', DB::raw('MIN(id) as min_id'))
    ->groupBy('name_en', 'country_id')
    ->havingRaw('COUNT(id) > 1')
    ->get();

$count = count($duplicates);
echo "Found $count groups of duplicates.\n";

$deleted = 0;
foreach ($duplicates as $dup) {
    $del = DB::table('cities')
        ->where('name_en', $dup->name_en)
        ->where('country_id', $dup->country_id)
        ->where('id', '!=', $dup->min_id)
        ->delete();
    $deleted += $del;
}

echo "Deleted $deleted duplicate cities!\n";
