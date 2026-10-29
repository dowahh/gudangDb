<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            // restrictOnDelete: kategori yang masih dipakai barang tidak boleh dihapus
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('sku')->unique();
            $table->string('name');
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
