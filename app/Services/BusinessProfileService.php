<?php

namespace App\Services;

use App\Models\BusinessProfile;
use App\Models\BusinessMedia;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class BusinessProfileService
{
    public function formatContactMethods(array $validated): array
    {
        $links = $validated['social_links'] ?? [];
        return [
            'whatsapp' => $links['whatsapp'] ?? null,
            'phone' => $links['phone'] ?? null,
            'website' => $links['website'] ?? null,
            'facebook' => $links['facebook'] ?? null,
            'instagram' => $links['instagram'] ?? null,
            'twitter' => $links['twitter'] ?? null,
            'tiktok' => $links['tiktok'] ?? null,
            'linkedin' => $links['linkedin'] ?? null,
            'youtube' => $links['youtube'] ?? null,
            'snapchat' => $links['snapchat'] ?? null,
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

        $data = [
            'user_id' => $user?->id,
            'owner_id' => array_key_exists('owner_id', $validated) ? $validated['owner_id'] : $user?->id,
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $this->generateUniqueSlug($validated['name']),
            'category_id' => $validated['category_id'],
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

        return BusinessProfile::create($data);
    }

    public function updateProfile(BusinessProfile $business, array $validated, $request): void
    {
        $data = collect($validated)->except([
            'logo', 'cover', 'social_links', 'cover_source', 'selected_category_cover'
        ])->toArray();

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
    }

    public function uploadMedia(BusinessProfile $business, array $files, ?array $captions): array
    {
        $existingCount = $business->media()->count();
        $uploaded = [];

        foreach ($files as $index => $image) {
            $path = $this->handleImageUpload($image, 'business_media');
            $media = $business->media()->create([
                'file_path' => $path,
                'type' => 'image',
                'caption' => $captions[$index] ?? null,
                'order' => $existingCount + $index,
                'disk' => 'r2',
            ]);
            $uploaded[] = [
                'id' => $media->id,
                'file_path' => $media->file_url,
                'caption' => $media->caption,
            ];
        }

        return $uploaded;
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
