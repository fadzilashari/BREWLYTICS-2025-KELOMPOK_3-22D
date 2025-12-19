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
        $response = Http::timeout(30)->post('http://localhost:8000/forecast', [
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
}
