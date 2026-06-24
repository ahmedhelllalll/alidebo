<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCountryRequest;
use App\Models\Country;

use App\Traits\LogsAdminActivity;

class CountryController extends Controller
{
    use LogsAdminActivity;

    public function index(\Illuminate\Http\Request $request)
    {
        $query = Country::withCount('cities');
        
        // Smart Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status') && in_array($request->status, ['active', 'pending'])) {
            $query->where('status', $request->status);
        }

        $countries = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.locations._countries_list', compact('countries'))->render();
        }

        return redirect()->route('admin.locations.index');
    }

    public function store(StoreCountryRequest $request)
    {
        $country = Country::create($request->validated());
        $this->logAdminAction('country_created', $country);
        return back()->with('success', __('admin.saved_successfully'));
    }

    public function destroy(Country $country)
    {
        try {
            $country->delete();
            $this->logAdminAction('country_deleted', $country);
            return back()->with('success', __('admin.deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('admin.cant_delete_dependency'));
        }
    }

    public function updateStatus(\Illuminate\Http\Request $request, Country $country)
    {
        $request->validate([
            'status' => 'required|in:active,pending'
        ]);

        $country->update(['status' => $request->status]);
        $this->logAdminAction('country_updated', $country);

        return response()->json([
            'success' => true,
            'message' => __('admin.saved_successfully')
        ]);
    }
}
