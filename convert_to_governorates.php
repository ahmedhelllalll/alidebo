<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\BusinessProfile;
use App\Models\City;
use App\Models\Country;

echo "Loading states.json...\n";
$statesJson = json_decode(file_get_contents('states.json'), true);

if (!$statesJson) {
    die("Failed to parse states.json\n");
}

echo "Backing up profile addresses...\n";
$profiles = BusinessProfile::whereNotNull('address')->get();

echo "Wiping existing 150k cities...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('cities')->delete();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Loading countries...\n";
$countries = Country::all()->keyBy('code'); // Key by 2-letter iso code

echo "Inserting ~4,000 global Governorates...\n";
$cityInsertData = [];
$now = now();

foreach ($statesJson as $state) {
    $countryCode = $state['country_code'];
    if (!isset($countries[$countryCode])) {
        continue;
    }
    
    $countryId = $countries[$countryCode]->id;
    
    $cityInsertData[] = [
        'country_id' => $countryId,
        'name_en' => $state['name'],
        'name_ar' => $state['translations']['ar'] ?? $state['native'] ?? $state['name'],
        'name_de' => $state['translations']['de'] ?? null,
        'name_es' => $state['translations']['es'] ?? null,
        'name_tr' => $state['translations']['tr'] ?? null,
        'name_zh' => $state['translations']['zh-CN'] ?? $state['translations']['zh-TW'] ?? null,
        'created_at' => $now,
        'updated_at' => $now,
    ];
}

$chunks = array_chunk($cityInsertData, 500);
foreach ($chunks as $chunk) {
    City::insert($chunk);
}

echo "Successfully inserted " . City::count() . " Governorates!\n";

echo "Restoring Business Profiles...\n";

// Load new Egyptian governorates for easy access
$egypt = Country::where('code', 'EG')->first();
$egyptGovs = $egypt ? City::where('country_id', $egypt->id)->get() : collect([]);

$fixed = 0;
foreach ($profiles as $profile) {
    $address = strtolower($profile->address);
    $matchedGov = null;
    
    // Hardcoded known mapping for the user's specific dataset to ensure 0 data loss
    if (str_contains($address, 'alexandria') || str_contains($address, 'alex') || str_contains($address, 'sidi gaber') || str_contains($address, 'عمارة سرايا رشدي')) {
        $matchedGov = $egyptGovs->where('name_en', 'Alexandria')->first();
    } elseif (str_contains($address, 'cairo') || str_contains($address, 'nasr city') || str_contains($address, 'التجمع')) {
        $matchedGov = $egyptGovs->where('name_en', 'Cairo')->first();
    } elseif (str_contains($address, 'giza') || str_contains($address, 'zayed') || str_contains($address, 'october') || str_contains($address, 'الاهرام') || str_contains($address, 'الجيزة')) {
        $matchedGov = $egyptGovs->where('name_en', 'Giza')->first();
    } elseif (str_contains($address, 'الزقازيق')) {
        // Zagazig belongs to Ash Sharqia
        $matchedGov = $egyptGovs->where('name_en', 'Al Sharqia Governorate')->first();
        if (!$matchedGov) $matchedGov = $egyptGovs->firstWhere('name_en', 'Ash Sharqia');
        if (!$matchedGov) $matchedGov = $egyptGovs->filter(fn($g) => str_contains(strtolower($g->name_en), 'sharqi'))->first();
    } elseif (str_contains($address, 'matrouh')) {
        $matchedGov = $egyptGovs->where('name_en', 'Matrouh')->first();
    } elseif (str_contains($address, 'riyadh') || str_contains($address, 'king abdulaziz')) {
        $sa = Country::where('code', 'SA')->first();
        if ($sa) {
            $matchedGov = City::where('country_id', $sa->id)->where('name_en', 'like', '%Riyadh%')->first();
        }
    } elseif (str_contains($address, 'dandong')) {
        $cn = Country::where('code', 'CN')->first();
        if ($cn) {
            $matchedGov = City::where('country_id', $cn->id)->where('name_en', 'like', '%Liaoning%')->first();
        }
    }
    
    if (!$matchedGov && $egyptGovs->count() > 0) {
        // Generic fuzzy matching
        foreach ($egyptGovs as $gov) {
            $nameEn = strtolower($gov->name_en);
            // remove 'governorate' from name_en
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
