@extends('components.layout.app')

@section('title', 'Transaksi')
@section('page-title', 'Transaksi')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500 w-full">
            <p class="text-xs text-gray-500 mb-1">Total Pemasukan</p>
            <p class="text-lg font-bold text-green-600 break-words">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500 w-full">
            <p class="text-xs text-gray-500 mb-1">Total Pengeluaran</p>
            <p class="text-lg font-bold text-red-600 break-words">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500 w-full">
            <p class="text-xs text-gray-500 mb-1">Saldo</p>
            <p class="text-lg font-bold text-blue-600 break-words">Rp {{ number_format($balance, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" action="{{ route('transactions.index') }}" id="filterForm">
            <div class="space-y-3 sm:space-y-0 sm:grid sm:grid-cols-6 sm:gap-3 sm:items-end">
                <div class="w-full sm:col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Dari</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="w-full sm:col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sampai</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="w-full sm:col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipe</label>
                    <select name="type" id="type" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua</option>
                        <option value="pemasukan" {{ request('type') == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="pengeluaran" {{ request('type') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>

                <div class="w-full sm:col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" id="category_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="Cari deskripsi..."
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           autocomplete="off">
                </div>

                <div class="flex flex-row gap-2 sm:col-span-1 sm:justify-end">
                    <a href="{{ route('transactions.index') }}"
                       class="flex-1 sm:flex-none px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm text-center">
                        Reset
                    </a>
                    <button type="submit" id="filterButton"
                            class="flex-1 sm:flex-none px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        <i class="fas fa-search mr-1"></i> Cari
                    </button>
                </div>
            </div>

            @if(request('search'))
            <div class="mt-3 text-xs text-blue-600 bg-blue-50 p-2 rounded-lg">
                <i class="fas fa-info-circle mr-1"></i>
                Mencari: "{{ request('search') }}"
            </div>
            @endif
        </form>
    </div>

    <!-- Transactions Container -->
    <div id="transactions-table">
        @include('transactions.partials.table')
    </div>

    <!-- Pagination -->
    @if(method_exists($transactions, 'links') && $transactions->hasPages())
    <div class="mt-4">
        {{ $transactions->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@section('fab')
    <div class="floating-action-btn fixed bottom-20 lg:bottom-6 right-4 z-50">
        <a href="{{ route('transactions.create') }}"
           class="w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 flex items-center justify-center transition-all hover:scale-110">
            <i class="fas fa-plus text-xl"></i>
        </a>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const filterForm = document.getElementById('filterForm');
        const transactionsTable = document.getElementById('transactions-table');
        let timeoutId = null;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                if (timeoutId) clearTimeout(timeoutId);

                timeoutId = setTimeout(() => {
                    transactionsTable.innerHTML = `
                        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                            <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
                            <p class="mt-2 text-gray-500">Mencari...</p>
                        </div>
                    `;

                    const formData = new FormData(filterForm);
                    const params = new URLSearchParams(formData).toString();

                    fetch(`{{ route('transactions.index') }}?${params}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.text())
                    .then(html => {
                        transactionsTable.innerHTML = html;
                    })
                    .catch(() => {
                        transactionsTable.innerHTML = `
                            <div class="bg-white rounded-lg shadow-sm p-8 text-center text-red-600">
                                <i class="fas fa-exclamation-circle text-3xl"></i>
                                <p class="mt-2">Terjadi kesalahan</p>
                            </div>
                        `;
                    });
                }, 500);
            });
        }

        ['start_date', 'end_date', 'type', 'category_id'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', function() {
                    filterForm.submit();
                });
            }
        });
    });
</script>
@endpush
