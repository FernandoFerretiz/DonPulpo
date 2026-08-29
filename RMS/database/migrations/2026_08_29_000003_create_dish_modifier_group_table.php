<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dish_modifier_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dish_id')
                  ->constrained('dishes')
                  ->cascadeOnDelete();
            $table->foreignId('modifier_group_id')
                  ->constrained('modifier_groups')
                  ->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['dish_id', 'modifier_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dish_modifier_group');
    }
};
