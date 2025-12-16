<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Business;
use App\Models\Subscription;

class BusinessDashboardController extends Controller
{
    /**
     * Display the business owner's dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $business = $user->business;

        // Redirect to registration if no business
        if (!$business) {
            return redirect()->route('business.register')
                ->with('info', 'Please register your business first.');
        }

        // Get current subscription
        $subscription = $business->subscriptions()
            ->orderBy('created_at', 'desc')
            ->first();

        // Calculate days until expiry
        $daysUntilExpiry = $subscription->expires_at->diffInDays(now());
        
        // Check if subscription is expired
        if (!$subscription->isValid()) {
            return redirect()->route('business.register')
                ->with('warning', 'Your subscription has expired. Please renew to continue.');
        }

        // Mock stats (replace with real queries later)
        $stats = [
            'today_revenue' => 1250.50,
            'weekly_revenue' => 8750.00,
            'monthly_revenue' => 35420.00,
            'total_orders' => 142,
            'pending_orders' => 8,
            'completed_orders' => 134,
            'inventory_count' => 24,
            'low_stock_items' => 3,
            'total_reviews' => 87,
            'average_rating' => 4.7,
            'total_likes' => 234,
        ];

        return Inertia::render('Business/Dashboard', [
            'user' => [
                'name' => $user->name,
                'business_name' => $business->name,
                'subscription_tier' => $subscription->tier,
                'subscription_expires' => $subscription->expires_at->format('Y-m-d'),
                'verified' => $business->is_verified,
                'business_type' => $business->category,
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Update business status (Available, Open, Closed, Full)
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:available,open,closed,full'
        ]);

        $business = Auth::user()->business;
        $business->update(['status' => $request->status]);

        return back()->with('success', 'Business status updated successfully.');
    }

    /**
     * Update business information
     */
    public function update(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'category' => 'required|in:retail,service',
        ]);

        $business = Auth::user()->business;
        $business->update([
            'name' => $request->business_name,
            'category' => $request->category,
        ]);

        return back()->with('success', 'Business information updated successfully.');
    }
}