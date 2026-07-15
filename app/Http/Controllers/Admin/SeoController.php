<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SitemapService;
use Illuminate\Support\Facades\Cache;

class SeoController extends Controller
{
    public function dashboard()
    {
        $sitemapUrl = url('/sitemap.xml');
        $lastGenerated = Cache::get('sitemap_last_generated', 'Never');
        $indexedLinks = Cache::get('sitemap_url_count', 0);

        return view('admin.seo.dashboard', compact('sitemapUrl', 'lastGenerated', 'indexedLinks'));
    }

    public function regenerateSitemap(SitemapService $sitemapService)
    {
        $sitemapService->generate();
        return redirect()->route('admin.dashboard.seo')->with('success', __('admin.sitemap_regenerated'));
    }
}
