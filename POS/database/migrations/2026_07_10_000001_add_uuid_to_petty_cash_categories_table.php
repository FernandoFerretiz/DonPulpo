<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petty_cash_categories', function (Blueprint $table) {
            $table->ulid('uuid')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('petty_cash_categories', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
