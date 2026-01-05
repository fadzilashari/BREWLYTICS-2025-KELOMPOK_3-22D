<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class ActivityLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('log_name')
                                    ->label('Kategori')
                                    ->disabled(),
                                TextInput::make('description')
                                    ->label('Aktivitas')
                                    ->disabled(),
                                TextInput::make('causer.name')
                                    ->label('Nama Akun')
                                    ->disabled()
                                    ->placeholder('System'),
                                DateTimePicker::make('created_at')
                                    ->label('Waktu Kejadian')
                                    ->disabled(),
                            ]),
                    ]),

                Section::make('Detail Perubahan')
                    ->schema([
                        Textarea::make('properties')
                            ->label('Data Perubahan')
                            ->disabled()
                            ->rows(10)
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT)),
                    ]),
            ]);
    }
}
