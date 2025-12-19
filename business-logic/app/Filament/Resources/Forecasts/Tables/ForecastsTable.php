<?php

namespace App\Filament\Resources\Forecasts\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Services\ForecastService;
use Filament\Notifications\Notification;

class ForecastsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('forecast_qty')
                    ->label('Forecast Qty')
                    ->sortable(),

                TextColumn::make('period_days')
                    ->label('Days'),

                TextColumn::make('forecast_start')
                    ->date(),

                TextColumn::make('forecast_end')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('generate')
                    ->label('Generate Forecast')
                    ->icon('heroicon-o-cpu-chip')
                    ->requiresConfirmation()
                    ->action(function () {
                        fn() =>
                        app(ForecastService::class)->generate(30);

                        Notification::make()
                            ->title('Forecasting started')
                            ->body('Forecast is running in background.')
                            ->success()
                            ->send();
                    }),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
