<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCityRequest;
use App\Models\City;

use App\Traits\LogsAdminActivity;

class CityController extends Controller
{
    use LogsAdminActivity;

    public function index(\Illuminate\Http\Request $request)
    {
        $query = City::with('country');
        
        // Smart Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function(\Illuminate\Database\Eloquent\Builder $q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhereHas('country', function($subQ) use ($search) {
                      $subQ->where('name_en', 'like', "%{$search}%")
                           ->orWhere('name_ar', 'like', "%{$search}%");
                  });
            });
        }

        // Status Filter
        if ($request->filled('status') && in_array($request->status, ['active', 'pending'])) {
            $query->where('status', $request->status);
        }

        $cities = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.locations._cities_list', compact('cities'))->render();
        }

        return redirect()->route('admin.locations.index');
    }

    public function store(StoreCityRequest $request)
    {
        $city = City::create($request->validated());
        $this->logAdminAction('city_created', $city);
        return back()->with('success', __('admin.saved_successfully'));
    }

    public function destroy(City $city)
    {
        try {
            $city->delete();
            $this->logAdminAction('city_deleted', $city);
            return back()->with('success', __('admin.deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('admin.cant_delete_dependency'));
        }
    }

    public function updateStatus(\Illuminate\Http\Request $request, City $city)
    {
        $request->validate([
            'status' => 'required|in:active,pending'
        ]);

        $city->update(['status' => $request->status]);
        $this->logAdminAction('city_updated', $city);

        return response()->json([
            'success' => true,
            'message' => __('admin.saved_successfully')
        ]);
    }
}
