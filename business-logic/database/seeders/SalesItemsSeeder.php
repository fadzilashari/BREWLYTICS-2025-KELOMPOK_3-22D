<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sales = DB::table('sales')->get();
        $products = DB::table('products')->get();

        foreach ($sales as $sale) {
            $totalSaleAmount = 0;

            // Setiap sale memiliki 1–4 item
            $itemsCount = rand(1, 4);
            $selectedProducts = $products->random($itemsCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 5);
                $price = $product->price;
                $total = $price * $quantity;

                DB::table('sales_items')->insert([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $total,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $totalSaleAmount += $total;
            }

            // Update total_amount di sales
            DB::table('sales')
                ->where('id', $sale->id)
                ->update([
                    'total_amount' => $totalSaleAmount,
                    'updated_at' => Carbon::now(),
                ]);
        }
    }
}
