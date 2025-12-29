<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@pili.com'], // Find by email
            [
                'name' => 'Admin User',
                'email' => 'admin@pili.com',
                'password' => Hash::make('09481527264heuki'), // Change this!
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}