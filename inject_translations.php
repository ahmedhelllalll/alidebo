<?php
$geoFile = __DIR__ . '/storage/app/geo_data.json';
if (!file_exists($geoFile)) {
    die("geo_data.json not found\n");
}

$data = json_decode(file_get_contents($geoFile), true);
$countries = &$data['countries'];

echo "Downloading multilingual countries database...\n";
$jsonUrl = 'https://raw.githubusercontent.com/mledoze/countries/master/countries.json';
$mledozeContent = file_get_contents($jsonUrl);
if (!$mledozeContent) {
    die("Failed to download countries dataset\n");
}

$mledozeCountries = json_decode($mledozeContent, true);

// Map by English common name
$mlMap = [];
foreach ($mledozeCountries as $ml) {
    $common = strtolower($ml['name']['common']);
    $mlMap[$common] = $ml;
    $official = strtolower($ml['name']['official']);
    $mlMap[$official] = $ml;
}

echo "Updating translations...\n";
$langs = [
    'ar' => 'ara',
    'de' => 'deu',
    'es' => 'spa',
    'tr' => 'tur',
    'zh' => 'zho'
];

$updated = 0;
foreach ($countries as &$country) {
    $name = strtolower($country['name_en']);
    
    // Some manual overrides for common mismatches
    if ($name == 'united states') $name = 'united states';
    if ($name == 'united kingdom') $name = 'united kingdom';
    
    if (isset($mlMap[$name])) {
        $ml = $mlMap[$name];
        foreach ($langs as $myLang => $mlLang) {
            $key = "name_$myLang";
            if (isset($ml['translations'][$mlLang]['common'])) {
                $country[$key] = $ml['translations'][$mlLang]['common'];
            }
        }
        $updated++;
    } else {
        echo "Warning: Could not find translations for {$country['name_en']}\n";
    }
}

file_put_contents($geoFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Successfully updated $updated countries with proper translations!\n";
