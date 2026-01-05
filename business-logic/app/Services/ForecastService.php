<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Forecast;
use App\Models\ForecastReport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ForecastService
{
    public function getForecast($productId)
    {
        $url = config('services.fastapi.url') . "/forecast/{$productId}";

        $response = Http::get($url);

        if ($response->failed()) {
            throw new \Exception("FastAPI error");
        }

        return $response->json();
    }

    public function generate(int $days = 30): void
    {
        // Increase execution time to 5 minutes
        set_time_limit(300);

        $port = env('FASTAPI_PORT', 8025);
        $host = env('FASTAPI_HOST', 'ai-service'); // Use 'ai-service' for Docker, 'localhost' for local dev
        $baseUrl = "http://{$host}:{$port}";

        foreach (Product::all() as $index => $product) {
            
            Log::info("Processing Forecast for Product ID: {$product->id} (Index: {$index})");

            // 1. Trigger Forecast generation (Pull from DB)
            try {
                $response = Http::timeout(120)->post("{$baseUrl}/forecast-from-db/{$product->id}", [
                    'days' => $days
                ]);

                if (!$response->successful()) {
                    Log::error("Forecast failed for Product {$product->id}", $response->json());
                    continue;
                }
                
                $data = $response->json();
                
                // Calculate total forecast qty
                $forecastQty = collect($data['forecast_data'] ?? [])->sum('yhat');
                Log::info("Forecast done for Product ID: {$product->id}. Total Qty: {$forecastQty}");

            } catch (\Exception $e) {
                Log::error("Forecast exception for Product {$product->id}: " . $e->getMessage());
                continue;
            }

            // 2. (REMOVED) Individual PDF Download inside loop
            // Kita skip download per-produk agar lebih cepat & hemat storage.

            // 3. Save to Database
            Forecast::updateOrCreate(
                [
                    'product_id' => $product->id,
                ],
                [
                    'period_days' => $days,
                    'forecast_qty' => (int) $forecastQty,
                    'forecast_start' => now(),
                    'forecast_end' => now()->addDays($days),
                    // 'file_path' => $pdfPath, // Tidak simpan individual file path dulu
                ]
            );
        }
        Log::info("Forecast Numbers Updated. Generating Master PDF...");

        // 4. Generate & Download Master PDF (Unified Report)
        try {
            // Increase timeout for generating large PDF
            $pdfResponse = Http::timeout(300)->get("{$baseUrl}/forecast-pdf-all");
            
            if ($pdfResponse->successful()) {
                // Format: forecast_2026-01-03_12-00-00.pdf
                // Note: Windows does not allow colons (:) in filenames. Using dashes (-) instead.
                $fileName = "forecast_" . date('Y-m-d_H-i-s') . ".pdf"; 
                $path = "forecasts/{$fileName}";
                \Illuminate\Support\Facades\Storage::disk('public')->put($path, $pdfResponse->body());
                
                Log::info("Master PDF saved at: {$path}");
                
                // Save to ForecastReport Table
                ForecastReport::create([
                    'file_name' => $fileName,
                    'file_path' => $path,
                    'period_days' => $days,
                    'generated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Master PDF generation failed: " . $e->getMessage());
        }

        Log::info("Forecast All Products Completed.");
    }

    function getProductHistory(int $productId)
    {
        return DB::select("
        SELECT 
            DATE(s.sale_date) AS date,
            SUM(si.quantity) AS quantity
        FROM sales_items si
        JOIN sales s ON s.id = si.sale_id
        WHERE si.product_id = ?
        GROUP BY DATE(s.sale_date)
        ORDER BY date ASC
        LIMIT 7
    ", [$productId]);
    }
}
