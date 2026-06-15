<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SellerDashboardService
{
    /**
     * Get product IDs belonging to a specific store.
     *
     * @param int $storeId
     * @return Collection
     */
    public function getStoreProductIds(int $storeId): Collection
    {
        return Product::where('id_store', $storeId)->pluck('id_product');
    }

    /**
     * Get store metrics for the dashboard.
     *
     * @param int $storeId
     * @param Collection $productIds
     * @return array
     */
    public function getMetrics(int $storeId, Collection $productIds): array
    {
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

        return [
            'total_orders'     => $totalOrders,
            'monthly_revenue'  => (float) $monthlyRevenue,
            'active_products'  => $activeProducts,
            'low_stock_count'  => $lowStockCount,
            'new_orders'       => $newOrders,
            'processed_orders' => $processedOrders,
        ];
    }

    /**
     * Get recent orders for the store dashboard.
     *
     * @param Collection $productIds
     * @param int $limit
     * @return Collection
     */
    public function getRecentOrders(Collection $productIds, int $limit = 10): Collection
    {
        return Order::whereHas('items', fn ($q) =>
                $q->whereIn('id_product', $productIds)
            )
            ->with([
                'buyer:id_user,name,phone_number',
                'items' => fn ($q) => $q->whereIn('id_product', $productIds)
                                        ->with('product:id_product,name,unit'),
            ])
            ->latest('order_date')
            ->take($limit)
            ->get()
            ->map(fn ($order) => [
                'id_order'         => $order->id_order,
                'buyer_name'       => $order->buyer?->name,
                'buyer_phone'      => $order->buyer?->phone_number,
                'products'         => $order->items->map(fn ($item) => [
                    'name'     => $item->product?->name,
                    'quantity' => $item->quantity,
                    'unit'     => $item->product?->unit,
                ]),
                'total_order'      => $order->total_order,
                'status'           => $order->status,
                'payment_method'   => $order->payment_method,
                'shipping_address' => $order->shipping_address,
                'order_date'       => $order->order_date?->toIso8601String(),
            ]);
    }
}
