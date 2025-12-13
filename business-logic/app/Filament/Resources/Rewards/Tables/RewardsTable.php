<?php

namespace App\Filament\Resources\Rewards\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class RewardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name') // Product Name in the list
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description') // Product Price in the list
                    ->searchable()
                    ->sortable(),

                TextColumn::make('review_date') // Product Stock in the list
                    ->searchable()
                    ->sortable(),
            ])

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
