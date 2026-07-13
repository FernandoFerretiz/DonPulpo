<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->ulid('uuid')->nullable()->unique()->after('id');
            $table->unsignedBigInteger('branch_id')->nullable()->after('uuid');
            $table->enum('sync_status', ['pending', 'sent', 'confirmed', 'failed'])->default('pending')->after('status');
            $table->text('cancelled_reason')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->index('branch_id');
            $table->index('sync_status');
        });
    }

    public function down(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'branch_id', 'sync_status', 'cancelled_reason', 'cancelled_by', 'cancelled_at']);
        });
    }
};
