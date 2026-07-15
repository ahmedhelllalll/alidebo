<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        $cacheKey = 'directory_index_' . md5(json_encode($request->all()));

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(10), function() use ($request) {
            $query = BusinessProfile::with(['category', 'city', 'owner'])->where('status', 'approved');

            // Apply Search Filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply Country Filter
            if ($request->filled('country')) {
                $countries = is_array($request->country) ? $request->country : explode(',', $request->country);
                $query->whereHas('city', function($q) use ($countries) {
                    $q->whereIn('country_id', $countries);
                });
            }

            // Apply Category Filter
            if ($request->filled('category')) {
                $categories = is_array($request->category) ? $request->category : explode(',', $request->category);
                $query->whereIn('category_id', $categories);
            }

            // Apply City Filter
            if ($request->filled('city')) {
                $query->where('city_id', $request->city);
            }

            // Apply Sort
            if ($request->filled('sort')) {
                if ($request->sort === 'newest') {
                    $query->latest('approved_at');
                } elseif ($request->sort === 'oldest') {
                    $query->oldest('approved_at');
                } elseif ($request->sort === 'a-z') {
                    $query->orderBy('name', 'asc');
                } elseif ($request->sort === 'z-a') {
                    $query->orderBy('name', 'desc');
                }
            } else {
                // Default sort
                $query->latest('approved_at');
            }

            $businesses = $query->paginate(12)->withQueryString();
            
            $categories = Category::where('status', 'active')->get();
            $countries = Country::where('status', 'active')->get();

            return compact('businesses', 'categories', 'countries');
        });

        extract($data);

        $hreflangs = [];
        $locales = ['en', 'ar', 'es', 'de', 'zh', 'tr'];
        $queryParameters = $request->query();
        foreach ($locales as $loc) {
            $hreflangs[$loc] = url('/' . $loc . '/directory' . ($queryParameters ? '?' . http_build_query($queryParameters) : ''));
        }
        view()->share('hreflangs', $hreflangs);

        if ($request->ajax() || $request->header('X-Alpine-Request')) {
            if ($request->boolean('append')) {
                return view('directory.index', compact('businesses', 'categories', 'countries'))->fragment('business-items');
            }
            return view('directory.index', compact('businesses', 'categories', 'countries'))->fragment('business-grid');
        }

        return view('directory.index', compact('businesses', 'categories', 'countries'));
    }

    public function liveSearch(Request $request)
    {
        $search = $request->query('q');

        if (empty($search)) {
            return response()->json([
                'categories' => [],
                'locations' => [],
                'companies' => []
            ]);
        }

        $cacheKey = 'live_search_' . md5($search);

        $results = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(10), function() use ($search) {
            // 1. Search Categories
            $categories = \App\Models\Category::where('status', 'active')
                ->where(function($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                      ->orWhere('name_ar', 'like', "%{$search}%")
                      ->orWhere('name_de', 'like', "%{$search}%")
                      ->orWhere('name_es', 'like', "%{$search}%")
                      ->orWhere('name_tr', 'like', "%{$search}%")
                      ->orWhere('name_zh', 'like', "%{$search}%");
                })
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'category',
                        'name' => $item->name,
                        'icon' => $item->icon_url,
                        'url' => route('directory.index', ['category' => $item->id])
                    ];
                });

            // 2. Search Countries
            $countries = \App\Models\Country::where('status', 'active')
                ->where(function($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                      ->orWhere('name_ar', 'like', "%{$search}%");
                })
                ->limit(2)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'country',
                        'name' => $item->name,
                        'url' => route('directory.index', ['country' => $item->id])
                    ];
                });

            // 3. Search Cities
            $cities = \App\Models\City::with('country')->where('status', 'active')
                ->where(function($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                      ->orWhere('name_ar', 'like', "%{$search}%");
                })
                ->limit(2)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'country_id' => $item->country_id,
                        'type' => 'city',
                        'name' => $item->name . ($item->country ? ', ' . $item->country->name : ''),
                        'url' => route('directory.index', ['city' => $item->id])
                    ];
                });

            // 4. Search Businesses
            $businesses = BusinessProfile::with('category')
                ->where('status', 'approved')
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                })
                ->limit(5)
                ->get()
                ->map(function ($business) {
                    return [
                        'id' => $business->id,
                        'type' => 'company',
                        'name' => $business->name,
                        'logo' => $business->logo_url,
                        'category' => $business->category ? $business->category->name : null,
                        'url' => route('directory.business.view', $business->slug)
                    ];
                });

            return [
                'categories' => $categories,
                'locations' => $countries->concat($cities),
                'companies' => $businesses
            ];
        });

        return response()->json($results);
    }
}

