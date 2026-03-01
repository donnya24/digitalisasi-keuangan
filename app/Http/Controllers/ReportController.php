<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\DailySummary;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'daily');
        $date = $request->get('date', Carbon::now('Asia/Jakarta')->toDateString());
        $month = $request->get('month', Carbon::now('Asia/Jakarta')->format('Y-m'));
        
        if ($type === 'daily') {
            $data = $this->getDailyReport($date);
        } else {
            $data = $this->getMonthlyReport($month);
        }
        
        return view('reports.index', array_merge([
            'type' => $type,
            'date' => $date,
            'month' => $month
        ], $data));
    }

    private function getDailyReport($date)
    {
        $user = auth()->user();
        
        $transactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->whereDate('transaction_date', $date)
            ->orderBy('created_at')
            ->get();
            
        $summary = [
            'total_income' => $transactions->where('type', 'pemasukan')->sum('amount'),
            'total_expense' => $transactions->where('type', 'pengeluaran')->sum('amount'),
            'net_profit' => $transactions->where('type', 'pemasukan')->sum('amount') - 
                           $transactions->where('type', 'pengeluaran')->sum('amount')
        ];
        
        $byCategory = $transactions->groupBy('category.name')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total' => $items->sum('amount')
                ];
            });
            
        return compact('transactions', 'summary', 'byCategory');
    }

    private function getMonthlyReport($month)
    {
        $user = auth()->user();
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();
        
        $dailySummaries = DailySummary::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
            
        $transactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date')
            ->get();
            
        $summary = [
            'total_income' => $transactions->where('type', 'pemasukan')->sum('amount'),
            'total_expense' => $transactions->where('type', 'pengeluaran')->sum('amount'),
            'net_profit' => $transactions->where('type', 'pemasukan')->sum('amount') - 
                           $transactions->where('type', 'pengeluaran')->sum('amount'),
            'avg_daily_income' => $transactions->where('type', 'pemasukan')->avg('amount'),
            'best_day' => $dailySummaries->sortByDesc('net_profit')->first()
        ];
        
        // Grafik perkembangan harian
        $chartData = $dailySummaries->map(function ($item) {
            return [
                'date' => Carbon::parse($item->date)->translatedFormat('d M'),
                'profit' => $item->net_profit
            ];
        });
        
        return compact('dailySummaries', 'transactions', 'summary', 'chartData');
    }

    public function exportPdf(Request $request)
    {
        $type = $request->get('type', 'daily');
        
        if ($type === 'daily') {
            $data = $this->getDailyReport($request->date);
            $title = 'Laporan Harian - ' . Carbon::parse($request->date)->translatedFormat('d F Y');
        } else {
            $data = $this->getMonthlyReport($request->month);
            $title = 'Laporan Bulanan - ' . Carbon::parse($request->month)->translatedFormat('F Y');
        }
        
        $pdf = Pdf::loadView('reports.pdf', array_merge($data, [
            'title' => $title,
            'business' => auth()->user()->business
        ]));
        
        return $pdf->download('laporan-' . $type . '-' . now()->format('YmdHis') . '.pdf');
    }
}