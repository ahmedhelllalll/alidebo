<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BusinessProfile;
use App\Models\City;

$c = City::where('name_en', 'Cairo')->first();
$g = City::where('name_en', 'Giza')->first();

foreach([5=>$c, 7=>$g, 39=>$g] as $id=>$city) {
    $p = BusinessProfile::find($id);
    if ($p && $city) {
        $p->city_id = $city->id;
        $p->save();
        echo "Fixed $id\n";
    }
}
