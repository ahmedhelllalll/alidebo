<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Services\CategoryRedirectService;

class BackfillCategoriesV2Command extends Command
{
    protected $signature = 'categories:backfill-v2 
                            {--database= : Target database connection (e.g. staging or staging_sim)}
                            {--path= : Path to the v4 CSV mapping file}
                            {--batch-size=100 : Number of rows per transaction batch}
                            {--dry-run : Run simulation without writing changes to the database}
                            {--force : Force overwrite existing new_category_id values (STAGING ONLY)}';

    protected $description = 'Safely and idempotently backfill business_profiles.new_category_id and category_needs_review from dry_run_full_v4.csv';

    public function handle(): int
    {
        $db = $this->option('database');
        $isDryRun = (bool)$this->option('dry-run');
        $isForce = (bool)$this->option('force');

        if (empty($db)) {
            $this->error("Safety abort: You must explicitly specify --database= (e.g. --database=staging or --database=staging_sim).");
            return Command::FAILURE;
        }

        if ($isForce && empty($db)) {
            $this->error("Safety abort: --force requires an explicit --database parameter.");
            return Command::FAILURE;
        }

        // Dynamically allow staging_sim if not explicitly declared in config
        if (!config("database.connections.{$db}") && ($db === 'staging_sim' || $db === 'alidebo_staging_sim')) {
            $baseConn = config('database.connections.staging') ?: config('database.connections.mysql');
            config(["database.connections.{$db}" => array_merge($baseConn, [
                'database' => 'alidebo_staging_sim',
            ])]);
        }

        $dbConfig = config("database.connections.{$db}");
        if (!$dbConfig) {
            $this->error("Database connection '{$db}' is not configured in config/database.php.");
            return Command::FAILURE;
        }

        $targetHost = $dbConfig['host'] ?? '';
        $targetDb = $dbConfig['database'] ?? '';

        $this->info("==================================================");
        $this->info("  Categories V2 Safe Backfill" . ($isDryRun ? " [DRY-RUN SIMULATION]" : ""));
        $this->info("==================================================");
        $this->info("Connection:      {$db}");
        $this->info("Target Host:     {$targetHost}");
        $this->info("Target Database: {$targetDb}");
        $this->info("Dry-Run Mode:    " . ($isDryRun ? "YES (NO WRITES)" : "NO (DATABASE WRITES ENABLED)"));
        $this->info("Force Mode:      " . ($isForce ? "YES (OVERWRITING EXISTING VALUES)" : "NO (ONLY UNSET ROWS)"));

        if ($isForce) {
            $this->newLine();
            $this->warn("**********************************************************************");
            $this->warn("  WARNING: --force is ENABLED. Existing new_category_id values will be overwritten!");
            $this->warn("  This option is strictly for staging simulation/testing purposes.");
            $this->warn("**********************************************************************");
            $this->newLine();
        }

        // Guard against accidental remote execution
        if ($targetHost !== '127.0.0.1' && $targetHost !== 'localhost') {
            $this->error("Safety violation: Target host '{$targetHost}' is not local (127.0.0.1 / localhost). Aborting.");
            return Command::FAILURE;
        }

        $csvPath = $this->option('path') ?: 'C:/Users/Mega Store/Documents/alidebo_backup/dry_run_full_v4.csv';
        if (!File::exists($csvPath)) {
            $this->error("CSV file not found at: {$csvPath}");
            return Command::FAILURE;
        }

        $this->info("Reading CSV mapping: {$csvPath}");

        $csvRows = [];
        $fp = fopen($csvPath, 'r');
        if (!$fp) {
            $this->error("Failed to open CSV file for reading.");
            return Command::FAILURE;
        }

        // Read header & strip possible BOM
        $header = fgetcsv($fp);
        if ($header && isset($header[0])) {
            $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0]);
        }

        while (($data = fgetcsv($fp)) !== false) {
            if (count($data) < 7) {
                continue;
            }
            $id = (int)$data[0];
            $name = $data[1] ?? '';
            $oldCatId = ($data[2] !== '' && $data[2] !== null) ? (int)$data[2] : null;
            $newCatId = ($data[4] !== '' && $data[4] !== null) ? (int)$data[4] : null;
            $needsReview = (strtolower(trim($data[6])) === 'true' || trim($data[6]) === '1') ? 1 : 0;
            $reason = $data[7] ?? '';

            $csvRows[$id] = [
                'id' => $id,
                'name' => $name,
                'old_category_id' => $oldCatId,
                'new_category_id' => $newCatId,
                'category_needs_review' => $needsReview,
                'reason' => $reason,
            ];
        }
        fclose($fp);

        $totalCsvRows = count($csvRows);
        $this->info("Loaded {$totalCsvRows} valid rows from CSV.");

        if ($totalCsvRows === 0) {
            $this->error("No valid profile mappings found in CSV.");
            return Command::FAILURE;
        }

        $conn = DB::connection($db);

        // Preload L1 mapping from CategoryRedirectService
        $legacySlugToId = CategoryRedirectService::LEGACY_SLUG_TO_ID;
        $legacySlugMap  = CategoryRedirectService::LEGACY_SLUG_MAP;
        $l1SlugToId = $conn->table('new_categories')->whereNull('parent_id')->pluck('id', 'slug')->toArray();

        $legacyIdToL1Id = [];
        foreach ($legacySlugToId as $slug => $legacyId) {
            $newSlug = $legacySlugMap[$slug] ?? null;
            if ($newSlug) {
                $l1Slug = explode('/', $newSlug)[0];
                if (isset($l1SlugToId[$l1Slug])) {
                    $legacyIdToL1Id[$legacyId] = (int)$l1SlugToId[$l1Slug];
                }
            }
        }

        // Query all business profiles from target database
        $dbProfiles = $conn->table('business_profiles')
            ->select('id', 'name', 'category_id', 'new_category_id', 'category_needs_review')
            ->orderBy('id')
            ->get();

        $totalDbProfiles = $dbProfiles->count();
        $this->info("Total business_profiles in {$targetDb}: {$totalDbProfiles}");

        // Track CSV IDs missing from database
        $dbProfileIds = $dbProfiles->pluck('id')->all();
        $dbIdLookup = array_flip($dbProfileIds);
        $missingCsvIds = [];
        foreach (array_keys($csvRows) as $csvId) {
            if (!isset($dbIdLookup[$csvId])) {
                $missingCsvIds[] = $csvId;
            }
        }

        // Counters
        $appliedFromCsvCount = 0;
        $fallbackCount = 0;
        $skippedAlreadySetCount = 0;
        $guardRejectedCount = 0;
        $stillNullCount = 0;

        $fallbackList = [];
        $guardRejectedList = [];

        $batchSize = max(1, (int)$this->option('batch-size'));
        $chunks = $dbProfiles->chunk($batchSize);

        $bar = $this->output->createProgressBar($totalDbProfiles);
        $bar->start();

        foreach ($chunks as $chunk) {
            if (!$isDryRun) {
                $conn->beginTransaction();
            }

            try {
                foreach ($chunk as $profile) {
                    $id = (int)$profile->id;
                    $dbCatId = $profile->category_id !== null ? (int)$profile->category_id : null;
                    $dbNewCatId = $profile->new_category_id !== null ? (int)$profile->new_category_id : null;
                    $dbNeedsReview = (int)($profile->category_needs_review ?? 0);
                    $alreadySet = ($dbNewCatId !== null);

                    $actionTaken = null;
                    $targetNewCatId = null;
                    $targetNeedsReview = 0;

                    // 2. Check if row exists in CSV
                    if (isset($csvRows[$id])) {
                        $csvItem = $csvRows[$id];
                        $csvOldCatId = $csvItem['old_category_id'];

                        // Stale-snapshot guard: verify legacy category_id matches
                        if ($csvOldCatId !== $dbCatId) {
                            $guardRejectedCount++;
                            $guardRejectedList[] = [
                                'id' => $id,
                                'name' => $profile->name,
                                'db_category_id' => $dbCatId,
                                'csv_old_category_id' => $csvOldCatId,
                            ];
                            // Row treated as NOT in CSV -> falls through to fallback pass
                            $actionTaken = 'fallback_guard_rejected';
                        } else {
                            if ($id === 128) {
                                $targetNewCatId = null;
                                $targetNeedsReview = 1;
                            } else {
                                $targetNewCatId = $csvItem['new_category_id'];
                                $targetNeedsReview = $csvItem['category_needs_review'];
                            }

                            // Part G: If row already equals the target value, count as skipped (already set)
                            if ($dbNewCatId === $targetNewCatId && $dbNeedsReview === (int)$targetNeedsReview && !$isForce) {
                                $skippedAlreadySetCount++;
                                if ($targetNewCatId === null) {
                                    $stillNullCount++;
                                }
                                $bar->advance();
                                continue;
                            }

                            // If new_category_id is already set and not in force mode
                            if ($alreadySet && !$isForce) {
                                $skippedAlreadySetCount++;
                                $bar->advance();
                                continue;
                            }

                            // Guard passed: apply CSV mapping
                            $actionTaken = 'applied_csv';
                            $appliedFromCsvCount++;
                        }
                    } else {
                        // 1. Skip if already set and not in force mode
                        if ($alreadySet && !$isForce) {
                            $skippedAlreadySetCount++;
                            $bar->advance();
                            continue;
                        }
                        // Not in CSV -> fallback pass
                        $actionTaken = 'fallback_not_in_csv';
                    }

                    // 3. Fallback Pass (for rows not in CSV or guard-rejected)
                    if ($actionTaken === 'fallback_not_in_csv' || $actionTaken === 'fallback_guard_rejected') {
                        $fallbackCount++;

                        if ($id === 128) {
                            $targetNewCatId = null;
                            $targetNeedsReview = 1;
                            $fallbackList[] = [
                                'id' => $id,
                                'name' => $profile->name,
                                'reason' => 'Special exclusion: ID 128 (Alidebo) remains NULL, needs_review = 1',
                            ];
                        } else {
                            if ($dbCatId !== null && isset($legacyIdToL1Id[$dbCatId])) {
                                $targetNewCatId = $legacyIdToL1Id[$dbCatId];
                                $targetNeedsReview = 1;
                                $fallbackList[] = [
                                    'id' => $id,
                                    'name' => $profile->name,
                                    'reason' => "Mapped legacy category_id {$dbCatId} -> L1 parent ID {$targetNewCatId} (needs_review = 1)",
                                ];
                            } else {
                                $targetNewCatId = null;
                                $targetNeedsReview = 1;
                                $fallbackList[] = [
                                    'id' => $id,
                                    'name' => $profile->name,
                                    'reason' => "Legacy category_id " . ($dbCatId !== null ? $dbCatId : "NULL") . " unmapped -> left NULL (needs_review = 1)",
                                ];
                            }
                        }
                    }

                    if ($targetNewCatId === null) {
                        $stillNullCount++;
                    }

                    // 4. Update row (only in real run mode)
                    if (!$isDryRun) {
                        $conn->table('business_profiles')
                            ->where('id', $id)
                            ->update([
                                'new_category_id' => $targetNewCatId,
                                'category_needs_review' => $targetNeedsReview,
                            ]);
                    }

                    $bar->advance();
                }

                if (!$isDryRun) {
                    $conn->commit();
                }
            } catch (\Throwable $e) {
                if (!$isDryRun) {
                    $conn->rollBack();
                }
                $this->newLine();
                $this->error("Batch transaction failed: " . $e->getMessage());
                return Command::FAILURE;
            }
        }

        $bar->finish();
        $this->newLine(2);

        // =========================================================================
        // FINAL COMPREHENSIVE REPORT
        // =========================================================================
        $this->info("==================================================");
        $this->info("  Backfill Execution Report" . ($isDryRun ? " [DRY-RUN]" : ""));
        $this->info("==================================================");
        $this->line("Target Database:             {$targetDb}");
        $this->line("Total Database Profiles:     {$totalDbProfiles}");
        $this->line("Applied from CSV:            {$appliedFromCsvCount}");
        $this->line("Fallback applied:            {$fallbackCount}");
        $this->line("Skipped (already set):       {$skippedAlreadySetCount}");
        $this->line("Guard-rejected (stale cat):  {$guardRejectedCount}");
        $this->line("CSV IDs missing in DB:       " . count($missingCsvIds));
        $this->line("Profiles still NULL:         {$stillNullCount}");

        if (count($missingCsvIds) > 0) {
            $this->newLine();
            $this->warn("CSV IDs missing in target database (" . count($missingCsvIds) . "): " . implode(', ', array_slice($missingCsvIds, 0, 20)));
        }

        if (count($guardRejectedList) > 0) {
            $this->newLine();
            $this->warn("=== GUARD-REJECTED PROFILES (" . count($guardRejectedList) . ") ===");
            foreach ($guardRejectedList as $item) {
                $this->line("  [ID {$item['id']}] {$item['name']} | DB category_id: " . ($item['db_category_id'] ?? 'NULL') . " vs CSV old_category_id: " . ($item['csv_old_category_id'] ?? 'NULL'));
            }
        }

        if (count($fallbackList) > 0) {
            $this->newLine();
            $this->warn("=== FALLBACK APPLIED PROFILES (" . count($fallbackList) . ") ===");
            foreach ($fallbackList as $item) {
                $this->line("  [ID {$item['id']}] {$item['name']} | {$item['reason']}");
            }
        }

        // In real-run mode: verify final state directly via database queries
        if (!$isDryRun) {
            $this->newLine();
            $this->info("=== POST-RUN DATABASE VERIFICATION QUERIES ===");
            $dbTotalProfiles = $conn->table('business_profiles')->count();
            $dbWithNewCat = $conn->table('business_profiles')->whereNotNull('new_category_id')->count();
            $dbNullNewCat = $conn->table('business_profiles')->whereNull('new_category_id')->count();
            $dbNeedsReview = $conn->table('business_profiles')->where('category_needs_review', 1)->count();

            $this->line("DB Query: Total business_profiles:        {$dbTotalProfiles}");
            $this->line("DB Query: Profiles with new_category_id:  {$dbWithNewCat}");
            $this->line("DB Query: Profiles with NULL category:    {$dbNullNewCat}");
            $this->line("DB Query: Profiles with needs_review = 1: {$dbNeedsReview}");
        }

        return Command::SUCCESS;
    }
}
