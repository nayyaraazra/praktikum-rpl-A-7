<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id('id_store');
            $table->foreignId('id_user')
                  ->constrained('users', 'id_user')
                  ->cascadeOnDelete();
            $table->string('store_name');
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->enum('verification_status', ['menunggu', 'disetujui', 'dibatalkan'])
                  ->default('menunggu');
            $table->string('operating_hours', 100);
            $table->string('district', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
