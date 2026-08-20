<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = env('DEMO_ADMIN_PASSWORD');
        $userPassword = env('DEMO_USER_PASSWORD');

        if (! is_string($adminPassword) || strlen($adminPassword) < 8
            || ! is_string($userPassword) || strlen($userPassword) < 8) {
            throw new RuntimeException('Set DEMO_ADMIN_PASSWORD and DEMO_USER_PASSWORD to at least 8 characters.');
        }

        User::updateOrCreate(
            ['email' => env('DEMO_ADMIN_EMAIL', 'admin@user-management.test')],
            ['name' => 'Demo Admin', 'password' => $adminPassword, 'role' => 'admin'],
        );

        User::updateOrCreate(
            ['email' => env('DEMO_USER_EMAIL', 'user@user-management.test')],
            ['name' => 'Demo User', 'password' => $userPassword, 'role' => 'user'],
        );
    }
}
