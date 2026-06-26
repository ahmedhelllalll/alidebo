<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\Country;
use App\Models\BusinessMedia;
use App\Models\BusinessView;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreBusinessProfileRequest;
use App\Http\Requests\UpdateBusinessProfileRequest;
use App\Services\BusinessAnalyticsService;
use App\Services\BusinessProfileService;
use App\Models\City;
use Illuminate\Support\Str;
class BusinessProfileController extends Controller
{
    private BusinessAnalyticsService $analyticsService;
    private BusinessProfileService $profileService;

    public function __construct(BusinessAnalyticsService $analyticsService, BusinessProfileService $profileService)
    {
        $this->analyticsService = $analyticsService;
        $this->profileService = $profileService;
    }

    public function index()
    {
        $user = Auth::user();
        $business = $user->businessProfile;
        
        $analytics = $this->analyticsService->getAnalyticsData($business?->id);
        
        return view('users.index', [
            'business' => $business,
            'totalViews' => $analytics['totalViews'],
            'viewsChange' => $analytics['viewsChange'],
            'countryStats' => $analytics['countryStats']
        ]);
    }

    public function create()
    {
        if (Auth::user()->businessProfile()->exists()) {
            return redirect()->route('business.index');
        }

        $categories = Cache::remember('categories_all', 86400, fn() => Category::all());
        $countries = Country::with('cities')->get();

        return view('users.create', compact('categories', 'countries'));
    }

    public function store(StoreBusinessProfileRequest $request)
    {
        $validated = $request->validated();

        if (isset($validated['social_links']) && is_array($validated['social_links'])) {
            $validated['social_links'] = array_filter($validated['social_links'], fn($link) => !empty($link));
        }

        if (!empty($validated['custom_category_name'])) {
            $category = Category::firstOrCreate([
                'name_en' => $validated['custom_category_name'],
                'name_ar' => $validated['custom_category_name'],
            ], [
                'slug' => Str::slug($validated['custom_category_name']) . '-' . uniqid(),
                'status' => 'active',
            ]);
            $validated['category_id'] = $category->id;
        }

        if (!empty($validated['custom_country_name']) || !empty($validated['custom_city_name'])) {
            $countryName = $validated['custom_country_name'] ?? 'Unknown Country';
            if (!empty($validated['country_id']) && empty($validated['custom_country_name'])) {
                 $country = Country::find($validated['country_id']);
            } else {
                 $country = Country::firstOrCreate([
                     'name_en' => $countryName,
                     'name_ar' => $countryName,
                 ], [
                     'code' => strtoupper(substr($countryName, 0, 2)),
                     'status' => 'active',
                 ]);
            }
            
            $cityName = $validated['custom_city_name'] ?? 'Unknown City';
            $city = City::firstOrCreate([
                'name' => $cityName,
                'country_id' => $country->id,
            ]);
            $validated['city_id'] = $city->id;
        }

        $this->profileService->createProfile(Auth::user(), $validated, $request);

        return redirect()->route('business.index')->with('success', __('forms.business.success_message') ?? 'تم تسجيل بياناتك بنجاح');
    }

    public function checkSlug(Request $request)
    {
        $slug = $request->query('slug');
        if (!$slug) {
            return response()->json(['exists' => false, 'slug' => '']);
        }

        $slug = Str::slug($slug);

        $exists = BusinessProfile::where('slug', $slug)->exists();
        
        $suggestions = [];
        if ($exists) {
            $baseSlug = Str::slug($slug);
            $suggestions = [
                $baseSlug . '-' . rand(10, 99),
                $baseSlug . '-' . date('Y'),
                $baseSlug . '-official',
                $baseSlug . '-eg',
                $baseSlug . '-hub'
            ];
            // Filter suggestions that also exist
            $existingSuggestions = BusinessProfile::whereIn('slug', $suggestions)->pluck('slug')->toArray();
            $suggestions = array_values(array_diff($suggestions, $existingSuggestions));
        }

        return response()->json([
            'exists' => $exists, 
            'slug' => $slug,
            'suggestions' => $suggestions
        ]);
    }

    public function edit()
    {
        $business = Auth::user()->businessProfile()->with(['media', 'city.country'])->firstOrFail();
        $categories = Cache::remember('categories_all', 86400, fn() => Category::all());
        $countries = Country::with('cities')->get();
        $categoryImages = Cache::remember('categories_images', 86400, fn() => Category::whereNotNull('image')->get());

        return view('users.edit', compact('business', 'categories', 'countries', 'categoryImages'));
    }

    public function update(UpdateBusinessProfileRequest $request)
    {
        $business = Auth::user()->businessProfile;
        
        $validated = $request->validated();
        if (isset($validated['social_links']) && is_array($validated['social_links'])) {
            $validated['social_links'] = array_filter($validated['social_links'], fn($link) => !empty($link));
        }

        $this->profileService->updateProfile($business, $validated, $request);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('business.index')
            ]);
        }

        return redirect()->route('business.index')->with('success', 'تم تحديث البيانات');
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $business = Auth::user()->businessProfile;
        $existingCount = $business->media()->count();
        $maxTotal = 10;

        if ($existingCount + count($request->file('images')) > $maxTotal) {
            return response()->json([
                'error' => "لا يمكنك إضافة أكثر من {$maxTotal} صورة. لديك حاليًا {$existingCount} صورة."
            ], 422);
        }

        $uploaded = $this->profileService->uploadMedia($business, $request->file('images'), $request->captions ?? []);

        return response()->json(['success' => true, 'media' => $uploaded]);
    }

    public function updateMediaOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:business_media,id',
        ]);

        $this->profileService->updateMediaOrder($request->order);

        return response()->json(['success' => true]);
    }

    public function updateMediaCaption(Request $request, $id)
    {
        $request->validate([
            'caption' => 'nullable|string|max:255',
        ]);

        $media = BusinessMedia::where('id', $id)
            ->whereHas('businessProfile', function ($q) {
                $q->where('owner_id', Auth::id());
            })->firstOrFail();

        $this->profileService->updateMediaCaption($media, $request->caption);

        return response()->json(['success' => true, 'caption' => $media->caption]);
    }

    public function destroyMedia($id)
    {
        $media = BusinessMedia::where('id', $id)
            ->whereHas('businessProfile', function ($q) {
                $q->where('owner_id', Auth::id());
            })->firstOrFail();

        $this->profileService->deleteMedia($media);

        return response()->json(['success' => true]);
    }

    public function show($slug)
    {
        $business = BusinessProfile::where('slug', $slug)
            ->with(['category', 'city', 'media'])
            ->firstOrFail();

        // Status-based visibility guard
        if (in_array($business->status, ['pending', 'rejected'])) {
            if (!Auth::check() || Auth::id() !== $business->owner_id) {
                abort(404);
            }
        }

        $sessionKey = 'viewed_business_' . $business->id;
        if (!session()->has($sessionKey)) {
            BusinessView::create([
                'business_profile_id' => $business->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'country_code' => request()->header('CF-IPCountry') ?? null,
            ]);
            session()->put($sessionKey, true);
        }

        return view('users.business.show', compact('business'));
    }
}
