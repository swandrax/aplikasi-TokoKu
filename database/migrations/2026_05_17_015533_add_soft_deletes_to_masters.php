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
        Schema::table('produk', function (Blueprint $table) {
            $table->softDeletes();
            $table->boolean('is_active')->default(true);
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
        });

        Schema::table('kategori', function (Blueprint $table) {
            $table->softDeletes();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['deleted_at', 'is_active', 'supplier_id']);
        });

        Schema::table('kategori', function (Blueprint $table) {
            $table->dropColumn(['deleted_at', 'is_active']);
        });
    }
};
