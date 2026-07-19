<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$gscService = app(\App\Services\GoogleSearchConsoleService::class);
$result = $gscService->getSitemaps('sc-domain:alidebo.com');
print_r($result);
