<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$businesses = \App\Models\BusinessProfile::where('status', 'approved')
    ->whereHas('products', function($q) {
        $q->where('status', 'approved');
    })
    ->with(['products' => function($q) {
        $q->where('status', 'approved')->with('images');
    }])
    ->get();

foreach ($businesses as $b) {
    echo "Business #{$b->id}: '{$b->name}', slug: '{$b->slug}', status: '{$b->status}'\n";
    foreach ($b->products as $p) {
        echo "  -> Product #{$p->id}: '{$p->name}', status: '{$p->status}', images: {$p->images->count()}\n";
    }
}
