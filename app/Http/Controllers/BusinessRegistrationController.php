<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class BusinessRegistrationController extends Controller
{
    /**
     * Show the "Upgrade to Business" page.
     * Use this to show the pricing tiers and the business details form.
     */
    public function create()
    {
        return Inertia::render('Business/Register', [
            // We can pass the pricing info to the frontend here if we want dynamic pricing
            'plans' => [
                'basic' => ['price' => 100, 'months' => 1],
                'standard' => ['price' => 150, 'months' => 1],
                'premium' => ['price' => 200, 'months' => 2],
            ]
        ]);
    }

    /**
     * Handle the form submission.
     */
    public function store(Request $request)
    {
        // 1. Validate the Input
        $request->validate([
            'business_name' => 'required|string|max:255|unique:businesses,name',
            'description' => 'required|string|max:1000',
            'category' => 'required|in:retail,service', // The two tracks you defined
            'tier' => 'required|in:basic,standard,premium',
        ]);

        $user = Auth::user();

        // Prevent user from having multiple businesses (if that's a rule)
        if ($user->business()->exists()) {
            return back()->withErrors(['message' => 'You already have a business profile.']);
        }

        // 2. Determine Price and Duration based on Tier
        $tierDetails = $this->getTierDetails($request->tier);
        
        // 3. Create the Business Profile
        $business = Business::create([
            'user_id' => $user->id,
            'name' => $request->business_name,
            'description' => $request->description,
            'category' => $request->category,
            'status' => 'closed', // Default to closed until they open it
            'is_verified' => $request->tier === 'premium', // Instant verification for Premium? (Optional logic)
            'theme_settings' => $request->tier === 'basic' ? null : ['color' => 'default'], // Placeholder for theme
        ]);

        // 4. Create the Subscription Record
        Subscription::create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'tier' => $request->tier,
            'price_paid' => $tierDetails['price'],
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonths($tierDetails['months']),
        ]);

        // 5. Update User Role (Optional)
        // If you want to officially change their role from 'resident' to 'business_owner'
        $user->update(['role' => 'business_owner']);

        // 6. Redirect to their new Dashboard
        return redirect()->route('business.dashboard')->with('success', 'Business registered successfully!');
    }

    /**
     * Helper to get price and duration
     */
    private function getTierDetails($tier)
    {
        $plans = [
            'basic' => ['price' => 100.00, 'months' => 1],
            'standard' => ['price' => 150.00, 'months' => 1],
            'premium' => ['price' => 200.00, 'months' => 2],
        ];

        return $plans[$tier];
    }
}