<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\BusinessProfile::all() as $b) {
    echo "Slug: {$b->slug}, Status: {$b->status}, Approved Products: " . $b->products()->where('status', 'approved')->count() . "\n";
}
