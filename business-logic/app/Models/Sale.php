<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Sale extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'sale_date',
        'total_amount',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('sale');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function salesItems()
    {
        return $this->hasMany(SalesItem::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    protected static function booted()
    {
        static::creating(function ($sale) {
            if (empty($sale->sale_date)) {
                $sale->sale_date = now();
            }
            
            if (empty($sale->user_id) && Auth::check()) {
                $sale->user_id = Auth::id();
            }
        });
    }

    public function updateTotalAmount(): void
    {
        $this->update([
            'total_amount' => $this->salesItems()->sum('total'),
        ]);
    }
}
