<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\BackupLog;

class BackupController extends Controller
{
    /**
     * Get the current backup storage path from .env or fallback.
     */
    private function getBackupPath(): string
    {
        return env('BACKUP_PATH', storage_path('app/private'));
    }

    public function index()
    {
        $files = BackupLog::orderBy('backup_date', 'desc')->get();

        // Check if queue runner is busy
        $isGenerating = \DB::table('jobs')->where('payload', 'LIKE', '%db:backup%')->exists()
            || BackupLog::where('status', 'in_progress')->exists();

        // Current backup storage path for the settings card
        $backupPath = $this->getBackupPath();

        return view('admin.backups.index', compact('files', 'isGenerating', 'backupPath'));
    }

    public function status(Request $request)
    {
        $isGenerating = \DB::table('jobs')->where('payload', 'LIKE', '%db:backup%')->exists()
            || BackupLog::where('status', 'in_progress')->exists();
            
        return response()->json([
            'isGenerating' => $isGenerating
        ]);
    }

    public function create(Request $request)
    {
        // Prevent duplicate backup processes
        $alreadyRunning = \DB::table('jobs')->where('payload', 'LIKE', '%db:backup%')->exists()
            || BackupLog::where('status', 'in_progress')->exists();
            
        if ($alreadyRunning) {
            return response()->json([
                'status' => 'error',
                'message' => __('admin.backup_already_running_desc') ?? 'A backup is already in progress.',
            ], 409);
        }

        Artisan::queue('db:backup');

        return response()->json(['status' => 'success', 'message' => __('admin.backup_started')]);
    }

    public function download(Request $request, $id)
    {
        $backup = BackupLog::findOrFail($id);
        $filename = $backup->filename;
        
        // Use direct path to guarantee we find it where the command saved it
        $localPath = storage_path("app/private/backups/{$filename}");
        $r2Path = "backups/{$filename}";

        if (File::exists($localPath)) {
            // Clear any stray output buffers (whitespace, warnings) before sending the binary file.
            // This prevents "The archive is corrupt" errors in WinRAR/Windows.
            if (ob_get_level()) {
                ob_end_clean();
            }
            return response()->download($localPath);
        }

        if ($backup->stored_on_r2 && Storage::disk('r2')->exists($r2Path)) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            return Storage::disk('r2')->download($r2Path);
        }

        abort(404, "Backup file not found locally or on R2.");
    }

    public function destroy(Request $request, $id)
    {
        $backup = BackupLog::findOrFail($id);
        $filename = $backup->filename;

        // Force delete from local storage regardless of db flag, using absolute path
        $localPath = storage_path("app/private/backups/{$filename}");
        try {
            if (File::exists($localPath)) {
                File::delete($localPath);
            }
        } catch (\Exception $e) {
            \Log::error('Local backup deletion failed: ' . $e->getMessage());
        }

        try {
            if ($backup->stored_on_r2) {
                $r2Path = "backups/{$filename}";
                if (Storage::disk('r2')->exists($r2Path)) {
                    Storage::disk('r2')->delete($r2Path);
                }
            }
        } catch (\Exception $e) {
            \Log::error('R2 backup deletion failed: ' . $e->getMessage());
        }

        $backup->delete();

        return response()->json(['status' => 'success']);
    }

    /**
     * Save new backup storage location path.
     * Updates .env BACKUP_PATH dynamically.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'backup_path' => 'required|string|max:500',
        ]);

        $newPath = rtrim($request->input('backup_path'), '/\\');

        // Normalize to forward slashes for .env compatibility (backslashes crash dotenv)
        $newPath = str_replace('\\', '/', $newPath);

        // Ensure the directory is writable (create if needed)
        if (!File::isDirectory($newPath)) {
            try {
                File::makeDirectory($newPath, 0755, true);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('admin.backup_path_invalid') ?? 'Cannot create directory. Please check permissions.',
                ], 422);
            }
        }

        if (!is_writable($newPath)) {
            return response()->json([
                'status' => 'error',
                'message' => __('admin.backup_path_not_writable') ?? 'Directory is not writable. Please check permissions.',
            ], 422);
        }

        // Update .env BACKUP_PATH
        $this->setEnv('BACKUP_PATH', $newPath);

        // Update the filesystem disk root at runtime
        config(['filesystems.disks.local.root' => $newPath]);

        return response()->json([
            'status' => 'success',
            'message' => __('admin.backup_path_saved') ?? 'Storage location saved successfully.',
        ]);
    }

    /**
     * Set or update a key in the .env file.
     */
    private function setEnv(string $key, string $value): void
    {
        $envPath = app()->environmentFilePath();
        $envContent = file_get_contents($envPath);

        // Wrap value in quotes if it contains spaces
        $wrappedValue = str_contains($value, ' ') ? "\"$value\"" : $value;

        if (preg_match("/^{$key}=.*/m", $envContent)) {
            $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$wrappedValue}", $envContent);
        } else {
            $envContent .= "\n{$key}={$wrappedValue}\n";
        }

        file_put_contents($envPath, $envContent);
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
