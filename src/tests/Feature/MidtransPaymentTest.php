<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_webhook_updates_order_status_to_processed_on_settlement(): void
    {
        $user = User::factory()->create(['roles' => ['buyer']]);
        $seller = User::factory()->create(['roles' => ['seller']]);
        $store = Store::create([
            'id_user' => $seller->id_user,
            'store_name' => 'Test Store',
            'store_category' => 'makanan_minuman',
            'address' => 'jebres',
            'operating_hours' => '24 hours',
            'verification_status' => 'disetujui',
            'district' => 'jebres'
        ]);

        $category = Category::create(['name_category' => 'makanan & minuman']);
        $product = Product::create([
            'id_store' => $store->id_store,
            'id_category' => $category->id_category,
            'name' => 'Test Item',
            'price' => 10000,
            'stock' => 10,
            'unit' => 'pcs',
            'min_order' => 1,
            'is_active' => 1
        ]);

        $order = Order::create([
            'id_user' => $user->id_user,
            'total_order' => 10000,
            'status' => 'menunggu',
            'payment_method' => 'midtrans',
            'shipping_address' => 'jebres'
        ]);

        OrderItem::create([
            'id_order' => $order->id_order,
            'id_product' => $product->id_product,
            'quantity' => 1,
            'price_at_purchase' => 10000
        ]);

        // Mock Midtrans Webhook request payload
        $orderIdPayload = 'KUL-' . $order->id_order . '-12345678';
        $statusCode = '200';
        $grossAmount = '10000.00';
        $serverKey = 'SB-Mid-server-dummykey';
        
        $signatureKey = hash('sha512', $orderIdPayload . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $orderIdPayload,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'transaction_status' => 'settlement',
            'signature_key' => $signatureKey
        ];

        $response = $this->postJson('/api/payment/callback', $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // Verify database updates
        $order->refresh();
        $this->assertEquals('settlement', $order->payment_status);
        $this->assertEquals('diproses', $order->status);
    }

    public function test_payment_webhook_signature_mismatch_returns_403(): void
    {
        $payload = [
            'order_id' => 'KUL-1-12345678',
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'transaction_status' => 'settlement',
            'signature_key' => 'invalid_signature_hash'
        ];

        $response = $this->postJson('/api/payment/callback', $payload);
        $response->assertStatus(403);
    }
}
