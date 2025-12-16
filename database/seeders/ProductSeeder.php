<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Business;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $business = Business::first();

        if ($business) {
            $products = [
                [
                    'name' => 'Premium Coffee Beans',
                    'description' => 'Locally sourced premium coffee beans from Batangas',
                    'price' => 299.00,
                    'stock' => 50,
                    'category' => 'food',
                    'images' => null,
                    'is_available' => true,
                ],
                [
                    'name' => 'Organic Rice 5kg',
                    'description' => 'Fresh organic rice from local farms in Nueva Ecija',
                    'price' => 250.00,
                    'stock' => 100,
                    'category' => 'food',
                    'images' => null,
                    'is_available' => true,
                ],
                [
                    'name' => 'Local Honey 500g',
                    'description' => 'Pure honey from local beekeepers',
                    'price' => 499.00,
                    'stock' => 25,
                    'category' => 'food',
                    'images' => null,
                    'is_available' => true,
                ],
                [
                    'name' => 'Fresh Vegetables Bundle',
                    'description' => 'Daily fresh vegetable bundle (lettuce, tomatoes, carrots)',
                    'price' => 150.00,
                    'stock' => 30,
                    'category' => 'food',
                    'images' => null,
                    'is_available' => true,
                ],
                [
                    'name' => 'Homemade Pandesal (10pcs)',
                    'description' => 'Freshly baked pandesal every morning',
                    'price' => 35.00,
                    'stock' => 200,
                    'category' => 'food',
                    'images' => null,
                    'is_available' => true,
                ],
                [
                    'name' => 'Bottled Water 1L',
                    'description' => 'Purified drinking water',
                    'price' => 25.00,
                    'stock' => 150,
                    'category' => 'beverages',
                    'images' => null,
                    'is_available' => true,
                ],
            ];

            foreach ($products as $product) {
                $business->products()->create($product);
            }
        }
    }
}