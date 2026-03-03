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
        Schema::create('prive_purposes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->default('tag');
            $table->string('color')->default('#6B7280');
            $table->string('is_active')->default('active');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('is_active');
            $table->unique(['user_id', 'name']);
        });

        // Add check constraint for PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE prive_purposes ADD CONSTRAINT prive_purposes_is_active_check CHECK (is_active IN ('active', 'inactive'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prive_purposes');
    }
};