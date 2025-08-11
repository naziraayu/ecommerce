<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\OrderItem;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user yang sudah ada (misal ID 9 & 10)
        $user1 = User::find(4);
        $user2 = User::find(5);

        // Ambil produk yang sudah ada (misal ID 1 & 2)
        $product1 = Product::find(5);
        $product2 = Product::find(6);

        // Buat order pertama
        $order1 = Order::create([
            'user_id' => $user1->id,
            'total_price' => 2 * $product1->price + 1 * $product2->price,
            'status' => 'completed',
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => $product1->price,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => $product2->price,
        ]);

        // Buat order kedua
        $order2 = Order::create([
            'user_id' => $user2->id,
            'total_price' => 3 * $product2->price,
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $product2->id,
            'quantity' => 3,
            'price' => $product2->price,
        ]);
    }
}
