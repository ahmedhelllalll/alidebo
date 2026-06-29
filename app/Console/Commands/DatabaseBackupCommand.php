<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\BackupLog;
use Symfony\Component\Process\Process;
use ZipArchive;

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automated Database Backup System: Dumps, encrypts, and distributes the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automated database backup...');

        // 1. Export & Compress (The Dump)
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');

        $timestamp = now()->format('Y-m-d-Hi');
        $sqlFilename = "project-db-{$timestamp}.sql";
        $zipFilename = "project-db-{$timestamp}.zip";
        
        $backupDir = storage_path('app/private/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $sqlPath = $backupDir . '/' . $sqlFilename;
        $zipPath = $backupDir . '/' . $zipFilename;

        // Initialize Log
        $backupLog = BackupLog::create([
            'filename' => $zipFilename,
            'backup_date' => now(),
            'status' => 'in_progress',
        ]);

        $this->info('1. Dumping database...');
        
        // Use environment variable for password to avoid warnings
        $command = [
            'mysqldump',
            '-h', $host,
            '-P', $port,
            '-u', $username,
            $database
        ];
        
        $env = $_ENV;
        if (!empty($password)) {
            $env['MYSQL_PWD'] = $password;
        }

        $process = new Process($command, null, $env);
        $process->setTimeout(300); // 5 minutes timeout

        // Redirect output directly to the SQL file to save memory
        $process->run(function ($type, $buffer) use ($sqlPath) {
            file_put_contents($sqlPath, $buffer, FILE_APPEND);
        });

        if (!$process->isSuccessful()) {
            $errorMsg = 'Database dump failed: ' . $process->getErrorOutput();
            $this->error($errorMsg);
            $backupLog->update([
                'status' => 'failed',
                'error_message' => $errorMsg,
            ]);
            @unlink($sqlPath);
            return Command::FAILURE;
        }

        // 2. Encryption (The Lock)
        $this->info('2. Compressing and encrypting backup...');
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFile($sqlPath, $sqlFilename);
            
            $backupPassword = env('BACKUP_PASSWORD');
            if ($backupPassword) {
                // PHP 7.2+ supports AES-256 for zip encryption
                $zip->setEncryptionName($sqlFilename, ZipArchive::EM_AES_256, $backupPassword);
            } else {
                $this->warn('No BACKUP_PASSWORD found in .env. The backup will NOT be encrypted.');
            }
            
            if (!$zip->close()) {
                $errorMsg = 'Failed to close the zip file.';
                $this->error($errorMsg);
                $backupLog->update(['status' => 'failed', 'error_message' => $errorMsg]);
                @unlink($sqlPath);
                @unlink($zipPath);
                return Command::FAILURE;
            }
        } else {
            $errorMsg = 'Failed to create the zip file.';
            $this->error($errorMsg);
            $backupLog->update(['status' => 'failed', 'error_message' => $errorMsg]);
            @unlink($sqlPath);
            return Command::FAILURE;
        }

        // Successfully zipped and encrypted
        $backupLog->update(['stored_locally' => true]);

        // Clean up the raw SQL file immediately
        @unlink($sqlPath);

        // 3. Smart Distribution (The 3-2-1 Rule)
        $this->info('3. Distributing backup...');
        
        // A. Local is already saved to $zipPath (storage/app/private/backups/)
        
        // B. Cloudflare R2 / S3
        $this->info('Uploading to R2...');
        try {
            $stream = fopen($zipPath, 'r+');
            Storage::disk('r2')->writeStream("backups/{$zipFilename}", $stream);
            fclose($stream);
            $backupLog->update(['stored_on_r2' => true]);
            $this->info('Upload to R2 successful.');
        } catch (\Exception $e) {
            $this->error('Failed to upload to R2: ' . $e->getMessage());
            Log::error('Backup R2 upload failed: ' . $e->getMessage());
            $backupLog->update(['error_message' => $backupLog->error_message . "\nR2 Upload failed: " . $e->getMessage()]);
            // Proceed anyway for local cleanup and notification
        }

        // 4. Cleanup & Notification (Maintenance)
        $this->info('4. Cleaning up old backups...');
        $retentionDays = 30;
        $thresholdTimestamp = now()->subDays($retentionDays)->getTimestamp();

        // Local Cleanup
        $localFiles = Storage::disk('local')->files('backups');
        $deletedLocal = 0;
        foreach ($localFiles as $file) {
            if (Storage::disk('local')->lastModified($file) < $thresholdTimestamp) {
                // Find matching log to mark as locally deleted
                $oldLog = BackupLog::where('filename', basename($file))->first();
                if ($oldLog) {
                    $oldLog->update(['stored_locally' => false]);
                }
                Storage::disk('local')->delete($file);
                $deletedLocal++;
            }
        }
        $this->info("Deleted {$deletedLocal} old local backup(s).");

        // R2 Cleanup
        try {
            $r2Files = Storage::disk('r2')->files('backups');
            $deletedR2 = 0;
            foreach ($r2Files as $file) {
                if (Storage::disk('r2')->lastModified($file) < $thresholdTimestamp) {
                    // Find matching log to mark as R2 deleted
                    $oldLog = BackupLog::where('filename', basename($file))->first();
                    if ($oldLog) {
                        $oldLog->update(['stored_on_r2' => false]);
                    }
                    Storage::disk('r2')->delete($file);
                    $deletedR2++;
                }
            }
            $this->info("Deleted {$deletedR2} old R2 backup(s).");
        } catch (\Exception $e) {
            $this->error('Failed to clean up R2 backups: ' . $e->getMessage());
        }

        // Notification
        $this->info('Sending notification...');
        $sizeBytes = filesize($zipPath);
        $sizeMb = round($sizeBytes / 1048576, 2);
        
        $backupLog->update([
            'status' => 'success',
            'file_size' => $sizeBytes
        ]);

        // Send notification to the requested emails
        $emails = [
            'ahmed.helllalll@gmail.com',
            'Tarekdeyab0@gmail.com'
        ];
        
        foreach ($emails as $emailAddress) {
            try {
                Mail::to($emailAddress)->send(new \App\Mail\BackupSuccessfulMail($zipFilename, $sizeMb));
                $this->info("Notification sent to {$emailAddress}.");
            } catch (\Exception $e) {
                $this->error("Failed to send notification email to {$emailAddress}: " . $e->getMessage());
                Log::error("Backup email notification failed for {$emailAddress}: " . $e->getMessage());
            }
        }

        $this->info('Backup process completed successfully!');
        return Command::SUCCESS;
    }
}
