<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_order_item_modifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_order_item_id')
                  ->constrained('pos_order_items')
                  ->cascadeOnDelete();
            $table->unsignedBigInteger('modifier_option_id')->nullable();
            $table->string('group_name_snapshot');
            $table->string('option_name_snapshot');
            $table->decimal('price_delta_snapshot', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_order_item_modifiers');
    }
};
