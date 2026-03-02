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
            'category_id',
            'city_id',
            'name',
            'slug',
            'description',
            'whatsapp',
            'phone',
            'address',
            'logo',
            'cover',
            'status',
            'rejection_reason',
            'meta_title',
            'meta_description',
            'approved_at',
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
            return $this->hasMany(ProfileMedia::class);
        }

        public function sections(): HasMany
        {
            return $this->hasMany(ProfileSection::class);
        }
    }
