<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_shift_id')->constrained('pos_shifts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('type', 50)->comment('FONDO_INICIAL, VENTA_EFECTIVO, VENTA_TARJETA, etc.');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 30)->nullable()->comment('cash, card, transfer');
            $table->string('description')->nullable();
            // Polymorphic relation to PosPayment, PettyCashVoucher, etc.
            $table->nullableMorphs('reference');
            $table->timestamps();

            $table->index(['pos_shift_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};
