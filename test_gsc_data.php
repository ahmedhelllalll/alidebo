<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$gscService = app(\App\Services\GoogleSearchConsoleService::class);
$result = $gscService->getAnalyticsData('sc-domain:alidebo.com', '2026-06-01', '2026-07-19', ['query'], 10);
print_r($result);
