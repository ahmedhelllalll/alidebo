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
        'disk',
        'business_image_category_id'
    ];

    protected $appends = ['file_url'];

    public function getFileUrlAttribute()
    {
        if (!$this->file_path) return null;
        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }
        return \Illuminate\Support\Facades\Storage::disk($this->disk)->url($this->file_path);
    }

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function imageCategory(): BelongsTo
    {
        return $this->belongsTo(BusinessImageCategory::class, 'business_image_category_id');
    }
}