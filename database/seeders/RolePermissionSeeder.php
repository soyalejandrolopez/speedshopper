<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $client = Role::firstOrCreate(['name' => 'client']);

        $adminPermissions = [
            'manage customers',
            'manage requests',
            'manage packages',
            'manage shipments',
            'manage payments',
            'manage settings',
        ];

        foreach ($adminPermissions as $permission) {
            $admin->givePermissionTo(Permission::firstOrCreate(['name' => $permission]));
        }

        $client->givePermissionTo(Permission::firstOrCreate(['name' => 'create requests']));
    }
}
