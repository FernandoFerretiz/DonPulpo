<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Local cache of the company's menu/prices, pulled from the
 * BackOffice (RMS). Read-only from POS's point of view — prices are
 * never edited here, only received on the next sync:pull.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('dishes')) {
            return;
        }

        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique();
            $table->foreignId('dish_category_id')
                  ->nullable()
                  ->constrained('dish_categories')
                  ->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('status', ['active', 'temporarily_inactive', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
