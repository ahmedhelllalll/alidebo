<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedLink extends Model
{
    protected $fillable = [
        'url',
        'hits',
        'last_seen',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
    ];
}
