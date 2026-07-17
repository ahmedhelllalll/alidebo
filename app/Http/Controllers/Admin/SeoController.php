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

    public function robots()
    {
        $path = public_path('robots.txt');
        $content = \Illuminate\Support\Facades\File::exists($path) ? \Illuminate\Support\Facades\File::get($path) : '';
        return view('admin.seo.robots', compact('content'));
    }

    public function updateRobots(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string'
        ]);
        
        $path = public_path('robots.txt');
        \Illuminate\Support\Facades\File::put($path, $request->input('content') ?? '');
        
        return redirect()->back()->with('success', __('admin.saved_successfully'));
    }

    public function searchInsights(Request $request, \App\Services\GoogleSearchConsoleService $gscService)
    {
        $period = $request->input('period', '30'); // 7, 30, 90 days
        $startDate = now()->subDays((int)$period)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        $siteUrl = env('APP_URL');

        // Get daily clicks and impressions for charts
        $chartData = $gscService->getAnalyticsData($siteUrl, $startDate, $endDate, ['date']);
        
        // Get top queries for data tables
        $topQueries = $gscService->getAnalyticsData($siteUrl, $startDate, $endDate, ['query'], 50);

        return view('admin.seo.search-insights', compact('chartData', 'topQueries', 'period'));
    }
}
