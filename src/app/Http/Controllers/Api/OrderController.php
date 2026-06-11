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
            ->get()
            ->map(fn($order) => $this->formatOrder($order));

        return response()->json([
            'success' => true,
            'data' => $orders
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
            'data' => $this->formatOrder($order)
        ]);
    }

    /**
     * POST /api/orders
     * Buat pesanan baru dari buyer (checkout langsung).
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'id_product'       => ['required', 'exists:products,id_product'],
            'quantity'         => ['required', 'integer', 'min:1'],
            'name'             => ['required', 'string', 'max:255'],
            'phone_number'     => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string'],
            'payment_method'   => ['required', 'in:cod,transfer'],
            'note'             => ['nullable', 'string', 'max:1000'],
        ]);

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
                'data'    => $this->formatOrder($order->load(['items.product.store']))
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

    private function formatOrder(Order $order): array
    {
        return [
            'id_order'         => $order->id_order,
            'id_user'          => $order->id_user,
            'total_order'      => $order->total_order,
            'status'           => $order->status,
            'payment_method'   => $order->payment_method,
            'shipping_address' => $order->shipping_address,
            'note'             => $order->note,
            'order_date'       => $order->order_date ? $order->order_date->toIso8601String() : null,
            'items'            => $order->items->map(fn($item) => [
                'id_order_detail'   => $item->id_order_detail,
                'quantity'          => $item->quantity,
                'price_at_purchase' => $item->price_at_purchase,
                'product'           => $item->product ? [
                    'id_product' => $item->product->id_product,
                    'name'       => $item->product->name,
                    'unit'       => $item->product->unit,
                    'image_url'  => $item->product->image_product ? asset('storage/' . $item->product->image_product) : null,
                    'store'      => $item->product->store ? [
                        'id_store'     => $item->product->store->id_store,
                        'store_name'   => $item->product->store->store_name,
                        'phone_number' => $item->product->store->owner?->phone_number,
                    ] : null,
                ] : null,
            ]),
        ];
    }
}
