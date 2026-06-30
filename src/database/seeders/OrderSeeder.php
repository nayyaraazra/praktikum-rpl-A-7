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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil atau buat Kategori yang sesuai
        $catMakanan = Category::firstOrCreate(['name_category' => 'makanan & minuman']);
        $catFashion = Category::firstOrCreate(['name_category' => 'fashion & aksesoris']);
        $catKerajinan = Category::firstOrCreate(['name_category' => 'kerajinan tangan']);
        $catSembako = Category::firstOrCreate(['name_category' => 'sembako & kebutuhan pokok']);
        $catHobi = Category::firstOrCreate(['name_category' => 'hobi & koleksi']);
        $catKesehatan = Category::firstOrCreate(['name_category' => 'kesehatan']);
        $catBuku = Category::firstOrCreate(['name_category' => 'buku & alat tulis']);

        // 2. Daftar Toko dan Akun Seller yang akan dibuat
        $sellersData = [
            [
                'email' => 'dapurbundasari@example.com',
                'name' => 'Bunda Sari',
                'store_name' => 'Dapur Bunda Sari',
                'store_category' => 'makanan_minuman',
                'description' => 'Penyedia catering sehat dan aneka kue tradisional khas Jebres.',
                'address' => 'Jl. Ir. Sutami No. 10, Jebres, Surakarta',
                'operating_hours' => 'Senin - Sabtu, 08:00 - 17:00',
                'logo' => 'stores/Yxc7PkGUuMY8KyoeB9zbPlebKSH8aNsTiFiwgxUb.png',
                'logo_url' => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=200&h=200&fit=crop&q=80',
            ],
            [
                'email' => 'danarhadi@example.com',
                'name' => 'Bapak Danar',
                'store_name' => 'Batik Danar Solo',
                'store_category' => 'fashion_batik',
                'description' => 'Menyediakan batik tulis dan cap premium asli Solo.',
                'address' => 'Jl. Urip Sumoharjo No. 45, Jebres, Surakarta',
                'operating_hours' => 'Setiap hari, 09:00 - 21:00',
                'logo' => null,
                'logo_url' => 'https://images.unsplash.com/photo-1524295928322-4b986a49ad23?w=200&h=200&fit=crop&q=80',
            ],
            [
                'email' => 'uraaa@example.com',
                'name' => 'Uraaa Florist',
                'store_name' => 'florist',
                'store_category' => 'kerajinan',
                'description' => 'Kreasi bunga rajut tangan (crochet) indah untuk hadiah wisuda dan hiasan.',
                'address' => 'jebres, Surakarta',
                'operating_hours' => 'Setiap hari, 08:00 - 20:00',
                'logo' => 'stores/TuKtZok155mxRSOnc0YhGvmSvdcMCmk2uW7OQMv5.jpg',
                'logo_url' => null, // DO NOT DOWNLOAD, KEEP USER'S ORIGINAL!
            ],
            [
                'email' => 'sembako.barokah@example.com',
                'name' => 'Pak Joko',
                'store_name' => 'Sembako Barokah',
                'store_category' => 'pertanian',
                'description' => 'Menyediakan kebutuhan pokok harian murah dan lengkap.',
                'address' => 'Jl. Ki Hajar Dewantara No. 12, Kentingan, Jebres',
                'operating_hours' => 'Setiap hari, 06:00 - 22:00',
                'logo' => null,
                'logo_url' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?w=200&h=200&fit=crop&q=80',
            ],
            [
                'email' => 'soul@example.com',
                'name' => 'Soul Owner',
                'store_name' => 'soul~',
                'store_category' => 'Lainnya',
                'description' => 'Toko mainan koleksi, pernak-pernik pop-culture, dan hobi unik.',
                'address' => 'kulon, Jebres, Surakarta',
                'operating_hours' => 'Senin - Jumat, 10:00 - 18:00',
                'logo' => null,
                'logo_url' => 'https://images.unsplash.com/photo-1566577134770-3d85bb3a9cc4?w=200&h=200&fit=crop&q=80',
            ],
            [
                'email' => 'sehat.apotek@example.com',
                'name' => 'Apoteker Rina',
                'store_name' => 'Apotek Sehat Jebres',
                'store_category' => 'kecantikan',
                'description' => 'Apotek obat, vitamin, herbal, dan kosmetik perawatan kulit terpercaya.',
                'address' => 'Jl. Kolonel Sutarto No. 150, Jebres, Surakarta',
                'operating_hours' => 'Setiap hari, 24 Jam',
                'logo' => null,
                'logo_url' => 'https://images.unsplash.com/photo-1586015555751-63bb77f4322a?w=200&h=200&fit=crop&q=80',
            ],
            [
                'email' => 'pustakailmu@example.com',
                'name' => 'Ibu Pustaka',
                'store_name' => 'Toko Buku Pustaka Ilmu',
                'store_category' => 'buku',
                'description' => 'Toko alat tulis sekolah, buku pelajaran, komik, dan novel berkualitas.',
                'address' => 'Kawasan Belakang Kampus UNS, Jebres, Surakarta',
                'operating_hours' => 'Senin - Sabtu, 08:00 - 20:00',
                'logo' => null,
                'logo_url' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=200&h=200&fit=crop&q=80',
            ]
        ];

        // Pastikan folder exist
        Storage::disk('public')->makeDirectory('stores');
        Storage::disk('public')->makeDirectory('products');

        $stores = [];
        foreach ($sellersData as $s) {
            $user = User::firstOrCreate(
                ['email' => $s['email']],
                [
                    'name'         => $s['name'],
                    'password'     => Hash::make('password'),
                    'phone_number' => '0812' . rand(10000000, 99999999),
                    'roles'        => ['seller'],
                ]
            );

            // Logika logo
            $logoPath = $s['logo'];
            if (!$logoPath) {
                $logoPath = 'stores/store_' . $user->id_user . '.jpg';
            }

            // Hapus file lama jika BUKAN file manual bawaan user (TuKtZok155mxRSOnc0YhGvmSvdcMCmk2uW7OQMv5)
            if ($s['logo_url'] && !Str::contains($logoPath, 'TuKtZok155mxRSOnc0YhGvmSvdcMCmk2uW7OQMv5')) {
                Storage::disk('public')->delete($logoPath);
            }

            // Unduh file baru jika belum ada
            if ($s['logo_url'] && !Storage::disk('public')->exists($logoPath)) {
                try {
                    $logoData = file_get_contents($s['logo_url']);
                    if ($logoData) {
                        Storage::disk('public')->put($logoPath, $logoData);
                    } else {
                        $logoPath = null;
                    }
                } catch (\Exception $e) {
                    $logoPath = null;
                }
            }

            $stores[] = Store::firstOrCreate(
                ['id_user' => $user->id_user],
                [
                    'store_name'          => $s['store_name'],
                    'store_category'      => $s['store_category'],
                    'description'         => $s['description'],
                    'address'             => $s['address'],
                    'district'            => 'jebres',
                    'operating_hours'     => $s['operating_hours'],
                    'verification_status' => 'disetujui',
                    'store_logo'          => $logoPath,
                ]
            );
        }

        // 3. Daftar Produk
        $productsData = [
            // Store 1 (Dapur Bunda Sari)
            [
                'store_idx' => 0,
                'category_id' => $catMakanan->id_category,
                'name' => 'Nasi Box Premium Lengkap',
                'price' => 18000,
                'unit' => 'box',
                'stock' => 50,
                'rating' => 4.8,
                'review_count' => 42,
                'image' => null,
                'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop&q=80',
            ],
            [
                'store_idx' => 0,
                'category_id' => $catMakanan->id_category,
                'name' => 'Nasi Liwet Solo Spesial',
                'price' => 22000,
                'unit' => 'porsi',
                'stock' => 35,
                'rating' => 4.9,
                'review_count' => 87,
                'image' => null,
                'image_url' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=400&h=300&fit=crop&q=80',
            ],
            [
                'store_idx' => 0,
                'category_id' => $catMakanan->id_category,
                'name' => 'Kue Lemper Ketan Isi Daging',
                'price' => 5000,
                'unit' => 'pcs',
                'stock' => 100,
                'rating' => 4.9,
                'review_count' => 63,
                'image' => null,
                'image_url' => 'https://images.unsplash.com/photo-1600891964599-f61ba0e24092?w=400&h=300&fit=crop&q=80',
            ],

            // Store 2 (Batik Danar Solo)
            [
                'store_idx' => 1,
                'category_id' => $catFashion->id_category,
                'name' => 'Kemeja Batik Tulis Premium',
                'price' => 350000,
                'unit' => 'pcs',
                'stock' => 15,
                'rating' => 4.9,
                'review_count' => 18,
                'image' => null,
                'image_url' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=400&h=300&fit=crop&q=80',
            ],
            [
                'store_idx' => 1,
                'category_id' => $catFashion->id_category,
                'name' => 'Daster Batik Cap Busui-Friendly',
                'price' => 75000,
                'unit' => 'pcs',
                'stock' => 40,
                'rating' => 4.7,
                'review_count' => 31,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],
            [
                'store_idx' => 1,
                'category_id' => $catFashion->id_category,
                'name' => 'Syal Batik Sutra Halus',
                'price' => 125000,
                'unit' => 'pcs',
                'stock' => 20,
                'rating' => 4.8,
                'review_count' => 12,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],

            // Store 3 (florist)
            [
                'store_idx' => 2,
                'category_id' => $catKerajinan->id_category,
                'name' => 'lily of the valley',
                'price' => 670000,
                'unit' => 'ikat',
                'stock' => 12,
                'rating' => 4.9,
                'review_count' => 25,
                'image' => 'products/jvEQcrM3o1PokfWpxkkZhkVSHVV1MsqroLMQnS7t.jpg',
                'image_url' => null, // DO NOT DOWNLOAD, KEEP USER'S ORIGINAL!
            ],
            [
                'store_idx' => 2,
                'category_id' => $catKerajinan->id_category,
                'name' => 'Buket Bunga Rajut Wisuda Mawar',
                'price' => 95000,
                'unit' => 'buket',
                'stock' => 18,
                'rating' => 4.9,
                'review_count' => 45,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],
            [
                'store_idx' => 2,
                'category_id' => $catKerajinan->id_category,
                'name' => 'Gantungan Kunci Rajut Mini',
                'price' => 15000,
                'unit' => 'pcs',
                'stock' => 80,
                'rating' => 4.6,
                'review_count' => 29,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],

            // Store 4 (Sembako Barokah)
            [
                'store_idx' => 3,
                'category_id' => $catSembako->id_category,
                'name' => 'Beras Delanggu Premium 5kg',
                'price' => 78000,
                'unit' => 'karung',
                'stock' => 25,
                'rating' => 4.9,
                'review_count' => 112,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],
            [
                'store_idx' => 3,
                'category_id' => $catSembako->id_category,
                'name' => 'Minyak Goreng Sawit 2 Liter',
                'price' => 34000,
                'unit' => 'pouch',
                'stock' => 30,
                'rating' => 4.8,
                'review_count' => 94,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],
            [
                'store_idx' => 3,
                'category_id' => $catSembako->id_category,
                'name' => 'Gula Pasir Kristal 1kg',
                'price' => 17000,
                'unit' => 'pack',
                'stock' => 50,
                'rating' => 4.7,
                'review_count' => 78,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],

            // Store 5 (soul~)
            [
                'store_idx' => 4,
                'category_id' => $catHobi->id_category,
                'name' => 'Figure Anime Nendoroid Kawaii',
                'price' => 850000,
                'unit' => 'box',
                'stock' => 5,
                'rating' => 5.0,
                'review_count' => 9,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],
            [
                'store_idx' => 4,
                'category_id' => $catHobi->id_category,
                'name' => 'Gundam RG 1/144 Model Kit',
                'price' => 450000,
                'unit' => 'box',
                'stock' => 8,
                'rating' => 4.9,
                'review_count' => 14,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],
            [
                'store_idx' => 4,
                'category_id' => $catHobi->id_category,
                'name' => 'Kartu Koleksi Booster Pack',
                'price' => 65000,
                'unit' => 'pack',
                'stock' => 100,
                'rating' => 4.8,
                'review_count' => 36,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],

            // Store 6 (Apotek Sehat Jebres)
            [
                'store_idx' => 5,
                'category_id' => $catKesehatan->id_category,
                'name' => 'Multivitamin Imunitas Tubuh 30 Tab',
                'price' => 120000,
                'unit' => 'botol',
                'stock' => 50,
                'rating' => 4.9,
                'review_count' => 104,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],
            [
                'store_idx' => 5,
                'category_id' => $catKesehatan->id_category,
                'name' => 'Minyak Kayu Putih Asli 120ml',
                'price' => 48000,
                'unit' => 'botol',
                'stock' => 35,
                'rating' => 4.9,
                'review_count' => 143,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],
            [
                'store_idx' => 5,
                'category_id' => $catKesehatan->id_category,
                'name' => 'Masker Medis 3-Ply Box 50',
                'price' => 25000,
                'unit' => 'box',
                'stock' => 100,
                'rating' => 4.8,
                'review_count' => 89,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],

            // Store 7 (Toko Buku Pustaka Ilmu)
            [
                'store_idx' => 6,
                'category_id' => $catBuku->id_category,
                'name' => 'Buku Kamus Lengkap Inggris-Indo',
                'price' => 85000,
                'unit' => 'buku',
                'stock' => 15,
                'rating' => 4.8,
                'review_count' => 22,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],
            [
                'store_idx' => 6,
                'category_id' => $catBuku->id_category,
                'name' => 'Set Pulpen Gel Hitam Isi 12',
                'price' => 24000,
                'unit' => 'box',
                'stock' => 60,
                'rating' => 4.7,
                'review_count' => 45,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ],
            [
                'store_idx' => 6,
                'category_id' => $catBuku->id_category,
                'name' => 'Buku Tulis Sekolah Kiky Box Isi 10',
                'price' => 42000,
                'unit' => 'box',
                'stock' => 30,
                'rating' => 4.9,
                'review_count' => 74,
                'image' => null,
                'image_url' => null, // GANTI EMOTICON FALLBACK!
            ]
        ];

        $createdProducts = [];
        foreach ($productsData as $idx => $p) {
            $store = $stores[$p['store_idx']];

            // Logika gambar produk
            $imagePath = $p['image'];
            if (!$imagePath) {
                $imagePath = 'products/prod_' . ($idx + 1) . '.jpg';
            }

            // Hapus file lama jika BUKAN file manual bawaan user (jvEQcrM3o1PokfWpxkkZhkVSHVV1MsqroLMQnS7t)
            if ($p['image_url'] && !Str::contains($imagePath, 'jvEQcrM3o1PokfWpxkkZhkVSHVV1MsqroLMQnS7t')) {
                Storage::disk('public')->delete($imagePath);
            }

            // Jika image_url bernilai null, pastikan file lokal dihapus agar sistem membaca image_product = null
            if (!$p['image_url'] && !Str::contains($imagePath, 'jvEQcrM3o1PokfWpxkkZhkVSHVV1MsqroLMQnS7t')) {
                Storage::disk('public')->delete($imagePath);
                $imagePath = null;
            }

            // Unduh file baru jika belum ada
            if ($p['image_url'] && !Storage::disk('public')->exists($imagePath)) {
                try {
                    $imgData = file_get_contents($p['image_url']);
                    if ($imgData) {
                        Storage::disk('public')->put($imagePath, $imgData);
                    } else {
                        $imagePath = null;
                    }
                } catch (\Exception $e) {
                    $imagePath = null;
                }
            }

            $createdProducts[] = Product::firstOrCreate(
                ['id_store' => $store->id_store, 'name' => $p['name']],
                [
                    'id_store'    => $store->id_store,
                    'id_category' => $p['category_id'],
                    'name'        => $p['name'],
                    'price'       => $p['price'],
                    'unit'        => $p['unit'],
                    'stock'       => $p['stock'],
                    'rating'      => $p['rating'],
                    'review_count'=> $p['review_count'],
                    'is_active'   => 1,
                    'min_order'   => 1,
                    'image_product' => $imagePath,
                ]
            );
        }

        // 4. Pembeli dummy (5 pembeli)
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

        // 5. Pesanan dummy untuk memicu sold_count di Produk Populer
        $paymentMethods = ['cod', 'transfer'];
        $statuses = ['selesai', 'diproses', 'menunggu'];

        foreach ($createdProducts as $idx => $product) {
            $buyer = $buyerUsers[$idx % count($buyerUsers)];
            $qty = rand(2, 15);
            $price = $product->price;
            $total = $price * $qty;

            $order = Order::create([
                'id_user'          => $buyer->id_user,
                'order_date'       => now()->subDays(rand(1, 10))->subHours(rand(1, 12)),
                'total_order'      => $total,
                'status'           => $statuses[rand(0, count($statuses) - 1)],
                'payment_method'   => $paymentMethods[rand(0, count($paymentMethods) - 1)],
                'shipping_address' => 'Jl. Kentingan No. ' . rand(1, 50) . ', Jebres, Surakarta',
                'note'             => 'Pesanan dummy otomatis.',
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