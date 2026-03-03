<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\DailySummary;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display reports index with filters
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $type = $request->get('type', 'daily');
        $date = $request->get('date', Carbon::now('Asia/Jakarta')->toDateString());
        $month = $request->get('month', Carbon::now('Asia/Jakarta')->format('Y-m'));
        
        if ($type === 'daily') {
            $data = $this->getDailyReport($user->id, $date);
        } else {
            $data = $this->getMonthlyReport($user->id, $month);
        }
        
        // Get categories for filter (if needed)
        $categories = Category::where('user_id', $user->id)
            ->where('is_active', 'active')
            ->orderBy('name')
            ->get();
        
        return view('reports.index', array_merge([
            'type' => $type,
            'date' => $date,
            'month' => $month,
            'categories' => $categories,
        ], $data));
    }

    /**
     * Get daily report data
     */
    private function getDailyReport($userId, $date)
    {
        $transactions = Transaction::with('category')
            ->where('user_id', $userId)
            ->whereDate('transaction_date', $date)
            ->orderBy('created_at')
            ->get();
            
        $summary = [
            'total_income' => $transactions->where('type', 'pemasukan')->sum('amount'),
            'total_expense' => $transactions->where('type', 'pengeluaran')->sum('amount'),
            'net_profit' => $transactions->where('type', 'pemasukan')->sum('amount') - 
                           $transactions->where('type', 'pengeluaran')->sum('amount'),
            'transaction_count' => $transactions->count(),
        ];
        
        // Group by category
        $byCategory = $transactions->groupBy('category.name')
            ->map(function ($items, $key) {
                return [
                    'category' => $key ?: 'Tanpa Kategori',
                    'income' => $items->where('type', 'pemasukan')->sum('amount'),
                    'expense' => $items->where('type', 'pengeluaran')->sum('amount'),
                    'count' => $items->count(),
                ];
            })->values();
        
        // Get top transactions (limited to 10)
        $topTransactions = $transactions->sortByDesc('amount')->take(10);
        
        return compact('transactions', 'summary', 'byCategory', 'topTransactions');
    }

    /**
     * Get monthly report data
     */
    private function getMonthlyReport($userId, $month)
    {
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();
        
        // Daily summaries for chart
        $dailySummaries = DailySummary::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
        
        // All transactions in month
        $transactions = Transaction::with('category')
            ->where('user_id', $userId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date')
            ->get();
        
        // Monthly summary
        $summary = [
            'total_income' => $transactions->where('type', 'pemasukan')->sum('amount'),
            'total_expense' => $transactions->where('type', 'pengeluaran')->sum('amount'),
            'net_profit' => $transactions->where('type', 'pemasukan')->sum('amount') - 
                           $transactions->where('type', 'pengeluaran')->sum('amount'),
            'transaction_count' => $transactions->count(),
            'avg_daily_income' => $transactions->where('type', 'pemasukan')->avg('amount'),
            'avg_daily_expense' => $transactions->where('type', 'pengeluaran')->avg('amount'),
            'best_day' => $dailySummaries->sortByDesc('net_profit')->first(),
            'worst_day' => $dailySummaries->sortBy('net_profit')->first(),
        ];
        
        // Category breakdown
        $byCategory = $transactions->groupBy('category.name')
            ->map(function ($items, $key) {
                return [
                    'category' => $key ?: 'Tanpa Kategori',
                    'income' => $items->where('type', 'pemasukan')->sum('amount'),
                    'expense' => $items->where('type', 'pengeluaran')->sum('amount'),
                    'profit' => $items->where('type', 'pemasukan')->sum('amount') - 
                               $items->where('type', 'pengeluaran')->sum('amount'),
                    'count' => $items->count(),
                ];
            })->sortByDesc('profit')->values();
        
        // Chart data
        $chartData = [
            'labels' => $dailySummaries->map(function ($item) {
                return Carbon::parse($item->date)->translatedFormat('d M');
            })->values(),
            'income' => $dailySummaries->pluck('total_income'),
            'expense' => $dailySummaries->pluck('total_expense'),
            'profit' => $dailySummaries->pluck('net_profit'),
        ];
        
        return compact(
            'dailySummaries', 
            'transactions', 
            'summary', 
            'byCategory', 
            'chartData'
        );
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $type = $request->get('type', 'daily');
        
        if ($type === 'daily') {
            $date = $request->get('date', Carbon::now('Asia/Jakarta')->toDateString());
            $data = $this->getDailyReport($user->id, $date);
            $title = 'Laporan Harian - ' . Carbon::parse($date)->translatedFormat('d F Y');
            $filename = 'laporan-harian-' . $date . '.pdf';
        } else {
            $month = $request->get('month', Carbon::now('Asia/Jakarta')->format('Y-m'));
            $data = $this->getMonthlyReport($user->id, $month);
            $title = 'Laporan Bulanan - ' . Carbon::parse($month)->translatedFormat('F Y');
            $filename = 'laporan-bulanan-' . $month . '.pdf';
        }
        
        $pdf = Pdf::loadView('reports.pdf', array_merge($data, [
            'title' => $title,
            'user' => $user,
            'business' => $user->business,
            'type' => $type,
        ]));
        
        return $pdf->download($filename);
    }

    /**
     * Print report (opens in new window)
     */
    public function print(Request $request)
    {
        $user = auth()->user();
        $type = $request->get('type', 'daily');
        
        if ($type === 'daily') {
            $date = $request->get('date', Carbon::now('Asia/Jakarta')->toDateString());
            $data = $this->getDailyReport($user->id, $date);
            $title = 'Laporan Harian - ' . Carbon::parse($date)->translatedFormat('d F Y');
        } else {
            $month = $request->get('month', Carbon::now('Asia/Jakarta')->format('Y-m'));
            $data = $this->getMonthlyReport($user->id, $month);
            $title = 'Laporan Bulanan - ' . Carbon::parse($month)->translatedFormat('F Y');
        }
        
        return view('reports.print', array_merge($data, [
            'title' => $title,
            'user' => $user,
            'business' => $user->business,
            'type' => $type,
        ]));
    }
}