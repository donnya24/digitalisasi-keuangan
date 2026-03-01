<?php
// database/migrations/2026_03_01_000003_create_prive_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prive', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->date('prive_date');
            $table->string('purpose')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('prive_date');
            $table->index('is_approved');
            $table->index(['user_id', 'prive_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prive');
    }
};
