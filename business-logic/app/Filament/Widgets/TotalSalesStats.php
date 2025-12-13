<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Sale;

class TotalSalesStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalSales = Sale::sum('total');

        return [
            Stat::make('Total Penjualan', 'Rp ' . number_format($totalSales, 0, ',', '.'))
                ->description('Akumulasi semua transaksi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
