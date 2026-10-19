<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessImportBatch;
use App\Models\BusinessProfile;
use App\Services\BusinessProfileService;
use App\Imports\BusinessProfilesImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class BusinessImportBatchController extends Controller
{
    protected $profileService;

    public function __construct(BusinessProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', BusinessProfile::class);

        $batches = BusinessImportBatch::with('admin:id,name,email')
            ->latest()
            ->paginate(15);

        return view('admin.import-batches.index', compact('batches'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', BusinessProfile::class);

        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240', // max 10MB
            'is_dry_run' => 'nullable|boolean',
        ]);

        try {
            $adminUser = auth()->user();
            $file = $request->file('file');
            $isDryRun = $request->boolean('is_dry_run', false);
            
            $batch = BusinessImportBatch::create([
                'admin_id' => $adminUser->id,
                'original_file_name' => $file->getClientOriginalName(),
                'status' => 'processing',
                'is_dry_run' => $isDryRun,
            ]);
            
            // Store file to local disk first, so the Queue worker can safely access it via string path
            $filePath = $file->store('imports/batches', 'local');
            $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($filePath);
            $targetSheet = null;
            try {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($fullPath);
                if (method_exists($reader, 'listWorksheetNames')) {
                    $sheetNames = $reader->listWorksheetNames($fullPath);
                    if (in_array('Import Template', $sheetNames)) {
                        $targetSheet = 'Import Template';
                    }
                }
            } catch (\Throwable $sheetEx) {
                // Ignore and fallback to sheet index 0
            }

            try {
                Excel::queueImport(new BusinessProfilesImport($adminUser->email, $adminUser->id, $batch->id, $isDryRun, $targetSheet), $filePath, 'local');
            } catch (\Throwable $queueException) {
                \Log::error('Queue Import Dispatch Failed: ' . $queueException->getMessage());
                $batch->update(['status' => 'failed']);
                throw $queueException;
            }

            $message = $isDryRun
                ? (__('admin.dry_run_started') ?? 'Dry run preview started in the background. You will receive an email upon completion.')
                : (__('admin.import_started') ?? 'Import started in the background. You will receive an email upon completion.');

            return response()->json([
                'success' => true,
                'message' => $message,
                'batch_id' => $batch->id,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Excel Import Trigger Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('admin.import_failed') . ' Details: ' . $e->getMessage() . ' in ' . basename($e->getFile()) . ':' . $e->getLine()
            ], 500);
        }
    }

    public function destroy(BusinessImportBatch $importBatch)
    {
        $this->authorize('delete', BusinessProfile::class);

        // We must delete the businesses individually to trigger image deletion
        // or we use the profile service
        $businesses = $importBatch->businesses()->get();
        foreach ($businesses as $business) {
            // Delete associated images
            if ($business->logo) $this->profileService->deleteImage($business->logo, $business->disk ?? 'public');
            if ($business->cover) $this->profileService->deleteImage($business->cover, $business->disk ?? 'public');
            
            // Delete gallery media
            foreach ($business->media as $media) {
                $this->profileService->deleteMedia($media);
            }
            
            $business->delete();
        }

        // Delete the error log file if exists
        if ($importBatch->error_log_path && Storage::disk('local')->exists($importBatch->error_log_path)) {
            Storage::disk('local')->delete($importBatch->error_log_path);
        }

        // Delete the rejected file if exists
        if ($importBatch->rejected_file_path && Storage::disk('local')->exists($importBatch->rejected_file_path)) {
            Storage::disk('local')->delete($importBatch->rejected_file_path);
        }

        $importBatch->delete();

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
