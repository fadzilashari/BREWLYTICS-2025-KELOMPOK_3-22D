<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('sale.item_name')->label('Item'),
                TextColumn::make('amount')->money('idr', true)->label('Amount'),
                TextColumn::make('type')->badge(),
                TextColumn::make('user.name')->label('Processed by'),
                TextColumn::make('created_at')->dateTime(),
            ])

            ->defaultSort('id', 'desc')

            ->filters([
                //
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
