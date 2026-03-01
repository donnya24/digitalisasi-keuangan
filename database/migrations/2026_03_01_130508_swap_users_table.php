<?php
// database/migrations/2026_03_01_xxxxxx_swap_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ganti nama tabel users lama
        Schema::rename('users', 'users_old');

        // Ganti nama users_uuid menjadi users
        Schema::rename('users_uuid', 'users');

        // Hapus tabel lama (opsional, bisa di-drop nanti)
        // Schema::dropIfExists('users_old');
    }

    public function down(): void
    {
        // Kembalikan seperti semula
        Schema::rename('users', 'users_uuid');
        Schema::rename('users_old', 'users');
    }
};
