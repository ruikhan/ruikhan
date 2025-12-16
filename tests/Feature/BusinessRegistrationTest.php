<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Business;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_resident_can_register_a_business_and_subscription()
    {
        // 1. Create a User (Resident)
        $user = User::factory()->create();

        // 2. Act: Send a POST request to register a Premium business
        $response = $this->actingAs($user)->post('/business/register', [
            'business_name' => 'My Awesome Startup',
            'description' => 'A tech startup in the local incubation center.',
            'category' => 'service',
            'tier' => 'premium', // Should give 2 months access
        ]);

        // 3. Assert: Check Redirection
        $response->assertRedirect(route('business.dashboard'));

        // 4. Assert: Database has the Business
        $this->assertDatabaseHas('businesses', [
            'user_id' => $user->id,
            'name' => 'My Awesome Startup',
            'status' => 'closed',
            'is_verified' => true, // Premium users get verified immediately (based on our controller logic)
        ]);

        // 5. Assert: Database has the Subscription
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'tier' => 'premium',
            'price_paid' => 200.00,
        ]);

        // 6. Assert: Subscription Dates are correct (Premium = 2 months)
        $subscription = Subscription::where('user_id', $user->id)->first();
        $this->assertTrue($subscription->expires_at->gt(now()->addMonth())); 
    }
}