<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Country;
use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Delete countries that don't have all translations OR don't have governorates
$deleted = Country::whereNull('name_ar')
    ->orWhereNull('name_de')
    ->orWhereNull('name_es')
    ->orWhereNull('name_tr')
    ->orWhereNull('name_zh')
    ->orWhereDoesntHave('cities')
    ->delete();

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Cleaned up $deleted invalid/empty countries!\n";
