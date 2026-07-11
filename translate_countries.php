<?php
require __DIR__ . '/vendor/autoload.php';

use Stichoza\GoogleTranslate\GoogleTranslate;

$geoFile = __DIR__ . '/storage/app/geo_data.json';
if (!file_exists($geoFile)) {
    die("geo_data.json not found\n");
}

$data = json_decode(file_get_contents($geoFile), true);
$countries = &$data['countries'];

echo "Translating " . count($countries) . " countries...\n";

$langs = ['ar', 'de', 'es', 'tr', 'zh'];

$tr = new GoogleTranslate();
$tr->setSource('en');

$total = count($countries);
$count = 0;

foreach ($countries as &$country) {
    $count++;
    echo "Translating [$count/$total] {$country['name_en']}...\n";
    foreach ($langs as $lang) {
        $key = "name_$lang";
        // If it's already translated (and not equal to English fallback), skip it
        if (!empty($country[$key]) && $country[$key] !== $country['name_en'] && $country[$key] !== null) {
            continue;
        }

        try {
            $tr->setTarget($lang == 'zh' ? 'zh-CN' : $lang);
            $translated = $tr->translate($country['name_en']);
            $country[$key] = $translated;
        } catch (Exception $e) {
            echo "Error translating to $lang: " . $e->getMessage() . "\n";
            sleep(1); // Wait a bit if rate limited
        }
    }
}

file_put_contents($geoFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Translations saved to geo_data.json!\n";
