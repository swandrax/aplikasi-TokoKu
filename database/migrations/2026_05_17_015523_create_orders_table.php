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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('nama_penerima');
            $table->string('telepon_penerima');
            $table->text('alamat_pengiriman');
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('metode_pembayaran');
            $table->decimal('total_harga', 15, 2);
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
