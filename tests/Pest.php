<?php

use App\Models\Customer;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

function seedRoles(): void
{
    app(RolePermissionSeeder::class)->run();
    app(SettingsSeeder::class)->run();
}

function createAdmin(): User
{
    seedRoles();
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    return $admin;
}

function createClient(?Customer $customer = null): User
{
    seedRoles();
    $user = User::factory()->create();
    $user->assignRole('client');
    $customer ??= Customer::create([
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
    ]);

    return $user;
}
