<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncGeoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geo:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync countries and cities data in 6 languages from a JSON file with zero downtime';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = storage_path('app/geo_data.json');

        if (!file_exists($filePath)) {
            $this->error("Geo data file not found at: {$filePath}");
            $this->info("Please place a JSON file containing countries and cities data in storage/app/geo_data.json");
            return;
        }

        $this->info('Reading geo data...');
        $data = json_decode(file_get_contents($filePath), true);

        if (!$data || !isset($data['countries']) || !isset($data['cities'])) {
            $this->error('Invalid JSON structure. Ensure it has "countries" and "cities" arrays.');
            return;
        }

        $this->info('Syncing countries...');
        $countries = collect($data['countries']);
        $countries->chunk(500)->each(function ($chunk) {
            \App\Models\Country::upsert(
                $chunk->toArray(),
                ['code'], // Unique column to match
                ['name_en', 'name_ar', 'name_de', 'name_es', 'name_tr', 'name_zh', 'status'] // Columns to update
            );
        });
        $this->info('Countries synced successfully.');

        $this->info('Syncing cities...');
        $cities = collect($data['cities']);
        $cities->chunk(500)->each(function ($chunk) {
            \App\Models\City::upsert(
                $chunk->toArray(),
                ['id'], // Assuming id is provided in the JSON to match existing records, or maybe name_en+country_id
                ['country_id', 'name_en', 'name_ar', 'name_de', 'name_es', 'name_tr', 'name_zh', 'status'] // Columns to update
            );
        });
        $this->info('Cities synced successfully.');

        $this->info('Geo data sync completed!');
    }
}
