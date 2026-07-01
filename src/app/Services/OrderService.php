<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\MidtransService;

class OrderService
{
    public function __construct(
        private MidtransService $midtransService
    ) {}

    /**
     * Place a new order inside a database transaction.
     *
     * @param User $user
     * @param Product $product
     * @param array $validated
     * @return Order
     * @throws \Exception
     */
    public function placeOrder(User $user, Product $product, array $validated): Order
    {
        $totalOrder = $product->price * $validated['quantity'];

        return DB::transaction(function () use ($user, $product, $validated, $totalOrder) {
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

            // 5. Jika metode pembayaran adalah midtrans, buat snap token
            if ($validated['payment_method'] === 'midtrans') {
                try {
                    $snapToken = $this->midtransService->createSnapToken($order, $user, $product, $validated['quantity']);
                    $order->update([
                        'snap_token' => $snapToken,
                        'payment_status' => 'pending'
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception("Gagal membuat token pembayaran: " . $e->getMessage());
                }
            }

            // 6. Buat notifikasi untuk penjual (pemilik toko)
            if ($product->store) {
                Notification::create([
                    'id_user'  => $product->store->id_user,
                    'id_order' => $order->id_order,
                    'message'  => "Pesanan baru masuk: {$validated['quantity']} {$product->unit} {$product->name} dari {$validated['name']}.",
                    'is_read'  => 0,
                ]);
            }

            // 7. Buat notifikasi untuk pembeli
            Notification::create([
                'id_user'  => $user->id_user,
                'id_order' => $order->id_order,
                'message'  => "Pesanan Anda untuk {$product->name} telah berhasil dibuat dan menunggu pembayaran.",
                'is_read'  => 0,
            ]);

            return $order;
        });
    }
}

