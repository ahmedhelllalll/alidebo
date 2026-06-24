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
    ];

    protected $casts = [
        'contact_methods' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'is_claimed' => 'boolean',
        'approved_at' => 'datetime',
    ];

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