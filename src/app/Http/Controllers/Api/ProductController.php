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
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

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
            'image'       => ['nullable', 'image', 'max:2048'],
        ]);

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
            'data'    => $this->formatProduct($product->load('category')),
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
            'image'         => ['nullable', 'image', 'max:2048'],
        ]);

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
            'data'    => $this->formatProduct($product->fresh()->load('category')),
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
            'data'    => $this->formatDetailItem($product),
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
                'store:id_store,store_name,district',
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

        $products->transform(fn ($p) => $this->formatPopularItem($p));

        return response()->json([
            'success' => true,
            'data'    => $products,
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
                'store:id_store,store_name,district',
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

        // Transform items
        $products->getCollection()->transform(fn($p) => $this->formatCatalogItem($p));

        return response()->json([
            'success' => true,
            'data'    => $products->items(),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    private function formatDetailItem(Product $product): array
    {
        $store = $product->store;

        return [
            'id_product'   => $product->id_product,
            'name'         => $product->name,
            'description'  => $product->description,
            'price'        => $product->price,
            'stock'        => $product->stock,
            'unit'         => $product->unit,
            'min_order'    => $product->min_order,
            'rating'       => $product->rating,
            'review_count' => $product->review_count,
            'image_url'    => $product->image_product
                ? asset('storage/' . $product->image_product)
                : null,
            'category'     => $product->category
                ? ['name_category' => $product->category->name_category]
                : null,
            'store'        => $store
                ? [
                    'store_name'      => $store->store_name,
                    'logo'            => $store->store_logo ? asset('storage/' . $store->store_logo) : null,
                    'description'     => $store->description,
                    'address'         => $store->address,
                    'district'        => $store->district,
                    'operating_hours' => $store->operating_hours,
                    'phone_number'    => $store->owner?->phone_number,
                    'payment_accounts' => $store->paymentAccounts->map(fn($pa) => [
                        'bank_name'      => $pa->bank_name,
                        'account_number' => $pa->account_number,
                        'account_name'   => $pa->account_name,
                        'qris_code'      => $pa->qris_code,
                    ]),
                ]
                : null,
            'reviews'      => $product->reviews->map(fn($r) => [
                'rating'     => $r->rating,
                'comment'    => $r->comment,
                'user'       => $r->user ? ['name' => $r->user->name] : null,
                'created_at' => $r->created_at,
            ]),
        ];
    }

    private function formatCatalogItem(Product $product): array
    {
        return [
            'id_product'   => $product->id_product,
            'name'         => $product->name,
            'price'        => $product->price,
            'unit'         => $product->unit,
            'stock'        => $product->stock,
            'rating'       => $product->rating,
            'review_count' => $product->review_count,
            'image_url'    => $product->image_product
                ? asset('storage/' . $product->image_product)
                : null,
            'store'        => $product->store
                ? [
                    'store_name' => $product->store->store_name,
                    'district'   => $product->store->district,
                ]
                : null,
            'category'     => $product->category
                ? [
                    'name_category' => $product->category->name_category,
                ]
                : null,
        ];
    }

    private function formatPopularItem(Product $product): array
    {
        $data = $this->formatCatalogItem($product);
        $data['sold_count'] = (int) $product->sold_count;
        return $data;
    }

    private function formatProduct(Product $product): array
    {
        $data = $product->toArray();
        $data['image_url'] = $product->image_product
            ? asset('storage/' . $product->image_product)
            : null;
        return $data;
    }
}