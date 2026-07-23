<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shrinkages', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->enum('reason', ['expired', 'damaged', 'spillage', 'internal_consumption', 'other']);
            $table->text('notes')->nullable();
            $table->date('shrinkage_date');
            $table->enum('status', ['draft', 'completed', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('shrinkage_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shrinkage_id')->constrained('shrinkages')->cascadeOnDelete();
            $table->foreignId('inventory_product_id')->constrained('inventory_products');
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_cost', 12, 4)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shrinkage_items');
        Schema::dropIfExists('shrinkages');
    }
};
