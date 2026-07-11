<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Country;

$missingTrans = Country::whereNull('name_ar')
    ->orWhereNull('name_de')
    ->orWhereNull('name_es')
    ->orWhereNull('name_tr')
    ->orWhereNull('name_zh')
    ->count();

$noCities = Country::doesntHave('cities')->count();

$valid = Country::whereNotNull('name_ar')
    ->whereNotNull('name_de')
    ->whereNotNull('name_es')
    ->whereNotNull('name_tr')
    ->whereNotNull('name_zh')
    ->has('cities')
    ->count();

echo "Missing translations: $missingTrans\n";
echo "No governorates: $noCities\n";
echo "Valid (Has translations AND governorates): $valid\n";
