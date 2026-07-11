<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessProfileTranslation;

class BusinessTranslationController extends Controller
{
    /**
     * Display the translation management view.
     */
    public function index()
    {
        $business = Auth::user()->businessProfile;

        if (!$business) {
            return redirect()->route('business.create')
                ->with('error', __('dashboard.index.create_profile_desc') ?? 'Please create a business profile first.');
        }

        $locales = ['ar' => 'العربية', 'en' => 'English', 'es' => 'Español', 'de' => 'Deutsch', 'zh' => '中文', 'tr' => 'Türkçe'];
        $translations = $business->translations()->get()->keyBy('locale');

        return view('users.business.translations', compact('business', 'locales', 'translations'));
    }

    /**
     * Update the translations.
     */
    public function update(Request $request)
    {
        $business = Auth::user()->businessProfile;

        if (!$business) {
            return response()->json(['success' => false, 'message' => 'Business profile not found.'], 404);
        }

        $request->validate([
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|in:ar,en,es,de,zh,tr',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string',
        ]);

        foreach ($request->translations as $locale => $data) {
            // Skip empty translations to avoid clutter, or save them if they want to clear it
            if (empty($data['name']) && empty($data['description'])) {
                BusinessProfileTranslation::where('business_profile_id', $business->id)
                    ->where('locale', $locale)
                    ->delete();
                continue;
            }

            BusinessProfileTranslation::updateOrCreate(
                [
                    'business_profile_id' => $business->id,
                    'locale' => $locale,
                ],
                [
                    'name' => $data['name'] ?? null,
                    'description' => $data['description'] ?? null,
                    'meta_title' => $data['meta_title'] ?? null,
                    'meta_description' => $data['meta_description'] ?? null,
                ]
            );
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('forms.business.success_message') ?? 'Translations saved successfully.']);
        }

        return redirect()->back()->with('success', __('forms.business.success_message') ?? 'Translations saved successfully.');
    }
}
