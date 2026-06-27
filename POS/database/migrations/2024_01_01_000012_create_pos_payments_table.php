<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_order_id')
                  ->constrained('pos_orders')
                  ->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->index();
            $table->enum('method', ['cash', 'card', 'transfer'])->default('cash');
            $table->decimal('amount', 10, 2);
            $table->decimal('change_amount', 10, 2)->default(0);
            $table->enum('status', ['paid', 'cancelled'])->default('paid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_payments');
    }
};
