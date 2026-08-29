<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('status');
            $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            $table->string('cancel_reason', 255)->nullable()->after('cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropColumn(['cancelled_by', 'cancelled_at', 'cancel_reason']);
        });
    }
};
