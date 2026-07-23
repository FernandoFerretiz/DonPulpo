<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->foreignId('inventory_product_id')->constrained('inventory_products');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('related_warehouse_id')
                  ->nullable()
                  ->constrained('warehouses')
                  ->nullOnDelete();
            $table->enum('type', [
                'purchase', 'sale', 'production', 'adjustment', 'shrinkage',
                'transfer', 'internal_consumption', 'return', 'cancellation', 'initial_stock',
            ]);
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_cost', 12, 4)->default(0);
            $table->decimal('previous_balance', 12, 3);
            $table->decimal('new_balance', 12, 3);
            $table->nullableMorphs('reference');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('movement_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
