<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),

                TextColumn::make('causer.name')
                    ->label('Nama Akun')
                    ->sortable()
                    ->searchable()
                    ->placeholder('System'),

                TextColumn::make('description')
                    ->label('Aktivitas')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('subject_type')
                    ->label('Modul')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')

            ->filters([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                //
            ]);
    }
}
