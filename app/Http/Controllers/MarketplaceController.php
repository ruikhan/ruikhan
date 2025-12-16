<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Business;
use Illuminate\Support\Facades\DB;

class MarketplaceController extends Controller
{
    /**
     * Display all businesses in the marketplace
     * Accessible by all authenticated users (residents/customers)
     */
    public function index(Request $request)
    {
        $query = Business::with(['owner', 'subscriptions' => function($q) {
            $q->where('expires_at', '>', now())->latest();
        }])
        ->whereHas('subscriptions', function($q) {
            $q->where('expires_at', '>', now());
        });

        // Filter by category
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort by featured (premium first)
        $businesses = $query->get()->map(function($business) {
            $subscription = $business->subscriptions->first();
            
            return [
                'id' => $business->id,
                'name' => $business->name,
                'description' => $business->description,
                'category' => $business->category,
                'status' => $business->status,
                'is_verified' => $business->is_verified,
                'tier' => $subscription->tier ?? 'basic',
                'owner_name' => $business->owner->name,
                // Mock data - replace with real reviews/likes later
                'rating' => rand(35, 50) / 10,
                'reviews_count' => rand(10, 100),
                'likes_count' => rand(50, 500),
            ];
        })->sortByDesc(function($business) {
            // Premium businesses appear first
            $tierWeight = ['premium' => 3, 'standard' => 2, 'basic' => 1];
            return $tierWeight[$business['tier']] ?? 0;
        })->values();

        return Inertia::render('Marketplace/Index', [
            'businesses' => $businesses,
            'filters' => [
                'category' => $request->category,
                'status' => $request->status,
                'search' => $request->search,
            ],
            'categories' => [
                ['value' => 'retail', 'label' => 'Retail & Resale'],
                ['value' => 'service', 'label' => 'Service & Hospitality'],
            ],
            'statuses' => [
                ['value' => 'open', 'label' => 'Open Now'],
                ['value' => 'available', 'label' => 'Available'],
                ['value' => 'closed', 'label' => 'Closed'],
            ]
        ]);
    }

    /**
     * Show individual business details
     */
public function show($id)
{
    $business = Business::with(['owner', 'subscriptions' => function($q) {
        $q->where('expires_at', '>', now())->latest();
    }])->findOrFail($id);

    $subscription = $business->subscriptions->first();
    if (!$subscription) {
        abort(404, 'Business not found or subscription expired.');
    }

    // ✅ FIXED: Fetch products and transform image paths
    $products = $business->products()
        ->where('is_available', true)
        ->latest()
        ->get()
        ->map(function ($product) {
            // Check if images exist and is an array
            if (!empty($product->images) && is_array($product->images)) {
                $product->images = array_map(function ($path) {
                    // Convert "products/image.jpg" to "http://site.com/storage/products/image.jpg"
                    return asset('storage/' . $path);
                }, $product->images);
            }
            return $product;
        });

    return Inertia::render('Marketplace/Show', [
        'business' => [
            // ... (keep your existing business mapping)
            'id' => $business->id,
            'name' => $business->name,
            'description' => $business->description,
            'category' => $business->category,
            'status' => $business->status,
            'is_verified' => $business->is_verified,
            'tier' => $subscription->tier,
            'owner_name' => $business->owner->name,
            'owner_phone' => $business->owner->phone_number,
            'rating' => 4.7,
            'reviews_count' => 87,
            'likes_count' => 234,
            'hours' => [
                'monday' => '8:00 AM - 6:00 PM',
            ],
        ],
        'products' => $products 
    ]);
}

    /**
     * Like/Unlike a business
     */
    public function toggleLike(Request $request, $id)
    {
        $business = Business::findOrFail($id);
        
        // TODO: Implement likes table and logic
        // For now, just return success
        
        return back()->with('success', 'Business liked!');
    }

    /**
     * Submit a review
     */
    public function submitReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
        ]);

        $business = Business::findOrFail($id);
        
        // TODO: Implement reviews table and logic
        // Review::create([
        //     'user_id' => auth()->id(),
        //     'business_id' => $business->id,
        //     'rating' => $request->rating,
        //     'comment' => $request->comment,
        // ]);

        return back()->with('success', 'Review submitted successfully!');
    }
}