<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleIndexLog extends Model
{
    protected $fillable = [
        'url',
        'indexable_type',
        'indexable_id',
        'status',
        'response'
    ];

    public function indexable()
    {
        return $this->morphTo();
    }
}
