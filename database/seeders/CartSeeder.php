<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $userId = 2;

        // Ambil 3 produk pertama
        $products = Product::take(3)->get();

        foreach ($products as $product) {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'quantity' => rand(1, 5), // jumlah random antara 1-5
            ]);
        }
    }
}
