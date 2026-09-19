<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

foreach (App\Models\Product::all() as $p) {
    echo "Product #{$p->id}: business_id={$p->business_profile_id}, status={$p->status}, images={$p->images->count()}, title={$p->name}\n";
}
echo "\nBusinesses:\n";
foreach (App\Models\BusinessProfile::all() as $b) {
    echo "Business #{$b->id}: name={$b->name}, slug={$b->slug}, status={$b->status}, products_count={$b->products()->where('status', 'approved')->count()}\n";
}
