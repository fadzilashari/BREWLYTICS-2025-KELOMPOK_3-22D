<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\TotalSalesStats;
use App\Filament\Widgets\TotalRewardStats;
use App\Filament\Widgets\SalesChart;
use App\Filament\Widgets\TopProductsChart;
use BackedEnum;

class Dashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            TotalSalesStats::class,
            TotalRewardStats::class,
            SalesChart::class,
            TopProductsChart::class,
        ];
    }
}
