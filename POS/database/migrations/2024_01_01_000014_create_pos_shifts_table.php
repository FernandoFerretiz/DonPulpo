<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('terminal_id')->nullable()->comment('Identificador de terminal/caja');
            $table->enum('status', ['open', 'closed', 'cancelled'])->default('open');
            $table->decimal('opening_cash', 10, 2)->default(0)->comment('Fondo inicial');
            $table->decimal('expected_cash', 10, 2)->nullable()->comment('Calculado al cerrar');
            $table->decimal('counted_cash', 10, 2)->nullable()->comment('Efectivo contado por cajero');
            $table->decimal('difference', 10, 2)->nullable()->comment('contado - esperado');
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_shifts');
    }
};
