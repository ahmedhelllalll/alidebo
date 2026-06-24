<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\City;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
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
            $query->whereHas('city', function($q) use ($request) {
                $q->where('country_id', $request->country);
            });
        }

        // Apply Category Filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
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
        $cities = City::where('status', 'active')->get();

        return view('directory.index', compact('businesses', 'categories', 'cities'));
    }

    public function liveSearch(Request $request)
    {
        $search = $request->query('q');

        if (empty($search)) {
            return response()->json([]);
        }

        $results = collect();

        // 1. Search Categories
        $categories = \App\Models\Category::where('status', 'active')
            ->where(function($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%");
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
        $results = $results->concat($categories);

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
        $results = $results->concat($countries);

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
                    'type' => 'city',
                    'name' => $item->name . ($item->country ? ', ' . $item->country->name : ''),
                    'url' => route('directory.index', ['city' => $item->id])
                ];
            });
        $results = $results->concat($cities);

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
                    'url' => route('business.view', $business->slug)
                ];
            });
        $results = $results->concat($businesses);

        return response()->json($results);
    }
}
