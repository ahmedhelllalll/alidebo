<?php

namespace App\Jobs;

use App\Imports\BusinessProfilesImport;
use App\Models\BusinessImportBatch;
use App\Services\BusinessImportQueueService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessSheetImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $batchId;
    public int $timeout = 1800; // 30 minutes master job timeout
    public int $tries = 1;

    public function __construct(int $batchId)
    {
        $this->batchId = $batchId;
        $this->onQueue(BusinessImportQueueService::QUEUE_NAME);
    }

    public function handle(BusinessImportQueueService $queueService): void
    {
        $batch = BusinessImportBatch::with('admin')->find($this->batchId);

        if (!$batch || $batch->status !== BusinessImportBatch::STATUS_PROCESSING) {
            Log::info("ProcessSheetImport: Batch #{$this->batchId} not found or not in processing state. Skipping.");
            return;
        }

        // Verify that the file exists on the local disk
        if (!$batch->file_path || !Storage::disk('local')->exists($batch->file_path)) {
            $errorMsg = "[FATAL] Stored file not found at path: " . ($batch->file_path ?? 'NULL');
            Log::error("ProcessSheetImport: Batch #{$batch->id} error - {$errorMsg}");

            $errorPath = "imports/errors/batch_{$batch->id}.txt";
            Storage::disk('local')->append($errorPath, $errorMsg . "\n");

            $batch->update([
                'status' => BusinessImportBatch::STATUS_FAILED,
                'error_log_path' => $errorPath,
            ]);

            // Unlock next batch in line
            $queueService->dispatchNext();
            return;
        }

        // Verify zip archive safety (Zip bomb / DoS defense)
        if (!$queueService->validateArchiveSafety($batch->file_path)) {
            $errorMsg = "[FATAL] File rejected: Failed archive safety verification (potential zip bomb, excessive compression ratio, or corrupt archive).";
            Log::error("ProcessSheetImport: Batch #{$batch->id} safety violation - {$errorMsg}");

            $errorPath = "imports/errors/batch_{$batch->id}.txt";
            Storage::disk('local')->append($errorPath, $errorMsg . "\n");

            $batch->update([
                'status' => BusinessImportBatch::STATUS_FAILED,
                'error_log_path' => $errorPath,
            ]);

            // Unlock next batch in line
            $queueService->dispatchNext();
            return;
        }

        $targetSheet = $batch->target_sheet ?: $queueService->detectTargetSheet($batch->file_path);
        if ($targetSheet && !$batch->target_sheet) {
            $batch->update(['target_sheet' => $targetSheet]);
        }

        $adminEmail = $batch->admin->email ?? config('mail.from.address', 'admin@alidebo.com');

        try {
            $import = new BusinessProfilesImport(
                $adminEmail,
                $batch->admin_id,
                $batch->id,
                $batch->is_dry_run,
                $targetSheet
            );

            // Queue chunks into the imports queue
            Excel::queueImport($import, $batch->file_path, 'local');

            Log::info("ProcessSheetImport: Successfully triggered chunked import for Batch #{$batch->id} ({$batch->original_file_name}).");
        } catch (\Throwable $e) {
            $errorMsg = "[FATAL] Failed to dispatch Excel chunked import: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
            Log::error("ProcessSheetImport Exception: " . $errorMsg, ['exception' => $e]);

            $errorPath = "imports/errors/batch_{$batch->id}.txt";
            Storage::disk('local')->append($errorPath, $errorMsg . "\n");

            $batch->update([
                'status' => BusinessImportBatch::STATUS_FAILED,
                'error_log_path' => $errorPath,
            ]);

            // Unlock next batch
            $queueService->dispatchNext();
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessSheetImport: Job failed for Batch #{$this->batchId}: " . $exception->getMessage());

        $batch = BusinessImportBatch::find($this->batchId);
        if ($batch && $batch->status === BusinessImportBatch::STATUS_PROCESSING) {
            $errorPath = "imports/errors/batch_{$batch->id}.txt";
            Storage::disk('local')->append($errorPath, "[FATAL] Queue job failed: " . $exception->getMessage() . "\n");

            $batch->update([
                'status' => BusinessImportBatch::STATUS_FAILED,
                'error_log_path' => $errorPath,
            ]);
        }

        app(BusinessImportQueueService::class)->dispatchNext();
    }
}
