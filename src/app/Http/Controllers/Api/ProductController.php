<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * GET /api/seller/products
     * Daftar semua produk milik toko seller yang login.
     */
    public function index(Request $request): JsonResponse
    {
        $store = $request->user()->store;

        if (! $store) {
            return response()->json(['success' => false, 'message' => 'Toko belum terdaftar.'], 404);
        }

        $products = Product::where('id_store', $store->id_store)
            ->with('category:id_category,name_category')
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $products]);
    }

    /**
     * POST /api/seller/products
     * Tambah produk baru.
     */
    public function store(Request $request): JsonResponse
    {
        $store = $request->user()->store;

        if (! $store) {
            return response()->json(['success' => false, 'message' => 'Toko belum terdaftar.'], 404);
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price'       => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'unit'        => ['required', 'string', 'max:50'],
            'min_order'   => ['required', 'integer', 'min:1'],
            'category_name' => ['required', 'string', 'max:100'],
            'is_active'   => ['boolean'],
        ]);

        // Cari atau buat kategori
        $category = Category::firstOrCreate(
            ['name_category' => $validated['category_name']]
        );

        $product = Product::create([
            'id_store'    => $store->id_store,
            'id_category' => $category->id_category,
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price'       => $validated['price'],
            'stock'       => $validated['stock'],
            'unit'        => $validated['unit'],
            'min_order'   => $validated['min_order'],
            'is_active'   => $validated['is_active'] ?? 1,
            'rating'      => 0.00,
            'review_count'=> 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan.',
            'data'    => $product->load('category'),
        ], 201);
    }

    /**
     * PUT /api/seller/products/{id}
     * Edit produk (hanya milik toko sendiri).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $store   = $request->user()->store;
        $product = Product::where('id_store', $store?->id_store)->findOrFail($id);

        $validated = $request->validate([
            'name'          => ['sometimes', 'string', 'max:255'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'price'         => ['sometimes', 'numeric', 'min:0'],
            'stock'         => ['sometimes', 'integer', 'min:0'],
            'unit'          => ['sometimes', 'string', 'max:50'],
            'min_order'     => ['sometimes', 'integer', 'min:1'],
            'category_name' => ['sometimes', 'string', 'max:100'],
            'is_active'     => ['boolean'],
        ]);

        if (isset($validated['category_name'])) {
            $category = Category::firstOrCreate(
                ['name_category' => $validated['category_name']]
            );
            $validated['id_category'] = $category->id_category;
            unset($validated['category_name']);
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui.',
            'data'    => $product->fresh()->load('category'),
        ]);
    }

    /**
     * DELETE /api/seller/products/{id}
     * Hapus produk (hanya milik toko sendiri).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $store   = $request->user()->store;
        $product = Product::where('id_store', $store?->id_store)->findOrFail($id);

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus.',
        ]);
    }
}