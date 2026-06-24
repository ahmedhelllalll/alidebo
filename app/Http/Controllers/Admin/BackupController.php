<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
        $disk = Storage::disk(config('backup.backup.destination.disks')[0] ?? 'local');
        $backupName = config('backup.backup.name');

        $files = [];
        if ($disk->exists($backupName)) {
            $rawFiles = $disk->files($backupName);
            foreach ($rawFiles as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                    $files[] = [
                        'name' => basename($file),
                        'size' => $this->formatSize($disk->size($file)),
                        'date' => date('Y-m-d H:i:s', $disk->lastModified($file)),
                        'path' => $file,
                    ];
                }
            }
        }

        // Sort newest first
        usort($files, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Check if queue runner is busy
        $isGenerating = \DB::table('jobs')->where('payload', 'LIKE', '%backup%')->exists();

        // Current backup storage path for the settings card
        $backupPath = $this->getBackupPath();

        return view('admin.backups.index', compact('files', 'isGenerating', 'backupPath'));
    }

    public function create(Request $request)
    {
        // Prevent duplicate backup processes
        $alreadyRunning = \DB::table('jobs')->where('payload', 'LIKE', '%backup%')->exists();
        if ($alreadyRunning) {
            return response()->json([
                'status' => 'error',
                'message' => __('admin.backup_already_running_desc') ?? 'A backup is already in progress.',
            ], 409);
        }

        $type = $request->input('type', 'all');

        $command = 'backup:run';
        $params = [];

        if ($type === 'db') {
            $params['--only-db'] = true;
        } elseif ($type === 'files') {
            $params['--only-files'] = true;
        }

        Artisan::queue($command, $params);

        return response()->json(['status' => 'success', 'message' => __('admin.backup_started')]);
    }

    public function download(Request $request)
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0] ?? 'local');
        $path = $request->input('path');

        if (!$disk->exists($path)) {
            abort(404, "Backup file not found.");
        }

        return $disk->download($path);
    }

    public function destroy(Request $request)
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0] ?? 'local');
        $path = $request->input('path');

        if ($disk->exists($path)) {
            $disk->delete($path);
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'File not found'], 404);
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
