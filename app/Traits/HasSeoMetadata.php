<?php

namespace App\Traits;

use App\Models\SeoMetadata;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeoMetadata
{
    public function seoMetadata(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    public function getMetaTitleAttribute($value = null)
    {
        $locale = app()->getLocale();
        $seo = $this->seoMetadata;
        if ($seo && $seo->meta_title) {
            return $seo->meta_title[$locale] ?? ($seo->meta_title['en'] ?? null);
        }
        
        // Fallbacks based on model
        if (method_exists($this, 'getFallbackMetaTitle')) {
            return $this->getFallbackMetaTitle($value);
        }

        return $this->name ?? $this->title ?? $value;
    }

    public function getMetaDescriptionAttribute($value = null)
    {
        $locale = app()->getLocale();
        $seo = $this->seoMetadata;
        if ($seo && $seo->meta_description) {
            return $seo->meta_description[$locale] ?? ($seo->meta_description['en'] ?? null);
        }

        if (method_exists($this, 'getFallbackMetaDescription')) {
            return $this->getFallbackMetaDescription($value);
        }

        return $value;
    }

    public function getOgImageAttribute($value = null)
    {
        $seo = $this->seoMetadata;
        if ($seo && $seo->og_image_url) {
            return $seo->og_image_url;
        }

        if (method_exists($this, 'getFallbackOgImage')) {
            return $this->getFallbackOgImage($value);
        }

        return $value;
    }
}
