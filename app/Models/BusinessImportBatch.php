<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessImportBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'original_file_name',
        'total_rows',
        'imported_rows',
        'skipped_rows',
        'status',
        'error_log_path',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(BusinessProfile::class, 'import_batch_id');
    }
}
