<?php

namespace App\Filament\Resources\Sales\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Created By')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('salesItems.product.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('salesItems.quantity')
                    ->label('Quantity')
                    ->sortable(),

                TextColumn::make('salesItems.price')
                    ->label('Price')
                    ->money('idr', true)
                    ->sortable(),

                TextColumn::make('salesItems.total')
                    ->label('Total')
                    ->money('idr', true)
                    ->sortable(),

                TextColumn::make('salesItems.sale_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('today')
                    ->query(fn($query) => $query->whereDate('sale_date', today())),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
