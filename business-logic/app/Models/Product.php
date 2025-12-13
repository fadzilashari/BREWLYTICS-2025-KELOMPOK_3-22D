<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'stock'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    use LogsActivity;

    protected static $logFillable = true;      // Record all fillable attributes
    protected static $logOnlyDirty = true;     // Record only changed attributes
    protected static $logName = 'product';     // log name

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('product');
    }
}
