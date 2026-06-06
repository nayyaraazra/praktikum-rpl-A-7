<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerController extends Controller
{
    /**
     * Get seller dashboard stats and incoming orders.
     */
    public function getDashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan. Silakan lengkapi profil toko terlebih dahulu.'
            ], 404);
        }

        $storeId = $store->id_store;

        // Fetch orders containing products from this store
        $orders = Order::whereHas('items.product', function ($query) use ($storeId) {
            $query->where('id_store', $storeId);
        })
        ->with(['user', 'items.product' => function ($query) use ($storeId) {
            $query->where('id_store', $storeId);
        }])
        ->latest()
        ->get();

        // Calculate metrics based on these scoped orders
        $totalOrders = $orders->count();
        $waitingOrders = $orders->where('status', 'menunggu')->count();
        $processedOrders = $orders->where('status', 'diproses')->count();
        $completedOrders = $orders->where('status', 'selesai')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'metrics' => [
                    'total_orders' => $totalOrders,
                    'waiting_orders' => $waitingOrders,
                    'processed_orders' => $processedOrders,
                    'completed_orders' => $completedOrders,
                ],
                'orders' => $orders
            ]
        ], 200);
    }

    /**
     * Get seller's store profile and products.
     */
    public function getStore(Request $request): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan.'
            ], 404);
        }

        $products = Product::where('id_store', $store->id_store)
            ->with('category')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'store' => $store,
                'products' => $products
            ]
        ], 200);
    }

    /**
     * Add a product to the seller's store.
     */
    public function addProduct(Request $request): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan.'
            ], 404);
        }

        // Validate verification status
        if ($store->verification_status !== 'disetujui') {
            return response()->json([
                'success' => false,
                'message' => 'Toko Anda belum disetujui oleh admin. Anda tidak dapat menambahkan produk.'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'id_category' => 'required|exists:categories,id_category',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'min_order' => 'required|integer|min:1',
            'image_product' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_product')) {
            $file = $request->file('image_product');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Move file directly to public directory for easy access
            $file->move(public_path('uploads/products'), $fileName);
            $imagePath = '/uploads/products/' . $fileName;
        }

        $product = Product::create([
            'id_store' => $store->id_store,
            'id_category' => $request->id_category,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image_product' => $imagePath,
            'unit' => $request->unit,
            'min_order' => $request->min_order,
            'rating' => 0.00,
            'review_count' => 0,
            'is_active' => 1, // default active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan.',
            'data' => $product
        ], 201);
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(Request $request, $id_order): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:menunggu,diproses,selesai,dibatalkan'
        ]);

        $user = $request->user();
        $store = $user->store;

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan.'
            ], 404);
        }

        // Verify order belongs to this store
        $order = Order::where('id_order', $id_order)
            ->whereHas('items.product', function ($query) use ($store) {
                $query->where('id_store', $store->id_store);
            })
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan atau bukan milik toko Anda.'
            ], 404);
        }

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui.',
            'data' => $order
        ], 200);
    }

    /**
     * Get all categories.
     */
    public function getCategories(): JsonResponse
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    }

    /**
     * Update seller account and store profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $store = $user->store;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:50',
            'store_name' => 'required|string|max:255',
            'category' => 'required|string',
            'district' => 'required|string',
            'address' => 'required|string',
            'operating_hours' => 'required|string',
            'description' => 'nullable|string|max:200',
            'logo' => 'nullable|image|max:2048',
        ]);

        // 1. Update user account
        $user->name = $request->name;
        $user->phone_number = $request->phone_number;
        $user->save();

        // 2. Handle logo upload
        $logoPath = $store ? $store->logo : null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/logos'), $fileName);
            $logoPath = '/uploads/logos/' . $fileName;
        }

        // 3. Update or create store details
        Store::updateOrCreate(
            ['id_user' => $user->id_user],
            [
                'store_name' => $request->store_name,
                'store_category' => $request->category,
                'description' => $request->description,
                'address' => $request->address,
                'operating_hours' => $request->operating_hours,
                'district' => $request->district,
                'logo' => $logoPath,
                'verification_status' => $store ? $store->verification_status : 'menunggu',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => $user->load('store')
        ], 200);
    }
}
