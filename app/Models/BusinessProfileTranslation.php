<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProfileTranslation extends Model
{
    protected $fillable = [
        'business_profile_id',
        'locale',
        'name',
        'description',
        'meta_title',
        'meta_description',
    ];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }
}
