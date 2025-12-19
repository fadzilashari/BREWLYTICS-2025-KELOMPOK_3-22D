<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->pluck('id');

        // Pastikan user ada
        if ($users->isEmpty()) {
            return;
        }

        // Buat 30 transaksi (misalnya 1 bulan)
        for ($i = 1; $i <= 30; $i++) {
            DB::table('sales')->insert([
                'user_id' => $users->random(),
                'sale_date' => Carbon::now()->subDays(rand(0, 29)),
                'total_amount' => 0, // akan diupdate oleh SalesItemSeeder
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
