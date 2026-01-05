<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopProductsChart extends ChartWidget
{
    protected ?string $heading = 'Top 5 Produk Terlaris';
    
    protected string $color = 'info';

    protected function getData(): array
    {
        try {
            // Mengambil data dari table sales_items (karena product_id ada di sana)
            $results = DB::table('sales_items')
                ->join('products', 'sales_items.product_id', '=', 'products.id')
                ->select('products.name', DB::raw('SUM(sales_items.quantity) as total_qty'))
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get();

            if ($results->isEmpty()) {
                return [
                    'datasets' => [['label' => 'No Data', 'data' => [0]]],
                    'labels' => ['Tidak ada data'],
                ];
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Jumlah Terjual',
                        'data' => $results->pluck('total_qty')->map(fn($v) => (int) $v)->toArray(),
                    ],
                ],
                'labels' => $results->pluck('name')->toArray(),
            ];
        } catch (\Exception $e) {
            // Jika error, tampilkan label error agar kita tahu di UI
            return [
                'datasets' => [['label' => 'Error', 'data' => [0]]],
                'labels' => ['Gagal memuat data: ' . substr($e->getMessage(), 0, 20)],
            ];
        }
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
