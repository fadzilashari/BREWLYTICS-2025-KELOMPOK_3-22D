<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SaleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class EditSale extends EditRecord
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }

    protected function afterSave(): void
    {
        $sale = $this->record;

        Transaction::create([
            'sale_id' => $sale->id,
            'amount'  => $sale->total,
            'user_id' => Auth::id(),
            'type'    => 'update'
        ]);
    }
}
