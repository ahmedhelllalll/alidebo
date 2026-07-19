<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$credentialsPath = storage_path('app/google/service-account.json');
$credentials = json_decode(file_get_contents($credentialsPath), true);

$header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
$now = time();
$claim = json_encode([
    'iss' => $credentials['client_email'],
    'scope' => 'https://www.googleapis.com/auth/webmasters.readonly',
    'aud' => $credentials['token_uri'],
    'exp' => $now + 3600,
    'iat' => $now,
]);

$base64UrlEncode = function ($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
};

$signatureInput = $base64UrlEncode($header) . '.' . $base64UrlEncode($claim);
$signature = '';
openssl_sign($signatureInput, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);

$jwt = $signatureInput . '.' . $base64UrlEncode($signature);

$response = \Illuminate\Support\Facades\Http::asForm()->post($credentials['token_uri'], [
    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
    'assertion' => $jwt
]);

$accessToken = $response->json('access_token');

$sitesResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
    ->get('https://searchconsole.googleapis.com/webmasters/v3/sites');

echo "Sites Response:\n";
print_r($sitesResponse->json());
