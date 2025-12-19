<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forecast extends Model
{
    protected $fillable = [
        'product_id',
        'forecast_qty',
        'period_days',
        'forecast_start',
        'forecast_end',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
