<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ProductWithHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define Products
        $productsData = [
            ['name' => 'Kopi Hitam (Hot)', 'price' => 6000],
            ['name' => 'Kopi Susu (Hot)', 'price' => 8000],
            ['name' => 'Kopi Susu (Ice)', 'price' => 10000],
            ['name' => 'Cappucino (Hot)', 'price' => 8000],
            ['name' => 'Cappucino (Ice)', 'price' => 10000],
            ['name' => 'Vanila Latte (Hot)', 'price' => 13000],
            ['name' => 'Vanila Latte (Ice)', 'price' => 15000],
            ['name' => 'Gula Aren (Hot)', 'price' => 13000],
            ['name' => 'Gula Aren (Ice)', 'price' => 15000],
            ['name' => 'Simple Caramel (Hot)', 'price' => 13000],
            ['name' => 'Simple Caramel (Ice)', 'price' => 15000],
            ['name' => 'Kopi Botol', 'price' => 15000],
            ['name' => 'Dynamic Iceland', 'price' => 15000],
            ['name' => 'Galaxy', 'price' => 15000],
            ['name' => 'Green Squash', 'price' => 15000],
            ['name' => 'Choco Milo Dino', 'price' => 12000],
            ['name' => 'Choco Latte Milk', 'price' => 12000],
            ['name' => 'Redvelvet Milk', 'price' => 12000],
            ['name' => 'Macha Milk', 'price' => 13000],
            ['name' => 'Air Mineral (Hot)', 'price' => 4000],
            ['name' => 'Air Mineral (Ice)', 'price' => 5000],
            ['name' => 'Teh (Hot)', 'price' => 4000],
            ['name' => 'Teh (Ice)', 'price' => 5000],
            ['name' => 'Jeruk Peras (Hot)', 'price' => 5000],
            ['name' => 'Jeruk Peras (Ice)', 'price' => 6000],
            ['name' => 'Lemon Tea (Hot)', 'price' => 7000],
            ['name' => 'Lemon Tea (Ice)', 'price' => 8000],
            ['name' => 'Bandrek Susu', 'price' => 15000],
            ['name' => 'Jahe Susu', 'price' => 12000],
            ['name' => 'Kukubima Susu', 'price' => 10000],
            ['name' => 'ExtraJoss Susu', 'price' => 10000],
            ['name' => 'Badak', 'price' => 20000],
            ['name' => 'TST (Teh Susu Telor)', 'price' => 15000],
            ['name' => 'Es Batu', 'price' => 1000],
        ];

        // 2. Clear Tables
        Schema::disableForeignKeyConstraints();
        DB::table('sales_items')->truncate();
        DB::table('sales')->truncate();
        DB::table('products')->truncate();
        DB::table('transactions')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::transaction(function () use ($productsData) {
            $insertedProducts = [];
            foreach ($productsData as $prod) {
                $id = DB::table('products')->insertGetId([
                    'name'       => $prod['name'],
                    'price'      => $prod['price'],
                    'stock'      => rand(10, 20),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $insertedProducts[] = ['id' => $id, 'price' => $prod['price']];
            }

            // Get Owner and Employee users for realistic cashier assignment
            $cashierIds = DB::table('users')
                ->whereIn('email', ['owner@example.com', 'employee@example.com'])
                ->pluck('id')
                ->toArray();
            
            // Fallback to ID 1 if no owner/employee found
            if (empty($cashierIds)) {
                $cashierIds = [1];
            }

            // 3. Generate History (Transaction-based)
            $days = 30;
            for ($i = $days; $i >= 1; $i--) {
                $date = Carbon::now()->subDays($i)->startOfDay();
                
                // Transactions per Day (10 to 15 transactions as per user request)
                $transactionCount = rand(10, 15); 

                for ($t = 0; $t < $transactionCount; $t++) {
                    $txDate = $date->copy()->addHours(rand(8, 22))->addMinutes(rand(0, 59));
                    $userId = $cashierIds[array_rand($cashierIds)];

                    // Basket Size: 1 to 3 distinct items
                    $basketSize = rand(1, 3);
                    $pickedKeys = (array) array_rand($insertedProducts, $basketSize);
                    
                    $totalAmount = 0;
                    $saleItems = [];

                    foreach ($pickedKeys as $key) {
                        $prod = $insertedProducts[$key];
                        $qty = rand(1, 4); 
                        $lineTotal = $prod['price'] * $qty;
                        $totalAmount += $lineTotal;

                        $saleItems[] = [
                            'product_id' => $prod['id'],
                            'quantity'   => $qty,
                            'price'      => $prod['price'],
                            'total'      => $lineTotal,
                        ];
                    }

                    // Insert Sale Header
                    $saleId = DB::table('sales')->insertGetId([
                        'user_id'      => $userId,
                        'sale_date'    => $txDate,
                        'total_amount' => $totalAmount,
                        'created_at'   => $txDate,
                        'updated_at'   => $txDate,
                    ]);

                    // Insert Sale Items
                    foreach ($saleItems as $item) {
                        DB::table('sales_items')->insert(array_merge($item, [
                            'sale_id'    => $saleId,
                            'created_at' => $txDate, 
                            'updated_at' => $txDate,
                        ]));
                    }

                    // Insert Transaction Record
                    DB::table('transactions')->insert([
                        'sale_id'    => $saleId,
                        'amount'     => $totalAmount,
                        'user_id'    => $userId,
                        'type'       => 'sale',
                        'created_at' => $txDate,
                        'updated_at' => $txDate,
                    ]);
                }
            }
        });
    }
}
