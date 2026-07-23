<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('internal_code')->unique();
            $table->string('barcode')->nullable();
            $table->foreignId('inventory_category_id')
                  ->nullable()
                  ->constrained('inventory_categories')
                  ->nullOnDelete();
            $table->foreignId('unit_of_measure_id')
                  ->constrained('units_of_measure');
            $table->decimal('average_cost', 12, 4)->default(0);
            $table->decimal('last_cost', 12, 4)->default(0);
            $table->decimal('min_stock', 12, 3)->default(0);
            $table->decimal('max_stock', 12, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('tracks_inventory')->default(true);
            $table->boolean('tracks_lots')->default(false);
            $table->boolean('tracks_expiration')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_products');
    }
};
