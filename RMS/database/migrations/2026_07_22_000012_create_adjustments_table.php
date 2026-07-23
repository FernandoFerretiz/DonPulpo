<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->date('adjustment_date');
            $table->enum('status', ['draft', 'completed', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adjustment_id')->constrained('adjustments')->cascadeOnDelete();
            $table->foreignId('inventory_product_id')->constrained('inventory_products');
            $table->decimal('previous_quantity', 12, 3);
            $table->decimal('new_quantity', 12, 3);
            $table->decimal('difference', 12, 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjustment_items');
        Schema::dropIfExists('adjustments');
    }
};
