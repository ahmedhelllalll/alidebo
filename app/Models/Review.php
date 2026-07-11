<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_profile_id',
        'reviewer_name',
        'reviewer_email',
        'ip_address',
        'rating',
        'comment',
        'reply',
        'replied_at',
        'status',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
    }
}
