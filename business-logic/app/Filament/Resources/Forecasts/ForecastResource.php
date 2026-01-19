<?php

namespace App\Filament\Resources\Forecasts;

use App\Filament\Resources\Forecasts\Pages\CreateForecast;
use App\Filament\Resources\Forecasts\Pages\EditForecast;
use App\Filament\Resources\Forecasts\Pages\ListForecasts;
use App\Filament\Resources\Forecasts\Schemas\ForecastForm;
use App\Filament\Resources\Forecasts\Tables\ForecastsTable;
use App\Models\Forecast;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ForecastResource extends Resource
{
    protected static ?string $model = \App\Models\ForecastReport::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Forecast';

    protected static ?string $recordTitleAttribute = 'Forecast';

    protected static string | UnitEnum | null $navigationGroup = 'Analytics';

    public static function form(Schema $schema): Schema
    {
        return ForecastForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForecastsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForecasts::route('/'),
            'create' => CreateForecast::route('/create'),
            'edit' => EditForecast::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->roles->whereIn('name', ['admin', 'owner'])->isNotEmpty() ?? false;
    }
}
