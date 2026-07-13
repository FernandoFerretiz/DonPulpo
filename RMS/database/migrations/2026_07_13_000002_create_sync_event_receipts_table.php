<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Server-side idempotency ledger: one row per sync_events.uuid received
 * from a branch. Lets /sync/push safely re-process a batch that was
 * retried after a dropped connection without double-applying it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_event_receipts', function (Blueprint $table) {
            $table->id();
            $table->ulid('event_uuid')->unique();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('branch_installation_id')->constrained();
            $table->string('event_type');
            $table->string('aggregate_type');
            $table->ulid('aggregate_uuid');
            $table->enum('status', ['received', 'processed', 'failed'])->default('received');
            $table->text('error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_event_receipts');
    }
};
