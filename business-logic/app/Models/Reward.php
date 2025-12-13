<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'name',
        'rating',
        'description',
        'review_date',
    ];

    protected $casts = [
        'review_date' => 'datetime',
    ];
}
