<?php

namespace App\Filament\Resources\Sales\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('sales_items')
                    ->label('Sales Items')
                    ->relationship('salesItems')
                    ->columns(2)
                    ->schema([
                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    $set('price', 0);

                                    return;
                                }

                                $product = \App\Models\Product::find($state);

                                $set('price', $product?->price ?? 0);
                            }),

                        TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $set('total', $state * $get('price', 0));
                            })
                            ->rule(function ($get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    $product = \App\Models\Product::find($get('product_id'));

                                    if ($product && $value > $product->stock) {
                                        $fail('Stock is not enough. Remaining stock: ' . $product->stock);
                                    }
                                };
                            }),

                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $set('total', $state * $get('quantity', 0));
                            }),

                        TextInput::make('total')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled() // Automatic calculation, not editable
                            ->dehydrated(true), // Save this field to the database
                    ])
                    ->defaultItems(1)
                    ->minItems(1)
                    ->required(),
            ]);
    }
}
