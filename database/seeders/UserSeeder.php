<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'      => 'Admin',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // Customer
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'      => 'User Demo',
                'password'  => Hash::make('password'),
                'role'      => 'user',
                'is_active' => true,
            ]
        );
    }
}
