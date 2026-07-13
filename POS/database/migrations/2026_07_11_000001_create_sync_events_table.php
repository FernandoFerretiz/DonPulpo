<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Local outbox: every business action worth syncing to the BackOffice
 * (RMS) is recorded here by App\Services\Sync\OutboxRecorder. sync:push
 * drains pending rows in batches; nothing here is ever bulk table-copied.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_events', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('event_type');
            $table->string('aggregate_type');
            $table->unsignedBigInteger('aggregate_id');
            $table->ulid('aggregate_uuid');
            $table->json('payload');
            $table->enum('sync_status', ['pending', 'sending', 'sent', 'confirmed', 'failed', 'stuck'])->default('pending');
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('last_attempted_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index(['sync_status', 'created_at']);
            $table->index(['aggregate_type', 'aggregate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_events');
    }
};
