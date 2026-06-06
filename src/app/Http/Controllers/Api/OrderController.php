<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * POST /api/orders
     * Fill Order Form (US-06) & Trigger Notification (US-07)
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'shipping_address' => ['required', 'string'],
            'payment_method'   => ['required', 'string', 'in:cod,transfer'],
            'note'             => ['nullable', 'string'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.id'       => ['required', 'integer', 'exists:products,id_product'],
            'items.*.qty'      => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();

        try {
            $result = DB::transaction(function () use ($request, $user) {
                // 1. Calculate total order value
                $totalOrder = 0;
                $itemsToSave = [];

                foreach ($request->items as $itemData) {
                    $product = Product::with('store')->lockForUpdate()->find($itemData['id']);
                    
                    if (!$product || !$product->is_active) {
                        throw new \Exception("Produk '{$product->name}' tidak aktif atau tidak ditemukan.");
                    }

                    if ($product->stock < $itemData['qty']) {
                        throw new \Exception("Stok produk '{$product->name}' tidak mencukupi (Tersedia: {$product->stock}).");
                    }

                    $minOrder = $product->min_order ?: 1;
                    if ($itemData['qty'] < $minOrder) {
                        throw new \Exception("Jumlah pesanan untuk '{$product->name}' kurang dari batas minimum ({$minOrder}).");
                    }

                    // Decrement stock
                    $product->stock -= $itemData['qty'];
                    $product->save();

                    $itemSubtotal = $product->price * $itemData['qty'];
                    $totalOrder += $itemSubtotal;

                    $itemsToSave[] = [
                        'id_product'        => $product->id_product,
                        'quantity'          => $itemData['qty'],
                        'price_at_purchase' => $product->price,
                        'product_name'      => $product->name,
                        'store_name'        => $product->store->store_name ?? 'Toko',
                    ];
                }

                // 2. Create Order
                $order = Order::create([
                    'id_user'          => $user->id_user,
                    'order_date'       => now(),
                    'total_order'      => $totalOrder,
                    'status'           => 'menunggu',
                    'payment_method'   => $request->payment_method,
                    'shipping_address' => $request->shipping_address,
                    'note'             => $request->note,
                ]);

                // 3. Create Order Items
                foreach ($itemsToSave as $item) {
                    OrderItem::create([
                        'id_order'          => $order->id_order,
                        'id_product'        => $item['id_product'],
                        'quantity'          => $item['quantity'],
                        'price_at_purchase' => $item['price_at_purchase'],
                    ]);
                }

                // 4. Create Notification for Buyer (US-07)
                $firstItemName = $itemsToSave[0]['product_name'];
                $firstItemStore = $itemsToSave[0]['store_name'];
                $firstItemQty = $itemsToSave[0]['quantity'];
                
                $message = "Pesanan {$firstItemName} ({$firstItemQty}x) dari {$firstItemStore} telah diterima. Penjual akan segera memproses pesanan Anda.";
                
                Notification::create([
                    'id_user'  => $user->id_user,
                    'id_order' => $order->id_order,
                    'message'  => $message,
                    'is_read'  => 0,
                ]);

                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat.',
                'data'    => $result,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * GET /api/orders/buyer
     * Fetch orders placed by the current buyer
     */
    public function buyerOrders(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $orders = Order::where('id_user', $user->id_user)
            ->with(['items.product.store'])
            ->orderBy('created_at', 'desc')
            ->get();

        $mappedOrders = $orders->map(function ($order) {
            // Check first item details for high-level info
            $firstItem = $order->items->first();
            $productName = $firstItem->product->name ?? 'Produk';
            $storeName = $firstItem->product->store->store_name ?? 'Toko';
            $storeId = $firstItem->product->store->id_store ?? null;
            $productId = $firstItem->id_product ?? null;
            $qty = $firstItem->quantity ?? 1;
            $price = $firstItem->price_at_purchase ?? 0;
            $sellerPhone = $firstItem->product->store->owner->phone_number ?? '';

            return [
                'id'               => 'ORD-' . str_pad($order->id_order, 3, '0', STR_PAD_LEFT),
                'product_name'     => $productName,
                'store_name'       => $storeName,
                'store_id'         => $storeId,
                'quantity'         => $qty,
                'price'            => floatval($price),
                'total'            => floatval($order->total_order),
                'status'           => $order->status,
                'payment_method'   => $order->payment_method,
                'shipping_address' => $order->shipping_address,
                'buyer_name'       => $order->buyer->name ?? 'Pembeli',
                'note'             => $order->note,
                'created_at'       => $order->created_at->toIso8601String(),
                'seller_phone'     => $sellerPhone,
                'product_id'       => $productId,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $mappedOrders,
        ]);
    }

    /**
     * GET /api/orders/seller
     * Fetch incoming orders for the seller's store (US-12)
     */
    public function sellerOrders(Request $request): JsonResponse
    {
        $user = $request->user();
        $store = Store::where('id_user', $user->id_user)->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Toko belum dikonfigurasi.',
            ], 404);
        }

        // Fetch orders containing products belonging to this seller's store
        $orders = Order::whereHas('items.product', function ($q) use ($store) {
            $q->where('id_store', $store->id_store);
        })
        ->with(['buyer', 'items.product.category'])
        ->orderBy('created_at', 'desc')
        ->get();

        $mappedOrders = $orders->map(function ($order) use ($store) {
            // Retrieve only items in this order that belong to this seller's store
            $sellerItems = $order->items->filter(function ($item) use ($store) {
                return $item->product && $item->product->id_store === $store->id_store;
            });

            $mappedItems = $sellerItems->map(function ($item) {
                return [
                    'product_id' => $item->id_product,
                    'name'       => $item->product->name ?? 'Produk',
                    'category'   => $item->product->category->name_category ?? '',
                    'qty'        => intval($item->quantity),
                    'price'      => floatval($item->price_at_purchase),
                ];
            })->values();

            return [
                'id'               => 'ORD-' . str_pad($order->id_order, 3, '0', STR_PAD_LEFT),
                'db_id'            => $order->id_order,
                'shipping_address' => $order->shipping_address,
                'buyer_name'       => $order->buyer->name ?? 'Pembeli',
                'buyer_phone'      => $order->buyer->phone_number ?? '', // US-10 Contact Pembeli
                'note'             => $order->note,
                'total'            => floatval($order->total_order),
                'status'           => $order->status,
                'payment_method'   => $order->payment_method,
                'created_at'       => $order->created_at->toIso8601String(),
                'items'            => $mappedItems,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $mappedOrders,
        ]);
    }

    /**
     * PUT /api/orders/{id}/status
     * Update order status (US-12) & Notify Buyer (US-07)
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:menunggu,diproses,selesai,dibatalkan'],
        ]);

        // Strip prefix ORD- if present
        $cleanId = str_replace('ORD-', '', $id);
        $cleanId = intval($cleanId);

        $order = Order::with(['buyer', 'items.product.store'])->find($cleanId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        // Verify that this order belongs to the seller's store products
        $user = $request->user();
        $store = Store::where('id_user', $user->id_user)->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki toko.',
            ], 403);
        }

        $hasProduct = $order->items->contains(function ($item) use ($store) {
            return $item->product && $item->product->id_store === $store->id_store;
        });

        if (!$hasProduct) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Pesanan ini bukan untuk toko Anda.',
            ], 403);
        }

        // Update status
        $order->status = $request->status;
        $order->save();

        // Create Buyer Notification
        $firstItem = $order->items->first();
        $productName = $firstItem->product->name ?? 'Produk';
        $storeName = $store->store_name;
        $qty = $firstItem->quantity ?? 1;

        $message = '';
        if ($request->status === 'diproses') {
            $paymentInstruction = $order->payment_method === 'transfer'
                ? " Silakan lakukan pembayaran transfer ke rekening yang tertera."
                : "";
            $message = "Pesanan {$productName} sedang diproses oleh {$storeName}.{$paymentInstruction}";
        } elseif ($request->status === 'selesai') {
            $message = "Pesanan {$productName} ({$qty}x) telah selesai. Terima kasih telah berbelanja di Kulaan.id!";
        } elseif ($request->status === 'dibatalkan') {
            $message = "Pesanan {$productName} ({$qty}x) telah dibatalkan oleh penjual.";
        } else {
            $message = "Pesanan {$productName} ({$qty}x) dalam status menunggu.";
        }

        Notification::create([
            'id_user'  => $order->id_user,
            'id_order' => $order->id_order,
            'message'  => $message,
            'is_read'  => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui.',
            'data'    => [
                'id'     => 'ORD-' . str_pad($order->id_order, 3, '0', STR_PAD_LEFT),
                'status' => $order->status,
            ]
        ]);
    }
}
