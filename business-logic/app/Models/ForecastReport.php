<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ForecastReport extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'file_name',
        'file_path',
        'period_days',
        'generated_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('forecast');
    }
    
    protected $casts = [
        'generated_at' => 'datetime',
    ];
}
