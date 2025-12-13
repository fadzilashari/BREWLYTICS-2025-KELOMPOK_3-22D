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

    protected function afterSave(): void
    {
        $sale = $this->record;

        Transaction::create([
            'sale_id' => $sale->id,
            'amount'  => $sale->total,
            'user_id' => Auth::id(),
            'type'    => 'sale'
        ]);
    }
}
