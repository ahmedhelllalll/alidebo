<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\ProductTranslation;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = BusinessProfile::take(5)->get();

        if ($profiles->isEmpty()) {
            $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();
            $profile = BusinessProfile::create([
                'user_id' => $user->id,
                'name' => 'Seeded Business Profile',
                'slug' => 'seeded-business-profile-' . uniqid(),
                'status' => 'approved',
            ]);
            $profiles = collect([$profile]);
        }

        foreach ($profiles as $profile) {
            Product::factory(3)->create(['business_profile_id' => $profile->id])->each(function ($product) {
                // Translations
                $locales = ['en', 'ar', 'es', 'de', 'tr', 'zh'];
                foreach ($locales as $locale) {
                    ProductTranslation::create([
                        'product_id' => $product->id,
                        'locale' => $locale,
                        'name' => "Product Name ({$locale}) - " . $product->id,
                        'short_description' => "Short description ({$locale}) - " . $product->id,
                        'full_description' => "Full detailed description ({$locale}) - " . $product->id,
                    ]);
                }

                // Images
                ProductImage::create([
                    'product_id' => $product->id,
                    'file_path' => 'products/sample.jpg',
                    'is_primary' => true,
                    'order' => 1,
                ]);

                // Specifications
                ProductSpecification::create([
                    'product_id' => $product->id,
                    'name' => ['en' => 'Color', 'ar' => 'اللون'],
                    'value' => ['en' => 'Red', 'ar' => 'أحمر'],
                ]);
            });
        }
    }
}
