@extends('components.layout.app')

@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Report Type Selector -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.index', ['type' => 'daily'] + request()->all()) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $type == 'daily' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-calendar-day mr-1"></i> Harian
                </a>
                <a href="{{ route('reports.index', ['type' => 'monthly'] + request()->all()) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $type == 'monthly' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-calendar-alt mr-1"></i> Bulanan
                </a>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.export-pdf', request()->all()) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm"
                   target="_blank">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
                <a href="{{ route('reports.print', request()->all()) }}" 
                   class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm"
                   target="_blank">
                    <i class="fas fa-print mr-1"></i> Print
                </a>
            </div>
        </div>
    </div>

    <!-- Date/Month Selector -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" action="{{ route('reports.index') }}" id="reportForm">
            <input type="hidden" name="type" value="{{ $type }}">
            
            @if($type == 'daily')
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
                    <input type="date" name="date" value="{{ $date }}" 
                           class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           onchange="this.form.submit()">
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Menampilkan laporan untuk tanggal {{ Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                </div>
            </div>
            @else
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Bulan</label>
                    <input type="month" name="month" value="{{ $month }}" 
                           class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           onchange="this.form.submit()">
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Menampilkan laporan untuk bulan {{ Carbon\Carbon::parse($month)->translatedFormat('F Y') }}
                </div>
            </div>
            @endif
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-xs text-gray-500 mb-1">Total Pemasukan</p>
            <p class="text-lg font-bold text-green-600 truncate">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-xs text-gray-500 mb-1">Total Pengeluaran</p>
            <p class="text-lg font-bold text-red-600 truncate">Rp {{ number_format($summary['total_expense'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-xs text-gray-500 mb-1">Laba Bersih</p>
            <p class="text-lg font-bold {{ $summary['net_profit'] >= 0 ? 'text-blue-600' : 'text-red-600' }} truncate">
                Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-xs text-gray-500 mb-1">Jumlah Transaksi</p>
            <p class="text-lg font-bold text-purple-600 truncate">{{ $summary['transaction_count'] }}</p>
        </div>
    </div>

    @if($type == 'monthly' && isset($chartData) && $chartData['labels']->isNotEmpty())
    <!-- Chart Section (Monthly only) -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Grafik Perkembangan Harian</h3>
        <div class="chart-container" style="height: 250px;">
            <canvas id="reportChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Category Breakdown -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Rincian per Kategori</h3>
        
        @if($byCategory->isNotEmpty())
            <!-- Desktop Table -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pemasukan</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pengeluaran</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Laba/Rugi</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($byCategory as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $item['category'] }}</td>
                            <td class="px-4 py-3 text-sm text-right text-green-600">Rp {{ number_format($item['income'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-right text-red-600">Rp {{ number_format($item['expense'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-right {{ ($item['profit'] ?? $item['income'] - $item['expense']) >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                Rp {{ number_format(($item['profit'] ?? $item['income'] - $item['expense']), 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right text-gray-600">{{ $item['count'] }}x</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="block sm:hidden space-y-3">
                @foreach($byCategory as $item)
                <div class="border border-gray-200 rounded-lg p-3">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-medium text-gray-900">{{ $item['category'] }}</span>
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $item['count'] }} transaksi</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-xs text-gray-500">Pemasukan</span>
                            <p class="text-green-600 font-medium">Rp {{ number_format($item['income'], 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Pengeluaran</span>
                            <p class="text-red-600 font-medium">Rp {{ number_format($item['expense'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-t border-gray-100 flex justify-between">
                        <span class="text-xs text-gray-500">Laba/Rugi</span>
                        <span class="font-medium {{ ($item['profit'] ?? $item['income'] - $item['expense']) >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            Rp {{ number_format(($item['profit'] ?? $item['income'] - $item['expense']), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-chart-pie text-4xl mb-3"></i>
                <p class="text-sm">Belum ada data untuk periode ini</p>
            </div>
        @endif
    </div>

    <!-- Top Transactions (Daily) or Daily Summary (Monthly) -->
    @if($type == 'daily' && isset($topTransactions))
    <div class="bg-white rounded-lg shadow-sm p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Transaksi Terbesar</h3>
        
        @if($topTransactions->isNotEmpty())
            <!-- Desktop Table -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($topTransactions as $transaction)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $transaction->description }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $transaction->category->name ?? 'Tanpa Kategori' }}</td>
                            <td class="px-4 py-3 text-sm text-right {{ $transaction->type == 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="block sm:hidden space-y-2">
                @foreach($topTransactions as $transaction)
                <div class="border border-gray-200 rounded-lg p-3">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-medium text-gray-900">{{ $transaction->description }}</span>
                        <span class="text-sm font-bold {{ $transaction->type == 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex items-center text-xs text-gray-500">
                        <i class="fas fa-tag mr-1"></i>
                        {{ $transaction->category->name ?? 'Tanpa Kategori' }}
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 py-4">Tidak ada transaksi</p>
        @endif
    </div>
    @endif

    @if($type == 'monthly' && isset($dailySummaries))
    <!-- Daily Summary Table (Monthly) -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Ringkasan Harian</h3>
        
        @if($dailySummaries->isNotEmpty())
            <!-- Desktop Table -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pemasukan</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pengeluaran</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Laba</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($dailySummaries as $day)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ Carbon\Carbon::parse($day->date)->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3 text-sm text-right text-green-600">Rp {{ number_format($day->total_income, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-right text-red-600">Rp {{ number_format($day->total_expense, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-right {{ $day->net_profit >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                Rp {{ number_format($day->net_profit, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="block sm:hidden space-y-2">
                @foreach($dailySummaries as $day)
                <div class="border border-gray-200 rounded-lg p-3">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-medium text-gray-900">{{ Carbon\Carbon::parse($day->date)->translatedFormat('d M Y') }}</span>
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">Harian</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div>
                            <span class="text-gray-500">Masuk</span>
                            <p class="text-green-600 font-medium">Rp {{ number_format($day->total_income, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Keluar</span>
                            <p class="text-red-600 font-medium">Rp {{ number_format($day->total_expense, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Laba</span>
                            <p class="{{ $day->net_profit >= 0 ? 'text-blue-600' : 'text-red-600' }} font-medium">
                                Rp {{ number_format($day->net_profit, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 py-4">Tidak ada data untuk periode ini</p>
        @endif
    </div>
    @endif
</div>
@endsection

@push('scripts')
@if($type == 'monthly' && isset($chartData) && $chartData['labels']->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('reportChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: {!! json_encode($chartData['income']) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                    },
                    {
                        label: 'Pengeluaran',
                        data: {!! json_encode($chartData['expense']) !!},
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                    },
                    {
                        label: 'Laba',
                        data: {!! json_encode($chartData['profit']) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        borderDash: [5, 5],
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 8, font: { size: 10 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.raw || 0;
                                return label + ': Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp' + (value / 1000) + 'k';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endpush