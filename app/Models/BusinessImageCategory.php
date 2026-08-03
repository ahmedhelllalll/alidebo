<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessImageCategory extends Model
{
    protected $fillable = [
        'business_profile_id',
        'name',
    ];

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(BusinessMedia::class, 'business_image_category_id')->orderBy('order');
    }
}
