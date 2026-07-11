<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ProcessGeoSource extends Command
{
    protected $signature = 'geo:process-source';
    protected $description = 'Process source_geo.json and filter it for the 6 required languages';

    public function handle()
    {
        $this->info('Reading source_geo.json...');
        
        $filePath = storage_path('app/source_geo.json');
        
        if (!file_exists($filePath)) {
            $this->error('File source_geo.json not found in storage/app/');
            return 1;
        }

        $rawJson = file_get_contents($filePath);
        $countries = json_decode($rawJson, true);

        if (!$countries) {
            $this->error('Invalid JSON format in source_geo.json');
            return 1;
        }

        $this->info('Processing and filtering data...');
        $processedCountries = [];
        $processedCities = [];

        $countryId = 1;
        $cityId = 1;

        foreach ($countries as $country) {
            $translations = $country['translations'] ?? [];
            
            $code = $country['iso2'] ?? $country['code'] ?? strtoupper(substr($country['name'], 0, 2));
            
            $processedCountry = [
                'id' => $country['id'] ?? $countryId,
                'code' => $code,
                'name_en' => $country['name'],
                'name_ar' => $translations['fa'] ?? $translations['ar'] ?? $country['name'],
                'name_de' => $translations['de'] ?? null,
                'name_es' => $translations['es'] ?? null,
                'name_tr' => $translations['tr'] ?? null,
                'name_zh' => $translations['cn'] ?? $translations['zh'] ?? null,
                'status' => 1
            ];

            $seenCities = [];
            if (isset($country['cities']) && is_array($country['cities'])) {
                foreach ($country['cities'] as $city) {
                    $cityName = is_string($city) ? $city : ($city['name'] ?? 'Unknown');
                    $cId = is_array($city) && isset($city['id']) ? $city['id'] : $cityId;
                    
                    if (isset($seenCities[$cityName])) continue;
                    $seenCities[$cityName] = true;

                    $processedCities[] = [
                        'id' => $cId,
                        'country_id' => $processedCountry['id'],
                        'name_en' => $cityName,
                        'name_ar' => $cityName,
                        'name_de' => null,
                        'name_es' => null,
                        'name_tr' => null,
                        'name_zh' => null,
                        'status' => 1
                    ];
                    $cityId++;
                }
            }

            $processedCountries[] = $processedCountry;
            $countryId++;
        }

        $finalData = [
            'countries' => $processedCountries,
            'cities' => $processedCities
        ];

        $this->info('Saving clean data to geo_data.json...');
        $outPath = storage_path('app/geo_data.json');
        file_put_contents($outPath, json_encode($finalData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->info('Success! geo_data.json is ready at storage/app/');
        return 0;
    }
}