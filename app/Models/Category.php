<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name_en', 'name_ar', 'slug', 'image', 'icon', 'status', 'disk'];
    protected $appends = ['name', 'image_url', 'icon_url'];

    public function getImageUrlAttribute()
    {
        if (!$this->image) return null;
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        return \Illuminate\Support\Facades\Storage::disk($this->disk)->url($this->image);
    }

    public function getIconUrlAttribute()
    {
        if (!$this->icon) return null;
        if (str_starts_with($this->icon, 'http://') || str_starts_with($this->icon, 'https://')) {
            return $this->icon;
        }
        return \Illuminate\Support\Facades\Storage::disk($this->disk)->url($this->icon);
    }

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en ?? $this->name_ar;
    }

    public function businessProfiles(): HasMany
    {
        return $this->hasMany(BusinessProfile::class);
    }
}