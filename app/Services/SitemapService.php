<?php

namespace App\Services;

use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class SitemapService
{
    public function generate()
    {
        $urls = [];
        $baseUrl = config('app.url');

        // Add static or dynamic pages
        $pages = Page::where('status', 'published')->get();
        foreach ($pages as $page) {
            $urls[] = [
                'loc' => $baseUrl . '/' . $page->slug,
                'lastmod' => $page->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }

        // Add Categories
        $categories = Category::where('status', 'active')->get();
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => $baseUrl . '/directory?category=' . $category->slug,
                'lastmod' => $category->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ];
        }

        // Add Businesses
        $businesses = BusinessProfile::where('status', 'approved')->get();
        foreach ($businesses as $business) {
            $urls[] = [
                'loc' => $baseUrl . '/' . $business->slug,
                'lastmod' => $business->updated_at->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.9'
            ];
        }

        // Construct XML
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

        foreach ($urls as $url) {
            $urlElement = $xml->addChild('url');
            $urlElement->addChild('loc', htmlspecialchars($url['loc']));
            $urlElement->addChild('lastmod', $url['lastmod']);
            $urlElement->addChild('changefreq', $url['changefreq']);
            $urlElement->addChild('priority', $url['priority']);
        }

        $xmlString = $xml->asXML();

        // Cache it for 24 hours
        Cache::put('sitemap_xml', $xmlString, now()->addHours(24));
        Cache::put('sitemap_last_generated', now(), now()->addHours(24));
        Cache::put('sitemap_url_count', count($urls), now()->addHours(24));

        return $xmlString;
    }

    public function getCachedSitemap()
    {
        if (!Cache::has('sitemap_xml')) {
            return $this->generate();
        }
        return Cache::get('sitemap_xml');
    }
}
