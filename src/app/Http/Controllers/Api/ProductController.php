<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get active products list with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::where('is_active', 1)
            ->with(['category', 'store']);

        // Filter by Search term (Product name or Store name)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('store', function ($sq) use ($search) {
                      $sq->where('store_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Category name (mapping to name_category in DB)
        if ($request->has('category') && !empty($request->category) && $request->category !== 'semua') {
            $category = $request->category;
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name_category', $category);
            });
        }

        // Filter by Price range
        if ($request->has('min_price') && $request->min_price !== '') {
            $query->where('price', '>=', floatval($request->min_price));
        }

        if ($request->has('max_price') && $request->max_price !== '') {
            $query->where('price', '<=', floatval($request->max_price));
        }

        $products = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }

    /**
     * Get a single product details.
     */
    public function show($id): JsonResponse
    {
        $product = Product::with(['category', 'store'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ], 200);
    }
}
