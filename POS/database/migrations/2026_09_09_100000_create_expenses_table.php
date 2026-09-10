<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('amount', 10, 2);
            $table->string('motivo', 100);
            $table->text('description')->nullable();
            // Gasto de un día que no necesariamente es hoy (utilidad de días
            // anteriores, inyección personal, etc.), por eso es independiente
            // de pos_shifts y de cash_movements.
            $table->date('expense_date');
            $table->timestamps();

            $table->index('expense_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
