<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Store a newly created order.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'id_product' => ['required', 'exists:products,id_product'],
            'quantity' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:cod,transfer'],
            'shipping_address' => ['required', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        try {
            $order = DB::transaction(function () use ($request) {
                // Lock product to prevent race condition on stock
                $product = Product::lockForUpdate()->findOrFail($request->id_product);

                // Verify product and store status
                if ($product->is_active != 1) {
                    throw new \Exception('Produk ini sedang tidak aktif.');
                }

                if ($product->store->verification_status !== 'disetujui') {
                    throw new \Exception('Toko penyedia produk belum disetujui.');
                }

                // Check min order
                if ($request->quantity < $product->min_order) {
                    throw new \Exception("Jumlah pesanan minimal adalah {$product->min_order} {$product->unit}.");
                }

                // Check stock
                if ($product->stock < $request->quantity) {
                    throw new \Exception("Stok tidak mencukupi. Stok saat ini: {$product->stock} {$product->unit}.");
                }

                // Deduct stock
                $product->stock -= $request->quantity;
                $product->save();

                // Calculate total
                $totalOrder = $product->price * $request->quantity;

                // Create Order record
                $order = Order::create([
                    'id_user' => $request->user()->id_user,
                    'order_date' => now(),
                    'total_order' => $totalOrder,
                    'status' => 'menunggu',
                    'payment_method' => $request->payment_method,
                    'shipping_address' => $request->shipping_address,
                    'note' => $request->note,
                ]);

                // Create OrderItem record
                $order->orderItems()->create([
                    'id_product' => $product->id_product,
                    'quantity' => $request->quantity,
                    'price_at_purchase' => $product->price,
                ]);

                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat.',
                'data' => $order->load('orderItems.product')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display a list of orders for the authenticated seller's store.
     */
    public function sellerIndex(Request $request): JsonResponse
    {
        try {
            $store = Store::where('id_user', $request->user()->id_user)->first();
            if (!$store) {
                return response()->json([
                    'success' => false,
                    'message' => 'Toko tidak ditemukan. Anda harus mendaftarkan toko terlebih dahulu.'
                ], 403);
            }

            $query = Order::query()
                ->whereHas('orderItems.product', function ($q) use ($store) {
                    $q->where('id_store', $store->id_store);
                });

            // Filter status if provided and is not 'semua'
            if ($request->has('status') && $request->status !== 'semua' && $request->status !== '') {
                $query->where('status', $request->status);
            }

            // Load buyer (user) and product detail
            $orders = $query->with([
                'orderItems.product',
                'user:id_user,name,phone_number'
            ])->latest()->get();

            return response()->json([
                'success' => true,
                'data' => $orders
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pesanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the status of an order (Seller action).
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:menunggu,diproses,selesai,dibatalkan']
        ]);

        try {
            DB::beginTransaction();

            $store = Store::where('id_user', $request->user()->id_user)->first();
            if (!$store) {
                return response()->json([
                    'success' => false,
                    'message' => 'Toko tidak ditemukan.'
                ], 403);
            }

            // Find order that belongs to this seller's store products
            $order = Order::whereHas('orderItems.product', function ($q) use ($store) {
                $q->where('id_store', $store->id_store);
            })->findOrFail($id);

            $oldStatus = $order->status;
            $newStatus = $request->status;

            // Transisi status: menunggu->diproses, diproses->selesai, menunggu->dibatalkan
            $allowed = false;
            if ($oldStatus === 'menunggu' && in_array($newStatus, ['diproses', 'dibatalkan'])) {
                $allowed = true;
            } elseif ($oldStatus === 'diproses' && $newStatus === 'selesai') {
                $allowed = true;
            }

            if (!$allowed) {
                return response()->json([
                    'success' => false,
                    'message' => "Perubahan status dari '{$oldStatus}' ke '{$newStatus}' tidak diperbolehkan."
                ], 422);
            }

            $order->status = $newStatus;
            $order->save();

            // If dibatalkan, return stock to product
            if ($newStatus === 'dibatalkan') {
                foreach ($order->orderItems as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item->id_product);
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diperbarui.',
                'data' => $order->load(['orderItems.product', 'user'])
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status pesanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
