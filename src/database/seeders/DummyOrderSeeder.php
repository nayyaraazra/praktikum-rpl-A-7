<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;

class DummyOrderSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get or create buyer users
        $mulyono = User::firstOrCreate(
            ['email' => 'mulyono@gmail.com'],
            [
                'name' => 'Mulyono',
                'phone_number' => '081234567890',
                'password' => Hash::make('password'),
                'roles' => ['buyer'],
            ]
        );

        $siti = User::firstOrCreate(
            ['email' => 'sitirahayu@gmail.com'],
            [
                'name' => 'Siti Rahayu',
                'phone_number' => '081234567891',
                'password' => Hash::make('password'),
                'roles' => ['buyer'],
            ]
        );

        $ahmad = User::firstOrCreate(
            ['email' => 'ahmadfauzi@gmail.com'],
            [
                'name' => 'Ahmad Fauzi',
                'phone_number' => '081234567892',
                'password' => Hash::make('password'),
                'roles' => ['buyer'],
            ]
        );

        // 2. Ensure we have the products for Store 1 (Warung Bu Sari)
        // Store 1: Warung Bu Sari (id_store = 1, id_user = 2)
        $tahu = Product::firstOrCreate(
            ['id_store' => 1, 'name' => 'Tahu Bacem'],
            [
                'id_category' => 1, // Makanan & Minuman
                'description' => 'Tahu bacem enak gurih manis khas Solo.',
                'price' => 7000.00,
                'stock' => 100,
                'unit' => 'pcs',
                'min_order' => 1,
                'is_active' => 1,
            ]
        );

        $ayam = Product::firstOrCreate(
            ['id_store' => 1, 'name' => 'Ayam Bacem'],
            [
                'id_category' => 1, // Makanan & Minuman
                'description' => 'Ayam bacem bumbu meresap gurih lezat.',
                'price' => 12000.00,
                'stock' => 50,
                'unit' => 'pcs',
                'min_order' => 1,
                'is_active' => 1,
            ]
        );

        $tempe = Product::find(1);
        if (!$tempe) {
            $tempe = Product::firstOrCreate(
                ['id_store' => 1, 'name' => 'Tempe Bacem'],
                [
                    'id_category' => 1,
                    'description' => 'Tempe bacem asli Solo dengan bumbu rempah pilihan.',
                    'price' => 25000.00,
                    'stock' => 50,
                    'unit' => 'bungkus',
                    'min_order' => 1,
                    'is_active' => 1,
                ]
            );
        }

        // 3. Delete existing orders for these buyers to prevent duplicates
        Order::whereIn('id_user', [$mulyono->id_user, $siti->id_user, $ahmad->id_user])->delete();

        // 4. Create orders
        // Order 1: Mulyono, Tempe Bacem, COD - Rp 50.000, Menunggu
        $order1 = Order::create([
            'id_user' => $mulyono->id_user,
            'order_date' => now()->subHours(2),
            'total_order' => 50000.00,
            'status' => 'menunggu',
            'payment_method' => 'cod',
            'shipping_address' => 'Jl. Surya No. 10, RT 01/RW 04, Jebres, Surakarta',
            'note' => 'Minta yang manis ya bu.',
        ]);

        OrderItem::create([
            'id_order' => $order1->id_order,
            'id_product' => $tempe->id_product,
            'quantity' => 2, // 2 pcs * 25,000 = 50,000
            'price_at_purchase' => 25000.00,
        ]);

        // Order 2: Siti Rahayu, Tahu Bacem, Transfer - Rp 70.000, Diproses
        $order2 = Order::create([
            'id_user' => $siti->id_user,
            'order_date' => now()->subHours(5),
            'total_order' => 70000.00,
            'status' => 'diproses',
            'payment_method' => 'transfer',
            'shipping_address' => 'Jl. Pucangsawit No. 12, RT 02/RW 03, Jebres, Surakarta',
            'note' => 'Sudah ditransfer ya.',
        ]);

        OrderItem::create([
            'id_order' => $order2->id_order,
            'id_product' => $tahu->id_product,
            'quantity' => 10, // 10 pcs * 7,000 = 70,000
            'price_at_purchase' => 7000.00,
        ]);

        // Order 3: Ahmad Fauzi, Ayam Bacem, COD - Rp 120.000, Selesai
        $order3 = Order::create([
            'id_user' => $ahmad->id_user,
            'order_date' => now()->subDay(),
            'total_order' => 120000.00,
            'status' => 'selesai',
            'payment_method' => 'cod',
            'shipping_address' => 'Jl. Kentingan No. 4, RT 03/RW 02, Jebres, Surakarta',
            'note' => 'Kirim jam makan siang.',
        ]);

        OrderItem::create([
            'id_order' => $order3->id_order,
            'id_product' => $ayam->id_product,
            'quantity' => 10, // 10 pcs * 12,000 = 120,000
            'price_at_purchase' => 12000.00,
        ]);
    }
}
