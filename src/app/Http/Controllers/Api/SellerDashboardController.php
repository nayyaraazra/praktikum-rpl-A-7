<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use App\Services\SellerDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellerDashboardController extends Controller
{
    use HasStoreAccess;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        private SellerDashboardService $dashboardService
    ) {}

    /**
     * GET /api/seller/dashboard
     * FR-12: Pemilik UMKM dapat melihat pesanan masuk & metrik toko.
     */
    public function index(Request $request): JsonResponse
    {
        $store = $this->getStoreOrAbort($request->user());
        $storeId = $store->id_store;

        // Retrieve product IDs, metrics, and recent orders via SellerDashboardService
        $productIds = $this->dashboardService->getStoreProductIds($storeId);
        $metrics = $this->dashboardService->getMetrics($storeId, $productIds);
        $recentOrders = $this->dashboardService->getRecentOrders($productIds);

        return response()->json([
            'success' => true,
            'data' => [
                'store'   => [
                    'store_name'          => $store->store_name,
                    'verification_status' => $store->verification_status,
                ],
                'metrics'       => $metrics,
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
        $productIds = $this->dashboardService->getStoreProductIds($store->id_store);

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
        $productIds = $this->dashboardService->getStoreProductIds($store->id_store);

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