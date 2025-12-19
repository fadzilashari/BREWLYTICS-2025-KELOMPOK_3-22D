<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Forecast;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ForecastService
{
    public function generate(int $days = 30): void
    {
        foreach (Product::all() as $product) {

            // Ambil histori penjualan
            $history = $product->salesItems()
                ->selectRaw('DATE(created_at) as date, SUM(quantity) as qty')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(fn($row) => [
                    'date' => $row->date,
                    'qty' => $row->qty,
                ])
                ->toArray();

            if (count($history) < 7) {
                continue;
            }

            // ====== PANGGIL PYTHON API ======
            $response = Http::timeout(10)
                ->post(config('services.forecast.url') . '/forecast', [
                    'product_id' => $product->id,
                    'days' => $days,
                    'history' => $history,
                ]);

            if (! $response->successful()) {
                Log::error('Forecast API failed', [
                    'product_id' => $product->id,
                ]);
                continue;
            }

            $forecastQty = $response->json('forecast_qty');

            Forecast::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'period_days' => $days,
                ],
                [
                    'forecast_qty' => $forecastQty,
                    'forecast_start' => now(),
                    'forecast_end' => now()->addDays($days),
                ]
            );
        }
    }
}
