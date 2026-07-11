<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_profile_id',
        'name',
        'email',
        'phone',
        'message',
        'status',
        'notes',
    ];

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
    }
}
