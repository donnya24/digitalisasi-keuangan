<?php
// database/migrations/2026_03_01_xxxxxx_modify_sessions_table_for_uuid.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hanya untuk PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Hapus foreign key constraint yang mungkin ada
            DB::statement('ALTER TABLE sessions DROP CONSTRAINT IF EXISTS sessions_user_id_foreign');

            // Ubah tipe kolom user_id menjadi string (untuk UUID)
            DB::statement('ALTER TABLE sessions ALTER COLUMN user_id TYPE VARCHAR(255)');
        } else {
            // Untuk database lain (mysql, dll)
            Schema::table('sessions', function (Blueprint $table) {
                // Hapus foreign key jika ada
                $table->dropForeign(['user_id']);

                // Ubah kolom user_id menjadi string
                $table->string('user_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Kembalikan ke bigint (hati-hati, data bisa hilang)
            DB::statement('ALTER TABLE sessions ALTER COLUMN user_id TYPE BIGINT USING (user_id::bigint)');
        } else {
            Schema::table('sessions', function (Blueprint $table) {
                $table->bigInteger('user_id')->unsigned()->nullable()->change();
            });
        }
    }
};
