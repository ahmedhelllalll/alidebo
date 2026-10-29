<?php

namespace App\Services;

use App\Models\BusinessProfile;
use App\Models\BusinessMedia;
use App\Models\BusinessImportBatch;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    /**
     * Resolve a clean relative storage/object key from a full URL or relative path.
     * Strips scheme, host, leading slashes, query parameters, and bucket name prefixes.
     * Defensively rejects and sanitizes directory traversal sequences ('..') and null bytes.
     */
    public function resolveStorageKey(?string $pathOrUrl): ?string
    {
        if (empty($pathOrUrl)) {
            return null;
        }

        $pathOrUrl = trim($pathOrUrl);

        // If it's a full URL, extract path component
        if (str_starts_with($pathOrUrl, 'http://') || str_starts_with($pathOrUrl, 'https://')) {
            $parsed = parse_url($pathOrUrl);
            $pathOrUrl = $parsed['path'] ?? '';
        }

        // 1. Strip null bytes and control characters
        $clean = str_replace(["\0", "\r", "\n"], '', $pathOrUrl);
        // Normalize backslashes to forward slashes
        $clean = str_replace('\\', '/', $clean);
        // URL decode components
        $clean = rawurldecode($clean);

        // 2. Disallow any directory traversal sequences (..)
        $segments = explode('/', $clean);
        $safeSegments = [];
        foreach ($segments as $segment) {
            $trimmed = trim($segment);
            if ($trimmed === '' || $trimmed === '.') {
                continue;
            }
            if ($trimmed === '..') {
                // Directory traversal detected! Abort immediately.
                Log::warning("BusinessProfileService: Directory traversal attempt blocked in storage key: {$pathOrUrl}");
                return null;
            }
            $safeSegments[] = $trimmed;
        }

        if (empty($safeSegments)) {
            return null;
        }

        $key = implode('/', $safeSegments);

        // 3. Strip configured bucket name prefix if present
        $bucket = config('filesystems.disks.r2.bucket') ?: env('CLOUDFLARE_R2_BUCKET');
        if (!empty($bucket) && str_starts_with($key, $bucket . '/')) {
            $key = substr($key, strlen($bucket) + 1);
        }

        return !empty($key) ? $key : null;
    }

    /**
     * Defensive deletion of an image from storage without crashing on missing files or network errors.
     */
    public function deleteImage(?string $path, string $disk = 'r2'): void
    {
        if (!$path || str_contains($path, 'categories')) {
            return;
        }

        try {
            $key = $this->resolveStorageKey($path);
            if ($key) {
                Storage::disk($disk)->delete($key);
            }
        } catch (\Throwable $e) {
            Log::warning("BusinessProfileService: Failed to delete image [{$path}] on disk [{$disk}]: " . $e->getMessage());
        }
    }

    /**
     * Hardened, chunked deletion of an import batch and all associated records and storage assets.
     *
     * @param BusinessImportBatch $importBatch
     * @return array Metrics of deleted records ['deleted_businesses' => int, 'deleted_media' => int]
     * @throws \RuntimeException if batch is currently processing
     */
    public function deleteImportBatch(BusinessImportBatch $importBatch): array
    {
        if ($importBatch->isProcessing()) {
            throw new \RuntimeException("Cannot delete Batch #{$importBatch->id} because it is currently processing. Wait for it to complete or fail.");
        }

        $deletedBusinessesCount = 0;
        $deletedMediaCount = 0;

        // Chunk through businesses to keep memory consumption near zero
        $importBatch->businesses()->chunkById(100, function ($businesses) use (&$deletedBusinessesCount, &$deletedMediaCount) {
            $businessIds = $businesses->pluck('id')->all();
            if (empty($businessIds)) {
                return;
            }

            // A. Collect all media keys for this chunk
            $mediaRecords = BusinessMedia::whereIn('business_profile_id', $businessIds)
                ->get(['id', 'file_path', 'disk']);

            $keysByDisk = [];

            foreach ($mediaRecords as $media) {
                $disk = $media->disk ?: 'r2';
                $key = $this->resolveStorageKey($media->file_path);
                if ($key) {
                    $keysByDisk[$disk][] = $key;
                    $deletedMediaCount++;
                }
            }

            foreach ($businesses as $business) {
                $disk = $business->disk ?: 'r2';
                if ($business->logo) {
                    $key = $this->resolveStorageKey($business->logo);
                    if ($key) {
                        $keysByDisk[$disk][] = $key;
                        $deletedMediaCount++;
                    }
                }
                if ($business->cover) {
                    $key = $this->resolveStorageKey($business->cover);
                    if ($key) {
                        $keysByDisk[$disk][] = $key;
                        $deletedMediaCount++;
                    }
                }
            }

            // B. Prune files from storage defensively using chunked array deletion (max 500 keys per call to respect S3/R2 1000 key limit)
            foreach ($keysByDisk as $disk => $keys) {
                try {
                    $uniqueKeys = array_values(array_unique($keys));
                    if (!empty($uniqueKeys)) {
                        foreach (array_chunk($uniqueKeys, 500) as $keyChunk) {
                            Storage::disk($disk)->delete($keyChunk);
                        }
                    }
                } catch (\Throwable $storageEx) {
                    Log::warning("deleteImportBatch: Storage deletion error on disk [{$disk}]: " . $storageEx->getMessage());
                }
            }

            // C. Delete related database records inside transaction
            DB::transaction(function () use ($businessIds) {
                // Delete child relations explicitly
                DB::table('business_media')->whereIn('business_profile_id', $businessIds)->delete();
                DB::table('business_views')->whereIn('business_profile_id', $businessIds)->delete();
                DB::table('business_leads')->whereIn('business_profile_id', $businessIds)->delete();
                DB::table('reviews')->whereIn('business_profile_id', $businessIds)->delete();
                DB::table('business_profile_translations')->whereIn('business_profile_id', $businessIds)->delete();
                DB::table('business_image_categories')->whereIn('business_profile_id', $businessIds)->delete();

                // Products & product images if any
                $productIds = DB::table('products')->whereIn('business_profile_id', $businessIds)->pluck('id')->all();
                if (!empty($productIds)) {
                    DB::table('product_images')->whereIn('product_id', $productIds)->delete();
                    DB::table('products')->whereIn('id', $productIds)->delete();
                }

                // Delete the business profiles
                DB::table('business_profiles')->whereIn('id', $businessIds)->delete();
            });

            $deletedBusinessesCount += count($businessIds);
        });

        // 3. Prune local batch files
        $localFilesToDelete = array_filter([
            $importBatch->file_path,
            $importBatch->error_log_path,
            $importBatch->rejected_file_path,
            "imports/errors/batch_{$importBatch->id}.txt",
            "imports/rejected/batch_{$importBatch->id}_rejected.csv",
        ]);

        foreach (array_unique($localFilesToDelete) as $localFile) {
            try {
                if (Storage::disk('local')->exists($localFile)) {
                    Storage::disk('local')->delete($localFile);
                }
            } catch (\Throwable $fileEx) {
                Log::warning("deleteImportBatch: Local file deletion failed for [{$localFile}]: " . $fileEx->getMessage());
            }
        }

        // 4. Delete the batch record itself
        $importBatch->delete();

        return [
            'deleted_businesses' => $deletedBusinessesCount,
            'deleted_media' => $deletedMediaCount,
        ];
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
        $this->deleteImage($media->file_path, $media->disk ?: 'r2');
        $media->delete();
    }
}
