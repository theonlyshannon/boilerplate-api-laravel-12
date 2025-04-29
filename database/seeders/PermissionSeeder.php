<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User permissions
        Permission::create(['name' => 'users-list']);
        Permission::create(['name' => 'users-create']);
        Permission::create(['name' => 'users-edit']);
        Permission::create(['name' => 'users-delete']);

        // Brand permissions
        Permission::create(['name' => 'brands-list']);
        Permission::create(['name' => 'brands-create']);
        Permission::create(['name' => 'brands-edit']);
        Permission::create(['name' => 'brands-delete']);

        // Assign permissions to roles
        $adminRole = Role::findByName('admin');
        $userRole = Role::findByName('user');

        // Admin gets all permissions
        $adminRole->givePermissionTo([
            'users-list',
            'users-create',
            'users-edit',
            'users-delete',

            'brands-list',
            'brands-create',
            'brands-edit',
            'brands-delete'
        ]);

        // User only gets view permissions
        $userRole->givePermissionTo([
            'brands-list',
            'users-list',
        ]);
    }
}
