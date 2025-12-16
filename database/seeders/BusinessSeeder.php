<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\Subscription;
use App\Models\User;

class BusinessSeeder extends Seeder
{
    public function run()
    {
        $businessOwner = User::where('email', 'maria@epili.local')->first();

        if ($businessOwner) {
            // Create Business
            $business = Business::create([
                'user_id' => $businessOwner->id,
                'name' => 'Tindahan ni Maria',
                'description' => 'Your friendly neighborhood sari-sari store with fresh products daily',
                'category' => 'retail',
                'status' => 'open',
                'is_verified' => true,
                'theme_settings' => null,
            ]);

            // Create Premium Subscription
            Subscription::create([
                'user_id' => $businessOwner->id,
                'business_id' => $business->id,
                'tier' => 'premium',
                'price_paid' => 200.00,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(2),
            ]);
        }
    }
}