@extends('components.layout.app')

@section('title', 'Edit Transaksi')
@section('page-title', 'Edit Transaksi')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('transactions.update', $transaction->id) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Type (Readonly) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Transaksi</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" disabled {{ $transaction->type == 'pemasukan' ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Pemasukan</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" disabled {{ $transaction->type == 'pengeluaran' ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Pengeluaran</span>
                    </label>
                </div>
                <input type="hidden" name="type" value="{{ $transaction->type }}">
            </div>

            <!-- Category - Text Input -->
            <div>
                <label for="category_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="category_name" 
                       id="category_name"
                       value="{{ old('category_name', $transaction->category->name ?? '') }}"
                       placeholder="Contoh: Kopi, Makanan Ringan, Bahan Baku, dll"
                       required
                       maxlength="100"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category_name') border-red-500 @enderror">
                @error('category_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount - Hanya Angka -->
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                    Jumlah <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="amount" 
                       id="amount"
                       value="{{ old('amount', $transaction->amount) }}"
                       placeholder="0"
                       min="0"
                       step="1"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror">
                @error('amount')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Masukkan angka saja (contoh: 50000)
                </p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="description" 
                       id="description"
                       value="{{ old('description', $transaction->description) }}"
                       placeholder="Contoh: Penjualan Kopi, Beli Gula, dll"
                       required
                       maxlength="255"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Transaction Date -->
            <div>
                <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Transaksi <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       name="transaction_date" 
                       id="transaction_date"
                       value="{{ old('transaction_date', $transaction->transaction_date) }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('transaction_date') border-red-500 @enderror">
                @error('transaction_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Payment Method -->
            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">
                    Metode Pembayaran
                </label>
                <select name="payment_method" id="payment_method"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Metode (Opsional)</option>
                    <option value="tunai" {{ old('payment_method', $transaction->payment_method) == 'tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="qris" {{ old('payment_method', $transaction->payment_method) == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="transfer bank" {{ old('payment_method', $transaction->payment_method) == 'transfer bank' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="gopay" {{ old('payment_method', $transaction->payment_method) == 'gopay' ? 'selected' : '' }}>GoPay</option>
                    <option value="ovo" {{ old('payment_method', $transaction->payment_method) == 'ovo' ? 'selected' : '' }}>OVO</option>
                    <option value="dana" {{ old('payment_method', $transaction->payment_method) == 'dana' ? 'selected' : '' }}>DANA</option>
                </select>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Catatan
                </label>
                <textarea name="notes" 
                          id="notes" 
                          rows="3"
                          placeholder="Catatan tambahan (opsional)"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('notes', $transaction->notes) }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('transactions.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i> Update Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection