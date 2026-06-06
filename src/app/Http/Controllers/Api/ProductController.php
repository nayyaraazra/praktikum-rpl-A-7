<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * Search and Filter products (US-03 & US-04)
     */
    public function index(Request $request): JsonResponse
    {
        // Only fetch active products from verified (approved) stores
        $query = Product::with(['store.owner', 'category'])
            ->where('is_active', 1)
            ->whereHas('store', function ($q) {
                $q->where('verification_status', 'disetujui');
            });

        // 1. Search Query
        if ($request->has('search') && !empty($request->search)) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('store', function ($sq) use ($search) {
                      $sq->whereRaw('LOWER(store_name) LIKE ?', ["%{$search}%"]);
                  });
            });
        }

        // 2. Category Filter
        if ($request->has('category') && !empty($request->category)) {
            $categoryVal = $request->category;
            $query->whereHas('category', function ($q) use ($categoryVal) {
                $q->where('name_category', $categoryVal);
            });
        }

        // 3. Price Filter
        if ($request->filled('price_min')) {
            $query->where('price', '>=', intval($request->price_min));
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', intval($request->price_max));
        }

        // 4. In Stock Filter
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // 5. Min Rating Filter
        if ($request->filled('min_rating') && intval($request->min_rating) > 0) {
            $query->where('rating', '>=', floatval($request->min_rating));
        }

        // 6. Sorting
        switch ($request->sort_by) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating_desc':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                // Default relevance or popularity
                $query->orderBy('rating', 'desc');
                break;
        }

        $products = $query->get();

        // Map data to match frontend naming expectations
        $mappedProducts = $products->map(function ($product) {
            return [
                'id'             => $product->id_product,
                'name'           => $product->name,
                'store_name'     => $product->store->store_name ?? 'Toko',
                'store_id'       => $product->store->id_store ?? null,
                'store_rating'   => 4.8, // Fallback/default rating UMKM
                'store_district' => $product->store->district ?? 'Jebres',
                'category'       => $product->category->name_category ?? '',
                'price'          => floatval($product->price),
                'stock'          => intval($product->stock),
                'unit'           => $product->unit,
                'min_order'      => intval($product->min_order),
                'rating'         => floatval($product->rating),
                'review_count'   => intval($product->review_count),
                'description'    => $product->description,
                'image'          => $product->image_product,
                'images'         => [],
                'is_active'      => $product->is_active == 1,
                'seller_phone'   => $product->store->owner->phone_number ?? '',
                'seller_name'    => $product->store->owner->name ?? '',
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $mappedProducts,
        ]);
    }

    /**
     * GET /api/products/{id}
     * View Product Details (US-05)
     */
    public function show($id): JsonResponse
    {
        $product = Product::with(['store.owner', 'category', 'reviews.user'])
            ->where('id_product', $id)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        // Map product
        $mappedProduct = [
            'id'             => $product->id_product,
            'name'           => $product->name,
            'store_name'     => $product->store->store_name ?? 'Toko',
            'store_id'       => $product->store->id_store ?? null,
            'store_rating'   => 4.8,
            'store_district' => $product->store->district ?? 'Jebres',
            'category'       => $product->category->name_category ?? '',
            'price'          => floatval($product->price),
            'stock'          => intval($product->stock),
            'unit'           => $product->unit,
            'min_order'      => intval($product->min_order),
            'rating'         => floatval($product->rating),
            'review_count'   => intval($product->review_count),
            'description'    => $product->description,
            'image'          => $product->image_product,
            'images'         => [],
            'is_active'      => $product->is_active == 1,
            'seller_phone'   => $product->store->owner->phone_number ?? '',
            'seller_name'    => $product->store->owner->name ?? '',
        ];

        // Map reviews
        $mappedReviews = $product->reviews->map(function ($review) {
            return [
                'id'         => $review->id_review,
                'product_id' => $review->id_product,
                'user_name'  => $review->user->name ?? 'Pembeli',
                'rating'     => intval($review->rating),
                'comment'    => $review->comment,
                'date'       => $review->created_at ? $review->created_at->toDateString() : date('Y-m-d'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'product' => $mappedProduct,
                'reviews' => $mappedReviews,
            ],
        ]);
    }
}
