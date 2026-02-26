<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\DailySummary;
use App\Models\Prive;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::now('Asia/Jakarta')->toDateString();

        // Data hari ini
        $todayIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'pemasukan')
            ->whereDate('transaction_date', $today)
            ->sum('amount');

        $todayExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'pengeluaran')
            ->whereDate('transaction_date', $today)
            ->sum('amount');

        $todayProfit = $todayIncome - $todayExpense;

        // Saldo usaha (akumulasi semua transaksi - prive)
        $totalIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'pemasukan')
            ->sum('amount');

        $totalExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'pengeluaran')
            ->sum('amount');

        $totalPrive = Prive::where('user_id', $user->id)
            ->sum('amount');

        $currentBalance = $totalIncome - $totalExpense - $totalPrive;

        // Data grafik 7 hari terakhir
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now('Asia/Jakarta')->subDays($i)->toDateString();
            $dayData = DailySummary::where('user_id', $user->id)
                ->where('date', $date)
                ->first();

            $last7Days->push([
                'date' => Carbon::parse($date)->translatedFormat('d M'),
                'income' => $dayData->total_income ?? 0,
                'expense' => $dayData->total_expense ?? 0,
                'profit' => $dayData->net_profit ?? 0,
            ]);
        }

        // Data grafik 30 hari terakhir (laba)
        $last30Days = DailySummary::where('user_id', $user->id)
            ->where('date', '>=', Carbon::now('Asia/Jakarta')->subDays(30))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->translatedFormat('d M'),
                    'profit' => $item->net_profit,
                ];
            });

        // Data bulan ini
        $monthStart = Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();
        $monthEnd = Carbon::now('Asia/Jakarta')->endOfMonth()->toDateString();

        $monthIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'pemasukan')
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $monthExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'pengeluaran')
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $monthProfit = $monthIncome - $monthExpense;

        // Notifikasi laba menurun
        $yesterday = Carbon::now('Asia/Jakarta')->subDay()->toDateString();
        $yesterdaySummary = DailySummary::where('user_id', $user->id)
            ->where('date', $yesterday)
            ->first();

        $todaySummary = DailySummary::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $profitDecreased = false;
        if ($yesterdaySummary && $todaySummary) {
            if ($todaySummary->net_profit < $yesterdaySummary->net_profit) {
                $profitDecreased = true;
            }
        }

        return view('dashboard.index', compact(
            'todayIncome',
            'todayExpense',
            'todayProfit',
            'currentBalance',
            'last7Days',
            'last30Days',
            'monthIncome',
            'monthExpense',
            'monthProfit',
            'profitDecreased'
        ));
    }
}
