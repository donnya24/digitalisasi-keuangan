<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\DailySummary;
use App\Models\Prive;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with real data from database.
     */
    public function index()
        {
            $user = auth()->user();
            $userId = $user->id;
            $today = Carbon::now('Asia/Jakarta')->toDateString();
            $yesterday = Carbon::now('Asia/Jakarta')->subDay()->toDateString();

            // ========== HAPUS SEMUA CACHE DULU ==========
            $this->clearDashboardCache($userId);

            // ========== CACHE KEY UNTUK SETIAP QUERY ==========
            $cacheKeyTodayIncome = "dashboard.{$userId}.today_income";
            $cacheKeyTodayExpense = "dashboard.{$userId}.today_expense";
            $cacheKeyYesterdayIncome = "dashboard.{$userId}.yesterday_income";
            $cacheKeyYesterdayExpense = "dashboard.{$userId}.yesterday_expense";
            $cacheKeyCurrentBalance = "dashboard.{$userId}.current_balance";
            $cacheKeyLast7Days = "dashboard.{$userId}.last_7_days";
            $cacheKeyLast30Days = "dashboard.{$userId}.last_30_days";
            $cacheKeyMonthStats = "dashboard.{$userId}.month_stats";

            // Cache time: 5 menit (300 detik)
            $cacheTime = 300;

            // ========== DATA STATISTIK HARI INI (CACHED) ==========
            $todayIncome = Cache::remember($cacheKeyTodayIncome, $cacheTime, function() use ($userId, $today) {
                return (float) Transaction::where('user_id', $userId)
                    ->where('type', 'pemasukan')
                    ->whereDate('transaction_date', $today)
                    ->sum('amount');
            });

            $todayExpense = Cache::remember($cacheKeyTodayExpense, $cacheTime, function() use ($userId, $today) {
                return (float) Transaction::where('user_id', $userId)
                    ->where('type', 'pengeluaran')
                    ->whereDate('transaction_date', $today)
                    ->sum('amount');
            });

            $todayProfit = $todayIncome - $todayExpense;

            // Data kemarin (cached)
            $yesterdayIncome = Cache::remember($cacheKeyYesterdayIncome, $cacheTime, function() use ($userId, $yesterday) {
                return (float) Transaction::where('user_id', $userId)
                    ->where('type', 'pemasukan')
                    ->whereDate('transaction_date', $yesterday)
                    ->sum('amount');
            });

            $yesterdayExpense = Cache::remember($cacheKeyYesterdayExpense, $cacheTime, function() use ($userId, $yesterday) {
                return (float) Transaction::where('user_id', $userId)
                    ->where('type', 'pengeluaran')
                    ->whereDate('transaction_date', $yesterday)
                    ->sum('amount');
            });

            $yesterdayProfit = $yesterdayIncome - $yesterdayExpense;

            // Hitung persentase perubahan
            $incomeChange = $this->calculatePercentageChange($yesterdayIncome, $todayIncome);
            $expenseChange = $this->calculatePercentageChange($yesterdayExpense, $todayExpense);
            $profitChange = $this->calculatePercentageChange($yesterdayProfit, $todayProfit);

            $incomeArrow = $incomeChange >= 0 ? 'up' : 'down';
            $expenseArrow = $expenseChange >= 0 ? 'up' : 'down';
            $profitArrow = $profitChange >= 0 ? 'up' : 'down';

            // Saldo usaha (cached - lebih lama)
            $currentBalance = Cache::remember($cacheKeyCurrentBalance, 600, function() use ($userId) {
                return $this->calculateCurrentBalance($userId);
            });

            // ========== DATA GRAFIK 7 HARI TERAKHIR (CACHED) ==========
            $cached7Days = Cache::remember($cacheKeyLast7Days, $cacheTime, function() use ($userId) {
                $chartLabels = [];
                $incomeData = [];
                $expenseData = [];

                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now('Asia/Jakarta')->subDays($i)->toDateString();
                    $dayData = DailySummary::where('user_id', $userId)
                        ->where('date', $date)
                        ->first();

                    $dayIncome = (float) ($dayData->total_income ?? 0);
                    $dayExpense = (float) ($dayData->total_expense ?? 0);

                    $chartLabels[] = Carbon::parse($date)->translatedFormat('D');
                    $incomeData[] = $dayIncome;
                    $expenseData[] = $dayExpense;
                }

                return [
                    'labels' => $chartLabels,
                    'income' => $incomeData,
                    'expense' => $expenseData,
                    'has_data' => collect($incomeData)->sum() > 0 || collect($expenseData)->sum() > 0,
                ];
            });

            $chartLabels = $cached7Days['labels'];
            $incomeData = $cached7Days['income'];
            $expenseData = $cached7Days['expense'];
            $has7DaysData = $cached7Days['has_data'];

            // ========== DATA GRAFIK LABA 30 HARI (CACHED) ==========
            $cached30Days = Cache::remember($cacheKeyLast30Days, 600, function() use ($userId) {
                $profitLabels = [];
                $profitData = [];
                $hasData = false;

                $dailyProfits = DailySummary::where('user_id', $userId)
                    ->where('date', '>=', Carbon::now('Asia/Jakarta')->subDays(30))
                    ->orderBy('date')
                    ->get();

                if ($dailyProfits->isNotEmpty() && $dailyProfits->sum('net_profit') != 0) {
                    $hasData = true;

                    // Jika data kurang dari 7, tampilkan semua
                    if ($dailyProfits->count() <= 7) {
                        foreach ($dailyProfits as $item) {
                            $profitLabels[] = Carbon::parse($item->date)->translatedFormat('d M');
                            $profitData[] = (float) $item->net_profit;
                        }
                    } else {
                        // Sampling data - ambil 7 titik merata
                        $step = floor($dailyProfits->count() / 7);
                        for ($i = 0; $i < 7; $i++) {
                            $index = min($i * $step, $dailyProfits->count() - 1);
                            $item = $dailyProfits[$index];
                            $profitLabels[] = Carbon::parse($item->date)->translatedFormat('d M');
                            $profitData[] = (float) $item->net_profit;
                        }
                    }
                }

                return [
                    'labels' => $profitLabels,
                    'data' => $profitData,
                    'has_data' => $hasData,
                ];
            });

            $profitLabels = $cached30Days['labels'];
            $profitData = $cached30Days['data'];
            $hasProfitData = $cached30Days['has_data'];

            // ========== DATA BULAN INI (CACHED) ==========
            $cachedMonthStats = Cache::remember($cacheKeyMonthStats, 600, function() use ($userId) {
                $monthStart = Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();
                $monthEnd = Carbon::now('Asia/Jakarta')->endOfMonth()->toDateString();

                $monthIncome = (float) Transaction::where('user_id', $userId)
                    ->where('type', 'pemasukan')
                    ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                    ->sum('amount');

                $monthExpense = (float) Transaction::where('user_id', $userId)
                    ->where('type', 'pengeluaran')
                    ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                    ->sum('amount');

                $monthProfit = $monthIncome - $monthExpense;

                $monthPrive = (float) Prive::where('user_id', $userId)
                    ->whereBetween('prive_date', [$monthStart, $monthEnd])
                    ->where('is_approved', 'approved')
                    ->sum('amount');

                return [
                    'income' => $monthIncome,
                    'expense' => $monthExpense,
                    'profit' => $monthProfit,
                    'prive' => $monthPrive,
                    'has_data' => $monthIncome > 0 || $monthExpense > 0,
                ];
            });

            $monthIncome = $cachedMonthStats['income'];
            $monthExpense = $cachedMonthStats['expense'];
            $monthProfit = $cachedMonthStats['profit'];
            $monthPrive = $cachedMonthStats['prive'];
            $hasMonthData = $cachedMonthStats['has_data'];

            // Target laba bulanan (dari config)
            $targetProfit = config('business.target_profit', 10000000);
            $profitPercentage = $targetProfit > 0 ? min(100, round(($monthProfit / $targetProfit) * 100)) : 0;

            // ========== GENERATE DAN SIMPAN NOTIFIKASI KE DATABASE ==========
            $this->generateAndSaveNotifications(
                $userId,
                $today,
                $yesterday,
                $todayIncome,
                $todayExpense,
                $currentBalance,
                $monthProfit,
                $targetProfit
            );

            // ========== TRANSAKSI TERBARU ==========
            $recentTransactions = Transaction::with('category')
                ->where('user_id', $userId)
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
                'has7DaysData',
                'profitLabels',
                'profitData',
                'hasProfitData',
                'monthIncome',
                'monthExpense',
                'monthProfit',
                'monthPrive',
                'hasMonthData',
                'targetProfit',
                'profitPercentage',
                'recentTransactions'
            ));
        }

        /**
         * Hapus semua cache dashboard untuk user tertentu
         */
        private function clearDashboardCache($userId): void
        {
            $cacheKeys = [
                "dashboard.{$userId}.today_income",
                "dashboard.{$userId}.today_expense",
                "dashboard.{$userId}.yesterday_income",
                "dashboard.{$userId}.yesterday_expense",
                "dashboard.{$userId}.current_balance",
                "dashboard.{$userId}.last_7_days",
                "dashboard.{$userId}.last_30_days",
                "dashboard.{$userId}.month_stats",
            ];

            foreach ($cacheKeys as $key) {
                if (Cache::has($key)) {
                    Cache::forget($key);
                }
            }

            // Log untuk debugging (opsional)
            \Log::info("Dashboard cache cleared for user {$userId}");
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
     * Generate dan simpan notifikasi ke database.
     */
    /**
 * Generate dan simpan notifikasi ke database.
 */
    private function generateAndSaveNotifications($userId, $today, $yesterday, $todayIncome, $todayExpense, $currentBalance, $monthProfit, $targetProfit): void
    {
        // Cek apakah user punya data transaksi sama sekali
        $hasAnyTransaction = Transaction::where('user_id', $userId)->exists();

        if (!$hasAnyTransaction) {
            return; // Jika tidak ada transaksi, jangan buat notifikasi
        }

        // ========== 1. CEK LABA MENURUN DENGAN PENCEGAHAN DUPLIKASI ==========
        $todaySummary = DailySummary::where('user_id', $userId)
            ->where('date', $today)
            ->first();
        $yesterdaySummary = DailySummary::where('user_id', $userId)
            ->where('date', $yesterday)
            ->first();

        if ($todaySummary && $yesterdaySummary && $todaySummary->net_profit < $yesterdaySummary->net_profit) {
            $difference = $yesterdaySummary->net_profit - $todaySummary->net_profit;
            $percentageDrop = $yesterdaySummary->net_profit > 0
                ? round(($difference / $yesterdaySummary->net_profit) * 100)
                : 0;

            if ($difference > 50000 || $percentageDrop > 10) {
                // 🔥 CEK APAKAH SUDAH ADA NOTIFIKASI SERUPA HARI INI
                $existingNotification = \App\Models\Notification::where('user_id', $userId)
                    ->where('type', 'profit_decrease')
                    ->whereDate('created_at', Carbon::today())
                    ->first();

                // HANYA BUAT JIKA BELUM ADA
                if (!$existingNotification) {
                    \App\Models\Notification::create([
                        'user_id' => $userId,
                        'type' => 'profit_decrease',
                        'title' => '⚠️ Laba Menurun',
                        'message' => 'Laba hari ini turun Rp ' . number_format($difference, 0, ',', '.') . ' (' . $percentageDrop . '%) dari kemarin',
                        'is_read' => 'unread',
                        'icon' => 'exclamation-triangle',
                        'bg_color' => 'bg-red-50',
                        'text_color' => 'text-red-600',
                        'data' => json_encode([
                            'today' => $todaySummary->net_profit,
                            'yesterday' => $yesterdaySummary->net_profit,
                            'difference' => $difference,
                            'percentage' => $percentageDrop,
                        ]),
                    ]);
                }
            }
        }

        // ========== 2. CEK LABA NAIK SIGNIFIKAN ==========
        if ($todaySummary && $yesterdaySummary && $todaySummary->net_profit > $yesterdaySummary->net_profit) {
            $increase = $todaySummary->net_profit - $yesterdaySummary->net_profit;
            $percentageIncrease = $yesterdaySummary->net_profit > 0
                ? round(($increase / $yesterdaySummary->net_profit) * 100)
                : 100;

            if ($percentageIncrease > 20) {
                // 🔥 CEK DUPLIKASI
                $existingNotification = \App\Models\Notification::where('user_id', $userId)
                    ->where('type', 'profit_increase')
                    ->whereDate('created_at', Carbon::today())
                    ->first();

                if (!$existingNotification) {
                    \App\Models\Notification::create([
                        'user_id' => $userId,
                        'type' => 'profit_increase',
                        'title' => '📈 Laba Meningkat',
                        'message' => 'Laba hari ini naik Rp ' . number_format($increase, 0, ',', '.') . ' (' . $percentageIncrease . '%) dari kemarin',
                        'is_read' => 'unread',
                        'icon' => 'chart-line',
                        'bg_color' => 'bg-green-50',
                        'text_color' => 'text-green-600',
                        'data' => json_encode([
                            'today' => $todaySummary->net_profit,
                            'yesterday' => $yesterdaySummary->net_profit,
                            'increase' => $increase,
                            'percentage' => $percentageIncrease,
                        ]),
                    ]);
                }
            }
        }

        // ========== 3. CEK PENGELUARAN BESAR ==========
        $avgExpense = (float) Transaction::where('user_id', $userId)
            ->where('type', 'pengeluaran')
            ->whereDate('transaction_date', '>=', Carbon::now()->subDays(30))
            ->avg('amount');

        if ($avgExpense > 0 && $todayExpense > $avgExpense * 1.5 && $todayExpense > 0) {
            $ratio = round(($todayExpense / $avgExpense), 1);

            // 🔥 CEK DUPLIKASI
            $existingNotification = \App\Models\Notification::where('user_id', $userId)
                ->where('type', 'large_expense')
                ->whereDate('created_at', Carbon::today())
                ->first();

            if (!$existingNotification) {
                \App\Models\Notification::create([
                    'user_id' => $userId,
                    'type' => 'large_expense',
                    'title' => '💰 Pengeluaran Besar',
                    'message' => 'Pengeluaran hari ini Rp ' . number_format($todayExpense, 0, ',', '.') . ' (' . $ratio . 'x rata-rata)',
                    'is_read' => 'unread',
                    'icon' => 'bell',
                    'bg_color' => 'bg-yellow-50',
                    'text_color' => 'text-yellow-600',
                    'data' => json_encode([
                        'amount' => $todayExpense,
                        'average' => $avgExpense,
                        'ratio' => $ratio,
                    ]),
                ]);
            }
        }

        // ========== 5. CEK SALDO MENIPIS (DENGAN PENCEGAHAN DUPLIKASI) ==========
        $lowBalanceThreshold = config('business.alerts.low_balance', 500000);
        if ($currentBalance < $lowBalanceThreshold && $currentBalance > 0) {
            // Cek apakah sudah ada notifikasi serupa dalam 3 hari terakhir
            $recentNotification = \App\Models\Notification::where('user_id', $userId)
                ->where('type', 'low_balance')
                ->whereDate('created_at', '>=', Carbon::now()->subDays(3))
                ->exists();

            if (!$recentNotification) {
                \App\Models\Notification::create([
                    'user_id' => $userId,
                    'type' => 'low_balance',
                    'title' => '⚠️ Saldo Menipis',
                    'message' => 'Saldo usaha Anda tinggal Rp ' . number_format($currentBalance, 0, ',', '.'),
                    'is_read' => 'unread',
                    'icon' => 'exclamation-circle',
                    'bg_color' => 'bg-yellow-50',
                    'text_color' => 'text-yellow-600',
                    'data' => json_encode([
                        'balance' => $currentBalance,
                        'threshold' => $lowBalanceThreshold,
                    ]),
                ]);
            }
        }

        // ========== 6. CEK PENCAPAIAN TARGET ==========
        if ($monthProfit >= $targetProfit && $targetProfit > 0) {
            // Cek apakah sudah ada notifikasi target tercapai bulan ini
            $existingNotification = \App\Models\Notification::where('user_id', $userId)
                ->where('type', 'target_achieved')
                ->whereMonth('created_at', Carbon::now()->month)
                ->exists();

            if (!$existingNotification) {
                \App\Models\Notification::create([
                    'user_id' => $userId,
                    'type' => 'target_achieved',
                    'title' => '🎉 Target Tercapai!',
                    'message' => 'Selamat! Target laba bulan ini Rp ' . number_format($targetProfit, 0, ',', '.') . ' sudah tercapai',
                    'is_read' => 'unread',
                    'icon' => 'trophy',
                    'bg_color' => 'bg-green-50',
                    'text_color' => 'text-green-600',
                    'data' => json_encode([
                        'profit' => $monthProfit,
                        'target' => $targetProfit,
                        'percentage' => round(($monthProfit / $targetProfit) * 100),
                    ]),
                ]);
            }
        }

        // ========== 7. CEK TARGET HAMPIR TERCAPAI (80%) ==========
        $progressPercentage = $targetProfit > 0 ? round(($monthProfit / $targetProfit) * 100) : 0;
        if ($progressPercentage >= 80 && $progressPercentage < 100 && $monthProfit > 0) {
            // Cek apakah sudah ada notifikasi progress dalam 7 hari terakhir
            $existingNotification = \App\Models\Notification::where('user_id', $userId)
                ->where('type', 'target_progress')
                ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
                ->exists();

            if (!$existingNotification) {
                \App\Models\Notification::create([
                    'user_id' => $userId,
                    'type' => 'target_progress',
                    'title' => '📊 Progress Target',
                    'message' => 'Laba bulan ini sudah mencapai ' . $progressPercentage . '% dari target',
                    'is_read' => 'unread',
                    'icon' => 'chart-pie',
                    'bg_color' => 'bg-blue-50',
                    'text_color' => 'text-blue-600',
                    'data' => json_encode([
                        'profit' => $monthProfit,
                        'target' => $targetProfit,
                        'percentage' => $progressPercentage,
                    ]),
                ]);
            }
        }

        // ========== 8. CEK TIDAK ADA TRANSAKSI HARI INI ==========
        $todayTransactionCount = Transaction::where('user_id', $userId)
            ->whereDate('transaction_date', $today)
            ->count();

        if ($todayTransactionCount == 0 && $hasAnyTransaction) {
            // Cek apakah sudah ada notifikasi serupa dalam 3 hari terakhir
            $recentNotification = \App\Models\Notification::where('user_id', $userId)
                ->where('type', 'no_transaction')
                ->whereDate('created_at', '>=', Carbon::now()->subDays(3))
                ->exists();

            if (!$recentNotification) {
                \App\Models\Notification::create([
                    'user_id' => $userId,
                    'type' => 'no_transaction',
                    'title' => '📝 Belum Ada Transaksi',
                    'message' => 'Anda belum mencatat transaksi hari ini. Jangan lupa catat pemasukan dan pengeluaran!',
                    'is_read' => 'unread',
                    'icon' => 'bell',
                    'bg_color' => 'bg-blue-50',
                    'text_color' => 'text-blue-600',
                    'data' => json_encode([
                        'date' => $today,
                    ]),
                ]);
            }
        }

        // ========== 9. HAPUS NOTIFIKASI LAMA ==========
        \App\Models\Notification::where('user_id', $userId)
            ->where('is_read', 'read')
            ->whereDate('created_at', '<=', Carbon::now()->subDays(30))
            ->delete();

        \App\Models\Notification::where('user_id', $userId)
            ->where('is_read', 'archived')
            ->whereDate('created_at', '<=', Carbon::now()->subDays(7))
            ->delete();
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
