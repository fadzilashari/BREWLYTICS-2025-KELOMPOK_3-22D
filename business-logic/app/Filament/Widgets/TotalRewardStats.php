<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Reward;

class TotalRewardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Review', Reward::count()),
            Stat::make('Rata-rata Rating', number_format(Reward::avg('rating'), 1)),
        ];
    }
}
