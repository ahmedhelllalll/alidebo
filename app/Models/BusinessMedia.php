<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessMedia extends Model
{
    protected $fillable = [
        'business_profile_id',
        'file_path',
        'type',
        'caption',
        'order',
        'disk'
    ];

    protected $appends = ['file_url'];

    public function getFileUrlAttribute()
    {
        return $this->file_path ? \Illuminate\Support\Facades\Storage::disk($this->disk)->url($this->file_path) : null;
    }

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
    }
}