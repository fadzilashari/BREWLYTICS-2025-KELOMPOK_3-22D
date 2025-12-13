<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'price',
        'total',
        'sale_date',
    ];

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

    // public static function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['total'] = $data['quantity'] * $data['price'];
    //     return $data;
    // }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['created_by'] = Auth::id();

    //     $product = Product::findOrFail($data['id']);

    //     // Kurangi stok
    //     $product->product_stock -= $data['quantity'];
    //     $product->save();

    //     return $data;
    // }

    // public static function mutateFormDataBeforeSave(array $data): array
    // {
    //     $data['total'] = $data['quantity'] * $data['price'];
    //     return $data;
    // }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        $product = Product::findOrFail($data['product_id']);

        if ($product->stock < $data['quantity']) {
            throw ValidationException::withMessages([
                'quantity' => 'Stok tidak mencukupi.',
            ]);
        }

        $product->decrement('stock', $data['quantity']);

        return $data;
    }

    protected static function booted()
    {
        // static::creating(function ($sale) {
        //     $sale->total = $sale->quantity * $sale->price; // Calculate total before creating
        //     if (Auth::check()) {
        //         $sale->user_id = Auth::id(); // Set the user_id to the currently authenticated user
        //     }
        // });
    }
}
