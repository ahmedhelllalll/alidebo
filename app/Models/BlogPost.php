<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use \App\Traits\HasSeoMetadata;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'media_type',
        'media_url',
        'media_alt',
        'status',
        'disk',
        'published_at',
    ];

    protected $appends = [
        'media_asset_url',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'content' => 'array',
        'media_alt' => 'array',
        'published_at' => 'datetime',
    ];

    public function getMediaAssetUrlAttribute(): ?string
    {
        if (!$this->media_url) {
            return null;
        }

        if (str_starts_with($this->media_url, 'http://') || str_starts_with($this->media_url, 'https://')) {
            return $this->media_url;
        }

        $disk = $this->disk ?? 'public';
        return \Illuminate\Support\Facades\Storage::disk($disk)->url($this->media_url);
    }
}
