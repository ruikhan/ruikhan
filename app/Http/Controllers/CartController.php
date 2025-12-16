<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    // Show Cart Page
    public function index()
    {
        $cart = session()->get('cart', []);
        
        // Calculate total
        $total = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        return Inertia::render('Marketplace/Cart/Index', [
            'cart' => $cart,
            'total' => $total
        ]);
    }

    // Add Item to Cart
    public function add(Product $product)
    {
        $cart = session()->get('cart', []);

        // If product exists, increment quantity
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            // Add new product
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->images[0] ?? null,
                'business_name' => $product->business->name,
                'quantity' => 1
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Added to cart!');
    }

    // Remove Item
    public function remove($id)
    {
        $cart = session()->get('cart', []);
        
        if(isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back();
    }
}