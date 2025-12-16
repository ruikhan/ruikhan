<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@epili.local',
            'password' => Hash::make('password'),
            'phone_number' => '09171234567',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Business Owner
        User::create([
            'name' => 'Maria Santos',
            'email' => 'maria@epili.local',
            'password' => Hash::make('password'),
            'phone_number' => '09181234567',
            'role' => 'business_owner',
            'email_verified_at' => now(),
        ]);

        // Regular Residents
        User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@epili.local',
            'password' => Hash::make('password'),
            'phone_number' => '09191234567',
            'role' => 'resident',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Pedro Reyes',
            'email' => 'pedro@epili.local',
            'password' => Hash::make('password'),
            'phone_number' => '09201234567',
            'role' => 'resident',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Ana Cruz',
            'email' => 'ana@epili.local',
            'password' => Hash::make('password'),
            'phone_number' => '09211234567',
            'role' => 'resident',
            'email_verified_at' => now(),
        ]);
    }
}