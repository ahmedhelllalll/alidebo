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
}
