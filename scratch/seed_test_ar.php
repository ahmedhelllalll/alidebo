<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$business = \App\Models\BusinessProfile::where('slug', 'test-business')->first();
if ($business) {
    $business->translations()->updateOrCreate(
        ['locale' => 'ar'],
        [
            'name' => 'شركة الاختبار للصناعات',
            'description' => 'مكتب استشارات هندسية وتصميم معماري متكامل.',
        ]
    );
    echo "Arabic translation created successfully.\n";
}
