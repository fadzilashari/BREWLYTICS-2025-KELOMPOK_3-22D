<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role sudah ada
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        // Dummy Admin
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123'),
            ]
        );
        $user->assignRole('admin'); // assign role

        // Dummy Owner
        $user = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Owner',
                'password' => Hash::make('123'),
            ]
        );
        $user->assignRole('owner'); // assign role

        // Dummy Employee
        $user = User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Employee',
                'password' => Hash::make('123'),
            ]
        );
        $user->assignRole('employee'); // assign role
    }
}
