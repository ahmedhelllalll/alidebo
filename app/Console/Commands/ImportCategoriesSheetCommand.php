<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportCategoriesSheetCommand extends Command
{
    protected $signature = 'categories:seed-v2 {--database= : Target database connection}';
    protected $description = 'Seed the 203 categories from database/data/categories.json into new_categories table';

    public function handle(): int
    {
        $db = $this->option('database') ?: config('database.default');
        $this->info("Target database connection: {$db}");

        $jsonPath = database_path('data/categories.json');
        if (!File::exists($jsonPath)) {
            $this->error("Data file not found at: {$jsonPath}");
            return Command::FAILURE;
        }

        $categories = json_decode(File::get($jsonPath), true);
        if (!$categories || !is_array($categories)) {
            $this->error("Invalid or empty JSON in: {$jsonPath}");
            return Command::FAILURE;
        }

        $this->info("Found " . count($categories) . " categories in JSON. Seeding into new_categories...");

        $conn = DB::connection($db);

        // Sort so level 1 (parents) are inserted before level 2 (children)
        usort($categories, fn($a, $b) => $a['level'] <=> $b['level'] ?: $a['id'] <=> $b['id']);

        $bar = $this->output->createProgressBar(count($categories));
        $bar->start();

        $conn->beginTransaction();
        try {
            foreach ($categories as $cat) {
                // Upsert into new_categories
                $conn->table('new_categories')->updateOrInsert(
                    ['id' => $cat['id']],
                    [
                        'parent_id'  => $cat['parent_id'],
                        'level'      => $cat['level'],
                        'name_en'    => $cat['name_en'],
                        'name_ar'    => $cat['name_ar'],
                        'slug'       => $cat['slug'],
                        'sort_order' => $cat['sort_order'],
                        'status'     => 'active',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                // Populate seo_metadata with scoped alias 'category_v2'
                $metaTitle = [
                    'ar' => $cat['seo_title'] ?: ($cat['name_ar'] . ' في مصر | alidebo'),
                    'en' => $cat['name_en'] . ' Companies & Suppliers | Alidebo',
                ];
                $metaDesc = [
                    'ar' => $cat['meta_description'] ?: ('تصفح دليل ' . $cat['name_ar'] . ' في مصر عبر alidebo.com'),
                    'en' => 'Find verified ' . $cat['name_en'] . ' companies, manufacturers, and suppliers on Alidebo B2B Directory.',
                ];

                $conn->table('seo_metadata')->updateOrInsert(
                    [
                        'seoable_type' => 'category_v2',
                        'seoable_id'   => $cat['id'],
                    ],
                    [
                        'meta_title'       => json_encode($metaTitle, JSON_UNESCAPED_UNICODE),
                        'meta_description' => json_encode($metaDesc, JSON_UNESCAPED_UNICODE),
                        'updated_at'       => now(),
                        'created_at'       => now(),
                    ]
                );

                $bar->advance();
            }

            $conn->commit();
            $bar->finish();
            $this->newLine();
            $this->info("Successfully seeded " . count($categories) . " categories and SEO metadata on connection: {$db}");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $conn->rollBack();
            $this->newLine();
            $this->error("Seeding failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
