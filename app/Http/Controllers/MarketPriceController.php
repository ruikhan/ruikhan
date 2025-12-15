<?php

namespace App\Http\Controllers;

use App\Models\MarketPrice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketPriceController extends Controller
{
    // 1. Show the Market Page
    public function index()
    {
        // Get all prices and group them by category (Fish, Meat, etc.)
        $prices = MarketPrice::all()->groupBy('category');

        return Inertia::render('Market/Index', [
            'marketPrices' => $prices
        ]);
    }

    // 2. Add a New Item
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string',
            'category' => 'required|string',
            'price' => 'required|numeric',
            'unit' => 'required|string'
        ]);

        MarketPrice::create($validated);

        return redirect()->back();
    }

    // 3. Update Price (For Admin)
    public function update(Request $request, $id)
    {
        $price = MarketPrice::findOrFail($id);
        
        $price->update([
            'price' => $request->price,
            'trend' => $request->trend ?? 'stable'
        ]);

        return redirect()->back();
    }
}