<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Represents a physical local server (one per branch), authenticated via
 * a Sanctum token. This is the "device" identity for sync — separate
 * from any human User — so a branch's server never needs a person's
 * credentials to push/pull.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_installations', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('device_name')->nullable();
            $table->string('app_version')->nullable();
            $table->enum('status', ['pending', 'active', 'revoked'])->default('pending');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->string('last_ip')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_installations');
    }
};
