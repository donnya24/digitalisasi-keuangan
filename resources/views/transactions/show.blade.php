@extends('components.layout.app')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="px-4 sm:px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900">
                <i class="fas fa-receipt mr-2 text-blue-600"></i>
                Detail Transaksi
            </h3>
            <span class="px-3 py-1 text-xs sm:text-sm rounded-full {{ $transaction->type == 'pemasukan' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                <i class="fas fa-{{ $transaction->type == 'pemasukan' ? 'arrow-down' : 'arrow-up' }} mr-1"></i>
                {{ ucfirst($transaction->type) }}
            </span>
        </div>

        <!-- Content -->
        <div class="p-4 sm:p-6 space-y-4">
            <!-- Grid 2 kolom -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">
                        <i class="far fa-calendar-alt mr-1"></i> Tanggal
                    </p>
                    <p class="text-sm sm:text-base font-medium">
                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d F Y') }}
                    </p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">
                        <i class="fas fa-hashtag mr-1"></i> Nomor Referensi
                    </p>
                    <p class="text-sm sm:text-base font-medium font-mono">
                        {{ $transaction->reference_number ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">
                        <i class="fas fa-tag mr-1"></i> Kategori
                    </p>
                    <div class="flex items-center">
                        @if($transaction->category && $transaction->category->icon)
                            <i class="fas fa-{{ $transaction->category->icon }} mr-2 text-sm" style="color: {{ $transaction->category->color ?? '#6B7280' }}"></i>
                        @else
                            <i class="fas fa-tag mr-2 text-gray-400 text-sm"></i>
                        @endif
                        <p class="text-sm sm:text-base font-medium">
                            {{ $transaction->category->name ?? 'Tanpa Kategori' }}
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">
                        <i class="fas fa-credit-card mr-1"></i> Metode Pembayaran
                    </p>
                    <p class="text-sm sm:text-base font-medium">
                        @if($transaction->payment_method)
                            <span class="capitalize">{{ $transaction->payment_method }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="bg-gray-50 p-3 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">
                    <i class="fas fa-align-left mr-1"></i> Deskripsi
                </p>
                <p class="text-sm sm:text-base font-medium">{{ $transaction->description }}</p>
            </div>

            <!-- Amount Card -->
            <div class="bg-gradient-to-r {{ $transaction->type == 'pemasukan' ? 'from-green-50 to-emerald-50' : 'from-red-50 to-rose-50' }} p-5 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">
                    <i class="fas fa-coins mr-1"></i> Jumlah
                </p>
                <p class="text-2xl sm:text-3xl font-bold {{ $transaction->type == 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $transaction->type == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                </p>
            </div>

            @if($transaction->notes)
            <div class="bg-gray-50 p-3 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">
                    <i class="fas fa-sticky-note mr-1"></i> Catatan
                </p>
                <p class="text-sm bg-white p-3 rounded-lg border border-gray-200">{{ $transaction->notes }}</p>
            </div>
            @endif

            <!-- Timestamps -->
            <div class="grid grid-cols-2 gap-4 text-xs text-gray-400 pt-4 border-t border-gray-200">
                <div>
                    <i class="far fa-clock mr-1"></i>
                    Dibuat: {{ $transaction->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="text-right">
                    <i class="far fa-edit mr-1"></i>
                    Diupdate: {{ $transaction->updated_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
        <!-- Actions -->
        <div class="px-4 sm:px-6 py-4 bg-gray-50 border-t flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
            <a href="{{ route('transactions.index') }}" 
            class="w-full sm:w-auto px-4 py-3 sm:py-2 text-center border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <a href="{{ route('transactions.edit', $transaction->id) }}" 
            class="w-full sm:w-auto px-4 py-3 sm:py-2 text-center bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors flex items-center justify-center">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            
            <!-- DELETE BUTTON - Langsung dengan form -->
            <form action="{{ route('transactions.destroy', $transaction->id) }}" 
                method="POST" 
                onsubmit="return confirmDelete('transaksi', '{{ addslashes($transaction->description) }}', {{ $transaction->amount }})"
                class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="w-full sm:w-auto px-4 py-3 sm:py-2 text-center bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
