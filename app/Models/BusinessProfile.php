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
        'name',
        'slug',
        'description',
        'status',
        'rejection_reason',
        'meta_title',
        'meta_description',
        'approved_at',
        'logo'
    ];

    protected $casts = [
        'meta_title' => 'array',
        'meta_description' => 'array',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProfileMedia::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ProfileSection::class);
    }
}