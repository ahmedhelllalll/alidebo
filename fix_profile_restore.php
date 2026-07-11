<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BusinessProfile;
use App\Models\City;
use App\Models\Country;

echo "Restoring Business Profiles...\n";
$profiles = BusinessProfile::whereNotNull('address')->get();

$egypt = Country::where('name_en', 'Egypt')->first();
$egyptGovs = $egypt ? City::where('country_id', $egypt->id)->get() : collect([]);

$fixed = 0;
foreach ($profiles as $profile) {
    $address = strtolower($profile->address);
    $matchedGov = null;
    
    // Hardcoded known mapping
    if (str_contains($address, 'alexandria') || str_contains($address, 'alex') || str_contains($address, 'sidi gaber') || str_contains($address, 'عمارة سرايا رشدي')) {
        $matchedGov = $egyptGovs->where('name_en', 'Alexandria')->first();
    } elseif (str_contains($address, 'cairo') || str_contains($address, 'nasr city') || str_contains($address, 'التجمع')) {
        $matchedGov = $egyptGovs->where('name_en', 'Cairo')->first();
    } elseif (str_contains($address, 'giza') || str_contains($address, 'zayed') || str_contains($address, 'october') || str_contains($address, 'الاهرام') || str_contains($address, 'الجيزة')) {
        $matchedGov = $egyptGovs->where('name_en', 'Giza')->first();
    } elseif (str_contains($address, 'الزقازيق')) {
        $matchedGov = $egyptGovs->filter(fn($g) => str_contains(strtolower($g->name_en), 'sharqi'))->first();
    } elseif (str_contains($address, 'matrouh')) {
        $matchedGov = $egyptGovs->where('name_en', 'Matrouh')->first();
    } elseif (str_contains($address, 'riyadh') || str_contains($address, 'king abdulaziz')) {
        $sa = Country::where('name_en', 'Saudi Arabia')->first();
        if ($sa) {
            $matchedGov = City::where('country_id', $sa->id)->where('name_en', 'like', '%Riyadh%')->first();
        }
    } elseif (str_contains($address, 'dandong')) {
        $cn = Country::where('name_en', 'China')->first();
        if ($cn) {
            $matchedGov = City::where('country_id', $cn->id)->where('name_en', 'like', '%Liaoning%')->first();
        }
    }
    
    if (!$matchedGov && $egyptGovs->count() > 0) {
        foreach ($egyptGovs as $gov) {
            $nameEn = strtolower($gov->name_en);
            $nameEn = trim(str_replace('governorate', '', $nameEn));
            $nameAr = strtolower($gov->name_ar);
            if (($nameEn && str_contains($address, $nameEn)) || ($nameAr && str_contains($address, $nameAr))) {
                $matchedGov = $gov;
                break;
            }
        }
    }
    
    if ($matchedGov) {
        $profile->city_id = $matchedGov->id;
        $profile->save();
        echo "Mapped profile {$profile->id} to {$matchedGov->name_en}\n";
        $fixed++;
    } else {
        echo "Could not map profile {$profile->id} with address: {$profile->address}\n";
    }
}

echo "Restored $fixed profiles!\n";
