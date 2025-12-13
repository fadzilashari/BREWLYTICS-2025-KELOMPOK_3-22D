<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dummy Admin
        $user = User::create(
            [
                'name' => 'Admin Dummy',
                'email' => 'admin@example.com',
                'password' => Hash::make('123'),
            ]
        );
        $user->assignRole('admin'); // assign role

        // Dummy Owner
        $user = User::create(
            [
                'name' => 'Owner Dummy',
                'email' => 'owner@example.com',
                'password' => Hash::make('123'),
            ]
        );
        $user->assignRole('owner'); // assign role

        // Dummy Employee
        $user = User::create(
            [
                'name' => 'Employee Dummy',
                'email' => 'employee@example.com',
                'password' => Hash::make('123'),
            ]
        );
        $user->assignRole('employee'); // assign role
    }
}
