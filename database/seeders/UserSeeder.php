<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'), // change for production
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Normal user
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Normal User',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
    }
}
