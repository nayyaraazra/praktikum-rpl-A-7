<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private OrderService $orderService
    ) {}

    /**
     * GET /api/orders
     * Daftar pesanan milik buyer yang login.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $orders = Order::where('id_user', $user->id_user)
            ->with(['items.product.store'])
            ->latest('order_date')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => OrderResource::collection($orders)->resolve()
        ]);
    }

    /**
     * GET /api/orders/{id}
     * Detail satu pesanan milik buyer.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $order = Order::where('id_user', $user->id_user)
            ->with(['items.product.store'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => (new OrderResource($order))->resolve()
        ]);
    }

    /**
     * POST /api/orders
     * Buat pesanan baru dari buyer (checkout langsung).
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $product = Product::with('store')->findOrFail($validated['id_product']);

        if ($errorResponse = $this->validateProductForOrder($product, $validated['quantity'])) {
            return $errorResponse;
        }

        try {
            $order = $this->orderService->placeOrder($user, $product, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat.',
                'data'    => (new OrderResource($order->load(['items.product.store'])))->resolve()
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan. Silakan coba lagi.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate product eligibility for an order.
     *
     * @param Product $product
     * @param int $quantity
     * @return JsonResponse|null Returns a JsonResponse with an error message, or null if valid.
     */
    private function validateProductForOrder(Product $product, int $quantity): ?JsonResponse
    {
        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Produk ini sedang tidak aktif.'
            ], 422);
        }

        if ($product->store && !$product->store->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Toko sedang tutup. Anda tidak dapat melakukan pemesanan saat ini.'
            ], 422);
        }

        if ($product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => "Stok tidak mencukupi. Stok tersedia: {$product->stock} {$product->unit}."
            ], 422);
        }

        if ($quantity < $product->min_order) {
            return response()->json([
                'success' => false,
                'message' => "Jumlah pesanan kurang dari minimal order ({$product->min_order} {$product->unit})."
            ], 422);
        }

        return null;
    }
}
