<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessImportBatch;
use App\Models\BusinessProfile;
use App\Services\BusinessProfileService;
use App\Services\BusinessImportQueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BusinessImportBatchController extends Controller
{
    protected $profileService;
    protected $queueService;

    public function __construct(BusinessProfileService $profileService, BusinessImportQueueService $queueService)
    {
        $this->profileService = $profileService;
        $this->queueService = $queueService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', BusinessProfile::class);

        $batches = BusinessImportBatch::with('admin:id,name,email')
            ->latest()
            ->paginate(15);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'batches' => $batches->items(),
                'total' => $batches->total(),
                'has_active' => BusinessImportBatch::whereIn('status', [
                    BusinessImportBatch::STATUS_QUEUED,
                    BusinessImportBatch::STATUS_PROCESSING
                ])->exists(),
            ]);
        }

        return view('admin.import-batches.index', compact('batches'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', BusinessProfile::class);

        // Normalize single file to files[] array for backward compatibility
        if ($request->hasFile('file') && !$request->hasFile('files')) {
            $request->merge(['files' => [$request->file('file')]]);
        }

        $request->validate([
            'files' => 'required|array|min:1|max:100',
            'files.*' => 'required|file|mimes:xlsx,csv,xls|max:10240', // max 10MB per file
            'is_dry_run' => 'nullable|boolean',
        ]);

        try {
            $adminUser = auth()->user();
            $uploadedFiles = $request->file('files');
            $isDryRun = $request->boolean('is_dry_run', false);

            // Step 1: Store files to local disk first (outside DB transaction to prevent long transactions)
            $storedBatchesData = [];
            foreach ($uploadedFiles as $file) {
                $originalName = $file->getClientOriginalName();
                $filePath = $file->store('imports/batches', 'local');

                $storedBatchesData[] = [
                    'admin_id'           => $adminUser->id,
                    'original_file_name' => $originalName,
                    'file_path'          => $filePath,
                    'target_sheet'       => null, // Resolved asynchronously by queue worker
                    'status'             => BusinessImportBatch::STATUS_QUEUED,
                    'is_dry_run'         => $isDryRun,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            }

            // Step 2: Atomic DB batch insert (runs in milliseconds)
            $createdBatches = [];
            DB::transaction(function () use ($storedBatchesData, &$createdBatches) {
                foreach ($storedBatchesData as $data) {
                    $batch = BusinessImportBatch::create($data);
                    $createdBatches[] = $batch->id;
                }
            });

            // Step 3: Trigger the sequential background pipeline
            $this->queueService->dispatchNext();

            $totalCount = count($createdBatches);
            $mode = $isDryRun ? __('admin.dry_run') : __('admin.new_import');
            $message = $totalCount === 1
                ? __('admin.batch_queued_successfully', ['mode' => $mode])
                : __('admin.batches_queued_successfully', ['count' => $totalCount]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'batch_ids' => $createdBatches,
                'total_queued' => $totalCount,
            ]);
        } catch (\Throwable $e) {
            Log::error('Excel Bulk Import Ingestion Failed: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => __('admin.import_failed') . ' Details: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(BusinessImportBatch $importBatch)
    {
        $this->authorize('delete', BusinessProfile::class);

        // Safeguard: Never delete an actively processing batch
        if ($importBatch->isProcessing()) {
            return response()->json([
                'success' => false,
                'message' => __('admin.cannot_delete_processing_batch') ?? 'Cannot delete a batch that is currently processing. Please wait for it to complete or fail.',
            ], 422);
        }

        try {
            $stats = $this->profileService->deleteImportBatch($importBatch);

            // In case there is another batch queued, advance the pipeline
            $this->queueService->dispatchNext();

            return response()->json([
                'success' => true,
                'message' => __('admin.batch_deleted_successfully') ?? 'Batch deleted successfully.',
                'stats' => $stats,
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to delete import batch #{$importBatch->id}: " . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete batch: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('delete', BusinessProfile::class);

        $request->validate([
            'batch_ids' => 'required|array|min:1|max:200',
            'batch_ids.*' => 'required|integer|exists:business_import_batches,id',
        ]);

        $batchIds = $request->input('batch_ids');

        // Safeguard: Check if any selected batch is processing
        $processingBatches = BusinessImportBatch::whereIn('id', $batchIds)
            ->where('status', BusinessImportBatch::STATUS_PROCESSING)
            ->pluck('id')
            ->all();

        if (!empty($processingBatches)) {
            return response()->json([
                'success' => false,
                'message' => __('admin.cannot_delete_processing_batches', ['batches' => implode(', #', $processingBatches)]),
            ], 422);
        }

        $batches = BusinessImportBatch::whereIn('id', $batchIds)->get();
        $deletedBatchesCount = 0;
        $totalBusinesses = 0;
        $totalMedia = 0;

        foreach ($batches as $batch) {
            try {
                $stats = $this->profileService->deleteImportBatch($batch);
                $deletedBatchesCount++;
                $totalBusinesses += $stats['deleted_businesses'];
                $totalMedia += $stats['deleted_media'];
            } catch (\Throwable $e) {
                Log::error("Bulk destroy error on batch #{$batch->id}: " . $e->getMessage(), ['exception' => $e]);
            }
        }

        // Advance pipeline if there are remaining queued batches
        $this->queueService->dispatchNext();

        $msg = $deletedBatchesCount === 1
            ? __('admin.single_batch_deleted_summary', ['businesses' => $totalBusinesses, 'media' => $totalMedia])
            : __('admin.bulk_batches_deleted_summary', ['count' => $deletedBatchesCount, 'businesses' => $totalBusinesses, 'media' => $totalMedia]);

        return response()->json([
            'success' => true,
            'message' => $msg,
            'deleted_batches' => $deletedBatchesCount,
            'deleted_businesses' => $totalBusinesses,
            'deleted_media' => $totalMedia,
        ]);
    }

    public function downloadErrors(BusinessImportBatch $importBatch)
    {
        $this->authorize('viewAny', BusinessProfile::class);

        $path = $importBatch->error_log_path;
        if (!$path || str_contains($path, '..') || !str_starts_with($path, 'imports/errors/') || !Storage::disk('local')->exists($path)) {
            return back()->with('error', __('admin.no_error_log_found') ?? 'No error log found for this batch.');
        }

        return Storage::disk('local')->download($path, 'batch_'.$importBatch->id.'_errors.txt');
    }

    public function downloadRejected(BusinessImportBatch $importBatch)
    {
        $this->authorize('viewAny', BusinessProfile::class);

        $path = $importBatch->rejected_file_path;
        if (!$path || str_contains($path, '..') || !str_starts_with($path, 'imports/rejected/') || !Storage::disk('local')->exists($path)) {
            return back()->with('error', __('admin.no_rejected_file_found') ?? 'No rejected rows found for this batch.');
        }

        return Storage::disk('local')->download($path, 'batch_'.$importBatch->id.'_rejected.csv');
    }
}
