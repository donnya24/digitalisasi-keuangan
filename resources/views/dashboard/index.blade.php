@extends('components.layout.app')

@section('page-title', 'Dashboard')

@section('content')
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-white mb-4 sm:mb-8">
        <h2 class="text-lg sm:text-2xl font-bold mb-1 sm:mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
        <p class="text-xs sm:text-sm text-blue-100">Berikut ringkasan keuangan usaha Anda hari ini</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-8">
        <x-cards.stat-card 
            title="Pemasukan Hari Ini"
            :value="$todayIncome"
            :change="$incomeChange"
            :changeArrow="$incomeArrow"
            :changeColor="$incomeChange >= 0 ? 'green' : 'red'"
            icon="arrow-down"
            iconBg="green"
            borderColor="green"
        />
        
        <x-cards.stat-card 
            title="Pengeluaran Hari Ini"
            :value="$todayExpense"
            :change="$expenseChange"
            :changeArrow="$expenseArrow"
            :changeColor="$expenseChange <= 0 ? 'green' : 'red'"
            icon="arrow-up"
            iconBg="red"
            borderColor="red"
        />
        
        <x-cards.stat-card 
            title="Laba Bersih Hari Ini"
            :value="$todayProfit"
            :change="$profitChange"
            :changeArrow="$profitArrow"
            :changeColor="$profitChange >= 0 ? 'green' : 'red'"
            icon="chart-line"
            iconBg="blue"
            borderColor="blue"
        />
        
        <x-cards.stat-card 
            title="Saldo Usaha"
            :value="$currentBalance"
            note="Termasuk prive"
            icon="wallet"
            iconBg="purple"
            borderColor="purple"
        />
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-8">
        <!-- Grafik 7 Hari Terakhir -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm p-3 sm:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
                <h3 class="text-sm sm:text-lg font-semibold text-gray-800">Pemasukan 7 Hari</h3>
                <span class="text-xs sm:text-sm text-gray-500">{{ \Carbon\Carbon::now()->subDays(6)->format('d M') }} - {{ \Carbon\Carbon::now()->format('d M') }}</span>
            </div>
            <div class="chart-container">
                <canvas id="incomeChart"></canvas>
            </div>
        </div>

        <!-- Grafik Laba 30 Hari -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm p-3 sm:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
                <h3 class="text-sm sm:text-lg font-semibold text-gray-800">Laba 30 Hari</h3>
                <span class="text-xs sm:text-sm text-gray-500">{{ \Carbon\Carbon::now()->subDays(30)->format('d M') }} - {{ \Carbon\Carbon::now()->format('d M') }}</span>
            </div>
            <div class="chart-container">
                <canvas id="profitChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Summary & Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Monthly Summary -->
        <div class="lg:col-span-1 bg-white rounded-lg sm:rounded-xl shadow-sm p-3 sm:p-6">
            <h3 class="text-sm sm:text-lg font-semibold text-gray-800 mb-4">Ringkasan Bulan {{ \Carbon\Carbon::now()->translatedFormat('F') }}</h3>
            <div class="space-y-3 sm:space-y-4">
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-600">Total Pemasukan</span>
                    <span class="font-semibold text-green-600">Rp {{ number_format($monthIncome, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-600">Total Pengeluaran</span>
                    <span class="font-semibold text-red-600">Rp {{ number_format($monthExpense, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t text-sm sm:text-base">
                    <span class="text-gray-800 font-medium">Laba Bulan Ini</span>
                    <span class="font-bold text-blue-600">Rp {{ number_format($monthProfit, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-xs sm:text-sm">
                    <span class="text-gray-600">Total Prive</span>
                    <span class="font-semibold text-purple-600">Rp {{ number_format($monthPrive, 0, ',', '.') }}</span>
                </div>

                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="flex justify-between text-xs mb-1">
                        <span>Target Laba</span>
                        <span>{{ $profitPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 sm:h-2">
                        <div class="bg-blue-600 rounded-full h-1.5 sm:h-2" style="width: {{ $profitPercentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Target: Rp {{ number_format($targetProfit, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="lg:col-span-2 bg-white rounded-lg sm:rounded-xl shadow-sm p-3 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm sm:text-lg font-semibold text-gray-800">Transaksi Terbaru</h3>
                <a href="{{ route('transactions.index') }}" class="text-xs sm:text-sm text-blue-600 hover:underline">
                    Lihat semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="space-y-2 sm:space-y-3">
                @forelse($recentTransactions as $transaction)
                    <x-cards.transaction-item :transaction="$transaction" />
                @empty
                    <div class="text-center py-6 sm:py-8 text-gray-500">
                        <i class="fas fa-exchange-alt text-3xl sm:text-4xl mb-3"></i>
                        <p class="text-xs sm:text-sm">Belum ada transaksi</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('fab')
    <div class="floating-action-btn fixed bottom-20 lg:bottom-6 right-4 sm:right-6">
        <a href="{{ route('transactions.create') }}"
           class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 flex items-center justify-center transition-all hover:scale-110">
            <i class="fas fa-plus text-base sm:text-xl"></i>
        </a>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Income Chart (7 days)
        const incomeCtx = document.getElementById('incomeChart');
        if (incomeCtx) {
            new Chart(incomeCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: {!! json_encode($incomeData) !!},
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        },
                        {
                            label: 'Pengeluaran',
                            data: {!! json_encode($expenseData) !!},
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { 
                                boxWidth: 8,
                                font: { size: 10 }
                            }
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
                                },
                                font: { size: 10 }
                            }
                        },
                        x: {
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });
        }

        // Profit Chart (30 days)
        const profitCtx = document.getElementById('profitChart');
        if (profitCtx) {
            new Chart(profitCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($profitLabels) !!},
                    datasets: [{
                        label: 'Laba',
                        data: {!! json_encode($profitData) !!},
                        backgroundColor: '#3b82f6',
                        borderRadius: 4,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw || 0;
                                    return 'Laba: Rp ' + value.toLocaleString('id-ID');
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
                                },
                                font: { size: 10 }
                            }
                        },
                        x: {
                            ticks: { 
                                font: { size: 10 },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush