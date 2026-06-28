<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_shifts', function (Blueprint $table) {
            $table->decimal('counted_card', 10, 2)->nullable()->after('counted_cash')
                  ->comment('Tarjeta contada por cajero al cierre');
            $table->decimal('counted_transfer', 10, 2)->nullable()->after('counted_card')
                  ->comment('Transferencia contada por cajero al cierre');
        });
    }

    public function down(): void
    {
        Schema::table('pos_shifts', function (Blueprint $table) {
            $table->dropColumn(['counted_card', 'counted_transfer']);
        });
    }
};
