<?php

namespace App\Imports;

use App\Models\BusinessProfile;
use App\Models\BusinessMedia;
use App\Models\Category;
use App\Models\NewCategory;
use App\Models\City;
use App\Models\Country;
use App\Models\BusinessImportBatch;
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

    private $adminEmail;
    private $adminId;
    private $importId;
    private $disk;
    private $normalizer;
    private $batchId;
    private $isDryRun;
    private $targetSheet;

    public $timeout = 1800; // 30 minutes to prevent chunk timeout

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

        // Initialize stats
        Cache::put("import_stats_{$this->importId}_total", 0, 3600);
        Cache::put("import_stats_{$this->importId}_imported", 0, 3600);
        Cache::put("import_stats_{$this->importId}_updated", 0, 3600);
        Cache::put("import_stats_{$this->importId}_skipped", 0, 3600);
        Cache::put("import_stats_{$this->importId}_flagged", 0, 3600);
        Cache::put("import_stats_{$this->importId}_new_categories", 0, 3600);
        Cache::put("import_stats_{$this->importId}_new_cities", 0, 3600);
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

        foreach ($rows as $row) {
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

            try {
                DB::transaction(function () use ($row, &$newCats, &$legacyCats, &$cities, &$countries) {
                    // 1. Validate Company Name (Mandatory)
                    $companyName = $this->getValueByHeaderName($row, 'company_name');
                    if (empty($companyName) || trim($companyName) === '') {
                        $this->recordRejectedRow($row, "Company name is missing/blank. Row rejected.", 'ERR_EMPTY_NAME');
                        return;
                    }
                    $companyName = trim($companyName);

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
                            // Logo re-download rule: only update if valid URL and changed
                            if ($logoUrl && filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                                if ($logoUrl !== $existing->logo_url && $logoUrl !== $existing->logo) {
                                    $newLogo = $this->downloadAndStoreImage($logoUrl, 'logos');
                                    if ($newLogo) {
                                        if ($existing->logo) {
                                            Storage::disk($existing->disk ?: $this->disk)->delete($existing->logo);
                                        }
                                        $updateData['logo'] = $newLogo;
                                    }
                                }
                            }

                            // Cover re-download rule: only update if valid URL and changed
                            if ($coverUrl && filter_var($coverUrl, FILTER_VALIDATE_URL)) {
                                if ($coverUrl !== $existing->cover_url && $coverUrl !== $existing->cover) {
                                    $newCover = $this->downloadAndStoreImage($coverUrl, 'covers');
                                    if ($newCover) {
                                        if ($existing->cover) {
                                            Storage::disk($existing->disk ?: $this->disk)->delete($existing->cover);
                                        }
                                        $updateData['cover'] = $newCover;
                                    }
                                }
                            }

                            // Gallery images update
                            for ($i = 1; $i <= 12; $i++) {
                                $imgUrl = $this->getValueByHeaderName($row, "image_{$i}");
                                if ($imgUrl && filter_var($imgUrl, FILTER_VALIDATE_URL)) {
                                    $existingMedia = BusinessMedia::where('business_profile_id', $existing->id)
                                        ->where('order', $i)
                                        ->first();
                                    if ($existingMedia) {
                                        Storage::disk($existingMedia->disk ?: $this->disk)->delete($existingMedia->file_path);
                                        $existingMedia->delete();
                                    }
                                    $path = $this->downloadAndStoreImage($imgUrl, 'gallery');
                                    if ($path) {
                                        BusinessMedia::create([
                                            'business_profile_id' => $existing->id,
                                            'file_path' => $path,
                                            'type' => 'image',
                                            'order' => $i,
                                            'disk' => $this->disk,
                                        ]);
                                    }
                                }
                            }

                            $existing->update($updateData);
                        }

                        Cache::increment("import_stats_{$this->importId}_updated");
                        if ($this->batchId) {
                            BusinessImportBatch::where('id', $this->batchId)->increment('updated_rows');
                        }
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

                    // Check 2: Phone
                    $phone = $contactMethods['phone'] ?? null;
                    if (!empty($phone)) {
                        $dupPhone = BusinessProfile::where('contact_methods->phone', $phone)->first();
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
                        $profile = BusinessProfile::create([
                            'user_id' => $this->adminId,
                            'name' => $companyName,
                            'slug' => Str::slug($companyName . '-' . Str::random(5)),
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
                            $profile->logo = $this->downloadAndStoreImage($logoUrl, 'logos');
                        }
                        if ($coverUrl && filter_var($coverUrl, FILTER_VALIDATE_URL)) {
                            $profile->cover = $this->downloadAndStoreImage($coverUrl, 'covers');
                        }
                        $profile->save();

                        // Process Gallery Images (Image 1 to Image 12)
                        for ($i = 1; $i <= 12; $i++) {
                            $imgUrl = $this->getValueByHeaderName($row, "image_{$i}");
                            if ($imgUrl && filter_var($imgUrl, FILTER_VALIDATE_URL)) {
                                $path = $this->downloadAndStoreImage($imgUrl, 'gallery');
                                if ($path) {
                                    BusinessMedia::create([
                                        'business_profile_id' => $profile->id,
                                        'file_path' => $path,
                                        'type' => 'image',
                                        'order' => $i,
                                        'disk' => $this->disk,
                                    ]);
                                }
                            }
                        }
                    }

                    Cache::increment("import_stats_{$this->importId}_imported");
                    if ($this->batchId) {
                        BusinessImportBatch::where('id', $this->batchId)->increment('imported_rows');
                    }
                });
            } catch (\Exception $e) {
                $this->recordRejectedRow($row, "System Exception: " . $e->getMessage(), 'ERR_SYSTEM_EXCEPTION');
                Log::error("Excel Import Row Failed: " . $e->getMessage(), ['exception' => $e]);
            }
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
        if (empty($cityName))
            return null;

        $cityMatch = $this->normalizer->match($cityName, $cities, ['name_ar', 'name_en', 'name_de', 'name_es', 'name_tr', 'name_zh']);
        if ($cityMatch) {
            return $cityMatch->id;
        }

        if ($this->isDryRun) {
            return null;
        }

        // We need a country to create a city
        $countryId = null;
        if (!empty($countryName)) {
            $countryMatch = $this->normalizer->match($countryName, $countries, ['name_ar', 'name_en', 'name_de', 'name_es', 'name_tr', 'name_zh']);
            if ($countryMatch) {
                $countryId = $countryMatch->id;
            } else {
                $country = Country::create([
                    'name_en' => $countryName,
                    'name_ar' => $countryName,
                    'code' => strtoupper(substr(Str::slug($countryName), 0, 2)),
                    'status' => 'pending',
                ]);
                $countries->push($country);
                $countryId = $country->id;
            }
        }

        $city = City::create([
            'name_en' => $cityName,
            'name_ar' => $cityName,
            'country_id' => $countryId,
            'status' => 'pending',
        ]);
        $cities->push($city);
        Cache::increment("import_stats_{$this->importId}_new_cities");
        return $city->id;
    }

    private function downloadAndStoreImage($url, $folder)
    {
        if ($this->isDryRun) {
            return null;
        }

        try {
            $response = Http::timeout(5)->get($url);
            if ($response->successful()) {
                $extension = 'jpg';
                $contentType = $response->header('Content-Type');
                if (str_contains($contentType, 'png'))
                    $extension = 'png';
                elseif (str_contains($contentType, 'webp'))
                    $extension = 'webp';

                $filename = 'businesses/' . $folder . '/' . Str::uuid() . '.' . $extension;
                Storage::disk($this->disk)->put($filename, $response->body());
                return $filename;
            }
        } catch (\Exception $e) {
            Log::warning("Failed to download image during import: {$url} - {$e->getMessage()}");
        }
        return null;
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
            return $this->getValueByFuzzyKey($row, ["image_{$num}", "image {$num}", "image{$num}"]);
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
     */
    private function recordRejectedRow($row, string $reason, string $errorCode = 'ERR_REJECTED'): void
    {
        Cache::increment("import_stats_{$this->importId}_skipped");

        if ($this->batchId) {
            BusinessImportBatch::where('id', $this->batchId)->increment('skipped_rows');

            // 1. Error Log
            $logPath = "imports/errors/batch_{$this->batchId}.txt";
            $companyName = $this->getValueByHeaderName($row, 'company_name') ?: 'Unknown';
            $logEntry = "[REJECTED] Code: {$errorCode} | Company: '{$companyName}' | Reason: {$reason}\n";
            Storage::disk('local')->append($logPath, $logEntry);

            // 2. Rejected CSV Output
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
                $csvRow[] = $this->getValueByHeaderName($row, $header);
            }
            $csvRow[] = $reason;
            fputcsv($stream, $csvRow);
            rewind($stream);
            Storage::disk('local')->append($rejectedPath, stream_get_contents($stream));
            fclose($stream);
        } else {
            file_put_contents(storage_path('logs/import_errors.txt'), "Rejected [{$errorCode}]: {$reason}\n", FILE_APPEND);
        }
    }

    private function logWarning(string $warning, string $companyName): void
    {
        if ($this->batchId) {
            $logPath = "imports/errors/batch_{$this->batchId}.txt";
            $logEntry = "[WARNING] Company: '{$companyName}' | Notice: {$warning}\n";
            Storage::disk('local')->append($logPath, $logEntry);
        }
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
                        'status'             => 'completed',
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
            },
            ImportFailed::class => function (ImportFailed $event) {
                if ($this->batchId) {
                    BusinessImportBatch::where('id', $this->batchId)->update(['status' => 'failed']);
                }
                Log::error('Excel Import Completely Failed', ['exception' => $event->getException()]);
            },
        ];
    }
}
