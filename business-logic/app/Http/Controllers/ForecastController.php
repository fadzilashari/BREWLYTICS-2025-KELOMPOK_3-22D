<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Product;


class ForecastController extends Controller
{
    public function generate($productId)
    {
        // Ambil produk
        $product = Product::findOrFail($productId);

        // Ambil data historis penjualan
        $data = DB::select("
            SELECT 
                DATE(s.sale_date) AS date, 
                SUM(si.quantity) AS quantity
            FROM sales_items si
            JOIN sales s ON s.id = si.sale_id
            WHERE si.product_id = ?
            GROUP BY DATE(s.sale_date)
            ORDER BY date ASC
        ", [$productId]);

        // Panggil API Python (FastAPI)
        $port = env('FASTAPI_PORT', 8025);
        $response = Http::timeout(30)->post("http://localhost:{$port}/forecast", [
            'product_id' => $productId,
            'data' => $data,
            'forecast_days' => 30,
            'current_stock' => $product->stock
        ]);

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Forecast service unavailable'
            ], 500);
        }

        return response()->json($response->json());
    }

    public function generateFromDb($productId)
    {
        $port = env('FASTAPI_PORT', 8025);
        
        // Panggil endpoint baru yang membaca langsung dari DB
        $response = Http::timeout(30)->post("http://localhost:{$port}/forecast-from-db/{$productId}", [
            'days' => 30
        ]);

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Forecast service unavailable or error in processing',
                'details' => $response->json()
            ], $response->status());
        }

    }

    public function downloadPdf($productId)
    {
        $port = env('FASTAPI_PORT', 8025);
        $url = "http://localhost:{$port}/forecast-pdf/{$productId}";

        // Use Http client with longer timeout (e.g., 120 seconds)
        // because forecasting might take time if the server is busy.
        return response()->streamDownload(function () use ($url) {
            $response = Http::timeout(120)->get($url);
            
            if ($response->successful()) {
                echo $response->body();
            } else {
                // If failed, maybe output error or nothing (client will see empty/broken PDF)
                // Ideally we should handle this better, but for streamDownload this is tricky.
                echo "Error generating PDF: " . $response->status();
            }
        }, "forecast_{$productId}.pdf");
    }
}
