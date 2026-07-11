<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\City;
use App\Models\Country;
use Stichoza\GoogleTranslate\GoogleTranslate;

$arabicCountries = [
    'Egypt', 'Saudi Arabia', 'United Arab Emirates', 'Kuwait', 'Qatar', 'Bahrain', 'Oman', 'Yemen', 
    'Jordan', 'Lebanon', 'Syria', 'Iraq', 'Palestine', 'Sudan', 'Libya', 'Tunisia', 'Algeria', 
    'Morocco', 'Mauritania', 'Somalia', 'Djibouti', 'Comoros', 'Palestinian Territory Occupied', 
    'State of Palestine', 'Syrian Arab Republic'
];

$countryIds = Country::whereIn('name_en', $arabicCountries)->pluck('id');

// Only translate cities where name_ar is the exact same as name_en (meaning it hasn't been translated)
$cities = City::whereIn('country_id', $countryIds)->whereColumn('name_ar', 'name_en')->get();
$total = $cities->count();
echo "Found $total cities to translate for Arabic countries.\n";

if ($total == 0) {
    echo "Nothing to translate!\n";
    exit;
}

$tr = new GoogleTranslate('ar', 'en');
$chunks = $cities->chunk(30);

$translatedCount = 0;

foreach ($chunks as $chunk) {
    $names = $chunk->pluck('name_en')->toArray();
    $textToTranslate = implode(" | ", $names);
    
    try {
        $translatedText = $tr->translate($textToTranslate);
        // The API might return Arabic comma or spaces around |. Let's clean it.
        $translatedArray = explode('|', $translatedText);
        
        if (count($translatedArray) === count($names)) {
            $i = 0;
            foreach ($chunk as $city) {
                $city->name_ar = trim($translatedArray[$i]);
                $city->save();
                $i++;
                $translatedCount++;
            }
            echo "Translated chunk... ($translatedCount / $total)\n";
        } else {
            // Fallback to one by one if delimiter broke
            echo "Chunk length mismatch, falling back to 1-by-1...\n";
            foreach ($chunk as $city) {
                try {
                    $city->name_ar = trim($tr->translate($city->name_en));
                    $city->save();
                    $translatedCount++;
                } catch (\Exception $e) {
                    echo "Error on city {$city->name_en}\n";
                }
            }
        }
    } catch (\Exception $e) {
        echo "Google Translate Error: " . $e->getMessage() . "\n";
        sleep(2);
    }
    
    // Pause to avoid rate limits
    sleep(1);
}

echo "Successfully translated $translatedCount cities!\n";
