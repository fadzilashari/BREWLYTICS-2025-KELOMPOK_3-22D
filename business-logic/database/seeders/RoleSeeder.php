<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $employee = Role::firstOrCreate(['name' => 'employee']);

        $permissions = [
            'manage products',
            'manage sales',
            'manage transactions',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'web'
            ]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $employee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]); // Create permission if it doesn't exist
        }

        // Assign permissions to roles
        // $admin->givePermissionTo(Permission::all()); // Admin gets all permissions
        // $owner->givePermissionTo(['manage sales', 'manage products', 'view dashboard']); // Owner specific permissions
        // $employee->givePermissionTo(['view dashboard']); // Employee specific permissions
    }
}
