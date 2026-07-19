<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$gscService = app(\App\Services\GoogleSearchConsoleService::class);
$siteUrl = 'sc-domain:alidebo.com';
$feedpath = 'https://alidebo.com/sitemap_ar.xml';
$url = 'https://searchconsole.googleapis.com/webmasters/v3/sites/' . urlencode($siteUrl) . '/sitemaps/' . urlencode($feedpath);

$reflection = new \ReflectionClass($gscService);
$method = $reflection->getMethod('getAccessToken');
$method->setAccessible(true);
$token = $method->invoke($gscService);

$response = \Illuminate\Support\Facades\Http::withToken($token)->get($url);
print_r($response->json());
