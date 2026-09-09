<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apk_releases', function (Blueprint $table) {
            $table->id();
            $table->string('version');
            $table->string('original_name');
            $table->string('file_path');
            $table->unsignedBigInteger('size_bytes');
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apk_releases');
    }
};
