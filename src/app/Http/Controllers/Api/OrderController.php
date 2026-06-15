<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
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

        if ($product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => "Stok tidak mencukupi. Stok tersedia: {$product->stock} {$product->unit}."
            ], 422);
        }

        if ($validated['quantity'] < $product->min_order) {
            return response()->json([
                'success' => false,
                'message' => "Jumlah pesanan kurang dari minimal order ({$product->min_order} {$product->unit})."
            ], 422);
        }

        $totalOrder = $product->price * $validated['quantity'];

        DB::beginTransaction();
        try {
            // 1. Update nama & no whatsapp user di profile
            $user->update([
                'name'         => $validated['name'],
                'phone_number' => $validated['phone_number'],
            ]);

            // 2. Buat Order
            $order = Order::create([
                'id_user'          => $user->id_user,
                'total_order'      => $totalOrder,
                'status'           => 'menunggu',
                'payment_method'   => $validated['payment_method'],
                'shipping_address' => $validated['shipping_address'],
                'note'             => $validated['note'] ?? null,
            ]);

            // 3. Buat Order Item
            OrderItem::create([
                'id_order'          => $order->id_order,
                'id_product'        => $product->id_product,
                'quantity'          => $validated['quantity'],
                'price_at_purchase' => $product->price,
            ]);

            // 4. Kurangi stok produk
            $product->decrement('stock', $validated['quantity']);

            // 5. Buat notifikasi untuk penjual (pemilik toko)
            if ($product->store) {
                Notification::create([
                    'id_user'  => $product->store->id_user,
                    'id_order' => $order->id_order,
                    'message'  => "Pesanan baru masuk: {$validated['quantity']} {$product->unit} {$product->name} dari {$validated['name']}.",
                    'is_read'  => 0,
                ]);
            }

            // 6. Buat notifikasi untuk pembeli
            Notification::create([
                'id_user'  => $user->id_user,
                'id_order' => $order->id_order,
                'message'  => "Pesanan Anda untuk {$product->name} telah berhasil dibuat dan menunggu konfirmasi penjual.",
                'is_read'  => 0,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat.',
                'data'    => (new OrderResource($order->load(['items.product.store'])))->resolve()
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan. Silakan coba lagi.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
