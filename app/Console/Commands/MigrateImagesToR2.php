<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\BusinessProfile;
use App\Models\BusinessMedia;
use Illuminate\Support\Facades\Storage;

class MigrateImagesToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:images-to-r2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate local images from public disk to Cloudflare R2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration to R2...');

        $this->migrateCategories();
        $this->migrateBusinessProfiles();
        $this->migrateBusinessMedia();

        $this->info('Migration to R2 completed successfully.');
    }

    private function migrateFile($filePath)
    {
        if (!$filePath || str_starts_with($filePath, 'http')) return false;

        $localDisk = Storage::disk('public');
        $r2Disk = Storage::disk('r2');

        try {
            if ($localDisk->exists($filePath)) {
                $contents = $localDisk->get($filePath);
                $r2Disk->put($filePath, $contents, 'public');
                return true;
            }
        } catch (\Exception $e) {
            $this->error('Failed to migrate file: ' . $filePath . ' - ' . $e->getMessage());
        }

        return false;
    }

    private function migrateCategories()
    {
        $this->info('Migrating Categories...');
        $categories = Category::where('disk', 'public')->get();
        $bar = $this->output->createProgressBar(count($categories));
        
        foreach ($categories as $category) {
            if ($category->image) $this->migrateFile($category->image);
            if ($category->icon) $this->migrateFile($category->icon);
            
            $category->update(['disk' => 'r2']);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function migrateBusinessProfiles()
    {
        $this->info('Migrating Business Profiles...');
        $profiles = BusinessProfile::where('disk', 'public')->get();
        $bar = $this->output->createProgressBar(count($profiles));

        foreach ($profiles as $profile) {
            if ($profile->logo) {
                $this->migrateFile($profile->logo);
            }
            if ($profile->cover && !str_contains($profile->cover, 'categories')) {
                $this->migrateFile($profile->cover);
            }
            $profile->update(['disk' => 'r2']);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function migrateBusinessMedia()
    {
        $this->info('Migrating Business Media...');
        $mediaList = BusinessMedia::where('disk', 'public')->get();
        $bar = $this->output->createProgressBar(count($mediaList));

        foreach ($mediaList as $media) {
            if ($media->file_path) {
                $this->migrateFile($media->file_path);
            }
            $media->update(['disk' => 'r2']);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }
}
