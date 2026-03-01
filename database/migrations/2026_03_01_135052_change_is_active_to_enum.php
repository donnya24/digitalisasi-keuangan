<?php
// database/migrations/2026_03_01_xxxxxx_change_is_active_to_enum.php

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
        // Hapus kolom is_active yang lama
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        // Buat ulang dengan tipe enum
        Schema::table('users', function (Blueprint $table) {
            $table->enum('is_active', ['active', 'inactive', 'suspended'])
                  ->default('active')
                  ->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('remember_token');
        });
    }
};
