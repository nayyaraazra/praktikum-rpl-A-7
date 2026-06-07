<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Kategori ──────────────────────────────────────────────
        $cat = Category::firstOrCreate(['name_category' => 'Makanan & Minuman']);

        // ── 2. Seller (Dapur Bunda Sari) ────────────────────────────
        $seller = User::firstOrCreate(
            ['email' => 'dapurbundasari@example.com'],
            [
                'name'         => 'Bunda Sari',
                'password'     => Hash::make('password'),
                'phone_number' => '081234567890',
                'roles'        => ['seller'],
            ]
        );

        $store = Store::firstOrCreate(
            ['id_user' => $seller->id_user],
            [
                'store_name'          => 'Dapur Bunda Sari',
                'store_category'      => 'makanan_minuman',
                'description'         => 'Penyedia catering sehat dan aneka kue tradisional.',
                'address'             => 'Jl. Ir. Sutami No. 10, Jebres, Surakarta',
                'district'            => 'jebres',
                'operating_hours'     => 'Senin - Sabtu, 08:00 - 17:00',
                'verification_status' => 'disetujui',
            ]
        );

        // ── 3. Produk milik toko ──────────────────────────────────────
        $products = [
            [
                'name'        => 'Nasi Box Premium Lengkap',
                'price'       => 18000,
                'unit'        => 'box',
                'stock'       => 50,
                'is_active'   => 1,
                'rating'      => 4.8,
                'review_count'=> 42,
            ],
            [
                'name'        => 'Nasi Liwet Solo Spesial',
                'price'       => 22000,
                'unit'        => 'porsi',
                'stock'       => 30,
                'is_active'   => 1,
                'rating'      => 4.9,
                'review_count'=> 87,
            ],
            [
                'name'        => 'Kue Lemper Ketan Isi',
                'price'       => 5000,
                'unit'        => 'pcs',
                'stock'       => 100,
                'is_active'   => 1,
                'rating'      => 4.9,
                'review_count'=> 63,
            ],
            [
                'name'        => 'Catering Acara 50 Pax',
                'price'       => 750000,
                'unit'        => 'paket',
                'stock'       => 5,
                'is_active'   => 1,
                'rating'      => 5.0,
                'review_count'=> 8,
            ],
        ];

        $createdProducts = [];
        foreach ($products as $p) {
            $createdProducts[] = Product::firstOrCreate(
                ['id_store' => $store->id_store, 'name' => $p['name']],
                array_merge($p, [
                    'id_store'    => $store->id_store,
                    'id_category' => $cat->id_category,
                    'min_order'   => 1,
                ])
            );
        }

        // ── 4. Pembeli dummy ──────────────────────────────────────────
        $buyers = [
            ['name' => 'Rizki Darmawan',  'email' => 'rizki@example.com',  'phone' => '081211110001'],
            ['name' => 'Siti Fatimah',    'email' => 'siti@example.com',   'phone' => '081211110002'],
            ['name' => 'Ahmad Fauzi',     'email' => 'ahmad@example.com',  'phone' => '081211110003'],
            ['name' => 'Dina Puspita',    'email' => 'dina@example.com',   'phone' => '081211110004'],
            ['name' => 'Nayyara Aqila',   'email' => 'nayyara@example.com','phone' => '081211110005'],
        ];

        $buyerUsers = [];
        foreach ($buyers as $b) {
            $buyerUsers[] = User::firstOrCreate(
                ['email' => $b['email']],
                [
                    'name'         => $b['name'],
                    'password'     => Hash::make('password'),
                    'phone_number' => $b['phone'],
                    'roles'        => ['buyer'],
                ]
            );
        }

        // ── 5. Pesanan dummy ──────────────────────────────────────────
        $ordersData = [
            [
                'buyer_idx'       => 0,
                'product_idx'     => 0,
                'quantity'        => 3,
                'status'          => 'menunggu',
                'payment_method'  => 'cod',
                'shipping_address'=> 'Jl. Jebres Indah No. 5, Surakarta',
                'note'            => 'Tolong sampai sebelum jam 12 siang.',
                'order_date'      => now()->subHours(2),
            ],
            [
                'buyer_idx'       => 1,
                'product_idx'     => 1,
                'quantity'        => 2,
                'status'          => 'diproses',
                'payment_method'  => 'transfer',
                'shipping_address'=> 'Jl. Sumber Baru No. 12, Surakarta',
                'note'            => '',
                'order_date'      => now()->subHours(5),
            ],
            [
                'buyer_idx'       => 2,
                'product_idx'     => 3,
                'quantity'        => 1,
                'status'          => 'selesai',
                'payment_method'  => 'transfer',
                'shipping_address'=> 'Gedung Serbaguna Jebres, Surakarta',
                'note'            => 'Untuk acara rapat kantor 50 orang.',
                'order_date'      => now()->subDays(2),
            ],
            [
                'buyer_idx'       => 3,
                'product_idx'     => 2,
                'quantity'        => 5,
                'status'          => 'menunggu',
                'payment_method'  => 'cod',
                'shipping_address'=> 'Pasar Kliwon Blok B No. 7, Surakarta',
                'note'            => '',
                'order_date'      => now()->subHours(1),
            ],
            [
                'buyer_idx'       => 4,
                'product_idx'     => 0,
                'quantity'        => 10,
                'status'          => 'selesai',
                'payment_method'  => 'transfer',
                'shipping_address'=> 'Jl. Kentingan No. 36A, Jebres, Surakarta',
                'note'            => 'Untuk seminar mahasiswa.',
                'order_date'      => now()->subDays(5),
            ],
            [
                'buyer_idx'       => 0,
                'product_idx'     => 1,
                'quantity'        => 4,
                'status'          => 'dibatalkan',
                'payment_method'  => 'cod',
                'shipping_address'=> 'Jl. Jebres Indah No. 5, Surakarta',
                'note'            => '',
                'order_date'      => now()->subDays(3),
            ],
            [
                'buyer_idx'       => 1,
                'product_idx'     => 2,
                'quantity'        => 20,
                'status'          => 'selesai',
                'payment_method'  => 'transfer',
                'shipping_address'=> 'Jl. Sumber Baru No. 12, Surakarta',
                'note'            => 'Untuk hajatan.',
                'order_date'      => now()->subDays(7),
            ],
        ];

        foreach ($ordersData as $od) {
            $product  = $createdProducts[$od['product_idx']];
            $buyer    = $buyerUsers[$od['buyer_idx']];
            $qty      = $od['quantity'];
            $price    = $product->price;
            $total    = $price * $qty;

            $order = Order::create([
                'id_user'          => $buyer->id_user,
                'order_date'       => $od['order_date'],
                'total_order'      => $total,
                'status'           => $od['status'],
                'payment_method'   => $od['payment_method'],
                'shipping_address' => $od['shipping_address'],
                'note'             => $od['note'],
            ]);

            OrderItem::create([
                'id_order'          => $order->id_order,
                'id_product'        => $product->id_product,
                'quantity'          => $qty,
                'price_at_purchase' => $price,
            ]);
        }
    }
}