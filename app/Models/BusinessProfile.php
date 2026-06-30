<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'owner_id',
        'category_id',
        'city_id',
        'name',
        'slug',
        'description',
        'logo',
        'cover',
        'address',
        'contact_methods',
        'is_claimed',
        'status',
        'rejection_reason',
        'admin_notes',
        'meta_title',
        'meta_description',
        'approved_at',
        'disk',
    ];

    protected $casts = [
        'contact_methods' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'is_claimed' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected $appends = ['logo_url', 'cover_url'];

    public function getLogoUrlAttribute()
    {
        if (!$this->logo) return null;
        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
            return $this->logo;
        }
        return \Illuminate\Support\Facades\Storage::disk($this->disk)->url($this->logo);
    }

    public function getCoverUrlAttribute()
    {
        if (!$this->cover) return null;
        if (str_starts_with($this->cover, 'http://') || str_starts_with($this->cover, 'https://')) {
            return $this->cover;
        }
        return \Illuminate\Support\Facades\Storage::disk($this->disk)->url($this->cover);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(BusinessMedia::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(BusinessView::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(BusinessProfileTranslation::class);
    }

    public function getNameAttribute($value)
    {
        $locale = app()->getLocale();
        $translation = $this->translations->where('locale', $locale)->first();
        return $translation ? $translation->name : $value;
    }

    public function getDescriptionAttribute($value)
    {
        $locale = app()->getLocale();
        $translation = $this->translations->where('locale', $locale)->first();
        return $translation ? $translation->description : $value;
    }

    public function getMetaTitleAttribute($value)
    {
        $locale = app()->getLocale();
        $translation = $this->translations->where('locale', $locale)->first();
        
        if ($translation && $translation->meta_title) {
            return $translation->meta_title;
        }

        $decodedValue = is_string($value) ? json_decode($value, true) : $value;
        return $decodedValue[$locale] ?? ($decodedValue['en'] ?? $this->name);
    }

    public function getMetaDescriptionAttribute($value)
    {
        $locale = app()->getLocale();
        $translation = $this->translations->where('locale', $locale)->first();
        
        if ($translation && $translation->meta_description) {
            return $translation->meta_description;
        }

        $decodedValue = is_string($value) ? json_decode($value, true) : $value;
        return $decodedValue[$locale] ?? ($decodedValue['en'] ?? null);
    }

    public function getCompletionPercentage()
    {
        $fields = ['description', 'logo', 'contact_methods', 'category_id', 'address'];
        $completed = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field))
                $completed++;
        }
        return ($completed / count($fields)) * 100;
    }
}