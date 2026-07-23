<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El folio de cada documento (compra, transferencia, ajuste, merma) ya es
 * único en su propia tabla. Una transferencia genera dos stock_movements
 * (salida + entrada) que comparten el mismo folio de referencia, así que
 * la unicidad a nivel de stock_movements sobra y bloquea ese caso.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropUnique(['folio']);
            $table->index('folio');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['folio']);
            $table->unique('folio');
        });
    }
};
