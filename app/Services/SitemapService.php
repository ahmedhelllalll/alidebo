<?php

namespace App\Services;

use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Page;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Cache;

class SitemapService
{
    protected array $locales = ['en', 'ar', 'es', 'de', 'zh', 'tr'];

    public function generate()
    {
        // Regenerate everything
        $indexXml = $this->generateIndex();
        Cache::put('sitemap_index_xml', $indexXml, now()->addHours(24));
        Cache::put('sitemap_last_generated', now(), now()->addHours(24));
        
        $totalUrls = 0;
        foreach ($this->locales as $locale) {
            $localeXml = $this->generateForLocale($locale);
            Cache::put("sitemap_xml_{$locale}", $localeXml['xml'], now()->addHours(24));
            $totalUrls += $localeXml['count'];
        }
        
        Cache::put('sitemap_url_count', $totalUrls, now()->addHours(24));

        return $indexXml;
    }

    protected function generateIndex()
    {
        $baseUrl = config('app.url');
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>');

        foreach ($this->locales as $locale) {
            $sitemap = $xml->addChild('sitemap');
            $sitemap->addChild('loc', htmlspecialchars($baseUrl . '/sitemap_' . $locale . '.xml'));
            $sitemap->addChild('lastmod', now()->toAtomString());
        }

        return $xml->asXML();
    }

    protected function generateForLocale($locale)
    {
        $urls = [];
        $baseUrl = config('app.url');

        // Add Pages
        $pages = Page::where('status', 'published')->get();
        foreach ($pages as $page) {
            // Check if page has translation in this locale
            if (isset($page->title[$locale]) && !empty($page->title[$locale])) {
                $urls[] = [
                    'loc' => $baseUrl . '/' . $locale . '/' . $page->slug,
                    'lastmod' => $page->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8'
                ];
            }
        }

        // Add Categories
        $categories = Category::where('status', 'active')->get();
        foreach ($categories as $category) {
            $nameField = 'name_' . $locale;
            if (!empty($category->$nameField)) {
                $urls[] = [
                    'loc' => $baseUrl . '/' . $locale . '/directory?category=' . $category->slug,
                    'lastmod' => $category->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7'
                ];
            }
        }

        // Add Blog Posts
        $posts = BlogPost::where('status', 'published')->get();
        foreach ($posts as $post) {
            if (isset($post->title[$locale]) && !empty($post->title[$locale])) {
                $urls[] = [
                    'loc' => $baseUrl . '/' . $locale . '/blog/' . $post->slug,
                    'lastmod' => $post->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8'
                ];
            }
        }

        // Add Businesses
        // BusinessProfile has translations in business_profile_translations or json columns?
        // Wait, BusinessProfile has 'name', 'about', etc. Is 'name' json or is there a translation table?
        // Let's check how BusinessProfile stores translations.
        // Actually, earlier I saw "business_profile_translations" table in migrations.
        // Let's assume BusinessProfile has a translations relationship, or JSON columns.
        $businesses = BusinessProfile::with('translations')->where('status', 'approved')->get();
        foreach ($businesses as $business) {
            // We need to check if a translation exists for the locale
            $hasTranslation = false;
            if ($locale === 'en') {
                // Assuming English is default and might not have a translation record, or it does.
                $hasTranslation = true; // Safe fallback, or check translations
            }
            
            // Checking the translations relation
            if ($business->translations && $business->translations->where('locale', $locale)->count() > 0) {
                $hasTranslation = true;
            } elseif ($business->language === $locale) {
                $hasTranslation = true; // if it has a base language column
            }

            if ($hasTranslation) {
                $urls[] = [
                    'loc' => $baseUrl . '/' . $locale . '/' . $business->slug,
                    'lastmod' => $business->updated_at->toAtomString(),
                    'changefreq' => 'daily',
                    'priority' => '0.9'
                ];
            }
        }

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

        foreach ($urls as $url) {
            $urlElement = $xml->addChild('url');
            $urlElement->addChild('loc', htmlspecialchars($url['loc']));
            $urlElement->addChild('lastmod', $url['lastmod']);
            $urlElement->addChild('changefreq', $url['changefreq']);
            $urlElement->addChild('priority', $url['priority']);
        }

        return ['xml' => $xml->asXML(), 'count' => count($urls)];
    }

    public function getIndex()
    {
        if (!Cache::has('sitemap_index_xml')) {
            return $this->generate();
        }
        return Cache::get('sitemap_index_xml');
    }

    public function getCachedSitemap($locale)
    {
        $cacheKey = "sitemap_xml_{$locale}";
        if (!Cache::has($cacheKey)) {
            $this->generate();
        }
        return Cache::get($cacheKey);
    }
}
