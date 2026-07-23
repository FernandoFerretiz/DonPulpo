<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_counts', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('adjustment_id')->nullable()->constrained('adjustments')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->date('count_date');
            $table->enum('status', ['open', 'confirmed', 'cancelled'])->default('open');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('physical_count_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('physical_count_id')->constrained('physical_counts')->cascadeOnDelete();
            $table->foreignId('inventory_product_id')->constrained('inventory_products');
            $table->decimal('system_quantity', 12, 3);
            $table->decimal('counted_quantity', 12, 3)->nullable();
            $table->decimal('difference', 12, 3)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_count_items');
        Schema::dropIfExists('physical_counts');
    }
};
