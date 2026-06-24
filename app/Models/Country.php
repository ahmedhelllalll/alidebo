<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = ['name_en', 'name_ar', 'code', 'status'];
    protected $appends = ['name'];

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en ?? $this->name_ar;
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}