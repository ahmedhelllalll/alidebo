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
        return $this->image ? \Illuminate\Support\Facades\Storage::disk($this->disk)->url($this->image) : null;
    }

    public function getIconUrlAttribute()
    {
        return $this->icon ? \Illuminate\Support\Facades\Storage::disk($this->disk)->url($this->icon) : null;
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