<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSearchConsoleService
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
     * Get search analytics data
     *
     * @param string $siteUrl
     * @param string $startDate (Y-m-d)
     * @param string $endDate (Y-m-d)
     * @param array $dimensions e.g., ['query', 'date']
     * @param int $rowLimit
     * @return array
     */
    public function getAnalyticsData($siteUrl, $startDate, $endDate, $dimensions = ['query'], $rowLimit = 50)
    {
        try {
            $accessToken = $this->getAccessToken();
            $url = 'https://searchconsole.googleapis.com/webmasters/v3/sites/' . urlencode($siteUrl) . '/searchAnalytics/query';

            $response = Http::withToken($accessToken)->post($url, [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'dimensions' => $dimensions,
                'rowLimit' => $rowLimit
            ]);

            if (!$response->successful()) {
                throw new \Exception('Search Console API Error: ' . $response->body());
            }

            return [
                'success' => true,
                'rows' => $response->json('rows') ?? []
            ];
        } catch (\Exception $e) {
            Log::error('Google Search Console API Error: ' . $e->getMessage(), [
                'siteUrl' => $siteUrl,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'rows' => []
            ];
        }
    }

    /**
     * Get sitemaps submitted to Google Search Console
     *
     * @param string $siteUrl
     * @return array
     */
    public function getSitemaps($siteUrl)
    {
        try {
            $accessToken = $this->getAccessToken();
            $url = 'https://searchconsole.googleapis.com/webmasters/v3/sites/' . urlencode($siteUrl) . '/sitemaps';

            $response = Http::withToken($accessToken)->get($url);

            if (!$response->successful()) {
                throw new \Exception('Search Console API Error: ' . $response->body());
            }

            $sitemaps = $response->json('sitemap') ?? [];
            $expandedSitemaps = [];

            foreach ($sitemaps as $sitemap) {
                $expandedSitemaps[] = $sitemap;
                
                // If it's an index, try to read the local sitemap to find children
                if (!empty($sitemap['isSitemapsIndex'])) {
                    $path = parse_url($sitemap['path'], PHP_URL_PATH);
                    $localPath = public_path(ltrim($path, '/'));
                    
                    if (file_exists($localPath)) {
                        $xml = simplexml_load_file($localPath);
                        if ($xml && isset($xml->sitemap)) {
                            foreach ($xml->sitemap as $childSitemap) {
                                $childUrl = (string) $childSitemap->loc;
                                
                                // Fetch child details from GSC API
                                $childApiUrl = 'https://searchconsole.googleapis.com/webmasters/v3/sites/' . urlencode($siteUrl) . '/sitemaps/' . urlencode($childUrl);
                                $childResponse = Http::withToken($accessToken)->get($childApiUrl);
                                
                                if ($childResponse->successful()) {
                                    $childData = $childResponse->json();
                                    $childData['is_child'] = true;
                                    $expandedSitemaps[] = $childData;
                                } else {
                                    // Add a placeholder if API fetch fails or it's not known to GSC yet
                                    $expandedSitemaps[] = [
                                        'path' => $childUrl,
                                        'is_child' => true,
                                        'status' => 'Pending'
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            return [
                'success' => true,
                'sitemaps' => $expandedSitemaps
            ];
        } catch (\Exception $e) {
            Log::error('Google Search Console Sitemaps API Error: ' . $e->getMessage(), [
                'siteUrl' => $siteUrl
            ]);
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'sitemaps' => []
            ];
        }
    }

    /**
     * Get the first available site URL from Google Search Console
     *
     * @return string
     */
    public function getSiteUrl()
    {
        return \Illuminate\Support\Facades\Cache::remember('google_search_console_site_url', 86400, function () {
            try {
                $accessToken = $this->getAccessToken();
                $response = Http::withToken($accessToken)
                    ->get('https://searchconsole.googleapis.com/webmasters/v3/sites');

                if ($response->successful()) {
                    $sites = $response->json('siteEntry');
                    if (!empty($sites)) {
                        return $sites[0]['siteUrl'];
                    }
                }
            } catch (\Exception $e) {
                Log::error('Google Search Console Sites API Error: ' . $e->getMessage());
            }

            // Fallback to sc-domain of the APP_URL
            return 'sc-domain:' . parse_url(env('APP_URL'), PHP_URL_HOST);
        });
    }
}
