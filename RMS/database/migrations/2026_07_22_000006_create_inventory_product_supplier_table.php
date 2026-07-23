<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_product_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_product_id')->constrained('inventory_products')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->decimal('cost', 12, 4)->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['inventory_product_id', 'supplier_id'], 'inv_product_supplier_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_product_supplier');
    }
};
