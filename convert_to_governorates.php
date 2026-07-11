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
// تحويل كود الدولة لـ lowercase دايماً لضمان التطابق
$countries = Country::all()->map(function($country) {
    $country->code = strtolower($country->code);
    return $country;
})->keyBy('code');

echo "Inserting ~4,000 global Governorates...\n";
$cityInsertData = [];
$now = now();

foreach ($statesJson as $state) {
    // تحويل كود الـ JSON لـ lowercase قبل الفحص
    $countryCode = strtolower($state['country_code']);
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

// جلب مصر باستخدام الحروف الصغيرة لضمان اللقط
$egypt = Country::where(DB::raw('LOWER(code)'), 'eg')->first();
$egyptGovs = $egypt ? City::where('country_id', $egypt->id)->get() : collect([]);

$fixed = 0;
foreach ($profiles as $profile) {
    $address = strtolower($profile->address);
    $matchedGov = null;
    
    if (str_contains($address, 'alexandria') || str_contains($address, 'alex') || str_contains($address, 'sidi gaber') || str_contains($address, 'عمارة سرايا رشدي')) {
        $matchedGov = $egyptGovs->filter(fn($g) => str_contains(strtolower($g->name_en), 'alexandria'))->first();
    } elseif (str_contains($address, 'cairo') || str_contains($address, 'nasr city') || str_contains($address, 'التجمع') || str_contains($address, 'مصر الجديدة') || str_contains($address, 'المقطم') || str_contains($address, 'الرحاب') || str_contains($address, 'مدينتي')) {
        $matchedGov = $egyptGovs->filter(fn($g) => str_contains(strtolower($g->name_en), 'cairo'))->first();
    } elseif (str_contains($address, 'giza') || str_contains($address, 'zayed') || str_contains($address, 'october') || str_contains($address, 'الاهرام') || str_contains($address, 'الجيزة') || str_contains($address, 'فيصل') || str_contains($address, 'الهرم') || str_contains($address, 'الدقي')) {
        $matchedGov = $egyptGovs->filter(fn($g) => str_contains(strtolower($g->name_en), 'giza'))->first();
    } elseif (str_contains($address, 'الزقازيق') || str_contains($address, 'sharqia') || str_contains($address, 'الشرقية')) {
        $matchedGov = $egyptGovs->filter(fn($g) => str_contains(strtolower($g->name_en), 'sharqia') || str_contains(strtolower($g->name_en), 'ash sharqiyah'))->first();
    } elseif (str_contains($address, 'matrouh') || str_contains($address, 'مطروح') || str_contains($address, 'el `alamein') || str_contains($address, 'العلمين')) {
        $matchedGov = $egyptGovs->filter(fn($g) => str_contains(strtolower($g->name_en), 'matrouh'))->first();
    } elseif (str_contains($address, 'riyadh') || str_contains($address, 'king abdulaziz') || str_contains($address, 'الرياض')) {
        $sa = Country::where(DB::raw('LOWER(code)'), 'sa')->first();
        if ($sa) {
            $matchedGov = City::where('country_id', $sa->id)->where(DB::raw('LOWER(name_en)'), 'like', '%riyadh%')->first();
        }
    } elseif (str_contains($address, 'dandong') || str_contains($address, 'china')) {
        $cn = Country::where(DB::raw('LOWER(code)'), 'cn')->first();
        if ($cn) {
            $matchedGov = City::where('country_id', $cn->id)->where(DB::raw('LOWER(name_en)'), 'like', '%liaoning%')->first();
        }
    }
    
    if (!$matchedGov && $egyptGovs->count() > 0) {
        foreach ($egyptGovs as $gov) {
            $nameEn = strtolower($gov->name_en);
            $nameEn = trim(str_replace('governorate', '', $nameEn));
            $nameAr = trim($gov->name_ar);
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
