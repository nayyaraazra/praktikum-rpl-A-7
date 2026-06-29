<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCatalogResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductPopularResource;
use App\Http\Resources\ProductSellerResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use HasStoreAccess;

    /**
     * GET /api/seller/products
     * Daftar semua produk milik toko seller yang login.
     */
    public function index(Request $request): JsonResponse
    {
        $store = $this->getStoreOrAbort($request->user());

        $products = Product::where('id_store', $store->id_store)
            ->with('category:id_category,name_category')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => ProductSellerResource::collection($products)->resolve()
        ]);
    }

    /**
     * POST /api/seller/products
     * Tambah produk baru.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $store = $this->getStoreOrAbort($request->user());
        $validated = $request->validated();

        // Cari atau buat kategori
        $category = Category::firstOrCreate(
            ['name_category' => $validated['category_name']]
        );

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'id_store'      => $store->id_store,
            'id_category'   => $category->id_category,
            'name'          => $validated['name'],
            'description'   => $validated['description'] ?? null,
            'price'         => $validated['price'],
            'stock'         => $validated['stock'],
            'unit'          => $validated['unit'],
            'min_order'     => $validated['min_order'],
            'is_active'     => $validated['is_active'] ?? 1,
            'image_product' => $imagePath,
            'rating'        => 0.00,
            'review_count'  => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan.',
            'data'    => (new ProductSellerResource($product->load('category')))->resolve(),
        ], 201);
    }

    /**
     * PUT /api/seller/products/{id}
     * Edit produk (hanya milik toko sendiri).
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $store   = $this->getStoreOrAbort($request->user());
        $product = Product::where('id_store', $store->id_store)->findOrFail($id);
        $validated = $request->validated();

        if (isset($validated['category_name'])) {
            $category = Category::firstOrCreate(
                ['name_category' => $validated['category_name']]
            );
            $validated['id_category'] = $category->id_category;
            unset($validated['category_name']);
        }

        if ($request->hasFile('image')) {
            $validated['image_product'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui.',
            'data'    => (new ProductSellerResource($product->fresh()->load('category')))->resolve(),
        ]);
    }

    /**
     * DELETE /api/seller/products/{id}
     * Hapus produk (hanya milik toko sendiri).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $store   = $this->getStoreOrAbort($request->user());
        $product = Product::where('id_store', $store->id_store)->findOrFail($id);

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus.',
        ]);
    }

    /**
     * GET /api/products/{id}
     * Detail produk publik — hanya produk published.
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::published()
            ->with([
                'category:id_category,name_category',
                'store' => function ($q) {
                    $q->with([
                        'owner:id_user,phone_number',
                        'paymentAccounts' => function ($q) {
                            $q->where('is_active', 1);
                        },
                    ]);
                },
                'reviews' => function ($q) {
                    $q->with('user:id_user,name')
                        ->latest()
                        ->take(5);
                },
            ])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => (new ProductDetailResource($product))->resolve(),
        ]);
    }

    /**
     * GET /api/products/popular
     * Produk populer berdasarkan jumlah terjual (dari order_items) lalu rating tertinggi.
     */
    public function popular(): JsonResponse
    {
        $products = Product::published()
            ->with([
                'store:id_store,store_name,district,operating_hours',
                'category:id_category,name_category',
            ])
            ->select('products.*')
            ->selectSub(function ($q) {
                $q->from('order_items')
                  ->join('orders', 'orders.id_order', '=', 'order_items.id_order')
                  ->whereColumn('order_items.id_product', 'products.id_product')
                  ->whereNotIn('orders.status', ['dibatalkan'])
                  ->selectRaw('COALESCE(SUM(order_items.quantity), 0)');
            }, 'sold_count')
            ->orderBy('sold_count', 'desc')
            ->orderBy('rating', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => ProductPopularResource::collection($products)->resolve(),
        ]);
    }

    /**
     * GET /api/products
     * Katalog produk publik (buyer & guest) — hanya produk published.
     */
    public function catalog(Request $request): JsonResponse
    {
        $query = Product::published()
            ->with([
                'store:id_store,store_name,district,operating_hours',
                'category:id_category,name_category',
            ]);

        // Keyword search (name + description)
        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Category filter
        if ($category = $request->input('category')) {
            $query->where('id_category', $category);
        }

        // Price range
        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Sort by rating DESC
        $query->orderBy('rating', 'desc');

        // Paginate 12 per page
        $perPage = min((int) $request->input('per_page', 12), 48);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => ProductCatalogResource::collection($products->getCollection())->resolve(),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ],
        ]);
    }
}