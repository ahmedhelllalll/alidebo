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

class BusinessProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            abort(403);
        }

        $businesses = $user->businessProfiles()
            ->withCount('media')
            ->orderBy('created_at', 'desc')
            ->get();

        $isEmpty = $businesses->isEmpty();

        return view('user.business.index', compact('businesses', 'isEmpty'));
    }

    public function create()
    {
        return view('user.business.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'meta_description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = Auth::user();

        if (!$user instanceof User) {
            abort(403);
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $business = $user->businessProfiles()->create([
            'name' => $request->input('name'),
            'meta_description' => $request->input('meta_description'),
            'slug' => Str::slug($request->input('name')) . '-' . Str::random(5),
            'status' => 'pending',
            'logo' => $logoPath,
        ]);

        $defaultSections = [
            ['type' => 'hero', 'order' => 0, 'content' => ['title' => $business->name, 'subtitle' => 'مرحباً بكم في بروفايل شركتنا']],
            ['type' => 'about', 'order' => 1, 'content' => ['text' => $business->meta_description ?? 'اكتب نبذة تعريفية عن شركتك هنا...']],
            ['type' => 'gallery', 'order' => 2, 'content' => ['title' => 'معرض أعمالنا']],
            ['type' => 'contact', 'order' => 3, 'content' => ['phone' => '', 'email' => $user->email]],
        ];

        foreach ($defaultSections as $section) {
            $business->sections()->create([
                'section_type' => $section['type'],
                'template_key' => 'default',
                'content' => $section['content'],
                'sort_order' => $section['order'],
            ]);
        }

        return redirect()->route('business.index')->with('success', 'تم إنشاء البروفايل بنجاح');
    }

    public function edit($id)
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            abort(403);
        }

        $business = $user->businessProfiles()
            ->with(['media', 'sections' => function ($q) {
                $q->orderBy('sort_order', 'asc');
            }])
            ->findOrFail($id);

        return view('user.business.edit', compact('business'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            abort(403);
        }

        $business = $user->businessProfiles()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'meta_description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'name' => $request->input('name'),
            'meta_description' => $request->input('meta_description'),
        ];

        if ($request->hasFile('logo')) {
            if ($business->logo) {
                Storage::disk('public')->delete($business->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $business->update($data);

        return redirect()->back()->with('success', 'تم التحديث بنجاح');
    }

    public function uploadMedia(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            abort(403);
        }

        $business = $user->businessProfiles()->withCount('media')->findOrFail($id);
        $currentCount = $business->media_count;

        if (!$request->hasFile('images')) {
            return redirect()->back()->with('error', 'خطأ في الرفع');
        }

        $files = $request->file('images');
        $incomingCount = count($files);

        if ($incomingCount < 3 || ($currentCount + $incomingCount) > 12) {
            return redirect()->back()->with('error', 'يجب رفع من 3 إلى 12 صورة');
        }

        foreach ($files as $image) {
            if ($image->isValid()) {
                $path = $image->store('business_media/' . $id, 'public');
                $business->media()->create([
                    'file_path' => $path,
                    'type' => 'image',
                ]);
            }
        }

        return redirect()->back()->with('success', 'تم الرفع');
    }

    public function destroyMedia($id)
    {
        $media = ProfileMedia::whereHas('businessProfile', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return redirect()->back()->with('success', 'تم الحذف');
    }

    public function storeSection(Request $request)
    {
        $request->validate([
            'business_profile_id' => 'required|exists:business_profiles,id',
            'section_type' => 'required|string|in:hero,about,services,gallery,contact',
        ]);

        $user = Auth::user();

        if (!$user instanceof User) {
            abort(403);
        }

        $business = $user->businessProfiles()->findOrFail($request->input('business_profile_id'));

        $business->sections()->create([
            'section_type' => $request->input('section_type'),
            'template_key' => 'default',
            'content' => [],
            'sort_order' => $business->sections()->count()
        ]);

        return redirect()->back()->with('success', 'تم إضافة القسم بنجاح');
    }

    public function updateSection(Request $request, $sectionId)
    {
        $section = ProfileSection::whereHas('businessProfile', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($sectionId);

        $section->update([
            'content' => $request->input('content'),
            'template_key' => $request->input('template_key') ?? $section->template_key
        ]);

        return redirect()->back()->with('success', 'تم تحديث القسم');
    }

    public function destroySection($id)
    {
        $section = ProfileSection::whereHas('businessProfile', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $section->delete();

        return redirect()->back()->with('success', 'تم حذف القسم بنجاح');
    }

    public function showPublicProfile($slug)
    {
        $business = BusinessProfile::with(['media', 'sections' => function ($q) {
            $q->orderBy('sort_order', 'asc');
        }])->where('slug', $slug)->firstOrFail();

        if ($business->status !== 'approved') {
            $user = Auth::user();
            if (!Auth::check() || ($user instanceof User && $user->id !== $business->user_id && !$user->isAdmin())) {
                abort(403, 'هذا البروفايل قيد المراجعة حالياً.');
            }
        }

        return view('public_profile.show', compact('business'));
    }
}
