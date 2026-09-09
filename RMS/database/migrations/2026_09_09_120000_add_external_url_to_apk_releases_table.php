<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apk_releases', function (Blueprint $table) {
            // Alternativa a subir el archivo: un link externo (Drive, S3, etc.)
            // cuando la subida directa falla por el tamaño/tiempo del APK.
            $table->string('external_url')->nullable()->after('file_path');
        });

        // Sin doctrine/dbal en el proyecto, se modifican las columnas por SQL crudo
        // en vez de Blueprint::change().
        DB::statement('ALTER TABLE apk_releases MODIFY file_path VARCHAR(255) NULL');
        DB::statement('ALTER TABLE apk_releases MODIFY size_bytes BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('apk_releases', function (Blueprint $table) {
            $table->dropColumn('external_url');
        });

        DB::statement('ALTER TABLE apk_releases MODIFY file_path VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE apk_releases MODIFY size_bytes BIGINT UNSIGNED NOT NULL');
    }
};
