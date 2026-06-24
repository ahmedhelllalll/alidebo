<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessView extends Model
{
    protected $fillable = [
        'business_profile_id',
        'ip_address',
        'country_code',
        'city_name',
        'user_agent'
    ];

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
    }
}