<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\DailySummary;
use App\Models\Prive;
// Hapus 'use App\Models\Category;' karena tidak digunakan
// Hapus 'use Illuminate\Support\Facades\DB;' karena tidak digunakan
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with real data from database.
     */
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $yesterday = Carbon::now('Asia/Jakarta')->subDay()->toDateString();

        // ========== DATA STATISTIK HARI INI ==========
        $todayIncome = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'pemasukan')
            ->whereDate('transaction_date', $today)
            ->sum('amount');

        $todayExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'pengeluaran')
            ->whereDate('transaction_date', $today)
            ->sum('amount');

        $todayProfit = $todayIncome - $todayExpense;

        // Data kemarin untuk perbandingan
        $yesterdayIncome = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'pemasukan')
            ->whereDate('transaction_date', $yesterday)
            ->sum('amount');

        $yesterdayExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'pengeluaran')
            ->whereDate('transaction_date', $yesterday)
            ->sum('amount');

        $yesterdayProfit = $yesterdayIncome - $yesterdayExpense;

        // Hitung persentase perubahan
        $incomeChange = $this->calculatePercentageChange($yesterdayIncome, $todayIncome);
        $expenseChange = $this->calculatePercentageChange($yesterdayExpense, $todayExpense);
        $profitChange = $this->calculatePercentageChange($yesterdayProfit, $todayProfit);

        // Tentukan arah panah (up/down)
        $incomeArrow = $incomeChange >= 0 ? 'up' : 'down';
        $expenseArrow = $expenseChange >= 0 ? 'up' : 'down';
        $profitArrow = $profitChange >= 0 ? 'up' : 'down';

        // Saldo usaha terkini
        $currentBalance = $this->calculateCurrentBalance($user->id);

        // ========== DATA GRAFIK 7 HARI TERAKHIR ==========
        $chartLabels = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now('Asia/Jakarta')->subDays($i)->toDateString();
            $dayData = DailySummary::where('user_id', $user->id)
                ->where('date', $date)
                ->first();

            $dayIncome = (float) ($dayData->total_income ?? 0);
            $dayExpense = (float) ($dayData->total_expense ?? 0);

            $chartLabels[] = Carbon::parse($date)->translatedFormat('D');
            $incomeData[] = $dayIncome;
            $expenseData[] = $dayExpense;
        }

        // ========== DATA GRAFIK LABA 30 HARI ==========
        $profitLabels = [];
        $profitData = [];

        // Ambil data 30 hari terakhir (ambil 7 titik sample untuk kejelasan)
        $dailyProfits = DailySummary::where('user_id', $user->id)
            ->where('date', '>=', Carbon::now('Asia/Jakarta')->subDays(30))
            ->orderBy('date')
            ->get();

        if ($dailyProfits->count() >= 7) {
            // Sampling data - ambil 7 titik merata
            $step = floor($dailyProfits->count() / 7);
            for ($i = 0; $i < 7; $i++) {
                $index = min($i * $step, $dailyProfits->count() - 1);
                $item = $dailyProfits[$index];
                $profitLabels[] = Carbon::parse($item->date)->translatedFormat('d M');
                $profitData[] = (float) $item->net_profit;
            }
        } else {
            // Data sample jika tidak ada data
            $sampleDates = ['1', '5', '10', '15', '20', '25', '30'];
            foreach ($sampleDates as $date) {
                $profitLabels[] = $date;
                $profitData[] = rand(200000, 400000);
            }
        }

        // ========== DATA BULAN INI ==========
        $monthStart = Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();
        $monthEnd = Carbon::now('Asia/Jakarta')->endOfMonth()->toDateString();

        $monthIncome = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'pemasukan')
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $monthExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'pengeluaran')
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $monthProfit = $monthIncome - $monthExpense;

        $monthPrive = (float) Prive::where('user_id', $user->id)
            ->whereBetween('prive_date', [$monthStart, $monthEnd])
            ->where('is_approved', 'approved')
            ->sum('amount');

        // Target laba bulanan (default Rp 10.000.000)
        $targetProfit = 10000000;
        $profitPercentage = $targetProfit > 0 ? min(100, round(($monthProfit / $targetProfit) * 100)) : 0;

        // ========== TRANSAKSI TERBARU ==========
        $recentTransactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($transaction) {
                $categoryIcon = $transaction->category->icon ?? 'tag';

                return [
                    'id' => $transaction->id,
                    'description' => $transaction->description,
                    'amount' => (float) $transaction->amount,
                    'type' => $transaction->type,
                    'category' => $transaction->category->name ?? 'Tanpa Kategori',
                    'icon' => $categoryIcon,
                    'color' => $transaction->type == 'pemasukan' ? 'green' : 'red',
                    'time_ago' => $this->timeAgo($transaction->created_at),
                ];
            });

        // ========== NOTIFIKASI ==========
        $notifications = collect();

        // Cek laba menurun
        $todaySummary = DailySummary::where('user_id', $user->id)
            ->where('date', $today)
            ->first();
        $yesterdaySummary = DailySummary::where('user_id', $user->id)
            ->where('date', $yesterday)
            ->first();

        if ($todaySummary && $yesterdaySummary && $todaySummary->net_profit < $yesterdaySummary->net_profit) {
            $difference = $yesterdaySummary->net_profit - $todaySummary->net_profit;
            $notifications->push([
                'type' => 'danger',
                'icon' => 'exclamation-triangle',
                'title' => 'Laba Menurun',
                'message' => 'Laba hari ini turun Rp ' . number_format($difference, 0, ',', '.') . ' dari kemarin',
                'time' => 'baru saja',
                'bg_color' => 'bg-red-50',
                'text_color' => 'text-red-600',
            ]);
        }

        // Cek pengeluaran besar
        $avgExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'pengeluaran')
            ->whereDate('transaction_date', '>=', Carbon::now()->subDays(30))
            ->avg('amount');

        if ($todayExpense > $avgExpense * 1.5 && $todayExpense > 0) {
            $notifications->push([
                'type' => 'warning',
                'icon' => 'bell',
                'title' => 'Pengeluaran Besar',
                'message' => 'Pengeluaran hari ini Rp ' . number_format($todayExpense, 0, ',', '.') . ' (di atas rata-rata)',
                'time' => 'baru saja',
                'bg_color' => 'bg-yellow-50',
                'text_color' => 'text-yellow-600',
            ]);
        }

        // Cek prive hari ini
        $todayPrive = (float) Prive::where('user_id', $user->id)
            ->whereDate('prive_date', $today)
            ->where('is_approved', 'approved')
            ->sum('amount');

        if ($todayPrive > 0) {
            $notifications->push([
                'type' => 'info',
                'icon' => 'money-bill-wave',
                'title' => 'Prive',
                'message' => 'Anda menarik Rp ' . number_format($todayPrive, 0, ',', '.') . ' untuk kebutuhan pribadi',
                'time' => 'baru saja',
                'bg_color' => 'bg-blue-50',
                'text_color' => 'text-blue-600',
            ]);
        }

        // Cek saldo menipis
        if ($currentBalance < 500000 && $currentBalance > 0) {
            $notifications->push([
                'type' => 'warning',
                'icon' => 'exclamation-circle',
                'title' => 'Saldo Menipis',
                'message' => 'Saldo usaha Anda tinggal Rp ' . number_format($currentBalance, 0, ',', '.'),
                'time' => 'hari ini',
                'bg_color' => 'bg-yellow-50',
                'text_color' => 'text-yellow-600',
            ]);
        }

        // Cek pencapaian target
        if ($monthProfit >= $targetProfit) {
            $notifications->push([
                'type' => 'success',
                'icon' => 'trophy',
                'title' => 'Target Tercapai!',
                'message' => 'Selamat! Target laba bulan ini sudah tercapai',
                'time' => 'hari ini',
                'bg_color' => 'bg-green-50',
                'text_color' => 'text-green-600',
            ]);
        }

        $unreadNotifications = $notifications->count();

        return view('dashboard.index', compact(
            'todayIncome',
            'todayExpense',
            'todayProfit',
            'currentBalance',
            'incomeChange',
            'expenseChange',
            'profitChange',
            'incomeArrow',
            'expenseArrow',
            'profitArrow',
            'chartLabels',
            'incomeData',
            'expenseData',
            'profitLabels',
            'profitData',
            'monthIncome',
            'monthExpense',
            'monthProfit',
            'monthPrive',
            'targetProfit',
            'profitPercentage',
            'recentTransactions',
            'notifications',
            'unreadNotifications'
        ));
    }

    /**
     * Calculate percentage change between two values.
     */
    private function calculatePercentageChange($old, $new): int
    {
        if ($old == 0) {
            return $new > 0 ? 100 : 0;
        }
        return (int) round((($new - $old) / $old) * 100);
    }

    /**
     * Calculate current cash balance.
     */
    private function calculateCurrentBalance($userId): float
    {
        $totalIncome = (float) Transaction::where('user_id', $userId)
            ->where('type', 'pemasukan')
            ->sum('amount');

        $totalExpense = (float) Transaction::where('user_id', $userId)
            ->where('type', 'pengeluaran')
            ->sum('amount');

        $totalPrive = (float) Prive::where('user_id', $userId)
            ->where('is_approved', 'approved')
            ->sum('amount');

        return $totalIncome - $totalExpense - $totalPrive;
    }

    /**
     * Format time ago.
     */
    private function timeAgo($datetime): string
    {
        $now = Carbon::now();
        $diff = $now->diffInMinutes($datetime);

        if ($diff < 1) return 'baru saja';
        if ($diff < 60) return $diff . ' menit yang lalu';

        $diff = $now->diffInHours($datetime);
        if ($diff < 24) return $diff . ' jam yang lalu';

        $diff = $now->diffInDays($datetime);
        if ($diff < 7) return $diff . ' hari yang lalu';

        return Carbon::parse($datetime)->format('d M');
    }
}
