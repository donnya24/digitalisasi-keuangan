<?php

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
        // Hapus kategori yang tidak memiliki transaksi terkait
        // Ini aman karena kategori yang dipakai di transaksi tidak akan terhapus
        
        $deletedCount = DB::table('categories')
            ->whereNotIn('id', function ($query) {
                $query->select('category_id')
                    ->from('transactions')
                    ->whereNotNull('category_id');
            })
            ->delete();
        
        // Log jumlah kategori yang dihapus
        \Illuminate\Support\Facades\Log::info("Migration: Removed {$deletedCount} unused categories");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada rollback karena ini operasi data
        // Jika ingin mengembalikan, jalankan CategorySeeder
    }
};