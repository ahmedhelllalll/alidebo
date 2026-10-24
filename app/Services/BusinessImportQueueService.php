<?php

namespace App\Services;

use App\Jobs\ProcessSheetImport;
use App\Models\BusinessImportBatch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BusinessImportQueueService
{
    public const QUEUE_NAME = 'imports';
    public const STALE_TIMEOUT_MINUTES = 30;

    /**
     * Inspect next queued batch and dispatch it if no batch is currently processing.
     * Uses cache lock to avoid race conditions when multiple jobs/requests finish together.
     */
    public function dispatchNext(): ?BusinessImportBatch
    {
        return Cache::lock('business_import_pipeline_lock', 15)->block(5, function () {
            // 1. Check for an active processing batch
            $activeBatch = BusinessImportBatch::where('status', BusinessImportBatch::STATUS_PROCESSING)->first();

            if ($activeBatch) {
                // Heartbeat check: detect stale/orphaned batch that crashed without event
                $staleThreshold = now()->subMinutes(self::STALE_TIMEOUT_MINUTES);
                if ($activeBatch->updated_at && $activeBatch->updated_at < $staleThreshold) {
                    Log::warning("BusinessImportQueueService: Batch #{$activeBatch->id} stalled (no update since {$activeBatch->updated_at}). Marking as failed.");
                    
                    $errorPath = "imports/errors/batch_{$activeBatch->id}.txt";
                    $timeoutMsg = "[FATAL] Batch stalled and exceeded timeout limit of " . self::STALE_TIMEOUT_MINUTES . " minutes without progress. Automatically marked as failed.\n";
                    Storage::disk('local')->append($errorPath, $timeoutMsg);

                    $activeBatch->update([
                        'status' => BusinessImportBatch::STATUS_FAILED,
                        'error_log_path' => $errorPath,
                    ]);
                } else {
                    // Active batch is still running healthy, do not dispatch next
                    return null;
                }
            }

            // 2. Find the next queued batch in FIFO order
            $nextBatch = BusinessImportBatch::where('status', BusinessImportBatch::STATUS_QUEUED)
                ->orderBy('id', 'asc')
                ->first();

            if (!$nextBatch) {
                return null;
            }

            // 3. Transition to processing status atomically
            $nextBatch->update(['status' => BusinessImportBatch::STATUS_PROCESSING]);

            // 4. Dispatch the master job to the dedicated imports queue
            ProcessSheetImport::dispatch($nextBatch->id)->onQueue(self::QUEUE_NAME);

            Log::info("BusinessImportQueueService: Dispatched Batch #{$nextBatch->id} ({$nextBatch->original_file_name}) to queue '" . self::QUEUE_NAME . "'.");

            return $nextBatch;
        });
    }

    /**
     * Detect if a specific worksheet named 'Import Template' exists.
     */
    public function detectTargetSheet(string $filePath): ?string
    {
        try {
            $fullPath = Storage::disk('local')->path($filePath);
            if (!file_exists($fullPath)) {
                return null;
            }

            $reader = IOFactory::createReaderForFile($fullPath);
            if (method_exists($reader, 'listWorksheetNames')) {
                $sheetNames = $reader->listWorksheetNames($fullPath);
                if (is_array($sheetNames) && in_array('Import Template', $sheetNames, true)) {
                    return 'Import Template';
                }
            }
        } catch (\Throwable $e) {
            Log::debug("BusinessImportQueueService: detectTargetSheet fallback to default: " . $e->getMessage());
        }

        return null;
    }
}
