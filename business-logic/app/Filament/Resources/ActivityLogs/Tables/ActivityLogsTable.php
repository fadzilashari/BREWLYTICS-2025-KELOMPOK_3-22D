<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('properties')->label('Properties'),
                TextColumn::make('log_name')->label('Log'),
                TextColumn::make('description')->label('Description'),
                TextColumn::make('subject_type')->label('Model'),
                TextColumn::make('causer_id')->label('Causer ID'),
                TextColumn::make('causer_name')->label('Causer Name'),
                TextColumn::make('subject_type')->label('Model'),
                TextColumn::make('created_at')->label('Created At')->dateTime(),
            ])
            ->defaultSort('created_at', 'desc')

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
