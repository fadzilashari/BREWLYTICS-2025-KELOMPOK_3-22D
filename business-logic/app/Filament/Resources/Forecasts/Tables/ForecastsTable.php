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
use App\Models\Forecast;

class ForecastsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('generated_at')
                    ->label('Generated At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('file_name')
                    ->label('File Name')
                    ->searchable(),

                TextColumn::make('period_days')
                    ->label('Period (Days)'),
            ])
            ->defaultSort('generated_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('download_pdf')
                    ->label('Download')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (\App\Models\ForecastReport $record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
                
                DeleteBulkAction::make(),
            ])
            ->toolbarActions([
                Action::make('generate')
                    ->label('Generate New Forecast')
                    ->icon('heroicon-o-cpu-chip')
                    ->requiresConfirmation()
                    ->action(function () {
                        try {
                            // Execute directly (synchronous)
                            app(ForecastService::class)->generate(30);

                            // Log aktivitas manual
                            activity()
                                ->log('Menjalankan forecast');

                            Notification::make()
                                ->title('Forecasting started')
                                ->body('New report generated successfully.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Log::error('Forecast generation failed: ' . $e->getMessage());
                            
                            Notification::make()
                                ->title('Forecast generation failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }
}
