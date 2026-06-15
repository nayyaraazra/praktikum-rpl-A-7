<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerDashboardController extends Controller
{
    use HasStoreAccess;

    /**
     * GET /api/seller/dashboard
     * FR-12: Pemilik UMKM dapat melihat pesanan masuk & metrik toko.
     */
    public function index(Request $request): JsonResponse
    {
        $store = $this->getStoreOrAbort($request->user());
        $storeId = $store->id_store;

        // ── Metrik ────────────────────────────────────────────────────
        // ID produk milik toko ini
        $productIds = Product::where('id_store', $storeId)
            ->pluck('id_product');

        // Total pesanan yang mengandung produk toko ini
        $totalOrders = OrderItem::whereIn('id_product', $productIds)
            ->distinct('id_order')
            ->count('id_order');

        // Pendapatan bulan ini (status selesai saja)
        $monthlyRevenue = OrderItem::whereIn('id_product', $productIds)
            ->whereHas('order', fn ($q) =>
                $q->where('status', 'selesai')
                  ->whereMonth('order_date', now()->month)
                  ->whereYear('order_date', now()->year)
            )
            ->sum(DB::raw('quantity * price_at_purchase'));

        // Produk aktif
        $activeProducts = Product::where('id_store', $storeId)
            ->where('is_active', 1)
            ->count();

        // Produk perlu restok (stok < 5)
        $lowStockCount = Product::where('id_store', $storeId)
            ->where('is_active', 1)
            ->where('stock', '<', 5)
            ->count();

        // Pesanan baru (status menunggu)
        $newOrders = OrderItem::whereIn('id_product', $productIds)
            ->whereHas('order', fn ($q) => $q->where('status', 'menunggu'))
            ->distinct('id_order')
            ->count('id_order');

        // Pesanan diproses (status diproses)
        $processedOrders = OrderItem::whereIn('id_product', $productIds)
            ->whereHas('order', fn ($q) => $q->where('status', 'diproses'))
            ->distinct('id_order')
            ->count('id_order');

        // ── Daftar pesanan terbaru (10 terakhir) ─────────────────────
        $recentOrders = Order::whereHas('items', fn ($q) =>
                $q->whereIn('id_product', $productIds)
            )
            ->with([
                'buyer:id_user,name,phone_number',
                'items' => fn ($q) => $q->whereIn('id_product', $productIds)
                                        ->with('product:id_product,name,unit'),
            ])
            ->latest('order_date')
            ->take(10)
            ->get()
            ->map(fn ($order) => [
                'id_order'        => $order->id_order,
                'buyer_name'      => $order->buyer?->name,
                'buyer_phone'     => $order->buyer?->phone_number,
                'products'        => $order->items->map(fn ($item) => [
                    'name'     => $item->product?->name,
                    'quantity' => $item->quantity,
                    'unit'     => $item->product?->unit,
                ]),
                'total_order'     => $order->total_order,
                'status'          => $order->status,
                'payment_method'  => $order->payment_method,
                'shipping_address'=> $order->shipping_address,
                'order_date'      => $order->order_date?->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'store'   => [
                    'store_name'          => $store->store_name,
                    'verification_status' => $store->verification_status,
                ],
                'metrics' => [
                    'total_orders'     => $totalOrders,
                    'monthly_revenue'  => (float) $monthlyRevenue,
                    'active_products'  => $activeProducts,
                    'low_stock_count'  => $lowStockCount,
                    'new_orders'       => $newOrders,
                    'processed_orders' => $processedOrders,
                ],
                'recent_orders' => $recentOrders,
            ],
        ]);
    }

    /**
     * GET /api/seller/orders/{id}
     * Detail satu pesanan (milik toko seller yang login).
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $store = $this->getStoreOrAbort($request->user());
        $productIds = Product::where('id_store', $store->id_store)
            ->pluck('id_product');

        $order = Order::whereHas('items', fn ($q) =>
                $q->whereIn('id_product', $productIds)
            )
            ->with([
                'buyer:id_user,name,phone_number',
                'items.product:id_product,name,unit,price',
            ])
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $order]);
    }

    /**
     * PUT /api/seller/orders/{id}/status
     * Ubah status pesanan (milik toko seller yang login).
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $store = $this->getStoreOrAbort($request->user());
        $productIds = Product::where('id_store', $store->id_store)->pluck('id_product');

        $order = Order::whereHas('items', fn ($q) =>
                $q->whereIn('id_product', $productIds)
            )->findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'in:menunggu,diproses,selesai,dibatalkan'],
        ]);

        $order->update(['status' => $validated['status']]);

        // Kirim notifikasi ke pembeli
        $statusLabels = [
            'menunggu'   => 'Menunggu',
            'diproses'   => 'Diproses',
            'selesai'    => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
        $label = $statusLabels[$validated['status']] ?? $validated['status'];

        Notification::create([
            'id_user'  => $order->id_user,
            'id_order' => $order->id_order,
            'message'  => "Status pesanan Anda #{$order->id_order} telah diubah menjadi: {$label}.",
            'is_read'  => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui.',
        ]);
    }
}