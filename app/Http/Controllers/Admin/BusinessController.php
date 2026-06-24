<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\User;
use App\Notifications\BusinessApprovedNotification;
use App\Notifications\BusinessRejectedNotification;
use App\Services\BusinessProfileService;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    use LogsAdminActivity;

    protected $profileService;

    public function __construct(BusinessProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', BusinessProfile::class);

        $query = BusinessProfile::select(
                'business_profiles.id', 
                'business_profiles.name', 
                'business_profiles.slug', 
                'business_profiles.category_id', 
                'business_profiles.city_id', 
                'business_profiles.user_id',
                'business_profiles.owner_id',
                'business_profiles.status', 
                'business_profiles.logo', 
                'business_profiles.created_at',
                'business_profiles.disk'
            )
            ->with([
                'category:id,name_en,name_ar',
                'city:id,name_en,country_id',
                'city.country:id,name_en',
                'user:id,name,email',
                'owner:id,name,email'
            ])->latest('business_profiles.created_at');
        
        // Smart Search (Name, Slug, Owner Email/Name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->leftJoin('users', 'business_profiles.owner_id', '=', 'users.id')
                  ->where(function($q) use ($search) {
                      $q->where('business_profiles.name', 'LIKE', "%{$search}%")
                        ->orWhere('business_profiles.slug', 'LIKE', "%{$search}%")
                        ->orWhere('users.email', 'LIKE', "%{$search}%")
                        ->orWhere('users.name', 'LIKE', "%{$search}%");
                  });
        }

        // Status Filter
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('business_profiles.status', $request->status);
        }

        if ($request->ajax() && $request->has('suggest')) {
            $suggestions = $query->limit(10)->get()->map(function($biz) {
                return [
                    'id' => $biz->id,
                    'name' => $biz->name,
                    'slug' => $biz->slug,
                    'logo' => $biz->logo_url,
                    'status' => $biz->status,
                    'category' => $biz->category->name ?? 'N/A',
                    'city' => $biz->city->name ?? 'N/A',
                    'owner' => $biz->owner->name ?? null,
                    'owner_email' => $biz->owner->email ?? null,
                    'edit_url' => route('admin.businesses.edit', $biz->id),
                    'view_url' => route('business.view', $biz->slug)
                ];
            });
            return response()->json($suggestions);
        }

        $businesses = $query->paginate(10);

        if ($request->ajax()) {
            return view('admin.businesses.partials.table', compact('businesses'))->render();
        }

        $categories = Category::select('id', 'name_en', 'name_ar')->get();
        $countries = Country::where('status', 'active')->select('id', 'name_en', 'name_ar')->get();

        return view('admin.businesses.index', compact('businesses', 'categories', 'countries'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', BusinessProfile::class);

        $categories = Category::select('id', 'name_en', 'name_ar')->get();
        $countries = Country::where('status', 'active')->select('id', 'name_en', 'name_ar')->get();

        return view('admin.businesses.create', compact('categories', 'countries'));
    }

    public function getCitiesByCountry(Country $country)
    {
        try {
            $cities = $country->cities()
                ->whereIn('status', ['active', 'pending'])
                ->orderBy('name_en')
                ->get(['id', 'name_en', 'name_ar']);
            
            return response()->json($cities);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function searchUsers(Request $request)
    {
        $this->authorize('viewAny', BusinessProfile::class);
        $query = $request->get('q', '');
        
        $users = User::where('role', 'user')
            ->when(!empty($query), function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('email', 'LIKE', "%{$query}%");
                });
            })
            ->select('id', 'name', 'email')
            ->limit(15)
            ->get();
            
        return response()->json($users);
    }

    public function checkSlug(Request $request)
    {
        $this->authorize('viewAny', BusinessProfile::class);
        $slug = $request->query('slug');
        if (!$slug) {
            return response()->json(['exists' => false, 'slug' => '']);
        }

        $exists = BusinessProfile::where('slug', $slug)->exists();
        return response()->json(['exists' => $exists, 'slug' => $slug]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', BusinessProfile::class);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'cover' => 'nullable|image|max:4096',
            'whatsapp' => 'nullable|string',
            'phone' => 'nullable|string',
            'website' => 'nullable|string',
            'facebook' => 'nullable|string',
            'instagram' => 'nullable|string',
            'twitter' => 'nullable|string',
            'tiktok' => 'nullable|string',
            'linkedin' => 'nullable|string',
            'youtube' => 'nullable|string',
            'snapchat' => 'nullable|string',
            'gallery.*' => 'nullable|image|max:4096',
            'captions.*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('admin.validation_errors_found'),
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['is_claimed'] = !empty($validated['user_id']);
        
        // Default to current admin user if no user specified
        if (empty($validated['user_id'])) {
            $validated['user_id'] = auth()->id();
            $validated['is_claimed'] = false;
        }
        
        $user = User::findOrFail($validated['user_id']);
        
        $validated['owner_id'] = null; // Forced null for admin creations
        $business = $this->profileService->createProfile($user, $validated, $request);

        // Handle Gallery Uploads
        if ($request->hasFile('gallery')) {
            $this->profileService->uploadMedia($business, $request->file('gallery'), $request->captions);
        }

        $this->logAdminAction('business_created', $business);

        return response()->json([
            'success' => true,
            'message' => __('admin.saved_successfully')
        ]);
    }


    public function destroy(BusinessProfile $business)
    {
        $this->authorize('delete', $business);

        // Delete images
        if ($business->logo) $this->profileService->deleteImage($business->logo, $business->disk ?? 'public');
        if ($business->cover) $this->profileService->deleteImage($business->cover, $business->disk ?? 'public');
        
        $business->delete();

        $this->logAdminAction('business_deleted', $business);

        return response()->json([
            'success' => true,
            'message' => __('admin.deleted_successfully')
        ]);
    }

    public function edit(BusinessProfile $business)
    {
        $this->authorize('update', $business);

        $categories = Category::select('id', 'name_en', 'name_ar')->get();
        $countries = Country::where('status', 'active')->select('id', 'name_en', 'name_ar')->get();
        
        return view('admin.businesses.edit', compact('business', 'categories', 'countries'));
    }

    public function update(Request $request, BusinessProfile $business)
    {
        $this->authorize('update', $business);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'cover' => 'nullable|image|max:4096',
            'whatsapp' => 'nullable|string',
            'phone' => 'nullable|string',
            'website' => 'nullable|string',
            'facebook' => 'nullable|string',
            'instagram' => 'nullable|string',
            'twitter' => 'nullable|string',
            'tiktok' => 'nullable|string',
            'linkedin' => 'nullable|string',
            'youtube' => 'nullable|string',
            'snapchat' => 'nullable|string',
            'gallery.*' => 'nullable|image|max:4096',
            'captions.*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('admin.validation_errors_found'),
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        $validated = $validator->validated();
        
        $this->profileService->updateProfile($business, $validated, $request);

        // Handle Gallery Uploads
        if ($request->hasFile('gallery')) {
            $this->profileService->uploadMedia($business, $request->file('gallery'), $request->captions);
        }

        $this->logAdminAction('business_updated', $business);

        return response()->json([
            'success' => true,
            'message' => __('admin.updated_successfully')
        ]);
    }

    public function bulkStatus(Request $request)
    {
        $this->authorize('update', BusinessProfile::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:business_profiles,id',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $status = $request->status;
        $now = now();
        $updateData = ['status' => $status];

        if ($status === 'approved') {
            $updateData['approved_at'] = $now;
            $updateData['rejection_reason'] = null;
        } elseif ($status === 'pending') {
            $updateData['rejection_reason'] = null;
        }

        BusinessProfile::whereIn('id', $request->ids)->update($updateData);

        return response()->json([
            'success' => true,
            'message' => __('admin.status_updated_bulk'),
        ]);
    }

    public function updateStatus(Request $request, BusinessProfile $business)
    {
        $this->authorize('update', $business);

        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_notes' => 'nullable|string'
        ]);

        $newStatus = $request->status;
        
        if ($newStatus === 'approved') {
            $business->update([
                'status' => 'approved',
                'approved_at' => now(),
                'rejection_reason' => null
            ]);
            if ($business->user) {
                $business->user->notify(new BusinessApprovedNotification($business));
            }
        } elseif ($newStatus === 'rejected') {
            $business->update([
                'status' => 'rejected',
                'rejection_reason' => $request->admin_notes,
                'admin_notes' => $request->admin_notes
            ]);
            if ($business->user) {
                $business->user->notify(new BusinessRejectedNotification($business, $request->admin_notes ?? __('admin.no_reason_provided')));
            }
        } else {
            $business->update(['status' => 'pending']);
        }

        $this->logAdminAction('business_status_updated', $business);

        return response()->json([
            'success' => true, 
            'message' => __('admin.status_updated'),
            'new_status' => $newStatus
        ]);
    }
    public function claim(Request $request, BusinessProfile $business)
    {
        if ($business->owner_id) {
            return response()->json([
                'success' => false,
                'message' => __('admin.business_already_claimed')
            ], 422);
        }

        $business->update([
            'owner_id' => auth()->id(),
            'is_claimed' => true,
        ]);

        $this->logAdminAction('business_claimed', $business);

        return response()->json([
            'success' => true,
            'message' => __('admin.claimed_successfully')
        ]);
    }
}
