<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$p10 = App\Models\Product::find(10);
$p12 = App\Models\Product::find(12);
$p16 = App\Models\Product::find(16);

$demoImages = [
    'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=1200&q=80',
    'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?auto=format&fit=crop&w=1200&q=80',
    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1200&q=80',
    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1200&q=80',
    'https://images.unsplash.com/photo-1572635196237-14b3f281503f?auto=format&fit=crop&w=1200&q=80',
];

if ($p16) {
    $p16->images()->delete();
    foreach ($demoImages as $idx => $url) {
        $p16->images()->create([
            'file_path' => $url,
            'is_primary' => ($idx === 0),
            'order' => $idx + 1,
            'disk' => 'public'
        ]);
    }
    if ($p16->specifications()->count() === 0) {
        $p16->specifications()->create(['name' => 'Packaging Material', 'value' => 'Corrugated Kraft Board']);
        $p16->specifications()->create(['name' => 'Load Capacity', 'value' => '45 kg']);
        $p16->specifications()->create(['name' => 'Origin', 'value' => 'Egypt / 10th of Ramadan']);
    }
    $p16->translations()->updateOrCreate(['locale' => 'en'], ['name' => 'Premium Industrial Packaging Unit', 'short_description' => 'Heavy-duty commercial packaging carton units.']);
    $p16->translations()->updateOrCreate(['locale' => 'ar'], ['name' => 'وحدة تغليف صناعية فاخرة', 'short_description' => 'كرتونة تغليف صناعية متينة للمنتجات التجارية.']);
    echo "Seeded 5 images and specs for Product 16.\n";
}
    $p10->images()->delete();
    foreach ($demoImages as $idx => $url) {
        $p10->images()->create([
            'file_path' => $url,
            'is_primary' => ($idx === 0),
            'order' => $idx + 1,
            'disk' => 'public'
        ]);
    }
    echo "Seeded 5 images for Product 10.\n";

// Ensure Product 12 has 2 images for < 4 carousel demo
if ($p12->images->count() !== 2) {
    $carouselImages = [
        'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1200&q=80',
    ];
    $p12->images()->delete();
    foreach ($carouselImages as $idx => $url) {
        $p12->images()->create([
            'file_path' => $url,
            'is_primary' => ($idx === 0),
            'order' => $idx + 1,
            'disk' => 'public'
        ]);
    }
    echo "Seeded 2 images for Product 12.\n";
}

// Add sample specifications if empty
if ($p10->specifications->count() === 0) {
    $p10->specifications()->create(['name' => 'Skin Type', 'value' => 'All Skin Types']);
    $p10->specifications()->create(['name' => 'Net Weight', 'value' => '250 ml / 8.4 fl oz']);
    $p10->specifications()->create(['name' => 'Organic Certified', 'value' => 'ISO-22716 & EcoCert']);
    echo "Added specifications for Product 10.\n";
}
