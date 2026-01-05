<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
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

        foreach ($products as $product) {
            DB::table('products')->insert(array_merge($product, [
                'stock' => rand(10, 20),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
    }
}
