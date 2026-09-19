<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ProductCategory extends Model
{
    use \App\Traits\HasSeoMetadata;

    protected $fillable = [
        'name_en',
        'name_ar',
        'name_de',
        'name_es',
        'name_tr',
        'name_zh',
        'slug',
        'icon',
        'image',
        'disk',
        'parent_id',
        'status',
        'order',
    ];

    protected $appends = ['name', 'image_url', 'icon_url'];

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{'name_' . $locale} ?? $this->name_en ?? $this->name_ar ?? '';
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        return Storage::disk($this->disk ?? 'public')->url($this->image);
    }

    public function getIconUrlAttribute(): ?string
    {
        if (!$this->icon) return null;
        if (str_starts_with($this->icon, 'http://') || str_starts_with($this->icon, 'https://')) {
            return $this->icon;
        }
        return Storage::disk($this->disk ?? 'public')->url($this->icon);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }
}
