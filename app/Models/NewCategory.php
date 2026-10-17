<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class NewCategory extends Model
{
    use \App\Traits\HasSeoMetadata;

    protected $table = 'new_categories';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'parent_id',
        'level',
        'name_en',
        'name_ar',
        'name_de',
        'name_es',
        'name_tr',
        'name_zh',
        'slug',
        'sort_order',
        'image',
        'icon',
        'status',
        'disk',
    ];

    protected $appends = ['name', 'image_url', 'icon_url'];

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        $name = $this->{'name_' . $locale} ?? null;
        if (!empty($name)) {
            return $name;
        }
        return $this->name_en ?? $this->name_ar ?? '';
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function businessProfiles(): HasMany
    {
        return $this->hasMany(BusinessProfile::class, 'new_category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeParents($query)
    {
        return $query->where('level', 1);
    }

    /**
     * Set of category IDs with at least one business (an L2 business counts for its L1 too), cached for 1 hour.
     */
    public static function getNonEmptyCategoryIds(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('new_categories_non_empty_ids', 3600, function () {
            $assignedIds = \Illuminate\Support\Facades\DB::table('business_profiles')
                ->where('status', 'approved')
                ->whereNotNull('new_category_id')
                ->distinct()
                ->pluck('new_category_id')
                ->all();

            if (empty($assignedIds)) {
                return [];
            }

            $parentIds = self::whereIn('id', $assignedIds)
                ->whereNotNull('parent_id')
                ->pluck('parent_id')
                ->all();

            $effective = array_unique(array_merge($assignedIds, $parentIds));
            return array_values($effective);
        });
    }
}
