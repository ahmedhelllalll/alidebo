<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BusinessProfile;
use App\Models\City;

function updateProfile($id, $cityNameEn) {
    $city = City::where('name_en', 'like', "%$cityNameEn%")->first();
    if ($city) {
        $p = BusinessProfile::find($id);
        if ($p) {
            $p->city_id = $city->id;
            $p->save();
            echo "Updated $id to $cityNameEn ({$city->id})\n";
        }
    } else {
        echo "City $cityNameEn not found\n";
    }
}

updateProfile(7, 'Zayed'); // El Sheikh Zayed
updateProfile(21, 'Alexandria');
updateProfile(22, 'October'); // 6th of October
updateProfile(25, 'Dandong');
updateProfile(39, 'Zayed');
updateProfile(54, 'Alexandria');

// 33 is Online, 69 and 70 are junk. 20 is King Abdulaziz Rd (probably Riyadh or Jeddah, we can just leave it or set to Riyadh).
updateProfile(20, 'Riyadh');

echo "Manual overrides complete!\n";
