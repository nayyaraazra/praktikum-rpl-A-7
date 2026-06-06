<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryAndProductSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key constraints to safely seed
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('reviews')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('notifications')->truncate();
        DB::table('products')->truncate();
        DB::table('categories')->truncate();

        // 1. Seed Categories
        $categoriesData = [
            ['id_category' => 1, 'name_category' => 'makanan_minuman'],
            ['id_category' => 2, 'name_category' => 'kerajinan'],
            ['id_category' => 3, 'name_category' => 'fashion_batik'],
            ['id_category' => 4, 'name_category' => 'kecantikan'],
            ['id_category' => 5, 'name_category' => 'pertanian'],
        ];

        foreach ($categoriesData as $cat) {
            Category::create($cat);
        }

        // Helper maps to assign user emails to stores
        $storeMappings = [
            'silverqueen@gmail.com' => [
                'store_name' => 'Warung Bu Sari',
                'store_category' => 'makanan_minuman',
                'description' => 'Warung makan tradisional yang menyajikan berbagai lauk pauk khas Solo sejak 1998. Spesialis tempe bacem dan sayur lodeh.',
                'address' => 'Jl. Surya No. 45, RT 02/RW 04, Jebres, Surakarta 57126',
                'operating_hours' => 'Senin–Sabtu, 07:00 – 20:00',
                'district' => 'jebres',
                'verification_status' => 'disetujui',
            ],
            'saklarlampu@gmail.com' => [
                'store_name' => 'Batik Jebres',
                'store_category' => 'fashion_batik',
                'description' => 'Menyediakan batik khas Solo kualitas premium, tulis maupun cap.',
                'address' => 'Jl. Jebres Tengah No. 12, RT 01/RW 03, Jebres, Surakarta',
                'operating_hours' => 'Setiap Hari, 09:00 – 21:00',
                'district' => 'jebres',
                'verification_status' => 'disetujui',
            ],
            'privateroom@gmail.com' => [
                'store_name' => 'Jamu Bu Lastri',
                'store_category' => 'makanan_minuman',
                'description' => 'Jamu tradisional segar berkhasiat tanpa pengawet.',
                'address' => 'Jl. Surya II No. 8, Jebres, Surakarta',
                'operating_hours' => 'Setiap Hari, 06:00 – 12:00',
                'district' => 'jebres',
                'verification_status' => 'disetujui',
            ],
            'kursimerah@gmail.com' => [
                'store_name' => 'Rajut Mbak Dewi',
                'store_category' => 'fashion_batik',
                'description' => 'Tas dan aksesoris rajut handmade cantik dan berkualitas.',
                'address' => 'Jl. Veteran No. 15, Jebres, Surakarta',
                'operating_hours' => 'Senin–Sabtu, 08:00 – 17:00',
                'district' => 'jebres',
                'verification_status' => 'disetujui',
            ],
            'tiraihijau@gmail.com' => [
                'store_name' => 'Dapur Bu Ati',
                'store_category' => 'makanan_minuman',
                'description' => 'Camilan keripik dan lauk praktis lezat buatan rumahan.',
                'address' => 'Jl. Surya No. 12, RT 03/RW 05, Jebres, Surakarta',
                'operating_hours' => 'Setiap Hari, 08:00 – 20:00',
                'district' => 'jebres',
                'verification_status' => 'disetujui',
            ],
            'greentea@gmail.com' => [
                'store_name' => 'Kerajinan Pak Hardi',
                'store_category' => 'kerajinan',
                'description' => 'Anyaman bambu tradisional Solo berkualitas tinggi untuk dekorasi.',
                'address' => 'Jl. Solo-Jebres No. 4, Jebres, Surakarta',
                'operating_hours' => 'Senin–Jumat, 08:00 – 16:00',
                'district' => 'jebres',
                'verification_status' => 'disetujui',
            ]
        ];

        // 2. Update existing stores or create them
        $stores = [];
        foreach ($storeMappings as $email => $sData) {
            $user = User::where('email', $email)->first();
            if ($user) {
                // Ensure user has seller role in DB
                $roles = $user->roles ?? [];
                if (!in_array('seller', $roles)) {
                    $roles[] = 'seller';
                    $user->update(['roles' => $roles]);
                }

                $store = Store::updateOrCreate(
                    ['id_user' => $user->id_user],
                    $sData
                );
                $stores[$sData['store_name']] = $store;
            }
        }

        // Backup fallback: if some store name wasn't created, map to first store
        $fallbackStore = count($stores) > 0 ? reset($stores) : null;

        // 3. Seed Products
        $productsData = [
            [
                'id_product' => 1,
                'store_name' => 'Warung Bu Sari',
                'category_name' => 'makanan_minuman',
                'name' => 'Tempe Bacem',
                'price' => 25000,
                'stock' => 50,
                'unit' => 'bungkus',
                'min_order' => 1,
                'rating' => 4.8,
                'review_count' => 42,
                'description' => 'Tempe bacem asli Solo dengan bumbu rempah pilihan. Dimasak dengan cara tradisional menggunakan kayu bakar, menghasilkan cita rasa autentik yang gurih dan sedikit manis. Cocok untuk lauk makan siang maupun makan malam.',
                'is_active' => 1
            ],
            [
                'id_product' => 2,
                'store_name' => 'Batik Jebres',
                'category_name' => 'fashion_batik',
                'name' => 'Batik Solo Motif Parang',
                'price' => 150000,
                'stock' => 15,
                'unit' => 'lembar',
                'min_order' => 1,
                'rating' => 4.5,
                'review_count' => 28,
                'description' => 'Batik tulis Solo asli motif Parang Rusak. Dibuat dengan teknik tulis tangan menggunakan malam panas berkualitas. Setiap lembar adalah karya unik dari pengrajin lokal Jebres. Kain katun prima 100% anti luntur.',
                'is_active' => 1
            ],
            [
                'id_product' => 3,
                'store_name' => 'Dapur Bu Ati',
                'category_name' => 'makanan_minuman',
                'name' => 'Keripik Singkong Pedas',
                'price' => 18000,
                'stock' => 100,
                'unit' => 'bungkus',
                'min_order' => 2,
                'rating' => 4.2,
                'review_count' => 67,
                'description' => 'Keripik singkong pedas level 1-3 bisa request. Dibuat dari singkong segar pilihan tanpa bahan pengawet. Dikemas higienis dalam plastik tebal anti lembab. Cocok untuk camilan santai bersama keluarga.',
                'is_active' => 1
            ],
            [
                'id_product' => 4,
                'store_name' => 'Kerajinan Pak Hardi',
                'category_name' => 'kerajinan',
                'name' => 'Anyaman Bambu Premium',
                'price' => 45000,
                'stock' => 8,
                'unit' => 'buah',
                'min_order' => 1,
                'rating' => 4.6,
                'review_count' => 15,
                'description' => 'Anyaman bambu handmade dari bambu pilihan usia 3 tahun. Cocok untuk dekorasi rumah, tempat penyimpanan, atau hadiah. Setiap produk unik karena dibuat secara manual oleh pengrajin berpengalaman 20 tahun.',
                'is_active' => 1
            ],
            [
                'id_product' => 5,
                'store_name' => 'Jamu Bu Lastri',
                'category_name' => 'makanan_minuman',
                'name' => 'Jamu Kunyit Asam',
                'price' => 8000,
                'stock' => 200,
                'unit' => 'botol',
                'min_order' => 3,
                'rating' => 4.7,
                'review_count' => 93,
                'description' => 'Jamu kunyit asam alami tanpa pengawet dan pewarna buatan. Baik untuk kesehatan lambung, meningkatkan imunitas, dan menyegarkan badan. Dibuat fresh setiap pagi dari bahan-bahan segar pilihan.',
                'is_active' => 1
            ],
            [
                'id_product' => 6,
                'store_name' => 'Rajut Mbak Dewi',
                'category_name' => 'fashion_batik',
                'name' => 'Tas Rajut Handmade',
                'price' => 85000,
                'stock' => 5,
                'unit' => 'buah',
                'min_order' => 1,
                'rating' => 4.9,
                'review_count' => 31,
                'description' => 'Tas rajut handmade dengan berbagai pilihan warna dan motif. Material benang berkualitas tinggi, tahan lama dan tidak mudah kusut. Bisa request warna dan ukuran custom dengan tambahan biaya.',
                'is_active' => 1
            ],
            [
                'id_product' => 7,
                'store_name' => 'Kerajinan Pak Hardi',
                'category_name' => 'pertanian',
                'name' => 'Pupuk Organik Kompos',
                'price' => 35000,
                'stock' => 30,
                'unit' => 'kg',
                'min_order' => 5,
                'rating' => 4.3,
                'review_count' => 19,
                'description' => 'Pupuk organik kompos murni dari limbah pertanian. Bebas bahan kimia berbahaya, cocok untuk semua jenis tanaman. Menyuburkan tanah secara alami dan meningkatkan hasil panen.',
                'is_active' => 1
            ],
            [
                'id_product' => 8,
                'store_name' => 'Jamu Bu Lastri',
                'category_name' => 'kecantikan',
                'name' => 'Lulur Beras Putih',
                'price' => 30000,
                'stock' => 45,
                'unit' => 'toples',
                'min_order' => 1,
                'rating' => 4.4,
                'review_count' => 57,
                'description' => 'Lulur beras putih tradisional untuk memutihkan dan melembutkan kulit. Formula alami dari beras, kunyit, dan rempah pilihan. Tanpa bahan kimia berbahaya, aman untuk semua jenis kulit termasuk kulit sensitif.',
                'is_active' => 1
            ]
        ];

        foreach ($productsData as $pData) {
            $cat = Category::where('name_category', $pData['category_name'])->first();
            $store = $stores[$pData['store_name']] ?? $fallbackStore;
            
            Product::create([
                'id_product' => $pData['id_product'],
                'id_store' => $store ? $store->id_store : 1,
                'id_category' => $cat ? $cat->id_category : 1,
                'name' => $pData['name'],
                'description' => $pData['description'],
                'price' => $pData['price'],
                'stock' => $pData['stock'],
                'unit' => $pData['unit'],
                'min_order' => $pData['min_order'],
                'rating' => $pData['rating'],
                'review_count' => $pData['review_count'],
                'is_active' => $pData['is_active'],
            ]);
        }

        // 4. Seed some mock reviews for Product 1 (Tempe Bacem)
        $reviewsData = [
            [
                'id_product' => 1,
                'user_email' => 'leminerale@gmail.com',
                'rating' => 5,
                'comment' => 'Tempenya enak banget, bumbunya meresap sempurna! Sudah langganan dari 3 bulan lalu.',
            ],
            [
                'id_product' => 1,
                'user_email' => 'tiraipink@gmail.com',
                'rating' => 5,
                'comment' => 'Murah, enak, dan pengirimannya cepat. Recommended banget!',
            ],
            [
                'id_product' => 1,
                'user_email' => 'leminerale@gmail.com',
                'rating' => 4,
                'comment' => 'Rasanya enak tapi porsinya agak kecil. Overall masih oke.',
            ]
        ];

        foreach ($reviewsData as $rData) {
            $user = User::where('email', $rData['user_email'])->first();
            if ($user) {
                Review::create([
                    'id_order' => 1, // dummy id_order mapping
                    'id_user' => $user->id_user,
                    'id_product' => $rData['id_product'],
                    'rating' => $rData['rating'],
                    'comment' => $rData['comment'],
                ]);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
