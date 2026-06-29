<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'backup_date',
        'file_size',
        'status',
        'stored_locally',
        'stored_on_r2',
        'error_message',
    ];

    protected $casts = [
        'backup_date' => 'datetime',
        'stored_locally' => 'boolean',
        'stored_on_r2' => 'boolean',
    ];

    /**
     * Get the formatted file size (e.g., 14.5 MB).
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
