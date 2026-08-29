<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dish_modifier_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dish_id')
                  ->constrained('dishes')
                  ->cascadeOnDelete();
            $table->foreignId('modifier_option_id')
                  ->constrained('modifier_options')
                  ->cascadeOnDelete();
            $table->decimal('price_delta', 10, 2)->default(0);
            $table->timestamps();
            $table->unique(['dish_id', 'modifier_option_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dish_modifier_options');
    }
};
