<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_shifts', function (Blueprint $table) {
            $table->ulid('uuid')->nullable()->unique()->after('id');
            $table->unsignedBigInteger('branch_id')->nullable()->after('uuid');
            $table->enum('sync_status', ['pending', 'sent', 'confirmed', 'failed'])->default('pending');

            $table->index('branch_id');
            $table->index('sync_status');
        });
    }

    public function down(): void
    {
        Schema::table('pos_shifts', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'branch_id', 'sync_status']);
        });
    }
};
