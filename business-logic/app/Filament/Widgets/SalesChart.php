<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class SalesChart extends ChartWidget
{
    protected ?string $heading = 'Grafik Penjualan Harian';
    
    protected string $color = 'success';

    protected function getData(): array
    {
        $data = Sale::query()
            ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as total')
            ->where('sale_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan',
                    'data' => $data->map(fn ($value) => $value->total),
                ],
            ],
            'labels' => $data->map(fn ($value) => Carbon::parse($value->date)->format('d M')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
