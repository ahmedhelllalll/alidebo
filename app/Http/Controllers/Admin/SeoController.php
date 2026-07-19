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
        
        // Dynamically get the site property from GSC or use env override
        $siteUrl = env('GOOGLE_SEARCH_CONSOLE_PROPERTY', $gscService->getSiteUrl());

        // Cache the charts data to avoid hitting limits when paginating
        $cacheKeyCharts = "gsc_charts_{$period}_{$siteUrl}";
        $chartData = Cache::remember($cacheKeyCharts, 3600, function () use ($gscService, $siteUrl, $startDate, $endDate) {
            return $gscService->getAnalyticsData($siteUrl, $startDate, $endDate, ['date']);
        });
        
        // Fetch up to 1000 queries and cache them
        $cacheKeyQueries = "gsc_queries_{$period}_{$siteUrl}";
        $topQueriesData = Cache::remember($cacheKeyQueries, 3600, function () use ($gscService, $siteUrl, $startDate, $endDate) {
            return $gscService->getAnalyticsData($siteUrl, $startDate, $endDate, ['query'], 1000);
        });

        // Fetch up to 1000 pages and cache them
        $cacheKeyPages = "gsc_pages_{$period}_{$siteUrl}";
        $topPagesData = Cache::remember($cacheKeyPages, 3600, function () use ($gscService, $siteUrl, $startDate, $endDate) {
            return $gscService->getAnalyticsData($siteUrl, $startDate, $endDate, ['page'], 1000);
        });

        // Fetch device breakdown and cache it
        $cacheKeyDevices = "gsc_devices_{$period}_{$siteUrl}";
        $deviceData = Cache::remember($cacheKeyDevices, 3600, function () use ($gscService, $siteUrl, $startDate, $endDate) {
            return $gscService->getAnalyticsData($siteUrl, $startDate, $endDate, ['device'], 10);
        });

        // Fetch up to 500 countries and cache them
        $cacheKeyCountries = "gsc_countries_{$period}_{$siteUrl}";
        $topCountriesData = Cache::remember($cacheKeyCountries, 3600, function () use ($gscService, $siteUrl, $startDate, $endDate) {
            return $gscService->getAnalyticsData($siteUrl, $startDate, $endDate, ['country'], 500);
        });

        // Fetch up to 100 search appearance types and cache them
        $cacheKeyAppearance = "gsc_appearance_{$period}_{$siteUrl}";
        $topAppearanceData = Cache::remember($cacheKeyAppearance, 3600, function () use ($gscService, $siteUrl, $startDate, $endDate) {
            return $gscService->getAnalyticsData($siteUrl, $startDate, $endDate, ['searchAppearance'], 100);
        });

        $perPage = 15;

        // Manually paginate the queries array
        $queriesArray = $topQueriesData['rows'] ?? [];
        $currentPageQueries = \Illuminate\Pagination\Paginator::resolveCurrentPage('query_page');
        $currentItemsQueries = array_slice($queriesArray, ($currentPageQueries - 1) * $perPage, $perPage);
        $topQueries = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItemsQueries,
            count($queriesArray),
            $perPage,
            $currentPageQueries,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'query_page']
        );
        $topQueries->appends(['period' => $period, 'pages_page' => request('pages_page'), 'country_page' => request('country_page'), 'appearance_page' => request('appearance_page')]);

        // Manually paginate the pages array
        $pagesArray = $topPagesData['rows'] ?? [];
        $currentPagePages = \Illuminate\Pagination\Paginator::resolveCurrentPage('pages_page');
        $currentItemsPages = array_slice($pagesArray, ($currentPagePages - 1) * $perPage, $perPage);
        $topPages = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItemsPages,
            count($pagesArray),
            $perPage,
            $currentPagePages,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'pages_page']
        );
        $topPages->appends(['period' => $period, 'query_page' => request('query_page'), 'country_page' => request('country_page'), 'appearance_page' => request('appearance_page')]);

        // Manually paginate the countries array
        $countriesArray = $topCountriesData['rows'] ?? [];
        $currentPageCountries = \Illuminate\Pagination\Paginator::resolveCurrentPage('country_page');
        $currentItemsCountries = array_slice($countriesArray, ($currentPageCountries - 1) * $perPage, $perPage);
        $topCountries = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItemsCountries,
            count($countriesArray),
            $perPage,
            $currentPageCountries,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'country_page']
        );
        $topCountries->appends(['period' => $period, 'query_page' => request('query_page'), 'pages_page' => request('pages_page'), 'appearance_page' => request('appearance_page')]);

        // Fetch Sitemaps status and cache them for an hour
        $cacheKeySitemaps = "gsc_sitemaps_{$siteUrl}";
        $sitemapsData = Cache::remember($cacheKeySitemaps, 3600, function () use ($gscService, $siteUrl) {
            return $gscService->getSitemaps($siteUrl);
        });

        // Manually paginate the search appearance array
        $appearanceArray = $topAppearanceData['rows'] ?? [];
        $currentPageAppearance = \Illuminate\Pagination\Paginator::resolveCurrentPage('appearance_page');
        $currentItemsAppearance = array_slice($appearanceArray, ($currentPageAppearance - 1) * $perPage, $perPage);
        $topAppearance = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItemsAppearance,
            count($appearanceArray),
            $perPage,
            $currentPageAppearance,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'appearance_page']
        );
        $topAppearance->appends(['period' => $period, 'query_page' => request('query_page'), 'pages_page' => request('pages_page'), 'country_page' => request('country_page'), 'sitemaps_page' => request('sitemaps_page')]);

        // Manually paginate the sitemaps array
        $sitemapsArray = $sitemapsData['sitemaps'] ?? [];
        $currentPageSitemaps = \Illuminate\Pagination\Paginator::resolveCurrentPage('sitemaps_page');
        $currentItemsSitemaps = array_slice($sitemapsArray, ($currentPageSitemaps - 1) * $perPage, $perPage);
        $sitemapsList = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItemsSitemaps,
            count($sitemapsArray),
            $perPage,
            $currentPageSitemaps,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'sitemaps_page']
        );
        $sitemapsList->appends(['period' => $period, 'query_page' => request('query_page'), 'pages_page' => request('pages_page'), 'country_page' => request('country_page'), 'appearance_page' => request('appearance_page')]);

        return view('admin.seo.search-insights', compact('chartData', 'topQueries', 'topPages', 'topCountries', 'topAppearance', 'sitemapsList', 'deviceData', 'period'));
    }
}
