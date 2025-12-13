<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Form fields to send database
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('price')
                    ->label('Price')
                    ->required()
                    ->numeric(),
                TextInput::make('stock')
                    ->label('Stock')
                    ->required()
                    ->numeric(),
            ]);
    }
}
