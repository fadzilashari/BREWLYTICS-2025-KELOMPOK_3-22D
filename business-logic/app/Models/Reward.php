<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Reward extends Model
{
    protected $fillable = [
        'name',
        'rating',
        'description',
        'review_date',
    ];

    protected static function booted()
    {
        static::creating(function ($reward) {
            $reward->review_date ??= now();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('reward');
    }

    use LogsActivity;

    protected $casts = [
        'review_date' => 'datetime',
    ];
}
