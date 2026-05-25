<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Categories ──────────────────────────────────────────────
        Schema::create('categories', function (Blueprint $table) {
            $table->id('id_category');
            $table->string('name_category'); // sesuai DD: name_category
            $table->timestamps();
        });

        // ── Payment Accounts ─────────────────────────────────────────
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id('id_payment');
            $table->foreignId('id_store')
                  ->constrained('stores', 'id_store')
                  ->cascadeOnDelete();
            $table->string('bank_name', 100);
            $table->string('account_number', 50);
            $table->string('account_name');
            $table->string('qris_code')->nullable();
            $table->tinyInteger('is_active')->default(0);
            $table->timestamps();
        });

        // ── Products ─────────────────────────────────────────────────
        Schema::create('products', function (Blueprint $table) {
            $table->id('id_product');
            $table->foreignId('id_store')
                  ->constrained('stores', 'id_store')
                  ->cascadeOnDelete();
            $table->foreignId('id_category')
                  ->constrained('categories', 'id_category');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('stock');
            $table->string('image_product')->nullable();
            $table->string('unit', 50);
            $table->integer('min_order')->default(1);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('review_count')->default(0);
            $table->tinyInteger('is_active')->default(0);
            $table->timestamps();
        });

        // ── Orders ───────────────────────────────────────────────────
        // DD terbaru: tidak ada kolom name, id_product, quantity di sini
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_order');
            $table->foreignId('id_user')
                  ->constrained('users', 'id_user');
            $table->timestamp('order_date')->useCurrent();
            $table->decimal('total_order', 12, 2);
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'dibatalkan'])
                  ->default('menunggu');
            $table->enum('payment_method', ['cod', 'transfer']);
            $table->text('shipping_address');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // ── Order Items ──────────────────────────────────────────────
        // Tabel baru di DD terbaru — menggantikan kolom langsung di orders
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('id_order_detail');
            $table->foreignId('id_order')
                  ->constrained('orders', 'id_order')
                  ->cascadeOnDelete();
            $table->foreignId('id_product')
                  ->constrained('products', 'id_product');
            $table->integer('quantity');
            $table->decimal('price_at_purchase', 12, 2); // harga snapshot saat beli
            $table->timestamps();
        });

        // ── Reviews ──────────────────────────────────────────────────
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('id_review');
            $table->foreignId('id_order')
                  ->constrained('orders', 'id_order');
            $table->foreignId('id_user')
                  ->constrained('users', 'id_user');
            $table->foreignId('id_product')
                  ->constrained('products', 'id_product');
            $table->tinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // ── Notifications ────────────────────────────────────────────
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('id_notification');
            $table->foreignId('id_user')
                  ->constrained('users', 'id_user')
                  ->cascadeOnDelete();
            $table->foreignId('id_order')
                  ->constrained('orders', 'id_order')
                  ->cascadeOnDelete();
            $table->text('message');
            $table->tinyInteger('is_read')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('payment_accounts');
        Schema::dropIfExists('categories');
    }
};
