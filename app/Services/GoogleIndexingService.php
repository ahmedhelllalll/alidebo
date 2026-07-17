<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleIndexingService
{
    /**
     * Get a short-lived access token for Google API.
     */
    protected function getAccessToken()
    {
        $credentialsPath = storage_path('app/google/service-account.json');
        
        if (!file_exists($credentialsPath)) {
            throw new \Exception('Service account JSON file not found at ' . $credentialsPath);
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);
        
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $now = time();
        $claim = json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/indexing',
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

        $response = Http::asForm()->post($credentials['token_uri'], [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);
        
        if (!$response->successful()) {
            throw new \Exception('Failed to get access token: ' . $response->body());
        }

        return $response->json('access_token');
    }

    /**
     * Submit a URL to Google for indexing (URL_UPDATED or URL_DELETED)
     *
     * @param string $url
     * @param string $type 'URL_UPDATED' or 'URL_DELETED'
     * @return array
     */
    public function updateUrl($url, $type = 'URL_UPDATED')
    {
        try {
            $accessToken = $this->getAccessToken();
            $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

            $response = Http::withToken($accessToken)->post($endpoint, [
                'url' => $url,
                'type' => $type
            ]);

            if (!$response->successful()) {
                throw new \Exception('Indexing API Error: ' . $response->body());
            }
            
            return [
                'success' => true,
                'response' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('Google Indexing API Error: ' . $e->getMessage(), [
                'url' => $url,
                'type' => $type
            ]);
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
