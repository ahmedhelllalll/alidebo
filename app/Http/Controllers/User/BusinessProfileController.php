<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\ProfileMedia;
use App\Models\BusinessProfile;
use App\Models\ProfileSection;
use App\Models\User;
use App\Models\Category;
use App\Models\City;

class BusinessProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user instanceof User) abort(403);

        $businesses = $user->businessProfiles()
            ->withCount('media')
            ->orderBy('created_at', 'desc')
            ->get();

        $isEmpty = $businesses->isEmpty();

        return view('user.business.index', compact('businesses', 'isEmpty'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->businessProfiles()->exists()) {
            return redirect()->route('business.index');
        }

        $categories = Category::all();
        $cities = City::all();
        return view('user.business.onboarding', compact('categories', 'cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'whatsapp' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'social_links' => 'nullable|array',
            'city_id' => 'required|exists:cities,id',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = Auth::user();
        if (!$user instanceof User) abort(403);

        $logoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('logos', 'public')
            : null;

        $business = $user->businessProfiles()->create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'city_id' => $request->city_id,
            'description' => $request->description,
            'whatsapp' => $request->whatsapp,
            'phone' => $request->phone,
            'website' => $request->website,
            'social_links' => $request->social_links,
            'address' => $request->address,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'status' => 'pending',
            'logo' => $logoPath,
        ]);

        $defaultSections = [
            [
                'type' => 'hero',
                'order' => 0,
                'content' => ['title' => $business->name, 'subtitle' => 'مرحباً بكم في بروفايل شركتنا']
            ],
            [
                'type' => 'about',
                'order' => 1,
                'content' => ['text' => $business->description ?? 'اكتب نبذة تعريفية عن شركتك هنا...']
            ],
            [
                'type' => 'gallery',
                'order' => 2,
                'content' => ['title' => 'معرض أعمالنا']
            ],
            [
                'type' => 'contact',
                'order' => 3,
                'content' => [
                    'phone' => $request->phone ?? $request->whatsapp,
                    'email' => $user->email,
                    'website' => $request->website
                ]
            ],
        ];

        foreach ($defaultSections as $section) {
            $business->sections()->create([
                'section_type' => $section['type'],
                'template_key' => 'default',
                'content' => $section['content'],
                'sort_order' => $section['order'],
            ]);
        }

        if (!$user->has_completed_onboarding) {
            $user->update(['has_completed_onboarding' => true]);
        }

        return redirect()->route('business.index')->with('success', 'تم إنشاء البروفايل وتفعيل حسابك بنجاح');
    }

    public function edit($id)
    {
        $user = Auth::user();
        if (!$user instanceof User) abort(403);

        $business = $user->businessProfiles()
            ->with(['media', 'sections' => fn($q) => $q->orderBy('sort_order', 'asc')])
            ->findOrFail($id);

        $categories = Category::all();
        $cities = City::all();

        return view('user.business.edit', compact('business', 'categories', 'cities'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user instanceof User) abort(403);

        $business = $user->businessProfiles()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:cities,id',
            'whatsapp' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'social_links' => 'nullable|array',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['name', 'category_id', 'city_id', 'whatsapp', 'phone', 'website', 'social_links', 'description', 'address']);

        if ($request->hasFile('logo')) {
            if ($business->logo) Storage::disk('public')->delete($business->logo);
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $business->update($data);

        return redirect()->back()->with('success', 'تم التحديث بنجاح');
    }

    public function syncSections(Request $request, $id)
    {
        $user = Auth::user();
        $business = $user->businessProfiles()->findOrFail($id);

        $incomingSections = $request->input('sections', []);
        $keepIds = [];

        foreach ($incomingSections as $index => $sectionData) {
            $section = $business->sections()->updateOrCreate(
                ['id' => $sectionData['id'] ?? null],
                [
                    'section_type' => $sectionData['section_type'],
                    'template_key' => $sectionData['template_key'] ?? 'default',
                    'content' => $sectionData['content'],
                    'sort_order' => $index
                ]
            );
            $keepIds[] = $section->id;
        }

        $business->sections()->whereNotIn('id', $keepIds)->delete();

        return response()->json(['status' => 'success', 'message' => 'تم مزامنة الأقسام بنجاح']);
    }

    public function uploadMedia(Request $request, $id)
    {
        $user = Auth::user();
        $business = $user->businessProfiles()->withCount('media')->findOrFail($id);

        if (!$request->hasFile('images')) return redirect()->back()->with('error', 'خطأ في الرفع');

        $files = $request->file('images');
        if (count($files) < 1 || ($business->media_count + count($files)) > 12) {
            return redirect()->back()->with('error', 'الحد الأقصى للميديا هو 12 صورة');
        }

        foreach ($files as $image) {
            if ($image->isValid()) {
                $path = $image->store('business_media/' . $id, 'public');
                $business->media()->create(['file_path' => $path, 'type' => 'image']);
            }
        }

        return redirect()->back()->with('success', 'تم الرفع');
    }

    public function destroyMedia($id)
    {
        $media = ProfileMedia::whereHas('businessProfile', fn($q) => $q->where('user_id', Auth::id()))->findOrFail($id);
        Storage::disk('public')->delete($media->file_path);
        $media->delete();
        return redirect()->back()->with('success', 'تم الحذف');
    }

    public function showPublicProfile($slug)
    {
        $business = BusinessProfile::with(['media', 'sections' => fn($q) => $q->orderBy('sort_order', 'asc')])
            ->where('slug', $slug)->firstOrFail();

        if ($business->status !== 'approved') {
            $user = Auth::user();
            if (!Auth::check() || ($user instanceof User && $user->id !== $business->user_id && !$user->isAdmin())) {
                abort(403, 'هذا البروفايل قيد المراجعة حالياً.');
            }
        }

        return view('public_profile.show', compact('business'));
    }
}
