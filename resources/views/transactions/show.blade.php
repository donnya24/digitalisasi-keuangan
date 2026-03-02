@extends('components.layout.app')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Informasi Transaksi</h3>
            <span class="px-3 py-1 text-sm rounded-full {{ $transaction->type == 'pemasukan' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ ucfirst($transaction->type) }}
            </span>
        </div>
        
        <!-- Content -->
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Tanggal</p>
                    <p class="text-base font-medium">{{ Carbon\Carbon::parse($transaction->transaction_date)->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nomor Referensi</p>
                    <p class="text-base font-medium">{{ $transaction->reference_number ?? '-' }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Kategori</p>
                    <div class="flex items-center mt-1">
                        <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $transaction->category->color ?? '#ccc' }}"></span>
                        <p class="text-base font-medium">{{ $transaction->category->name ?? 'Tanpa Kategori' }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Metode Pembayaran</p>
                    <p class="text-base font-medium">{{ $transaction->payment_method ?? '-' }}</p>
                </div>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Deskripsi</p>
                <p class="text-base font-medium">{{ $transaction->description }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Jumlah</p>
                <p class="text-2xl font-bold {{ $transaction->type == 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $transaction->type == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                </p>
            </div>
            
            @if($transaction->notes)
            <div>
                <p class="text-sm text-gray-500">Catatan</p>
                <p class="text-base bg-gray-50 p-3 rounded-lg">{{ $transaction->notes }}</p>
            </div>
            @endif
            
            <div class="grid grid-cols-2 gap-4 text-xs text-gray-400 pt-4 border-t">
                <div>Dibuat: {{ $transaction->created_at->format('d/m/Y H:i') }}</div>
                <div>Diupdate: {{ $transaction->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
            <a href="{{ route('transactions.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                Kembali
            </a>
            <a href="{{ route('transactions.edit', $transaction) }}" 
               class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <form action="{{ route('transactions.destroy', $transaction) }}" 
                  method="POST" 
                  onsubmit="return confirm('Hapus transaksi ini?')"
                  class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection