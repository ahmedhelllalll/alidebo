<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMetadata extends Model
{
    protected $table = 'seo_metadata';

    protected $fillable = [
        'meta_title',
        'meta_description',
        'og_image',
    ];

    protected $casts = [
        'meta_title' => 'array',
        'meta_description' => 'array',
    ];

    public function seoable()
    {
        return $this->morphTo();
    }

    public function getOgImageUrlAttribute()
    {
        if (!$this->og_image) return null;
        if (str_starts_with($this->og_image, 'http://') || str_starts_with($this->og_image, 'https://')) {
            return $this->og_image;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->og_image);
    }
}
