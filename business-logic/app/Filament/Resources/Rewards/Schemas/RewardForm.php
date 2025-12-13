<?php

namespace App\Filament\Resources\Rewards\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\DateTimePicker;

class RewardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Pelanggan')
                    ->required()
                    ->maxLength(255),

                Select::make('rating')
                    ->label('Rating')
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                    ])
                    ->required(),

                TextArea::make('description')
                    ->label('Deskripsi')
                    ->rows(4)
                    ->maxLength(1000),

                DateTimePicker::make('review_date')
                    ->label('Tanggal Review')
                    ->required(),
            ]);
    }
}
