<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BusinessProfile;
use App\Models\City;
use App\Models\Country;

$profiles = BusinessProfile::whereNull('city_id')->whereNotNull('address')->get();
$fixed = 0;

$egypt = Country::where('name_en', 'Egypt')->first();

if (!$egypt) {
    echo "Egypt not found!\n";
    exit;
}

$egyptCities = City::where('country_id', $egypt->id)->get();

foreach ($profiles as $profile) {
    $address = strtolower($profile->address);
    $matchedCity = null;
    
    // Check if address matches any Egypt city
    foreach ($egyptCities as $city) {
        $cityNameEn = strtolower($city->name_en);
        $cityNameAr = strtolower($city->name_ar);
        
        if ($cityNameEn && str_contains($address, $cityNameEn)) {
            $matchedCity = $city;
            break;
        }
        if ($cityNameAr && str_contains($address, $cityNameAr)) {
            $matchedCity = $city;
            break;
        }
    }
    
    // Specific overrides based on known addresses in output
    if (!$matchedCity) {
        if (str_contains($address, 'alexandria') || str_contains($address, 'alex')) {
            $matchedCity = $egyptCities->where('name_en', 'Alexandria')->first();
        } elseif (str_contains($address, 'cairo') || str_contains($address, 'nasr city') || str_contains($address, 'التجمع') || str_contains($address, 'zayed')) {
            $matchedCity = $egyptCities->where('name_en', 'Cairo')->first();
        } elseif (str_contains($address, 'giza') || str_contains($address, 'الاهرام') || str_contains($address, 'الجيزة')) {
            $matchedCity = $egyptCities->where('name_en', 'Giza')->first();
        } elseif (str_contains($address, 'الزقازيق')) {
            $matchedCity = $egyptCities->where('name_en', 'Zagazig')->first();
        } elseif (str_contains($address, 'matrouh')) {
            $matchedCity = $egyptCities->where('name_en', 'Mersa Matruh')->first();
        }
    }
    
    if ($matchedCity) {
        $profile->city_id = $matchedCity->id;
        $profile->save();
        echo "Mapped profile {$profile->id} to city: {$matchedCity->name_en} ({$matchedCity->id})\n";
        $fixed++;
    } else {
        echo "Could not map profile {$profile->id} with address: {$profile->address}\n";
    }
}

echo "Fixed $fixed profiles.\n";
