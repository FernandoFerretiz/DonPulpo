<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pos_payments MODIFY method ENUM('cash', 'card', 'transfer', 'credit') NOT NULL DEFAULT 'cash'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pos_payments MODIFY method ENUM('cash', 'card', 'transfer') NOT NULL DEFAULT 'cash'");
    }
};
