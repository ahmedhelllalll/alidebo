<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessMedia extends Model
{
    protected $fillable = [
        'business_profile_id',
        'file_path',
        'type',
        'caption',
        'order'
    ];

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
    }
}