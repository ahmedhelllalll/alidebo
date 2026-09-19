<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'disk',
        'file_path',
        'order',
        'is_primary',
    ];

    protected $appends = ['file_url'];

    public function getFileUrlAttribute()
    {
        if (!$this->file_path) return null;
        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }
        return Storage::disk($this->disk)->url($this->file_path);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
