<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "ProductCategories count: " . App\Models\ProductCategory::count() . PHP_EOL;
echo "Products count: " . App\Models\Product::count() . PHP_EOL;
echo "Products with category: " . App\Models\Product::whereNotNull('category_id')->count() . PHP_EOL;

foreach (App\Models\Product::whereNotNull('category_id')->get() as $p) {
    echo "Product #{$p->id}: cat_id={$p->category_id} ({$p->category?->name_en} / {$p->category?->name_ar})\n";
}

echo "\nCategories in ProductCategory:\n";
foreach (App\Models\ProductCategory::orderBy('order')->get() as $cat) {
    echo "ID: {$cat->id}, Slug: {$cat->slug}, EN: {$cat->name_en}, AR: {$cat->name_ar}\n";
}
