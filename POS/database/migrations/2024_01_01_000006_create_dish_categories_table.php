<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Local cache of the company's menu categories, pulled from the
 * BackOffice (RMS). Read-only from POS's point of view.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('dish_categories')) {
            return;
        }

        Schema::create('dish_categories', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('display_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dish_categories');
    }
};
