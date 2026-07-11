<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Models\City;

echo "Starting zero-downtime addition of 14 countries...\n";

$newCountries = [
    'AE' => ['en' => 'United Arab Emirates', 'ar' => 'الإمارات العربية المتحدة', 'de' => 'Vereinigte Arabische Emirate', 'es' => 'Emiratos Árabes Unidos', 'tr' => 'Birleşik Arap Emirlikleri', 'zh' => '阿拉伯联合酋长国'],
    'LY' => ['en' => 'Libya', 'ar' => 'ليبيا', 'de' => 'Libyen', 'es' => 'Libia', 'tr' => 'Libya', 'zh' => '利比亚'],
    'TN' => ['en' => 'Tunisia', 'ar' => 'تونس', 'de' => 'Tunesien', 'es' => 'Túnez', 'tr' => 'Tunus', 'zh' => '突尼斯'],
    'MA' => ['en' => 'Morocco', 'ar' => 'المغرب', 'de' => 'Marokko', 'es' => 'Marruecos', 'tr' => 'Fas', 'zh' => '摩洛哥'],
    'MY' => ['en' => 'Malaysia', 'ar' => 'ماليزيا', 'de' => 'Malaysia', 'es' => 'Malasia', 'tr' => 'Malezya', 'zh' => '马来西亚'],
    'TH' => ['en' => 'Thailand', 'ar' => 'تايلاند', 'de' => 'Thailand', 'es' => 'Tailandia', 'tr' => 'Tayland', 'zh' => '泰国'],
    'CN' => ['en' => 'China', 'ar' => 'الصين', 'de' => 'China', 'es' => 'China', 'tr' => 'Çin', 'zh' => '中国'],
    'KW' => ['en' => 'Kuwait', 'ar' => 'الكويت', 'de' => 'Kuwait', 'es' => 'Kuwait', 'tr' => 'Kuveyt', 'zh' => '科威特'],
    'IN' => ['en' => 'India', 'ar' => 'الهند', 'de' => 'Indien', 'es' => 'India', 'tr' => 'Hindistan', 'zh' => '印度'],
    'TR' => ['en' => 'Turkey', 'ar' => 'تركيا', 'de' => 'Türkei', 'es' => 'Turquía', 'tr' => 'Türkiye', 'zh' => '土耳其'],
    'HK' => ['en' => 'Hong Kong', 'ar' => 'هونغ كونغ', 'de' => 'Hongkong', 'es' => 'Hong Kong', 'tr' => 'Hong Kong', 'zh' => '香港'],
    'KR' => ['en' => 'South Korea', 'ar' => 'كوريا الجنوبية', 'de' => 'Südkorea', 'es' => 'Corea del Sur', 'tr' => 'Güney Kore', 'zh' => '韩国'],
    'LK' => ['en' => 'Sri Lanka', 'ar' => 'سريلانكا', 'de' => 'Sri Lanka', 'es' => 'Sri Lanka', 'tr' => 'Sri Lanka', 'zh' => '斯里兰卡'],
    'VN' => ['en' => 'Vietnam', 'ar' => 'فيتنام', 'de' => 'Vietnam', 'es' => 'Vietnam', 'tr' => 'Vietnam', 'zh' => '越南'],
];

$now = now();
$targetCodes = array_keys($newCountries);

DB::beginTransaction();
try {
    foreach ($newCountries as $code => $names) {
        Country::updateOrCreate(
            ['code' => $code],
            [
                'name_en' => $names['en'],
                'name_ar' => $names['ar'],
                'name_de' => $names['de'],
                'name_es' => $names['es'],
                'name_tr' => $names['tr'],
                'name_zh' => $names['zh'],
                'status'  => 'active'
            ]
        );
    }
    DB::commit();
    echo "1. Successfully added/updated 14 Countries.\n";
} catch (\Exception $e) {
    DB::rollBack();
    die("Failed to insert countries: " . $e->getMessage() . "\n");
}

echo "2. Loading states.json...\n";
$statesJson = json_decode(file_get_contents('states.json'), true);
if (!$statesJson) {
    die("Failed to parse states.json\n");
}

$countryRecords = Country::whereIn('code', $targetCodes)->get()->keyBy(function($c) {
    return strtoupper($c->code);
});

$cityInsertData = [];
foreach ($statesJson as $state) {
    $code = strtoupper($state['country_code']);
    if (isset($countryRecords[$code])) {
        $countryId = $countryRecords[$code]->id;
        
        // Prevent duplicates by checking if it already exists?
        // Since it's zero-downtime, it's safer to just build an array and insertOrIgnore (if Laravel 8+ supports it, else we manually check).
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
}

echo "3. Found " . count($cityInsertData) . " Governorates for these 14 countries. Processing...\n";

// To ensure zero downtime and no duplicates, we will process one by one using updateOrCreate on name_en & country_id
$insertedCount = 0;
foreach ($cityInsertData as $data) {
    City::updateOrCreate(
        [
            'country_id' => $data['country_id'],
            'name_en' => $data['name_en']
        ],
        [
            'name_ar' => $data['name_ar'],
            'name_de' => $data['name_de'],
            'name_es' => $data['name_es'],
            'name_tr' => $data['name_tr'],
            'name_zh' => $data['name_zh'],
        ]
    );
    $insertedCount++;
}

echo "4. Successfully processed $insertedCount Governorates with Zero Downtime!\n";
