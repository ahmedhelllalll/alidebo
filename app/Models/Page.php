<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use \App\Traits\HasSeoMetadata;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'location',
        'layout_style',
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
    ];

    public function getFallbackMetaTitle()
    {
        $locale = app()->getLocale();
        return $this->title[$locale] ?? ($this->title['en'] ?? null);
    }
}
