<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['country_id', 'name_en', 'name_ar', 'name_de', 'name_es', 'name_tr', 'name_zh', 'status'];
    protected $appends = ['name'];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en ?? $this->name_ar;
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function businessProfiles(): HasMany
    {
        return $this->hasMany(BusinessProfile::class);
    }
}