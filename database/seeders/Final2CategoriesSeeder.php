<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class Final2CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Running categories:seed-final2...');
        Artisan::call('categories:seed-final2', [
            '--target' => 'both',
            '--parent-mode' => 'l1-17',
        ], $this->command->getOutput());
    }
}
