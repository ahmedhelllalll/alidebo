<?php

namespace App\Console\Commands;

use App\Jobs\OptimizeProfileContent;
use App\Models\BusinessProfile;
use Illuminate\Console\Command;

class OptimizeExistingProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'businesses:optimize-old-data {--force : Optimize all even if they already have translations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch the AI content optimization job for all existing business profiles.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to queue existing business profiles for AI optimization...');

        $force = $this->option('force');

        $query = BusinessProfile::query();

        if (!$force) {
            $query->whereDoesntHave('translations');
        }

        $count = $query->count();
        if ($count === 0) {
            $this->info('No existing profiles found that need optimization.');
            return;
        }

        $this->info("Found {$count} profiles. Dispatching to the background queue...");

        $bar = $this->output->createProgressBar($count);

        $query->chunk(100, function ($profiles) use ($bar) {
            foreach ($profiles as $profile) {
                OptimizeProfileContent::dispatch($profile);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info('Successfully dispatched all jobs! Start your queue worker to process them.');
    }
}
