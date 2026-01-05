<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SaleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Transaction;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id(); // Set the user_id to the currently authenticated user

        // $product = Product::find($data['product_id']);

        // $product->stock -= $data['quantity'];
        // $product->save();

        return $data;
    }

    protected function afterCreate(): void
    {
        $sale = $this->record;

        // Hitung total_amount dari items (dobel proteksi)
        $sale->updateTotalAmount();
        $sale->refresh();

        // Buat record transaksi
        Transaction::create([
            'sale_id' => $sale->id,
            'amount'  => $sale->total_amount,
            'user_id' => Auth::id(),
            'type'    => 'sale'
        ]);
    }
}
