<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Category;
use App\Models\Product;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kategori Baru sesuai permintaan user
        $newCategories = [
            'pakaian',
            'fashion & aksesoris',
            'makanan & minuman',
            'perawatan & kecantikan',
            'perlengkapan rumah',
            'hobi & koleksi',
            'kesehatan',
            'olahraga & outdoor',
            'buku & alat tulis',
            'kerajinan tangan',
            'sembako & kebutuhan pokok',
            'Jasa & Layanan',
            'Katering',
            'lain lain',
        ];

        // Pemetaan kategori lama ke kategori baru agar data produk terhubung dengan benar
        $mapping = [
            'makanan_minuman' => 'makanan & minuman',
            'Makanan & Minuman' => 'makanan & minuman',
            'fashion_batik' => 'fashion & aksesoris',
            'Fashion & Batik' => 'fashion & aksesoris',
            'jeans' => 'fashion & aksesoris',
            'kerajinan' => 'kerajinan tangan',
            'Kerajinan Tangan' => 'kerajinan tangan',
            'kecantikan' => 'perawatan & kecantikan',
            'Kecantikan & Perawatan' => 'perawatan & kecantikan',
            'buku' => 'buku & alat tulis',
            'pertanian' => 'sembako & kebutuhan pokok',
            'Pertanian & Hasil Bumi' => 'sembako & kebutuhan pokok',
            'Elektronik & Aksesori' => 'fashion & aksesoris',
            'Jasa & Layanan' => 'Jasa & Layanan',
            'Lainnya' => 'lain lain',
            'lainnya' => 'lain lain',
        ];

        // 2. Buat semua kategori baru di database
        $createdIds = [];
        foreach ($newCategories as $catName) {
            $cat = Category::firstOrCreate(['name_category' => $catName]);
            $createdIds[$catName] = $cat->id_category;
        }

        // 3. Remap produk-produk yang sudah ada ke kategori baru
        $products = Product::all();
        foreach ($products as $prod) {
            $oldCat = Category::find($prod->id_category);
            if ($oldCat) {
                $oldName = $oldCat->name_category;
                $newName = $mapping[$oldName] ?? 'lain lain';
                if (isset($createdIds[$newName])) {
                    $prod->update(['id_category' => $createdIds[$newName]]);
                }
            }
        }

        // 4. Hapus kategori lama yang tidak ada dalam daftar kategori baru
        Category::whereNotIn('name_category', $newCategories)->delete();
    }

    public function down(): void
    {
        // down migration is empty
    }
};
