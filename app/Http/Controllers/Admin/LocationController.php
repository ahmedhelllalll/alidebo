<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\City;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        // Check permissions or use authorize if needed
        // $this->authorize('viewAny', Country::class);
        
        $countries = Country::withCount('cities')->latest()->paginate(10)->withQueryString();
        $cities = City::with('country')->latest()->paginate(10)->withQueryString();

        return view('admin.locations.index', compact('countries', 'cities'));
    }
}
