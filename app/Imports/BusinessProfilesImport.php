<?php

namespace App\Imports;

use App\Models\BusinessProfile;
use App\Models\BusinessMedia;
use App\Models\Category;
use App\Models\NewCategory;
use App\Models\City;
use App\Models\Country;
use App\Models\BusinessImportBatch;
use App\Services\BusinessImportQueueService;
use App\Services\DataNormalizationService;
use App\Mail\ImportCompleted;
use App\Support\CategoryAliasMapping;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BusinessProfilesImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents, SkipsEmptyRows, WithMultipleSheets
{
    public const HEADERS = [
        'business_id',
        'company_name',
        'main_category',
        'sub_category',
        'new_category_id',
        'category_slug',
        'category',
        'city',
        'country',
        'description',
        'address',
        'phone',
        'whatsapp',
        'website',
        'facebook',
        'instagram',
        'twitter',
        'x_twitter',
        'tiktok',
        'linkedin',
        'youtube',
        'snapchat',
        'business_logo',
        'logo_url',
        'business_cover',
        'cover_url',
        'image_1',
        'image_2',
        'image_3',
        'image_4',
        'image_5',
        'image_6',
        'image_7',
        'image_8',
        'image_9',
        'image_10',
        'image_11',
        'image_12',
    ];

    public const MAX_IMAGE_SIZE = 5242880; // 5MB limit
    public const IMAGE_TIMEOUT = 5;        // 5s total timeout
    public const IMAGE_CONNECT_TIMEOUT = 3; // 3s connection timeout

    public string $queue = BusinessImportQueueService::QUEUE_NAME;
    public $timeout = 1800; // 30 minutes to prevent chunk timeout

    private $adminEmail;
    private $adminId;
    private $importId;
    private $disk;
    private $normalizer;
    private $batchId;
    private $isDryRun;
    private $targetSheet;

    private int $chunkImported = 0;
    private int $chunkUpdated = 0;
    private int $chunkSkipped = 0;

    public function __construct($adminEmail, $adminId = null, $batchId = null, bool $isDryRun = false, ?string $targetSheet = null)
    {
        \Maatwebsite\Excel\Imports\HeadingRowFormatter::default('none');

        $this->adminEmail = $adminEmail;
        $this->adminId = $adminId ?? (\App\Models\User::where('email', $adminEmail)->first()->id ?? 1);
        $this->importId = Str::uuid()->toString();
        $this->disk = config('filesystems.default');
        $this->batchId = $batchId;
        $this->isDryRun = $isDryRun;
        $this->targetSheet = $targetSheet;
        $this->normalizer = new DataNormalizationService();

        // Initialize stats with 24-hour TTL (86400s) to prevent expiration during large/long-running imports
        Cache::put("import_stats_{$this->importId}_total", 0, 86400);
        Cache::put("import_stats_{$this->importId}_imported", 0, 86400);
        Cache::put("import_stats_{$this->importId}_updated", 0, 86400);
        Cache::put("import_stats_{$this->importId}_skipped", 0, 86400);
        Cache::put("import_stats_{$this->importId}_flagged", 0, 86400);
        Cache::put("import_stats_{$this->importId}_new_categories", 0, 86400);
        Cache::put("import_stats_{$this->importId}_new_cities", 0, 86400);
    }

    public function sheets(): array
    {
        if ($this->targetSheet) {
            return [
                $this->targetSheet => $this,
            ];
        }

        return [
            0 => $this,
        ];
    }

    public function collection(Collection $rows)
    {
        $this->normalizer = new DataNormalizationService();
        $legacyCats = Category::all();
        $newCats = NewCategory::all()->keyBy('id');
        $cities = City::all();
        $countries = Country::all();

        $this->chunkImported = 0;
        $this->chunkUpdated = 0;
        $this->chunkSkipped = 0;

        foreach ($rows as $rowIndex => $row) {
            try {
                // Skip entirely blank rows (e.g. empty rows in pre-formatted templates)
                $rowArray = $row->toArray();
                $hasAnyContent = false;
                foreach ($rowArray as $cellVal) {
                    if ($cellVal !== null && trim((string)$cellVal) !== '') {
                        $hasAnyContent = true;
                        break;
                    }
                }
                if (!$hasAnyContent) {
                    continue;
                }

                Cache::increment("import_stats_{$this->importId}_total");

                DB::transaction(function () use ($row, &$newCats, &$legacyCats, &$cities, &$countries) {
                    // 1. Validate Company Name (Mandatory) & normalize zero-width/non-breaking unicode whitespace
                    $companyName = $this->cleanString($this->getValueByHeaderName($row, 'company_name'));
                    if (empty($companyName)) {
                        $this->recordRejectedRow($row, "Company name is missing/blank. Row rejected.", 'ERR_EMPTY_NAME');
                        return;
                    }

                    // 2. Resolve Category (3-step priority: new_category_id -> category_slug -> legacy category text)
                    $catRes = $this->resolveCategoryData($row, $companyName, $newCats, $legacyCats);
                    if (!$catRes['success']) {
                        $this->recordRejectedRow($row, $catRes['error'], $catRes['error_code'] ?? 'ERR_CATEGORY_NOT_FOUND');
                        return;
                    }

                    if (!empty($catRes['warning']) && $this->batchId) {
                        $this->logWarning($catRes['warning'], $companyName);
                    }
                    if ($catRes['needs_review']) {
                        Cache::increment("import_stats_{$this->importId}_flagged");
                    }

                    // 3. Resolve City & Country
                    $cityName = $this->getValueByHeaderName($row, 'city');
                    $countryName = $this->getValueByHeaderName($row, 'country');
                    $cityId = $this->resolveCity($cityName, $countryName, $cities, $countries);

                    // 4. Contact Methods
                    $contactMethods = [
                        'phone'     => $this->getValueByHeaderName($row, 'phone'),
                        'whatsapp'  => $this->getValueByHeaderName($row, 'whatsapp'),
                        'website'   => $this->getValueByHeaderName($row, 'website'),
                        'facebook'  => $this->getValueByHeaderName($row, 'facebook'),
                        'instagram' => $this->getValueByHeaderName($row, 'instagram'),
                        'twitter'   => $this->getValueByHeaderName($row, 'twitter'),
                        'tiktok'    => $this->getValueByHeaderName($row, 'tiktok'),
                        'linkedin'  => $this->getValueByHeaderName($row, 'linkedin'),
                        'youtube'   => $this->getValueByHeaderName($row, 'youtube'),
                        'snapchat'  => $this->getValueByHeaderName($row, 'snapchat'),
                    ];

                    $description = $this->getValueByHeaderName($row, 'description');
                    $address = $this->getValueByHeaderName($row, 'address');
                    $logoUrl = $this->getValueByHeaderName($row, 'business_logo');
                    $coverUrl = $this->getValueByHeaderName($row, 'business_cover');

                    // 5. UPDATE vs CREATE Handling
                    $businessId = $this->getValueByHeaderName($row, 'business_id');

                    if (!empty($businessId) && is_numeric($businessId)) {
                        $existing = BusinessProfile::find((int)$businessId);
                        if (!$existing) {
                            $this->recordRejectedRow($row, "Business ID [{$businessId}] not found in database for update.", 'ERR_BUSINESS_NOT_FOUND');
                            return;
                        }

                        // UPDATE EXISTING BUSINESS (Blank cells never clear existing values)
                        $updateData = [];
                        if (!empty($companyName) && $companyName !== $existing->name) {
                            $updateData['name'] = $companyName;
                        }
                        if (!empty($description)) {
                            $updateData['description'] = $description;
                        }
                        if (!empty($address)) {
                            $updateData['address'] = $address;
                        }
                        if ($cityId) {
                            $updateData['city_id'] = $cityId;
                        }

                        if ($catRes['cat']) {
                            $updateData['new_category_id'] = $catRes['cat']->id;
                            $updateData['category_id'] = $catRes['legacy_id'];
                            $updateData['category_needs_review'] = $catRes['needs_review'];
                        } elseif ($catRes['legacy_id']) {
                            $updateData['category_id'] = $catRes['legacy_id'];
                            $updateData['category_needs_review'] = true;
                        }

                        // Merge contact methods preserving existing ones if not provided
                        $existingContacts = is_array($existing->contact_methods) ? $existing->contact_methods : [];
                        foreach ($contactMethods as $key => $val) {
                            if (!empty($val)) {
                                $existingContacts[$key] = $val;
                            }
                        }
                        $updateData['contact_methods'] = $existingContacts;

                        if (!$this->isDryRun) {
                            // Logo update rule: only update if valid URL and changed
                            if ($logoUrl && filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                                if ($logoUrl !== $existing->logo_url && $logoUrl !== $existing->logo) {
                                    $newLogo = $this->resolveMediaUrl($logoUrl, 'logos', $companyName);
                                    if ($newLogo) {
                                        if ($existing->logo && !str_starts_with($existing->logo, 'http')) {
                                            Storage::disk($existing->disk ?: $this->disk)->delete($existing->logo);
                                        }
                                        $updateData['logo'] = $newLogo;
                                        if ($this->isPreUploadedR2Url($logoUrl)) {
                                            $updateData['disk'] = 'r2';
                                        }
                                    }
                                }
                            }

                            // Cover update rule: only update if valid URL and changed
                            if ($coverUrl && filter_var($coverUrl, FILTER_VALIDATE_URL)) {
                                if ($coverUrl !== $existing->cover_url && $coverUrl !== $existing->cover) {
                                    $newCover = $this->resolveMediaUrl($coverUrl, 'covers', $companyName);
                                    if ($newCover) {
                                        if ($existing->cover && !str_starts_with($existing->cover, 'http')) {
                                            Storage::disk($existing->disk ?: $this->disk)->delete($existing->cover);
                                        }
                                        $updateData['cover'] = $newCover;
                                        if ($this->isPreUploadedR2Url($coverUrl)) {
                                            $updateData['disk'] = 'r2';
                                        }
                                    }
                                }
                            }

                            // Gallery images update (Image 1 to Image 12)
                            for ($i = 1; $i <= 12; $i++) {
                                $imgUrl = $this->getValueByHeaderName($row, "image_{$i}");
                                if ($imgUrl && filter_var($imgUrl, FILTER_VALIDATE_URL)) {
                                    $existingMedia = BusinessMedia::where('business_profile_id', $existing->id)
                                        ->where('order', $i)
                                        ->first();

                                    // If media is already pointing to this exact URL, skip
                                    if ($existingMedia && ($existingMedia->file_path === $imgUrl || $existingMedia->url === $imgUrl)) {
                                        continue;
                                    }

                                    $pathOrUrl = $this->resolveMediaUrl($imgUrl, 'gallery', $companyName);
                                    if ($pathOrUrl) {
                                        if ($existingMedia) {
                                            if ($existingMedia->file_path && !str_starts_with($existingMedia->file_path, 'http')) {
                                                Storage::disk($existingMedia->disk ?: $this->disk)->delete($existingMedia->file_path);
                                            }
                                            $existingMedia->update([
                                                'file_path' => $pathOrUrl,
                                                'disk' => $this->isPreUploadedR2Url($imgUrl) ? 'r2' : $this->disk,
                                                'type' => 'image',
                                            ]);
                                        } else {
                                            BusinessMedia::create([
                                                'business_profile_id' => $existing->id,
                                                'file_path' => $pathOrUrl,
                                                'type' => 'image',
                                                'order' => $i,
                                                'disk' => $this->isPreUploadedR2Url($imgUrl) ? 'r2' : $this->disk,
                                            ]);
                                        }
                                    }
                                }
                            }

                            $existing->update($updateData);
                        }

                        Cache::increment("import_stats_{$this->importId}_updated");
                        $this->chunkUpdated++;
                        return;
                    }

                    // 6. CREATE NEW BUSINESS (Duplicate Detection Safeguards)
                    // Check 1: Name + City
                    if ($cityId) {
                        $dupNameCity = BusinessProfile::where('name', $companyName)->where('city_id', $cityId)->first();
                        if ($dupNameCity) {
                            $this->recordRejectedRow($row, "Duplicate detected: Company with name '{$companyName}' and same city already exists as Business #{$dupNameCity->id}.", 'ERR_DUPLICATE_DETECTED');
                            return;
                        }
                    } else {
                        $dupName = BusinessProfile::where('name', $companyName)->first();
                        if ($dupName) {
                            $this->recordRejectedRow($row, "Duplicate detected: Company with name '{$companyName}' already exists as Business #{$dupName->id}.", 'ERR_DUPLICATE_DETECTED');
                            return;
                        }
                    }

                    // Check 2: Phone (Exact & Digits-only Normalized)
                    $phone = $contactMethods['phone'] ?? null;
                    if (!empty($phone)) {
                        $dupPhone = BusinessProfile::where('contact_methods->phone', $phone)->first();
                        if (!$dupPhone) {
                            $digitPhone = preg_replace('/\D+/', '', $phone);
                            if (strlen($digitPhone) >= 7) {
                                $dupPhone = BusinessProfile::where('contact_methods->phone', 'like', '%' . substr($digitPhone, -7) . '%')->first();
                            }
                        }
                        if ($dupPhone) {
                            $this->recordRejectedRow($row, "Duplicate detected: Phone '{$phone}' already exists on Business #{$dupPhone->id} ('{$dupPhone->name}').", 'ERR_DUPLICATE_DETECTED');
                            return;
                        }
                    }

                    // Check 3: Website
                    $website = $contactMethods['website'] ?? null;
                    if (!empty($website)) {
                        $dupWebsite = BusinessProfile::where('contact_methods->website', $website)->first();
                        if ($dupWebsite) {
                            $this->recordRejectedRow($row, "Duplicate detected: Website '{$website}' already exists on Business #{$dupWebsite->id} ('{$dupWebsite->name}').", 'ERR_DUPLICATE_DETECTED');
                            return;
                        }
                    }

                    // Create Profile
                    if (!$this->isDryRun) {
                        // Idempotency check: if chunk retried, avoid duplicate creation in same batch
                        if ($this->batchId) {
                            $existingInBatch = BusinessProfile::where('import_batch_id', $this->batchId)
                                ->where('name', $companyName)
                                ->first();
                            if ($existingInBatch) {
                                Cache::increment("import_stats_{$this->importId}_imported");
                                $this->chunkImported++;
                                return;
                            }
                        }

                        $baseSlug = Str::slug($companyName);
                        if (empty($baseSlug)) {
                            $baseSlug = 'business-' . Str::lower(Str::random(6));
                        }
                        $slug = $baseSlug . '-' . Str::lower(Str::random(5));
                        while (BusinessProfile::where('slug', $slug)->exists()) {
                            $slug = $baseSlug . '-' . Str::lower(Str::random(8));
                        }

                        $profile = BusinessProfile::create([
                            'user_id' => $this->adminId,
                            'name' => $companyName,
                            'slug' => $slug,
                            'description' => $description,
                            'address' => $address,
                            'category_id' => $catRes['legacy_id'],
                            'new_category_id' => $catRes['cat'] ? $catRes['cat']->id : null,
                            'category_needs_review' => $catRes['needs_review'],
                            'city_id' => $cityId,
                            'contact_methods' => array_filter($contactMethods),
                            'status' => 'approved', // Auto-approve imported businesses (published)
                            'is_claimed' => false,
                            'disk' => $this->disk,
                            'import_batch_id' => $this->batchId,
                        ]);

                        // Process Logo & Cover
                        if ($logoUrl && filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                            $profile->logo = $this->resolveMediaUrl($logoUrl, 'logos', $companyName);
                        }
                        if ($coverUrl && filter_var($coverUrl, FILTER_VALIDATE_URL)) {
                            $profile->cover = $this->resolveMediaUrl($coverUrl, 'covers', $companyName);
                        }
                        if ($this->isPreUploadedR2Url($logoUrl) || $this->isPreUploadedR2Url($coverUrl)) {
                            $profile->disk = 'r2';
                        }
                        $profile->save();

                        // Process Gallery Images (Image 1 to Image 12)
                        for ($i = 1; $i <= 12; $i++) {
                            $imgUrl = $this->getValueByHeaderName($row, "image_{$i}");
                            if ($imgUrl && filter_var($imgUrl, FILTER_VALIDATE_URL)) {
                                $pathOrUrl = $this->resolveMediaUrl($imgUrl, 'gallery', $companyName);
                                if ($pathOrUrl) {
                                    BusinessMedia::create([
                                        'business_profile_id' => $profile->id,
                                        'file_path' => $pathOrUrl,
                                        'type' => 'image',
                                        'order' => $i,
                                        'disk' => $this->isPreUploadedR2Url($imgUrl) ? 'r2' : $this->disk,
                                    ]);
                                }
                            }
                        }
                    }

                    Cache::increment("import_stats_{$this->importId}_imported");
                    $this->chunkImported++;
                });
            } catch (\Throwable $e) {
                $errorMsg = "Fatal/System Error: " . $e->getMessage();
                Log::error("Excel Import Row Fatal Failure: " . $e->getMessage(), [
                    'row_index' => $rowIndex,
                    'exception' => $e,
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                ]);
                $this->recordRejectedRow($row, $errorMsg, 'ERR_FATAL_ROW');
                continue;
            }
        }

        // Flush chunk stats and touch heartbeat timestamp unconditionally
        if ($this->batchId) {
            $totalInChunk = $this->chunkImported + $this->chunkUpdated + $this->chunkSkipped;
            if ($totalInChunk > 0) {
                BusinessImportBatch::where('id', $this->batchId)->update([
                    'imported_rows' => DB::raw("imported_rows + {$this->chunkImported}"),
                    'updated_rows'  => DB::raw("updated_rows + {$this->chunkUpdated}"),
                    'skipped_rows'  => DB::raw("skipped_rows + {$this->chunkSkipped}"),
                    'total_rows'    => DB::raw("total_rows + {$totalInChunk}"),
                    'updated_at'    => now(),
                ]);
            } else {
                BusinessImportBatch::where('id', $this->batchId)->touch();
            }
        }

        $this->chunkImported = 0;
        $this->chunkUpdated = 0;
        $this->chunkSkipped = 0;

        unset($rows, $legacyCats, $newCats, $cities, $countries);
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    /**
     * Resolve V2 category using priority:
     * 1. new_category_id (if present)
     * 2. category_slug (if present)
     * 3. sub_category (search by name, level 2 leaf)
     * 4. main_category fallback (when sub_category is empty or invalid, flagged for review)
     * 5. legacy category text fallback
     */
    private function resolveCategoryData($row, $companyName, $newCats, $legacyCats): array
    {
        $newCatId = $this->getValueByHeaderName($row, 'new_category_id');
        $categorySlug = $this->getValueByHeaderName($row, 'category_slug');
        $subCategory = $this->getValueByHeaderName($row, 'sub_category');
        $mainCategory = $this->getValueByHeaderName($row, 'main_category');
        $legacyCategory = $this->getValueByHeaderName($row, 'category');

        // Priority 1: new_category_id
        if (!empty($newCatId)) {
            $id = (int)$newCatId;
            $cat = $newCats->get($id);
            if (!$cat) {
                return [
                    'success' => false,
                    'error_code' => 'ERR_CATEGORY_NOT_FOUND',
                    'error' => "Category ID [{$newCatId}] does not exist in new_categories.",
                ];
            }

            $needsReview = ($cat->level === 1 && $cat->id !== 21);
            $legacyId = CategoryAliasMapping::getLegacyCategoryId($cat->id);

            return [
                'success' => true,
                'cat' => $cat,
                'legacy_id' => $legacyId,
                'needs_review' => $needsReview,
                'warning' => $needsReview ? "Assigned to top-level L1 parent [{$cat->id} - {$cat->name_ar}]. Flagged for review." : null,
            ];
        }

        // Priority 2: category_slug
        if (!empty($categorySlug)) {
            $cat = $newCats->firstWhere('slug', trim($categorySlug));
            if (!$cat) {
                return [
                    'success' => false,
                    'error_code' => 'ERR_CATEGORY_NOT_FOUND',
                    'error' => "Category slug [{$categorySlug}] does not exist in new_categories.",
                ];
            }

            $needsReview = ($cat->level === 1 && $cat->id !== 21);
            $legacyId = CategoryAliasMapping::getLegacyCategoryId($cat->id);

            return [
                'success' => true,
                'cat' => $cat,
                'legacy_id' => $legacyId,
                'needs_review' => $needsReview,
                'warning' => $needsReview ? "Assigned to top-level L1 parent [{$cat->id} - {$cat->name_ar}]. Flagged for review." : null,
            ];
        }

        // Priority 3: sub_category (search by name, level 2 leaf)
        if (!empty($subCategory)) {
            $matchedSub = $this->matchCategoryByName($subCategory, $newCats, 2);
            if ($matchedSub) {
                $needsReview = ($matchedSub->level === 1 && $matchedSub->id !== 21);
                $legacyId = CategoryAliasMapping::getLegacyCategoryId($matchedSub->id);

                return [
                    'success' => true,
                    'cat' => $matchedSub,
                    'legacy_id' => $legacyId,
                    'needs_review' => $needsReview,
                    'warning' => $needsReview ? "Assigned to top-level L1 parent [{$matchedSub->id} - {$matchedSub->name_ar}] from sub_category. Flagged for review." : null,
                ];
            }
            // If sub_category was provided but could not be resolved, fallback to main_category below!
        }

        // Priority 4: main_category fallback (when sub_category is empty or invalid)
        if (!empty($mainCategory)) {
            $matchedMain = $this->matchCategoryByName($mainCategory, $newCats, 1);
            if ($matchedMain) {
                $legacyId = CategoryAliasMapping::getLegacyCategoryId($matchedMain->id);
                $reason = !empty($subCategory)
                    ? "sub_category '{$subCategory}' could not be resolved"
                    : "sub_category was empty";

                return [
                    'success' => true,
                    'cat' => $matchedMain,
                    'legacy_id' => $legacyId,
                    'needs_review' => true, // Fallback to main category is always flagged for review
                    'warning' => "Fallback to main category [{$matchedMain->name_ar}] because {$reason}. Flagged for review.",
                ];
            }
        }

        // Priority 5: legacy single-column category text fallback
        if (!empty($legacyCategory)) {
            $matchedLegacy = $this->matchCategoryByName($legacyCategory, $newCats, null);
            if ($matchedLegacy) {
                $needsReview = ($matchedLegacy->level === 1 && $matchedLegacy->id !== 21);
                $legacyId = CategoryAliasMapping::getLegacyCategoryId($matchedLegacy->id);
                return [
                    'success' => true,
                    'cat' => $matchedLegacy,
                    'legacy_id' => $legacyId,
                    'needs_review' => $needsReview,
                    'warning' => $needsReview ? "Matched category [{$matchedLegacy->name_ar}] from legacy category column. Flagged for review." : null,
                ];
            }

            // Match against legacy categories table
            $legacyModelMatch = $this->normalizer->match($legacyCategory, $legacyCats, ['name_ar', 'name_en', 'name_de', 'name_es', 'name_tr', 'name_zh']);
            if ($legacyModelMatch) {
                $mapped = CategoryAliasMapping::resolve($legacyModelMatch->name_en ?: $legacyModelMatch->name_ar);
                $cat = !empty($mapped['new_category_id']) ? $newCats->get($mapped['new_category_id']) : null;
                return [
                    'success' => true,
                    'cat' => $cat,
                    'legacy_id' => $legacyModelMatch->id,
                    'needs_review' => $mapped['category_needs_review'] ?? ($cat ? false : true),
                    'warning' => "Matched legacy category [{$legacyModelMatch->name_ar}]. Flagged for review.",
                ];
            }
        }

        // 6. Unmapped or empty category
        $unmappedTerm = $subCategory ?: ($mainCategory ?: $legacyCategory);
        if (!empty($unmappedTerm)) {
            return [
                'success' => true,
                'cat' => null,
                'legacy_id' => null,
                'needs_review' => true,
                'warning' => "Unmapped category '{$unmappedTerm}' for company '{$companyName}' (set to null and flagged for review).",
            ];
        }

        return [
            'success' => true,
            'cat' => null,
            'legacy_id' => null,
            'needs_review' => true,
            'warning' => "No category provided for company '{$companyName}' (set to null and flagged for review).",
        ];
    }

    /**
     * Search new_categories collection by name or alias.
     */
    private function matchCategoryByName(string $name, $newCats, ?int $preferredLevel = null): ?NewCategory
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            return null;
        }

        $normSearch = $this->normalizer->normalize($trimmed);
        $fields = ['name_ar', 'name_en', 'name_de', 'name_es', 'name_tr', 'name_zh'];

        // 1. Check CategoryAliasMapping first (exact alias mapping)
        $mapped = CategoryAliasMapping::resolve($trimmed);
        if ($mapped && !empty($mapped['new_category_id'])) {
            $cat = $newCats->get($mapped['new_category_id']);
            if ($cat) {
                return $cat;
            }
        }

        // 2. Exact normalized match in preferred level (if specified)
        if ($preferredLevel !== null) {
            $levelCats = $newCats->where('level', $preferredLevel);
            foreach ($levelCats as $cat) {
                foreach ($fields as $field) {
                    if (!empty($cat->$field) && $this->normalizer->normalize($cat->$field) === $normSearch) {
                        return $cat;
                    }
                }
            }
        }

        // 3. Exact normalized match across all new_categories
        foreach ($newCats as $cat) {
            foreach ($fields as $field) {
                if (!empty($cat->$field) && $this->normalizer->normalize($cat->$field) === $normSearch) {
                    return $cat;
                }
            }
        }

        // 4. Slug match
        $slugSearch = Str::slug($trimmed);
        $slugMatch = $newCats->firstWhere('slug', $slugSearch) ?: $newCats->firstWhere('slug', $trimmed);
        if ($slugMatch) {
            return $slugMatch;
        }

        // 5. Loose match (contains) in preferred level
        if ($preferredLevel !== null) {
            $levelCats = $newCats->where('level', $preferredLevel);
            foreach ($levelCats as $cat) {
                foreach ($fields as $field) {
                    if (!empty($cat->$field)) {
                        $normItem = $this->normalizer->normalize($cat->$field);
                        if (mb_strlen($normItem) >= 4 && mb_strlen($normSearch) >= 4) {
                            if (str_contains($normItem, $normSearch) || str_contains($normSearch, $normItem)) {
                                return $cat;
                            }
                        }
                    }
                }
            }
        }

        // 6. Loose match across all new_categories
        foreach ($newCats as $cat) {
            foreach ($fields as $field) {
                if (!empty($cat->$field)) {
                    $normItem = $this->normalizer->normalize($cat->$field);
                    if (mb_strlen($normItem) >= 4 && mb_strlen($normSearch) >= 4) {
                        if (str_contains($normItem, $normSearch) || str_contains($normSearch, $normItem)) {
                            return $cat;
                        }
                    }
                }
            }
        }

        return null;
    }

    private function resolveCity($cityName, $countryName, &$cities, &$countries)
    {
        $cityName = $this->cleanString($cityName);
        $countryName = $this->cleanString($countryName);

        if (empty($cityName)) {
            return null;
        }

        $cityMatch = $this->normalizer->match($cityName, $cities, ['name_ar', 'name_en', 'name_de', 'name_es', 'name_tr', 'name_zh']);
        if ($cityMatch) {
            return $cityMatch->id;
        }

        if ($this->isDryRun) {
            return null;
        }

        // Resolve or create country (required non-null foreign key on cities table)
        $countryId = null;
        if (!empty($countryName)) {
            $countryMatch = $this->normalizer->match($countryName, $countries, ['name_ar', 'name_en', 'name_de', 'name_es', 'name_tr', 'name_zh']);
            if ($countryMatch) {
                $countryId = $countryMatch->id;
            } else {
                // Ensure safe 2-character country code even if non-Latin or Arabic script
                $cleanCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', Str::slug($countryName)), 0, 2));
                if (empty($cleanCode) || strlen($cleanCode) < 2) {
                    $cleanCode = strtoupper(substr(md5($countryName), 0, 2));
                }

                $country = Country::firstOrCreate(
                    ['name_en' => $countryName],
                    [
                        'name_ar' => $countryName,
                        'code'    => $cleanCode,
                        'status'  => 'pending',
                    ]
                );
                if (!$countries->contains('id', $country->id)) {
                    $countries->push($country);
                }
                $countryId = $country->id;
            }
        } else {
            // Country not specified; fall back to the first active platform country to prevent NOT NULL SQL constraint violation
            $defaultCountry = $countries->firstWhere('status', 'active') ?: $countries->first();
            if ($defaultCountry) {
                $countryId = $defaultCountry->id;
            }
        }

        // If no country exists in the system at all, log warning and do not create orphaned city
        if (!$countryId) {
            Log::warning("resolveCity: Cannot create city '{$cityName}' without a valid country_id.");
            return null;
        }

        $city = City::firstOrCreate(
            ['name_en' => $cityName, 'country_id' => $countryId],
            [
                'name_ar' => $cityName,
                'status'  => 'pending',
            ]
        );
        if (!$cities->contains('id', $city->id)) {
            $cities->push($city);
        }
        Cache::increment("import_stats_{$this->importId}_new_cities");
        return $city->id;
    }

    /**
     * Determine if an image URL is a pre-uploaded Cloudflare R2 or CDN asset.
     */
    private function isPreUploadedR2Url(?string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        $url = trim($url);
        if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $url)) {
            return false;
        }

        // 1. Matches Cloudflare R2 default domain (*.r2.dev or *.cloudflarestorage.com)
        if (str_contains($url, 'r2.dev') || str_contains($url, 'r2.cloudflarestorage.com')) {
            return true;
        }

        // 2. Matches configured Cloudflare R2 public URL / custom domain
        $r2ConfigUrl = config('filesystems.disks.r2.url') ?: env('CLOUDFLARE_R2_URL');
        if (!empty($r2ConfigUrl)) {
            $configuredHost = parse_url($r2ConfigUrl, PHP_URL_HOST);
            $urlHost = parse_url($url, PHP_URL_HOST);
            if ($configuredHost && $urlHost && strcasecmp($configuredHost, $urlHost) === 0) {
                return true;
            }
            if (str_starts_with($url, rtrim($r2ConfigUrl, '/'))) {
                return true;
            }
        }

        // 3. Matches standard pre-processed R2 storage paths
        if (str_contains($url, '/businesses/logos/') ||
            str_contains($url, '/businesses/covers/') ||
            str_contains($url, '/businesses/gallery/')) {
            return true;
        }

        // 4. When remote image scraping is disabled (default), all valid HTTP/HTTPS URLs
        // in the spreadsheet are treated as pre-uploaded CDN/R2 links.
        if (!config('services.import.enable_image_scraping', env('IMPORT_ENABLE_IMAGE_SCRAPING', false))) {
            return true;
        }

        return false;
    }

    /**
     * Resolve image URL for import.
     * Pre-uploaded R2/CDN links are returned immediately with zero network latency.
     * Other remote links fall back to downloadAndStoreImage() if scraping is enabled.
     */
    private function resolveMediaUrl(?string $url, string $folder, ?string $companyName = null): ?string
    {
        if ($this->isDryRun || empty($url)) {
            return null;
        }

        $url = trim($url);
        if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $url)) {
            return null;
        }

        // Fast-path bypass for pre-uploaded Cloudflare R2 / CDN media
        if ($this->isPreUploadedR2Url($url)) {
            return $url;
        }

        // Fallback: download, validate MIME/bytes, and upload to storage disk
        return $this->downloadAndStoreImage($url, $folder, $companyName);
    }

    private function downloadAndStoreImage($url, string $folder, ?string $companyName = null): ?string
    {
        if ($this->isDryRun || empty($url)) {
            return null;
        }

        $url = trim($url);
        if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $url)) {
            return null;
        }

        // Fast-path bypass: return pre-uploaded R2/CDN links directly
        if ($this->isPreUploadedR2Url($url)) {
            return $url;
        }

        // EMERGENCY BANDWIDTH PROTECTION: Remote image scraping and R2 uploads are paused
        // to prevent bandwidth exhaustion, queue worker timeouts, and hung jobs.
        // Pure textual, category, contact, and business metadata imports proceed at maximum speed.
        if (!config('services.import.enable_image_scraping', env('IMPORT_ENABLE_IMAGE_SCRAPING', false))) {
            return null;
        }

        // SSRF Defense: strictly verify that the target URL resolves to a public, routable IP address
        if (!$this->isSafePublicUrl($url)) {
            $msg = "Image skipped: URL blocked by SSRF defense (non-public or cloud metadata endpoint): {$url}";
            Log::warning($msg);
            if ($companyName) {
                $this->logWarning($msg, $companyName);
            }
            return null;
        }

        try {
            // Stream GET with abort callback if size exceeds 5MB and disable redirects to prevent SSRF bypass
            $response = Http::timeout(self::IMAGE_TIMEOUT)
                ->connectTimeout(self::IMAGE_CONNECT_TIMEOUT)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; AlideboBot/1.0)'])
                ->withOptions([
                    'allow_redirects' => false,
                    'progress' => function ($downloadTotal, $downloadedNow) use ($url) {
                        if (($downloadTotal > self::MAX_IMAGE_SIZE) || ($downloadedNow > self::MAX_IMAGE_SIZE)) {
                            throw new \RuntimeException("Image exceeded maximum 5MB size limit ({$downloadedNow} bytes downloaded): {$url}");
                        }
                    },
                ])
                ->get($url);

            if (!$response->successful()) {
                Log::warning("Image download failed with status {$response->status()} for URL: {$url}");
                return null;
            }

            $body = $response->body();
            if (empty($body) || strlen($body) > self::MAX_IMAGE_SIZE) {
                unset($body);
                return null;
            }

            // 3. Inspect MIME type (magic bytes) & URL extension
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($body);
            $contentType = strtolower($response->header('Content-Type') ?? '');

            $urlPath = parse_url($url, PHP_URL_PATH);
            $urlExt = strtolower(pathinfo($urlPath ?? '', PATHINFO_EXTENSION));

            $allowedMimes = [
                'image/webp'      => 'webp',
                'image/x-webp'    => 'webp',
                'image/jpeg'      => 'jpg',
                'image/jpg'       => 'jpg',
                'image/pjpeg'     => 'jpg',
                'image/png'       => 'png',
                'image/x-png'     => 'png',
                'image/gif'       => 'gif',
                'image/svg+xml'   => 'svg',
                'image/svg'       => 'svg',
                'image/avif'      => 'avif',
                'image/bmp'       => 'bmp',
                'image/x-ms-bmp'  => 'bmp',
            ];

            $extension = null;
            if (isset($allowedMimes[$mimeType])) {
                $extension = $allowedMimes[$mimeType];
            } elseif (str_contains($contentType, 'webp') || $urlExt === 'webp') {
                $extension = 'webp';
            } elseif (str_contains($contentType, 'png') || $urlExt === 'png') {
                $extension = 'png';
            } elseif (str_contains($contentType, 'jpeg') || str_contains($contentType, 'jpg') || in_array($urlExt, ['jpg', 'jpeg'])) {
                $extension = 'jpg';
            } elseif (str_contains($contentType, 'gif') || $urlExt === 'gif') {
                $extension = 'gif';
            } elseif (str_contains($contentType, 'svg') || $urlExt === 'svg') {
                $extension = 'svg';
            } elseif (str_contains($contentType, 'avif') || $urlExt === 'avif') {
                $extension = 'avif';
            }

            if (!$extension) {
                $msg = "Image skipped: unsupported or non-image format (mime: {$mimeType}) for URL: {$url}";
                Log::warning($msg);
                if ($companyName) {
                    $this->logWarning($msg, $companyName);
                }
                return null;
            }

            // 4. Store safely to configured disk
            $filename = 'businesses/' . $folder . '/' . Str::uuid() . '.' . $extension;
            Storage::disk($this->disk)->put($filename, $body);

            return $filename;
        } catch (\Throwable $e) {
            $msg = "Image processing/download failed for {$url}: " . $e->getMessage();
            Log::warning($msg);
            if ($companyName) {
                $this->logWarning($msg, $companyName);
            }
            return null;
        }
    }

    /**
     * Map standard header name to raw row value using fuzzy search across multiple keys.
     */
    private function getValueByHeaderName($row, string $header): ?string
    {
        $keyMap = [
            'business_id'     => ['business_id', 'business id', 'id', 'معرف الشركة'],
            'company_name'    => ['company_name', 'company name', 'اسم الشركة', 'name'],
            'main_category'   => ['main_category', 'main category', 'التصنيف الرئيسي', 'القسم الرئيسي', 'main_cat'],
            'sub_category'    => ['sub_category', 'sub category', 'التصنيف الفرعي', 'القسم الفرعي', 'sub_cat', 'subcategory'],
            'new_category_id' => ['new_category_id', 'new category id', 'category_id_v2', 'معرف التصنيف الجديد'],
            'category_slug'   => ['category_slug', 'full_slug', 'slug_category', 'رابط التصنيف', 'slug'],
            'category'        => ['category', 'التصنيف'],
            'city'            => ['city', 'المدينة'],
            'country'         => ['country', 'الدولة'],
            'description'     => ['description', 'الوصف'],
            'address'         => ['address', 'physical address', 'العنوان بالتفصيل', 'العنوان'],
            'phone'           => ['phone', 'الهاتف', 'phone_call', 'phone call'],
            'whatsapp'        => ['whatsapp', 'واتساب'],
            'website'         => ['website', 'website_url', 'website url', 'رابط الموقع'],
            'facebook'        => ['facebook', 'facebook_url'],
            'instagram'       => ['instagram', 'instagram_url'],
            'twitter'         => ['x_twitter', 'x twitter', 'twitter', 'x_url', 'x', 'تويتر'],
            'x_twitter'       => ['x_twitter', 'x twitter', 'twitter', 'x_url', 'x', 'تويتر'],
            'tiktok'          => ['tiktok', 'tiktok_url'],
            'linkedin'        => ['linkedin', 'linkedin_url'],
            'youtube'         => ['youtube', 'youtube_url'],
            'snapchat'        => ['snapchat', 'snapchat_url'],
            'business_logo'   => ['logo_url', 'logo url', 'business_logo', 'business logo', 'شعار الشركة', 'logo'],
            'logo_url'        => ['logo_url', 'logo url', 'business_logo', 'business logo', 'شعار الشركة', 'logo'],
            'business_cover'  => ['cover_url', 'cover url', 'business_cover', 'business cover', 'صورة الغلاف', 'cover'],
            'cover_url'       => ['cover_url', 'cover url', 'business_cover', 'business cover', 'صورة الغلاف', 'cover'],
        ];

        if (isset($keyMap[$header])) {
            return $this->getValueByFuzzyKey($row, $keyMap[$header]);
        }

        if (str_starts_with($header, 'image_')) {
            $num = str_replace('image_', '', $header);
            return $this->getValueByFuzzyKey($row, [
                "image_{$num}", "image {$num}", "image{$num}",
                "صورة_{$num}", "صورة {$num}", "صورة{$num}",
            ]);
        }

        return null;
    }

    private function getValueByFuzzyKey($row, array $possibleKeys): ?string
    {
        $rowArray = $row->toArray();

        // Pass 1: EXACT match first (highest priority)
        foreach ($possibleKeys as $key) {
            $normalizedSearchKey = $this->normalizer->normalize($key);
            foreach ($rowArray as $rowKey => $value) {
                $normalizedRowKey = $this->normalizer->normalize((string) $rowKey);
                if ($normalizedRowKey === $normalizedSearchKey) {
                    $cleanVal = trim((string) $value);
                    if ($cleanVal === 'غير متوفر' || $cleanVal === 'N/A' || $cleanVal === 'null' || $cleanVal === '') {
                        return null;
                    }
                    return $cleanVal;
                }
            }
        }

        // Pass 2: SUBSTRING match (only for specific, non-colliding keys)
        foreach ($possibleKeys as $key) {
            $normalizedSearchKey = $this->normalizer->normalize($key);
            // Skip short keys like 'id' or 'x' from substring matching to prevent false positives
            if (mb_strlen($normalizedSearchKey) <= 2) {
                continue;
            }

            foreach ($rowArray as $rowKey => $value) {
                $normalizedRowKey = $this->normalizer->normalize((string) $rowKey);
                if (str_contains($normalizedRowKey, $normalizedSearchKey)) {
                    // Prevent 'category' search key from matching 'new_category_id', 'category_slug', 'main_category', or 'sub_category'
                    if ($normalizedSearchKey === 'category' || $normalizedSearchKey === 'التصنيف') {
                        if (
                            str_contains($normalizedRowKey, 'new') ||
                            str_contains($normalizedRowKey, 'جديد') ||
                            str_contains($normalizedRowKey, 'slug') ||
                            str_contains($normalizedRowKey, 'رابط') ||
                            str_contains($normalizedRowKey, 'id') ||
                            str_contains($normalizedRowKey, 'main') ||
                            str_contains($normalizedRowKey, 'sub') ||
                            str_contains($normalizedRowKey, 'فرعي') ||
                            str_contains($normalizedRowKey, 'رئيسي')
                        ) {
                            continue;
                        }
                    }

                    $cleanVal = trim((string) $value);
                    if ($cleanVal === 'غير متوفر' || $cleanVal === 'N/A' || $cleanVal === 'null' || $cleanVal === '') {
                        return null;
                    }
                    return $cleanVal;
                }
            }
        }

        return null;
    }

    /**
     * Record a rejected/skipped row into both the text audit log and the rejected CSV.
     * Hardened against CSV / Formula Injection and Log Forging.
     */
    private function recordRejectedRow($row, string $reason, string $errorCode = 'ERR_REJECTED'): void
    {
        try {
            Cache::increment("import_stats_{$this->importId}_skipped");
            $this->chunkSkipped++;

            // Sanitize strings against Log Forging (strip CR/LF)
            $companyName = $this->cleanString($this->getValueByHeaderName($row, 'company_name')) ?: 'Unknown';
            $safeCompany = preg_replace('/[\r\n]+/', ' ', $companyName);
            $safeReason = preg_replace('/[\r\n]+/', ' ', $reason);

            if ($this->batchId) {
                // 1. Error Log
                $logPath = "imports/errors/batch_{$this->batchId}.txt";
                $logEntry = "[REJECTED] Code: {$errorCode} | Company: '{$safeCompany}' | Reason: {$safeReason}\n";
                Storage::disk('local')->append($logPath, $logEntry);

                // 2. Rejected CSV Output (sanitized against CSV formula injection)
                $rejectedPath = "imports/rejected/batch_{$this->batchId}_rejected.csv";
                if (!Storage::disk('local')->exists($rejectedPath)) {
                    $stream = fopen('php://temp', 'r+');
                    fwrite($stream, "\xEF\xBB\xBF"); // UTF-8 BOM for Arabic support
                    $headersWithReason = array_merge(self::HEADERS, ['rejection_reason']);
                    fputcsv($stream, $headersWithReason);
                    rewind($stream);
                    Storage::disk('local')->put($rejectedPath, stream_get_contents($stream));
                    fclose($stream);
                }

                $stream = fopen('php://temp', 'r+');
                $csvRow = [];
                foreach (self::HEADERS as $header) {
                    $val = $this->getValueByHeaderName($row, $header);
                    $csvRow[] = $this->sanitizeCsvCell($val);
                }
                $csvRow[] = $this->sanitizeCsvCell($reason);
                fputcsv($stream, $csvRow);
                rewind($stream);
                Storage::disk('local')->append($rejectedPath, stream_get_contents($stream));
                fclose($stream);
            } else {
                file_put_contents(storage_path('logs/import_errors.txt'), "Rejected [{$errorCode}]: {$safeReason}\n", FILE_APPEND);
            }
        } catch (\Throwable $e) {
            Log::error("Failed to record rejected row: " . $e->getMessage(), ['exception' => $e]);
        }
    }

    private function logWarning(string $warning, string $companyName): void
    {
        try {
            if ($this->batchId) {
                $logPath = "imports/errors/batch_{$this->batchId}.txt";
                $safeCompany = preg_replace('/[\r\n]+/', ' ', $companyName);
                $safeWarning = preg_replace('/[\r\n]+/', ' ', $warning);
                $logEntry = "[WARNING] Company: '{$safeCompany}' | Notice: {$safeWarning}\n";
                Storage::disk('local')->append($logPath, $logEntry);
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to append warning to batch error log: " . $e->getMessage());
        }
    }

    /**
     * Clean and normalize unicode strings: strip zero-width characters, BOM, and normalize whitespace.
     */
    private function cleanString(?string $str): ?string
    {
        if ($str === null) {
            return null;
        }

        // Strip zero-width spaces, joiners, non-joiners, byte-order marks
        $clean = preg_replace('/[\x{200B}\x{200C}\x{200D}\x{FEFF}]/u', '', $str);
        // Replace non-breaking and unusual spaces with standard space
        $clean = preg_replace('/[\x{00A0}\x{202F}\x{3000}]/u', ' ', $clean);
        // Collapse multiple whitespace
        $clean = preg_replace('/\s+/u', ' ', $clean);

        return trim($clean);
    }

    /**
     * Sanitize cell value against CSV / Excel Formula Injection (OWASP standard).
     * If cell starts with '=', '+', '-', '@', '\t', or '\r', prepend an apostrophe (').
     */
    private function sanitizeCsvCell(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        // Strip non-printable control characters
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

        // Neutralize formulas
        if (preg_match('/^[=+\-@\t\r]/', $value)) {
            return "'" . $value;
        }

        return $value;
    }

    /**
     * Defense-in-depth against SSRF (Server-Side Request Forgery).
     * Validates that the target URL resolves exclusively to public, routable IP addresses.
     * Blocks loopback (127.0.0.1), private RFC1918 subnets, cloud metadata endpoints, and non-HTTP protocols.
     */
    private function isSafePublicUrl(string $url): bool
    {
        $parsed = parse_url($url);
        if (!$parsed || empty($parsed['host']) || empty($parsed['scheme'])) {
            return false;
        }

        $scheme = strtolower($parsed['scheme']);
        if ($scheme !== 'http' && $scheme !== 'https') {
            return false;
        }

        $host = strtolower($parsed['host']);

        // 1. Block dangerous hostnames & cloud metadata services
        $blockedHosts = [
            'localhost',
            '127.0.0.1',
            '0.0.0.0',
            '169.254.169.254',          // AWS / OpenStack metadata
            'metadata.google.internal',    // GCP metadata
            'metadata.goog',
            'instance-data',
        ];
        if (in_array($host, $blockedHosts, true) || str_ends_with($host, '.localhost')) {
            return false;
        }

        // 2. Resolve DNS host to IPs
        $ips = @gethostbynamel($host);
        if (!$ips || !is_array($ips)) {
            return false;
        }

        // 3. Ensure EVERY resolved IP is public and not private / reserved / loopback / link-local
        foreach ($ips as $ip) {
            $isPublic = filter_var(
                $ip,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            );
            if (!$isPublic) {
                Log::warning("SSRF Protection: Blocked attempt to query private/reserved IP [{$ip}] via host [{$host}]");
                return false;
            }
        }

        return true;
    }

    public function chunkSize(): int
    {
        return 25;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                $stats = [
                    'total'          => Cache::get("import_stats_{$this->importId}_total", 0),
                    'imported'       => Cache::get("import_stats_{$this->importId}_imported", 0),
                    'updated'        => Cache::get("import_stats_{$this->importId}_updated", 0),
                    'skipped'        => Cache::get("import_stats_{$this->importId}_skipped", 0),
                    'flagged'        => Cache::get("import_stats_{$this->importId}_flagged", 0),
                    'new_categories' => Cache::get("import_stats_{$this->importId}_new_categories", 0),
                    'new_cities'     => Cache::get("import_stats_{$this->importId}_new_cities", 0),
                ];

                $errorPath = "imports/errors/batch_{$this->batchId}.txt";
                $rejectedPath = "imports/rejected/batch_{$this->batchId}_rejected.csv";
                $rejectedExists = Storage::disk('local')->exists($rejectedPath);

                $mode = $this->isDryRun ? "DRY RUN / PREVIEW ONLY" : "REAL IMPORT";
                $summary = "\n================================================================================\n" .
                           "BATCH AUDIT REPORT SUMMARY ({$mode})\n" .
                           "Total Rows: {$stats['total']} | Created: {$stats['imported']} | Updated: {$stats['updated']} | Flagged for Review: {$stats['flagged']} | Skipped/Rejected: {$stats['skipped']}\n" .
                           "================================================================================\n";
                Storage::disk('local')->append($errorPath, $summary);

                if ($this->batchId) {
                    BusinessImportBatch::where('id', $this->batchId)->update([
                        'status'             => BusinessImportBatch::STATUS_COMPLETED,
                        'total_rows'         => $stats['total'],
                        'imported_rows'      => $stats['imported'],
                        'updated_rows'       => $stats['updated'],
                        'skipped_rows'       => $stats['skipped'],
                        'error_log_path'     => $errorPath,
                        'rejected_file_path' => $rejectedExists ? $rejectedPath : null,
                    ]);
                }

                try {
                    Mail::to($this->adminEmail)
                        ->cc(['mazenramadan320@gmail.com', 'ahmed.helllalll@gmail.com', 'Tarekdeyab0@gmail.com'])
                        ->send(new ImportCompleted($stats));
                } catch (\Throwable $mailEx) {
                    Log::warning("Failed to send import completion mail: " . $mailEx->getMessage());
                }

                // Cleanup Cache
                Cache::forget("import_stats_{$this->importId}_total");
                Cache::forget("import_stats_{$this->importId}_imported");
                Cache::forget("import_stats_{$this->importId}_updated");
                Cache::forget("import_stats_{$this->importId}_skipped");
                Cache::forget("import_stats_{$this->importId}_flagged");
                Cache::forget("import_stats_{$this->importId}_new_categories");
                Cache::forget("import_stats_{$this->importId}_new_cities");

                // Trigger next batch in line
                try {
                    app(BusinessImportQueueService::class)->dispatchNext();
                } catch (\Throwable $dispatchEx) {
                    Log::error("Failed to dispatch next batch after batch #{$this->batchId} completion: " . $dispatchEx->getMessage());
                }
            },
            ImportFailed::class => function (ImportFailed $event) {
                if ($this->batchId) {
                    BusinessImportBatch::where('id', $this->batchId)->update(['status' => BusinessImportBatch::STATUS_FAILED]);
                }
                Log::error('Excel Import Completely Failed', ['exception' => $event->getException()]);

                // Trigger next batch in line
                try {
                    app(BusinessImportQueueService::class)->dispatchNext();
                } catch (\Throwable $dispatchEx) {
                    Log::error("Failed to dispatch next batch after batch #{$this->batchId} failure: " . $dispatchEx->getMessage());
                }
            },
        ];
    }
}
