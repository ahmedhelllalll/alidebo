<?php

namespace App\Imports;

use App\Models\BusinessProfile;
use App\Models\BusinessMedia;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\BusinessImportBatch;
use App\Services\DataNormalizationService;
use App\Mail\ImportCompleted;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Support\Facades\Log;

class BusinessProfilesImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents, SkipsEmptyRows
{
    private $adminEmail;
    private $adminId;
    private $importId;
    private $disk;
    private $normalizer;
    private $batchId;

    public function __construct($adminEmail, $adminId = null, $batchId = null)
    {
        \Maatwebsite\Excel\Imports\HeadingRowFormatter::default('none');

        $this->adminEmail = $adminEmail;
        // Fallback to finding user by email if adminId wasn't passed by older queued jobs
        $this->adminId = $adminId ?? (\App\Models\User::where('email', $adminEmail)->first()->id ?? 1);
        $this->importId = Str::uuid()->toString();
        $this->normalizer = new DataNormalizationService();
        $this->disk = config('filesystems.default');
        $this->batchId = $batchId;

        // Initialize stats
        Cache::put("import_stats_{$this->importId}_total", 0, 3600);
        Cache::put("import_stats_{$this->importId}_imported", 0, 3600);
        Cache::put("import_stats_{$this->importId}_skipped", 0, 3600);
        Cache::put("import_stats_{$this->importId}_new_categories", 0, 3600);
        Cache::put("import_stats_{$this->importId}_new_cities", 0, 3600);
    }

    public function collection(Collection $rows)
    {
        $categories = Category::all();
        $cities = City::all();
        $countries = Country::all();

        $firstRow = $rows->first();
        if ($firstRow) {
            file_put_contents(storage_path('logs/import_debug.txt'), print_r($firstRow->toArray(), true) . "\n", FILE_APPEND);
        }

        foreach ($rows as $row) {
            Cache::increment("import_stats_{$this->importId}_total");

            try {
                DB::transaction(function () use ($row, &$categories, &$cities, &$countries) {
                    // Extract data (handling potential Arabic or English headers by checking values or assuming standard order/keys)
                    // We will use fuzzy keys in case headers differ slightly
                    $companyName = $this->getValueByFuzzyKey($row, ['company name', 'اسم الشركة', 'name']);
                    if (empty($companyName)) {
                        // FORCE SAVE: If the name is empty (due to mismatched columns), we still save it
                        // to see exactly what the system is reading from the file.
                        $companyName = 'Unknown Name - ' . Str::random(5);
                    }

                    $categoryName = $this->getValueByFuzzyKey($row, ['category', 'التصنيف']);
                    $categoryId = $this->resolveCategory($categoryName, $categories);

                    $cityName = $this->getValueByFuzzyKey($row, ['city', 'المدينة']);
                    $countryName = $this->getValueByFuzzyKey($row, ['country', 'الدولة']);
                    $cityId = $this->resolveCity($cityName, $countryName, $cities, $countries);

                    $contactMethods = [
                        'phone' => $this->getValueByFuzzyKey($row, ['phone', 'الهاتف']),
                        'whatsapp' => $this->getValueByFuzzyKey($row, ['whatsapp', 'واتساب']),
                        'website' => $this->getValueByFuzzyKey($row, ['website url', 'رابط الموقع', 'website']),
                        'facebook' => $this->getValueByFuzzyKey($row, ['facebook']),
                        'instagram' => $this->getValueByFuzzyKey($row, ['instagram']),
                        'twitter' => $this->getValueByFuzzyKey($row, ['twitter']),
                        'tiktok' => $this->getValueByFuzzyKey($row, ['tiktok']),
                        'linkedin' => $this->getValueByFuzzyKey($row, ['linkedin']),
                        'youtube' => $this->getValueByFuzzyKey($row, ['youtube']),
                        'snapchat' => $this->getValueByFuzzyKey($row, ['snapchat']),
                    ];

                    $logoUrl = $this->getValueByFuzzyKey($row, ['business logo', 'شعار الشركة', 'logo']);
                    $coverUrl = $this->getValueByFuzzyKey($row, ['business cover', 'صورة الغلاف', 'cover']);

                    $profile = BusinessProfile::create([
                        'user_id' => $this->adminId,
                        'name' => $companyName,
                        'slug' => Str::slug($companyName . '-' . Str::random(5)),
                        'description' => $this->getValueByFuzzyKey($row, ['description', 'الوصف']),
                        'address' => $this->getValueByFuzzyKey($row, ['physical address', 'العنوان بالتفصيل', 'address']),
                        'category_id' => $categoryId,
                        'city_id' => $cityId,
                        'contact_methods' => array_filter($contactMethods),
                        'status' => 'approved', // Auto-approve imported businesses
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
                        $imgUrl = $this->getValueByFuzzyKey($row, ["image {$i}"]);
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

                    Cache::increment("import_stats_{$this->importId}_imported");
                    if ($this->batchId) {
                        BusinessImportBatch::where('id', $this->batchId)->increment('imported_rows');
                    }
                });
            } catch (\Exception $e) {
                if ($this->batchId) {
                    $errorMsg = "Row Error: " . $e->getMessage() . " | Data: " . json_encode($row->toArray(), JSON_UNESCAPED_UNICODE);
                    Storage::disk('local')->append("imports/errors/batch_{$this->batchId}.txt", $errorMsg);
                    BusinessImportBatch::where('id', $this->batchId)->increment('skipped_rows');
                } else {
                    file_put_contents(storage_path('logs/import_errors.txt'), "Row Error: " . $e->getMessage() . "\n", FILE_APPEND);
                }
                \Log::error("Excel Import Row Failed: " . $e->getMessage());
                Cache::increment("import_stats_{$this->importId}_skipped");
            }
        }
    }

    private function resolveCategory($name, &$categories)
    {
        if (empty($name))
            return null;

        $match = $this->normalizer->match($name, $categories, ['name_ar', 'name_en', 'name_de', 'name_es', 'name_tr', 'name_zh']);
        if ($match) {
            return $match->id;
        }

        // Auto-create category
        $category = Category::create([
            'name_en' => $name,
            'name_ar' => $name,
            'slug' => Str::slug($name . '-' . Str::random(4)),
            'status' => 'pending', // Pending review by admin
            'disk' => $this->disk,
        ]);
        $categories->push($category);
        Cache::increment("import_stats_{$this->importId}_new_categories");
        return $category->id;
    }

    private function resolveCity($cityName, $countryName, &$cities, &$countries)
    {
        if (empty($cityName))
            return null;

        $cityMatch = $this->normalizer->match($cityName, $cities, ['name_ar', 'name_en', 'name_de', 'name_es', 'name_tr', 'name_zh']);
        if ($cityMatch) {
            return $cityMatch->id;
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
        try {
            $response = Http::timeout(10)->get($url);
            if ($response->successful()) {
                $extension = 'jpg'; // Default, or parse from content-type
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
            \Log::warning("Failed to download image during import: {$url} - {$e->getMessage()}");
        }
        return null;
    }

    private function getValueByFuzzyKey($row, array $possibleKeys)
    {
        $rowArray = $row->toArray();
        foreach ($possibleKeys as $key) {
            $normalizedSearchKey = $this->normalizer->normalize($key);
            foreach ($rowArray as $rowKey => $value) {
                $normalizedRowKey = $this->normalizer->normalize((string) $rowKey);
                // We use str_contains to match combined headers like "اسم الشركة / Company Name"
                if (str_contains($normalizedRowKey, $normalizedSearchKey)) {
                    $cleanVal = trim((string) $value);
                    if ($cleanVal === 'غير متوفر' || $cleanVal === 'N/A' || $cleanVal === 'null') {
                        return null;
                    }
                    return $cleanVal;
                }
            }
        }
        return null;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                $stats = [
                    'total' => Cache::get("import_stats_{$this->importId}_total", 0),
                    'imported' => Cache::get("import_stats_{$this->importId}_imported", 0),
                    'skipped' => Cache::get("import_stats_{$this->importId}_skipped", 0),
                    'new_categories' => Cache::get("import_stats_{$this->importId}_new_categories", 0),
                    'new_cities' => Cache::get("import_stats_{$this->importId}_new_cities", 0),
                ];

                Mail::to($this->adminEmail)
                    ->cc(['mazenramadan320@gmail.com', 'ahmed.helllalll@gmail.com', 'Tarekdeyab0@gmail.com'])
                    ->send(new ImportCompleted($stats));

                if ($this->batchId) {
                    BusinessImportBatch::where('id', $this->batchId)->update([
                        'status' => 'completed',
                        'error_log_path' => "imports/errors/batch_{$this->batchId}.txt",
                    ]);
                }

                // Cleanup
                Cache::forget("import_stats_{$this->importId}_total");
                Cache::forget("import_stats_{$this->importId}_imported");
                Cache::forget("import_stats_{$this->importId}_skipped");
                Cache::forget("import_stats_{$this->importId}_new_categories");
                Cache::forget("import_stats_{$this->importId}_new_cities");
            },
            ImportFailed::class => function (ImportFailed $event) {
                if ($this->batchId) {
                    BusinessImportBatch::where('id', $this->batchId)->update(['status' => 'failed']);
                }
                \Log::error('Excel Import Completely Failed', ['exception' => $event->getException()]);
            },
        ];
    }
}
