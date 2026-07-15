<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use \App\Traits\HasSeoMetadata;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'media_type',
        'media_url',
        'media_alt',
        'status',
        'published_at',
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'media_alt' => 'array',
        'published_at' => 'datetime',
    ];

    public function getMediaAssetUrlAttribute()
    {
        if (!$this->media_url) {
            return null;
        }

        if ($this->media_type === 'image') {
            return asset('storage/' . $this->media_url);
        }

        return $this->media_url;
    }
}
