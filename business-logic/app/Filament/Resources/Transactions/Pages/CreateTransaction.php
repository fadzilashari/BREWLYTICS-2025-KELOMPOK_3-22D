<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Transaction;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;
}
