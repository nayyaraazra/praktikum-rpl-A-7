<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->change();
            $table->string('snap_token')->nullable()->after('payment_method');
            $table->string('payment_status')->default('pending')->after('snap_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Note: Down migration doesn't strictly need to revert back to enum for SQLite,
            // but we can drop the columns we added.
            $table->dropColumn(['snap_token', 'payment_status']);
        });
    }
};
