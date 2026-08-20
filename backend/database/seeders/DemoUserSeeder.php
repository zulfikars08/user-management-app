<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
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

        $directoryUsers = [
            ['Amanda Wijaya', 'amanda.wijaya@demo.test', 'user'],
            ['Bima Pratama', 'bima.pratama@demo.test', 'user'],
            ['Citra Lestari', 'citra.lestari@demo.test', 'admin'],
            ['Dimas Saputra', 'dimas.saputra@demo.test', 'user'],
            ['Farah Maharani', 'farah.maharani@demo.test', 'user'],
            ['Galang Putra', 'galang.putra@demo.test', 'user'],
            ['Intan Permata', 'intan.permata@demo.test', 'admin'],
            ['Kevin Santoso', 'kevin.santoso@demo.test', 'user'],
            ['Nadia Rahma', 'nadia.rahma@demo.test', 'user'],
            ['Raka Mahendra', 'raka.mahendra@demo.test', 'user'],
            ['Salsa Putri', 'salsa.putri@demo.test', 'user'],
            ['Taufik Hidayat', 'taufik.hidayat@demo.test', 'admin'],
            ['Vina Amelia', 'vina.amelia@demo.test', 'user'],
        ];

        foreach ($directoryUsers as [$name, $email, $role]) {
            User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => Str::random(40), 'role' => $role],
            );
        }
    }
}
