<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DailySummary;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Prive; // <-- TAMBAHKAN IMPORT INI!
use Carbon\Carbon;
use Illuminate\Support\Str;

class DailySummarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('is_active', 'active')->get();

        // Hapus daily summary yang ada
        DailySummary::truncate();

        $totalSummaries = 0;

        foreach ($users as $user) {
            // Ambil semua tanggal transaksi untuk user ini
            $transactionDates = Transaction::where('user_id', $user->id)
                ->selectRaw('DISTINCT DATE(transaction_date) as date')
                ->orderBy('date')
                ->pluck('date');

            $priveDates = Prive::where('user_id', $user->id)
                ->selectRaw('DISTINCT DATE(prive_date) as date')
                ->orderBy('date')
                ->pluck('date');

            // Gabungkan semua tanggal unik
            $allDates = $transactionDates->merge($priveDates)->unique()->sort();

            $cashBalance = 0;

            foreach ($allDates as $date) {
                $income = (float) Transaction::where('user_id', $user->id)
                    ->whereDate('transaction_date', $date)
                    ->where('type', 'pemasukan')
                    ->sum('amount');

                $expense = (float) Transaction::where('user_id', $user->id)
                    ->whereDate('transaction_date', $date)
                    ->where('type', 'pengeluaran')
                    ->sum('amount');

                $prive = (float) Prive::where('user_id', $user->id)
                    ->whereDate('prive_date', $date)
                    ->where('is_approved', 'approved') // Hanya prive yang disetujui
                    ->sum('amount');

                $netProfit = $income - $expense;
                $cashBalance += $netProfit - $prive;

                DailySummary::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'date' => $date,
                    ],
                    [
                        'id' => Str::uuid(),
                        'total_income' => $income,
                        'total_expense' => $expense,
                        'net_profit' => $netProfit,
                        'cash_balance' => $cashBalance,
                        'created_at' => Carbon::parse($date)->setTime(23, 59, 59),
                        'updated_at' => Carbon::parse($date)->setTime(23, 59, 59),
                    ]
                );

                $totalSummaries++;
            }
        }

        $this->command->info("✅ Daily Summary berhasil dibuat: {$totalSummaries} ringkasan");
    }
}
