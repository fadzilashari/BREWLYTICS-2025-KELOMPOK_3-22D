<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\SaleSeeder;
use Database\Seeders\SalesItemsSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\DummyUserSeeder;
use Database\Seeders\RewardSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            ProductWithHistorySeeder::class,
            SaleSeeder::class,
            SalesItemsSeeder::class,
            RewardSeeder::class,
        ]);
    }
}
