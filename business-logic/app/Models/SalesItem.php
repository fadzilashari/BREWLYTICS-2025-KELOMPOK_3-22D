<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price',
        'total',
    ];

    protected static function booted()
    {
        static::saving(function ($salesItem) {
            // Hitung total item sebelum simpan (baik create maupun update)
            $salesItem->total = $salesItem->quantity * $salesItem->price;
        });

        static::created(function ($salesItem) {
            // Kurangi stok produk
            $salesItem->product?->decrement('stock', $salesItem->quantity);
            
            // Update total_amount di model Sale
            $salesItem->sale?->updateTotalAmount();
        });

        static::updated(function ($salesItem) {
            // Update total_amount di model Sale saat item berubah
            $salesItem->sale?->updateTotalAmount();
        });

        static::deleted(function ($salesItem) {
            // Update total_amount di model Sale saat item dihapus
            $salesItem->sale?->updateTotalAmount();
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
