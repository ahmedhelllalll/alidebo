<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Country;

$codes = ['ae', 'ly', 'tn', 'ma', 'my', 'th', 'cn', 'kw', 'in', 'tr', 'hk', 'kr', 'lk', 'vn'];
$countries = Country::whereIn('code', $codes)->get();
echo "Found " . $countries->count() . " out of " . count($codes) . " countries.\n";
foreach($countries as $c) {
    echo $c->code . " - " . $c->name_en . "\n";
}
