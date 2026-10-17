<?php

namespace App\Services;

use App\Models\BusinessProfile;
use App\Models\BusinessMedia;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Jobs\OptimizeProfileContent;
use App\Support\CategoryAliasMapping;

class BusinessProfileService
{
    public function formatContactMethods(array $validated): array
    {
        return [
            'whatsapp' => $validated['whatsapp'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'website' => $validated['website'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'twitter' => $validated['twitter'] ?? null,
            'tiktok' => $validated['tiktok'] ?? null,
            'linkedin' => $validated['linkedin'] ?? null,
            'youtube' => $validated['youtube'] ?? null,
            'snapchat' => $validated['snapchat'] ?? null,
        ];
    }

    public function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        if (!$baseSlug) $baseSlug = 'business';

        if (!BusinessProfile::where('slug', $baseSlug)->exists()) {
            return $baseSlug;
        }

        $counter = 1;
        while (BusinessProfile::where('slug', $baseSlug . '-' . $counter)->exists()) {
            $counter++;
        }
        return $baseSlug . '-' . $counter;
    }

    public function handleImageUpload(?UploadedFile $file, string $directory): ?string
    {
        if (!$file) return null;
        return $file->store($directory, 'r2');
    }

    public function deleteImage(?string $path, string $disk = 'public'): void
    {
        if ($path && !str_contains($path, 'categories')) {
            Storage::disk($disk)->delete($path);
        }
    }

    public function createProfile($user, array $validated, $request): BusinessProfile
    {
        $logoPath = $this->handleImageUpload($request->file('logo'), 'logos');
        $coverPath = $this->handleImageUpload($request->file('cover'), 'covers');
        $contactMethods = $this->formatContactMethods($validated);

        // Resolve dual-write for category_id & new_category_id
        $newCategoryId = $validated['new_category_id'] ?? null;
        $legacyCategoryId = $validated['category_id'] ?? null;
        $needsReview = array_key_exists('category_needs_review', $validated)
            ? (bool)$validated['category_needs_review']
            : ($newCategoryId ? false : false);

        if ($newCategoryId && !$legacyCategoryId) {
            $legacyCategoryId = CategoryAliasMapping::getLegacyCategoryId($newCategoryId);
        } elseif ($legacyCategoryId && !$newCategoryId) {
            $legacyCat = Category::find($legacyCategoryId);
            if ($legacyCat) {
                $mapped = CategoryAliasMapping::resolve($legacyCat->name_en ?: $legacyCat->name_ar);
                $newCategoryId = $mapped['new_category_id'] ?? null;
                $needsReview = $mapped['category_needs_review'] ?? ($newCategoryId ? false : true);
            }
        }

        $data = [
            'user_id' => $user?->id,
            'owner_id' => array_key_exists('owner_id', $validated) ? $validated['owner_id'] : $user?->id,
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $this->generateUniqueSlug($validated['name']),
            'category_id' => $legacyCategoryId,
            'new_category_id' => $newCategoryId,
            'category_needs_review' => (bool)$needsReview,
            'city_id' => $validated['city_id'],
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'] ?? null,
            'logo' => $logoPath,
            'cover' => $coverPath,
            'contact_methods' => $contactMethods,
            'is_claimed' => (bool)$user,
            'status' => 'pending',
            'meta_title' => ['ar' => $validated['name'], 'en' => $validated['name']],
            'meta_description' => ['ar' => Str::limit($validated['description'] ?? '', 150), 'en' => ''],
            'disk' => 'r2',
        ];

        $business = BusinessProfile::create($data);
        
        OptimizeProfileContent::dispatch($business);

        return $business;
    }

    public function updateProfile(BusinessProfile $business, array $validated, $request): void
    {
        $data = collect($validated)->except([
            'logo', 'cover', 'social_links', 'cover_source', 'selected_category_cover'
        ])->toArray();

        // Dual-write sync for category updates
        $hasNewCat = array_key_exists('new_category_id', $validated) && !is_null($validated['new_category_id']);
        $hasLegacyCat = array_key_exists('category_id', $validated) && !is_null($validated['category_id']);

        $legacyChanged = $hasLegacyCat && ((int)$validated['category_id'] !== (int)$business->category_id);

        if ($hasNewCat) {
            // Case 1: new_category_id explicitly updated / provided
            $newCategoryId = (int)$validated['new_category_id'];

            // Clear review flag unless explicitly passed as true
            $needsReview = array_key_exists('category_needs_review', $validated)
                ? (bool)$validated['category_needs_review']
                : false;

            // Legacy safety: never overwrite an existing non-null category_id with null
            if ($hasLegacyCat) {
                $legacyCategoryId = (int)$validated['category_id'];
            } else {
                $derivedLegacy = CategoryAliasMapping::getLegacyCategoryId($newCategoryId);
                $legacyCategoryId = $derivedLegacy ?: $business->category_id;
            }

            $data['new_category_id'] = $newCategoryId;
            $data['category_id'] = $legacyCategoryId;
            $data['category_needs_review'] = $needsReview;

        } elseif ($legacyChanged) {
            // Case 2: category_id actually changed and new_category_id was not provided
            $legacyCategoryId = (int)$validated['category_id'];
            $legacyCat = Category::find($legacyCategoryId);

            $newCategoryId = null;
            $needsReview = false;
            if ($legacyCat) {
                $mapped = CategoryAliasMapping::resolve($legacyCat->name_en ?: $legacyCat->name_ar);
                $newCategoryId = $mapped['new_category_id'] ?? null;
                $needsReview = $mapped['category_needs_review'] ?? ($newCategoryId ? false : true);
            }

            $data['category_id'] = $legacyCategoryId;
            $data['new_category_id'] = $newCategoryId;
            $data['category_needs_review'] = (bool)$needsReview;

        } else {
            // Case 3: Neither category changed, or category_id was submitted with the exact same value.
            // Retain existing classifications completely untouched!
            unset($data['new_category_id'], $data['category_needs_review']);

            // Admin may still update review flag independently
            if (array_key_exists('category_needs_review', $validated)) {
                $data['category_needs_review'] = (bool)$validated['category_needs_review'];
            }

            // Legacy safety: ensure existing non-null category_id is never nulled
            if (array_key_exists('category_id', $data) && is_null($data['category_id']) && !is_null($business->category_id)) {
                $data['category_id'] = $business->category_id;
            }
        }

        if ($request->hasFile('logo')) {
            $this->deleteImage($business->logo, $business->disk ?? 'public');
            $data['logo'] = $this->handleImageUpload($request->file('logo'), 'logos');
            $data['disk'] = 'r2';
        }

        if ($request->cover_source === 'upload' && $request->hasFile('cover')) {
            $this->deleteImage($business->cover, $business->disk ?? 'public');
            $data['cover'] = $this->handleImageUpload($request->file('cover'), 'covers');
            $data['disk'] = 'r2';
        } elseif ($request->cover_source === 'from_category' && !empty($validated['selected_category_cover'])) {
            $category = Category::find($validated['selected_category_cover']);
            if ($category && $category->image) {
                $this->deleteImage($business->cover, $business->disk ?? 'public');
                $data['cover'] = $category->image;
                $data['disk'] = $category->disk ?? 'public';
            }
        }

        $data['contact_methods'] = $this->formatContactMethods($validated);

        $business->update($data);

        OptimizeProfileContent::dispatch($business);
    }

    public function uploadMedia(BusinessProfile $business, array $files, ?array $captions, ?int $categoryId = null): array
    {
        $query = $business->media();
        if ($categoryId) {
            $query->where('business_image_category_id', $categoryId);
        } else {
            $query->whereNull('business_image_category_id');
        }
        $existingCount = $query->count();
        $uploaded = [];

        foreach ($files as $index => $image) {
            $path = $this->handleImageUpload($image, 'business_media');
            $media = $business->media()->create([
                'file_path' => $path,
                'type' => 'image',
                'caption' => $captions[$index] ?? null,
                'order' => $existingCount + $index,
                'disk' => 'r2',
                'business_image_category_id' => $categoryId,
            ]);
            $uploaded[] = [
                'id' => $media->id,
                'file_path' => $media->file_url,
                'caption' => $media->caption,
                'category_id' => $categoryId,
            ];
        }

        return $uploaded;
    }

    public function createImageCategory(BusinessProfile $business, string $name)
    {
        return $business->imageCategories()->create([
            'name' => $name
        ]);
    }

    public function updateMediaOrder(array $orderIds): void
    {
        foreach ($orderIds as $index => $id) {
            BusinessMedia::where('id', $id)->update(['order' => $index]);
        }
    }

    public function updateMediaCaption(BusinessMedia $media, ?string $caption): void
    {
        $media->update(['caption' => $caption]);
    }

    public function deleteMedia(BusinessMedia $media): void
    {
        $this->deleteImage($media->file_path, $media->disk ?? 'public');
        $media->delete();
    }
}
