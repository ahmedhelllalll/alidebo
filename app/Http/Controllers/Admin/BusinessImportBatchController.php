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
            $createdBatches = [];

            DB::transaction(function () use ($uploadedFiles, $adminUser, $isDryRun, &$createdBatches) {
                foreach ($uploadedFiles as $file) {
                    $originalName = $file->getClientOriginalName();
                    $filePath = $file->store('imports/batches', 'local');
                    $targetSheet = $this->queueService->detectTargetSheet($filePath);

                    $batch = BusinessImportBatch::create([
                        'admin_id'           => $adminUser->id,
                        'original_file_name' => $originalName,
                        'file_path'          => $filePath,
                        'target_sheet'       => $targetSheet,
                        'status'             => BusinessImportBatch::STATUS_QUEUED,
                        'is_dry_run'         => $isDryRun,
                    ]);

                    $createdBatches[] = $batch->id;
                }
            });

            // Trigger the sequential pipeline
            $this->queueService->dispatchNext();

            $totalCount = count($createdBatches);
            $mode = $isDryRun ? 'Dry run' : 'Import';
            $message = $totalCount === 1
                ? "{$mode} queued successfully. Processing sequentially in the background."
                : "{$totalCount} sheets queued successfully. Processing sequentially in the background.";

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

        // Delete associated businesses and images
        $businesses = $importBatch->businesses()->get();
        foreach ($businesses as $business) {
            if ($business->logo) $this->profileService->deleteImage($business->logo, $business->disk ?? 'public');
            if ($business->cover) $this->profileService->deleteImage($business->cover, $business->disk ?? 'public');
            
            foreach ($business->media as $media) {
                $this->profileService->deleteMedia($media);
            }
            
            $business->delete();
        }

        // Delete the uploaded source excel file if exists
        if ($importBatch->file_path && Storage::disk('local')->exists($importBatch->file_path)) {
            Storage::disk('local')->delete($importBatch->file_path);
        }

        // Delete the error log file if exists
        if ($importBatch->error_log_path && Storage::disk('local')->exists($importBatch->error_log_path)) {
            Storage::disk('local')->delete($importBatch->error_log_path);
        }

        // Delete the rejected file if exists
        if ($importBatch->rejected_file_path && Storage::disk('local')->exists($importBatch->rejected_file_path)) {
            Storage::disk('local')->delete($importBatch->rejected_file_path);
        }

        $wasProcessing = $importBatch->isProcessing();
        $importBatch->delete();

        // If the deleted batch was currently processing, advance the pipeline
        if ($wasProcessing) {
            $this->queueService->dispatchNext();
        }

        return response()->json([
            'success' => true,
            'message' => __('admin.batch_deleted_successfully') ?? 'Batch deleted successfully.'
        ]);
    }

    public function downloadErrors(BusinessImportBatch $importBatch)
    {
        $this->authorize('viewAny', BusinessProfile::class);

        if (!$importBatch->error_log_path || !Storage::disk('local')->exists($importBatch->error_log_path)) {
            return back()->with('error', __('admin.no_error_log_found') ?? 'No error log found for this batch.');
        }

        return Storage::disk('local')->download($importBatch->error_log_path, 'batch_'.$importBatch->id.'_errors.txt');
    }

    public function downloadRejected(BusinessImportBatch $importBatch)
    {
        $this->authorize('viewAny', BusinessProfile::class);

        if (!$importBatch->rejected_file_path || !Storage::disk('local')->exists($importBatch->rejected_file_path)) {
            return back()->with('error', __('admin.no_rejected_file_found') ?? 'No rejected rows found for this batch.');
        }

        return Storage::disk('local')->download($importBatch->rejected_file_path, 'batch_'.$importBatch->id.'_rejected.csv');
    }
}
