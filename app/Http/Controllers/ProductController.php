<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $business = auth()->user()->business;
        
        if (!$business) {
            return redirect()->route('business.register')
                ->with('error', 'Please register your business first.');
        }

        // FIXED: Removed withCount until tables exist
        $products = $business->products()
            ->latest()
            ->paginate(12);

        return Inertia::render('Business/Products/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Business/Products/Create', [
            'categories' => $this->getCategories(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|max:5120', // 5MB max per image
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
        }

        // Create product
        auth()->user()->business->products()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'category' => $validated['category'] ?? null,
            'images' => $imagePaths,
            'is_available' => true,
        ]);

        return redirect()->route('business.products.index')
            ->with('success', 'Product added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Check ownership
        if ($product->business_id !== auth()->user()->business->id) {
            abort(403, 'Unauthorized');
        }

        // FIXED: Removed load until tables exist
        // $product->load(['likes', 'reviews.user']);

        return Inertia::render('Business/Products/Show', [
            'product' => $product,
            'averageRating' => 0, // Temporary
            'totalLikes' => 0, // Temporary
            'totalReviews' => 0, // Temporary
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Check ownership
        if ($product->business_id !== auth()->user()->business->id) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Business/Products/Edit', [
            'product' => $product,
            'categories' => $this->getCategories(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Check ownership
        if ($product->business_id !== auth()->user()->business->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'is_available' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('business.products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Check ownership
        if ($product->business_id !== auth()->user()->business->id) {
            abort(403, 'Unauthorized');
        }

        // Delete images from storage
        if ($product->images) {
            foreach ($product->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $product->delete();

        return redirect()->route('business.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Toggle product availability
     */
    public function toggleAvailability(Product $product)
    {
        // Check ownership
        if ($product->business_id !== auth()->user()->business->id) {
            abort(403, 'Unauthorized');
        }

        $product->update([
            'is_available' => !$product->is_available
        ]);

        return back()->with('success', 'Product availability updated!');
    }

    /**
     * Get product categories
     */
    private function getCategories()
    {
        return [
            ['value' => 'food', 'label' => 'Food & Beverages'],
            ['value' => 'clothing', 'label' => 'Clothing & Apparel'],
            ['value' => 'electronics', 'label' => 'Electronics'],
            ['value' => 'home', 'label' => 'Home & Garden'],
            ['value' => 'health', 'label' => 'Health & Beauty'],
            ['value' => 'sports', 'label' => 'Sports & Outdoors'],
            ['value' => 'books', 'label' => 'Books & Media'],
            ['value' => 'toys', 'label' => 'Toys & Games'],
            ['value' => 'automotive', 'label' => 'Automotive'],
            ['value' => 'other', 'label' => 'Other'],
        ];
    }
}