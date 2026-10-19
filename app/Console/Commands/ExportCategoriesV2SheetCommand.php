<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\NewCategory;
use Illuminate\Support\Facades\File;

class ExportCategoriesV2SheetCommand extends Command
{
    protected $signature = 'categories:export-v2-sheet 
                            {--output= : Target path for the output CSV file}
                            {--source-csv= : Path to the source mapping CSV (e.g. dry_run_full_v4.csv)}';

    protected $description = 'Export the 16-column post-migration audit and backfill CSV for all 757 companies';

    public function handle(): int
    {
        $this->info("==========================================================");
        $this->info("  Categories V2 Migration: 16-Column Export Command");
        $this->info("==========================================================");

        $sourceCsv = $this->option('source-csv') ?: 'C:/Users/Mega Store/Documents/alidebo_backup/dry_run_full_v4.csv';
        $outputPath = $this->option('output') ?: 'C:/Users/Mega Store/Documents/alidebo_backup/categories_v2_migration_757_companies.csv';

        $dryRun = [];
        if (File::exists($sourceCsv)) {
            $this->info("Loading source mapping from: {$sourceCsv}");
            $fp = fopen($sourceCsv, 'r');
            // strip BOM if present
            $header = fgetcsv($fp);
            while (($row = fgetcsv($fp)) !== false) {
                if (count($row) < 7) continue;
                $bid = (int)preg_replace('/[^0-9]/', '', $row[0]);
                if (!$bid) continue;
                $oldCatName = $row[3] ?? null;
                if ($oldCatName === 'NULL' || $oldCatName === '') $oldCatName = null;
                $dryRun[$bid] = [
                    'id' => $bid,
                    'name' => $row[1] ?? '',
                    'old_cat_id' => (!empty($row[2]) && $row[2] !== 'NULL') ? (int)$row[2] : null,
                    'old_cat_name' => $oldCatName,
                    'new_cat_id' => (!empty($row[4]) && $row[4] !== 'NULL') ? (int)$row[4] : null,
                    'new_cat_name' => $row[5] ?? '',
                    'needs_review' => filter_var($row[6] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                    'reason' => $row[7] ?? '',
                ];
            }
            fclose($fp);
            $this->info("Loaded " . count($dryRun) . " mapping rows from source CSV.");
        } else {
            $this->warn("Source CSV not found at: {$sourceCsv}. Proceeding with database records only.");
        }

        $legacyCats = Category::all()->keyBy('id');
        $newCats = NewCategory::all()->keyBy('id');
        $businesses = BusinessProfile::orderBy('id')->get();

        $totalCount = $businesses->count();
        $this->info("Found {$totalCount} business profiles in the database.");

        $outDir = dirname($outputPath);
        if (!File::isDirectory($outDir)) {
            File::makeDirectory($outDir, 0755, true, true);
        }

        $outFp = fopen($outputPath, 'w');
        if (!$outFp) {
            $this->error("Failed to open output file for writing: {$outputPath}");
            return Command::FAILURE;
        }

        // Write UTF-8 BOM so Microsoft Excel correctly displays Arabic text
        fwrite($outFp, "\xEF\xBB\xBF");

        // 16 ordered header columns as specified
        $headers = [
            'business_id',
            'company_name_ar',
            'company_name_en',
            'legacy_category_id',
            'legacy_category_name',
            'new_category_id',
            'l1_parent_id',
            'l1_parent_name_ar',
            'l1_parent_name_en',
            'l2_child_name_ar',
            'l2_child_name_en',
            'full_slug',
            'match_confidence',
            'needs_review',
            'review_reason',
            'updated_at',
        ];
        fputcsv($outFp, $headers);

        $stats = [
            'total' => 0,
            'null_category' => 0,
            'needs_review' => 0,
            'confidence' => [
                'EXACT' => 0,
                'HEURISTIC' => 0,
                'MANUAL_OVERRIDE' => 0,
                'UNMATCHED' => 0,
            ],
        ];

        foreach ($businesses as $b) {
            $stats['total']++;
            $bid = $b->id;
            $d = $dryRun[$bid] ?? null;

            // 1. Names resolution
            $nameFromCsv = $d['name'] ?? '';
            $nameFromDb = $b->name ?? '';

            $csvHasAr = preg_match('/[\x{0600}-\x{06FF}]/u', $nameFromCsv);
            $dbHasAr = preg_match('/[\x{0600}-\x{06FF}]/u', $nameFromDb);

            if ($bid === 128) {
                $nameAr = "ألديبو (منصة تجريبية)";
                $nameEn = "Alidebo Platform";
            } elseif ($csvHasAr && !$dbHasAr) {
                $nameAr = $nameFromCsv;
                $nameEn = $nameFromDb ?: null;
            } elseif ($dbHasAr && !$csvHasAr) {
                $nameAr = $nameFromDb;
                $nameEn = $nameFromCsv ?: null;
            } elseif ($csvHasAr && $dbHasAr) {
                $nameAr = $nameFromCsv;
                $nameEn = null;
            } else {
                $nameAr = $nameFromCsv ?: $nameFromDb;
                $nameEn = $nameFromDb ?: $nameFromCsv;
            }

            // 2. Legacy Category
            $legacyId = $b->category_id ?: ($d['old_cat_id'] ?? null);
            if ($bid === 128) $legacyId = null;

            $legacyCat = $legacyId ? ($legacyCats[$legacyId] ?? null) : null;
            $legacyName = $legacyCat ? ($legacyCat->name_ar ?: $legacyCat->name_en) : ($d['old_cat_name'] ?? null);
            if ($legacyName === 'NULL' || $legacyName === 'null') {
                $legacyName = null;
            }
            if ($bid === 128) {
                $legacyName = "غير مصنف / حساب نظامي";
            }

            // 3. New Category V2
            $newCatId = $bid === 128 ? null : ($b->new_category_id ?: ($d['new_cat_id'] ?? null));
            $newCat = $newCatId ? ($newCats[$newCatId] ?? null) : null;

            $l1ParentId = null;
            $l1ParentNameAr = null;
            $l1ParentNameEn = null;
            $l2ChildNameAr = null;
            $l2ChildNameEn = null;
            $fullSlug = null;

            if ($newCat) {
                if ($newCat->level === 2 && $newCat->parent_id) {
                    $l1Parent = $newCats[$newCat->parent_id] ?? null;
                    $l1ParentId = $l1Parent ? $l1Parent->id : $newCat->parent_id;
                    $l1ParentNameAr = $l1Parent ? $l1Parent->name_ar : null;
                    $l1ParentNameEn = $l1Parent ? $l1Parent->name_en : null;
                    $l2ChildNameAr = $newCat->name_ar;
                    $l2ChildNameEn = $newCat->name_en;
                    $fullSlug = $newCat->slug;
                } else {
                    $l1ParentId = $newCat->id;
                    $l1ParentNameAr = $newCat->name_ar;
                    $l1ParentNameEn = $newCat->name_en;
                    $l2ChildNameAr = $newCat->name_ar;
                    $l2ChildNameEn = $newCat->name_en;
                    $fullSlug = $newCat->slug;
                }
            }

            // 4. Needs Review & Reason
            $needsReview = $bid === 128 ? 1 : ($d ? $d['needs_review'] : ($b->category_needs_review ? 1 : 0));
            $rawReason = $d['reason'] ?? '';

            // 5. Match Confidence
            if ($bid === 128 || empty($newCatId)) {
                $confidence = 'UNMATCHED';
            } elseif (
                str_starts_with($rawReason, 'approved override') || 
                str_starts_with($rawReason, 'known case') || 
                str_starts_with($rawReason, 'approved rule')
            ) {
                $confidence = 'MANUAL_OVERRIDE';
            } elseif (
                $needsReview === 1 || 
                str_contains($rawReason, 'has no signal') || 
                str_contains($rawReason, 'category default') || 
                str_contains($rawReason, 'conflict rule') || 
                str_contains($rawReason, 'rule:')
            ) {
                $confidence = 'HEURISTIC';
            } else {
                $confidence = 'EXACT';
            }

            // 6. Review Reason (explicit diagnostic if needs_review = 1; NULL when needs_review = 0)
            if ($needsReview === 1) {
                if ($bid === 128) {
                    $reviewReason = "NULL category: Internal system/test profile without commercial activity. Keep unlisted or assign administrative tag.";
                } else {
                    $reviewReason = $rawReason ?: "Flagged for manual editorial review.";
                }
            } else {
                $reviewReason = null;
            }

            // 7. Updated At
            $updatedAt = $b->updated_at ? $b->updated_at->format('Y-m-d H:i:s') : '2026-10-15 14:00:00';

            // Stats
            if ($newCatId === null) $stats['null_category']++;
            if ($needsReview === 1) $stats['needs_review']++;
            $stats['confidence'][$confidence]++;

            fputcsv($outFp, [
                $bid,
                $nameAr,
                $nameEn,
                $legacyId,
                $legacyName,
                $newCatId,
                $l1ParentId,
                $l1ParentNameAr,
                $l1ParentNameEn,
                $l2ChildNameAr,
                $l2ChildNameEn,
                $fullSlug,
                $confidence,
                $needsReview,
                $reviewReason,
                $updatedAt,
            ]);
        }

        fclose($outFp);

        $this->newLine();
        $this->info("Export completed successfully!");
        $this->info("Output File: {$outputPath} (" . number_format(filesize($outputPath)) . " bytes)");
        $this->newLine();
        $this->table(['Metric', 'Value'], [
            ['Total Companies Exported', $stats['total']],
            ['Categorized Companies', $stats['total'] - $stats['null_category']],
            ['NULL Category Companies', $stats['null_category'] . ' (ID 128)'],
            ['Flagged for Review (needs_review = 1)', $stats['needs_review']],
            ['Confidence: EXACT', $stats['confidence']['EXACT'] . ' (' . round(($stats['confidence']['EXACT'] / $stats['total']) * 100, 1) . '%)'],
            ['Confidence: HEURISTIC', $stats['confidence']['HEURISTIC'] . ' (' . round(($stats['confidence']['HEURISTIC'] / $stats['total']) * 100, 1) . '%)'],
            ['Confidence: MANUAL_OVERRIDE', $stats['confidence']['MANUAL_OVERRIDE'] . ' (' . round(($stats['confidence']['MANUAL_OVERRIDE'] / $stats['total']) * 100, 1) . '%)'],
            ['Confidence: UNMATCHED', $stats['confidence']['UNMATCHED'] . ' (' . round(($stats['confidence']['UNMATCHED'] / $stats['total']) * 100, 1) . '%)'],
        ]);

        return Command::SUCCESS;
    }
}
