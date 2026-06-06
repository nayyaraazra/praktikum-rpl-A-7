<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of active products from verified stores.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::query()
                ->where('is_active', 1)
                ->whereHas('store', function ($q) {
                    $q->where('verification_status', 'disetujui');
                });

            // Filter by search
            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by category (by id or by category name)
            if ($request->has('category') && $request->category !== '') {
                $category = $request->category;
                $query->whereHas('category', function ($q) use ($category) {
                    if (is_numeric($category)) {
                        $q->where('id_category', $category);
                    } else {
                        $q->where('name_category', $category);
                    }
                });
            }

            // Filter by district (store district)
            if ($request->has('district') && $request->district !== '') {
                $district = $request->district;
                $query->whereHas('store', function ($q) use ($district) {
                    $q->where('district', $district);
                });
            }

            // Filter by price range
            if ($request->has('min_price') && $request->min_price !== '') {
                $query->where('price', '>=', (float) $request->min_price);
            }
            if ($request->has('max_price') && $request->max_price !== '') {
                $query->where('price', '<=', (float) $request->max_price);
            }

            // Sort products
            $sort = $request->input('sort', 'terbaru');
            if ($sort === 'termurah') {
                $query->orderBy('price', 'asc');
            } elseif ($sort === 'termahal') {
                $query->orderBy('price', 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Eager load store and category relations
            $products = $query->with([
                'store:id_store,store_name,district,verification_status', 
                'category:id_category,name_category'
            ])->paginate(12);

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products)->response()->getData(true)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show($id): JsonResponse
    {
        try {
            $product = Product::with([
                'store', // load all fields including description, address, operating_hours
                'category'
            ])->findOrFail($id);

            // Check if store verification status is approved
            if ($product->store->verification_status !== 'disetujui') {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product)
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail produk.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
