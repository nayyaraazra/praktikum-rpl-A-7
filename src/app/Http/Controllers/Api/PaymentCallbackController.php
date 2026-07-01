<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentCallbackController extends Controller
{
    /**
     * POST /api/payment/callback
     * Handle payment notification from Midtrans.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        
        $orderIdPayload = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;

        if (!$orderIdPayload || !$statusCode || !$grossAmount || !$signatureKey) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid notification payload'
            ], 400);
        }

        // Verify Signature Key
        $serverKey = config('services.midtrans.server_key') ?: 'SB-Mid-server-dummykey';
        $signature = hash('sha512', $orderIdPayload . $statusCode . $grossAmount . $serverKey);

        if ($signature !== $signatureKey) {
            return response()->json([
                'success' => false,
                'message' => 'Signature mismatch'
            ], 403);
        }

        // Extract internal order ID from format "KUL-{id_order}-{timestamp}"
        $parts = explode('-', $orderIdPayload);
        if (count($parts) < 2) {
            return response()->json(['success' => false, 'message' => 'Invalid order ID format'], 400);
        }
        $idOrder = $parts[1];

        $order = Order::with('items.product.store')->find($idOrder);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        DB::transaction(function () use ($order, $transactionStatus) {
            if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                // Payment success
                $order->update([
                    'payment_status' => 'settlement',
                    'status' => 'diproses' // Automatically process the order
                ]);

                // Create notifications
                foreach ($order->items as $item) {
                    if ($item->product && $item->product->store) {
                        Notification::create([
                            'id_user' => $item->product->store->id_user,
                            'id_order' => $order->id_order,
                            'message' => "Pembayaran diterima: Pesanan #{$order->id_order} ({$item->product->name}) telah lunas dan siap diproses.",
                            'is_read' => 0
                        ]);
                    }
                }

                Notification::create([
                    'id_user' => $order->id_user,
                    'id_order' => $order->id_order,
                    'message' => "Pembayaran pesanan #{$order->id_order} berhasil diterima. Pesanan Anda sedang diproses oleh penjual.",
                    'is_read' => 0
                ]);

            } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                // Payment failed / cancelled
                $order->update([
                    'payment_status' => $transactionStatus,
                    'status' => 'dibatalkan'
                ]);

                // Restore stock
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }

                Notification::create([
                    'id_user' => $order->id_user,
                    'id_order' => $order->id_order,
                    'message' => "Pembayaran pesanan #{$order->id_order} gagal atau dibatalkan. Stok barang telah dikembalikan.",
                    'is_read' => 0
                ]);
            } else {
                // Keep pending state
                $order->update([
                    'payment_status' => $transactionStatus
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Notification handled successfully'
        ]);
    }
}
