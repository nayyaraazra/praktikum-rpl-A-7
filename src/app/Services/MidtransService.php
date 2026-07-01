<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.server_key') ?: 'SB-Mid-server-dummykey';
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = (bool) config('services.midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('services.midtrans.is_3ds', true);
    }

    /**
     * Generate Midtrans Snap Token for an Order.
     *
     * @param Order $order
     * @param User $user
     * @param Product $product
     * @param int $quantity
     * @return string
     * @throws \Exception
     */
    public function createSnapToken(Order $order, User $user, Product $product, int $quantity): string
    {
        $params = [
            'transaction_details' => [
                'order_id' => 'KUL-' . $order->id_order . '-' . time(),
                'gross_amount' => (int) $order->total_order,
            ],
            'item_details' => [
                [
                    'id' => (string) $product->id_product,
                    'price' => (int) $product->price,
                    'quantity' => $quantity,
                    'name' => substr($product->name, 0, 50),
                ]
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone_number,
                'billing_address' => [
                    'first_name' => $user->name,
                    'phone' => $user->phone_number,
                    'address' => $order->shipping_address,
                ],
                'shipping_address' => [
                    'first_name' => $user->name,
                    'phone' => $user->phone_number,
                    'address' => $order->shipping_address,
                ]
            ],
            'callbacks' => [
                'finish' => config('app.url') . '/buyer/orders',
            ]
        ];

        return Snap::getSnapToken($params);
    }
}
