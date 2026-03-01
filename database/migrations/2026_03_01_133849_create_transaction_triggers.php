<?php
// database/migrations/2026_03_01_000006_create_transaction_triggers.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hanya jalankan jika menggunakan PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Buat function untuk update daily summary
            DB::statement('
                CREATE OR REPLACE FUNCTION update_daily_summary()
                RETURNS TRIGGER AS $$
                DECLARE
                    v_date DATE;
                    v_user_id UUID;
                BEGIN
                    -- Tentukan tanggal dan user_id berdasarkan operasi
                    IF TG_OP = \'DELETE\' THEN
                        v_date = OLD.transaction_date;
                        v_user_id = OLD.user_id;
                    ELSE
                        v_date = NEW.transaction_date;
                        v_user_id = NEW.user_id;
                    END IF;

                    -- Insert atau update daily summary
                    INSERT INTO daily_summaries (
                        id, user_id, date, total_income, total_expense,
                        net_profit, created_at, updated_at
                    )
                    SELECT
                        gen_random_uuid(),
                        v_user_id,
                        v_date,
                        COALESCE((SELECT SUM(amount) FROM transactions
                                  WHERE user_id = v_user_id
                                  AND transaction_date = v_date
                                  AND type = \'pemasukan\'), 0),
                        COALESCE((SELECT SUM(amount) FROM transactions
                                  WHERE user_id = v_user_id
                                  AND transaction_date = v_date
                                  AND type = \'pengeluaran\'), 0),
                        COALESCE((SELECT SUM(amount) FROM transactions
                                  WHERE user_id = v_user_id
                                  AND transaction_date = v_date
                                  AND type = \'pemasukan\'), 0) -
                        COALESCE((SELECT SUM(amount) FROM transactions
                                  WHERE user_id = v_user_id
                                  AND transaction_date = v_date
                                  AND type = \'pengeluaran\'), 0),
                        NOW(),
                        NOW()
                    ON CONFLICT (user_id, date)
                    DO UPDATE SET
                        total_income = EXCLUDED.total_income,
                        total_expense = EXCLUDED.total_expense,
                        net_profit = EXCLUDED.net_profit,
                        updated_at = NOW();

                    RETURN NULL;
                END;
                $$ LANGUAGE plpgsql;
            ');

            // Buat trigger untuk transactions
            DB::statement('
                CREATE TRIGGER trigger_update_daily_summary
                AFTER INSERT OR UPDATE OR DELETE ON transactions
                FOR EACH ROW
                EXECUTE FUNCTION update_daily_summary();
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP TRIGGER IF EXISTS trigger_update_daily_summary ON transactions');
            DB::statement('DROP FUNCTION IF EXISTS update_daily_summary()');
        }
    }
};
