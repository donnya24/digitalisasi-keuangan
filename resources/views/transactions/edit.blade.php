@extends('components.layout.app')

@section('title', 'Edit Transaksi')
@section('page-title', 'Edit Transaksi')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-xl shadow-sm p-5">
        <form method="POST" action="{{ route('transactions.update', $transaction->id) }}" class="space-y-5" id="transactionForm">
            @csrf
            @method('PUT')
            
            <!-- Type (Fixed - tidak bisa diubah di edit) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Transaksi</label>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center justify-center p-4 border-2 rounded-lg {{ $transaction->type == 'pemasukan' ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                        <span class="flex items-center">
                            <i class="fas fa-arrow-down text-green-600 mr-2"></i>
                            <span class="text-sm font-medium">Pemasukan</span>
                        </span>
                    </div>
                    <div class="flex items-center justify-center p-4 border-2 rounded-lg {{ $transaction->type == 'pengeluaran' ? 'border-red-500 bg-red-50' : 'border-gray-200 bg-gray-50' }}">
                        <span class="flex items-center">
                            <i class="fas fa-arrow-up text-red-600 mr-2"></i>
                            <span class="text-sm font-medium">Pengeluaran</span>
                        </span>
                    </div>
                </div>
                <input type="hidden" name="type" value="{{ $transaction->type }}">
            </div>

            <!-- Category -->
            <div>
                <label for="category_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="category_name" 
                       id="category_name"
                       value="{{ old('category_name', $transaction->category->name ?? '') }}"
                       placeholder="Contoh: Kopi, Makanan, Bahan Baku"
                       required
                       class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category_name') border-red-500 @enderror">
                @error('category_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                    Jumlah (Rp) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-gray-500 font-medium">Rp</span>
                    </div>
                    <input type="text" 
                           name="amount" 
                           id="amount"
                           value="{{ old('amount', number_format($transaction->amount, 0, ',', '.')) }}"
                           placeholder="0"
                           class="w-full pl-14 pr-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror">
                </div>
                @error('amount')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
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
                       placeholder="Contoh: Penjualan Kopi, Beli Gula"
                       required
                       class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Transaction Date -->
            <div>
                <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       name="transaction_date" 
                       id="transaction_date"
                       value="{{ old('transaction_date', $transaction->transaction_date) }}"
                       required
                       class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('transaction_date') border-red-500 @enderror">
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
                        class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Metode (Opsional)</option>
                    <option value="tunai" {{ old('payment_method', $transaction->payment_method) == 'tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="qris" {{ old('payment_method', $transaction->payment_method) == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="transfer" {{ old('payment_method', $transaction->payment_method) == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
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
                          class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('notes', $transaction->notes) }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" 
                        class="w-full px-4 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-save mr-2"></i> Update Transaksi
                </button>
                <a href="{{ route('transactions.index') }}" 
                   class="w-full px-4 py-4 text-center border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ========== AMOUNT FORMATTING ==========
        const amountInput = document.getElementById('amount');
        
        function formatRupiah(angka) {
            if (!angka) return '';
            let numberString = angka.toString().replace(/[^0-9]/g, '');
            if (numberString === '') return '';
            return new Intl.NumberFormat('id-ID').format(numberString);
        }
        
        function getRawNumber(formattedValue) {
            if (!formattedValue) return '';
            return formattedValue.replace(/\./g, '');
        }
        
        if (amountInput.value) {
            let rawInitial = amountInput.value.replace(/[^0-9]/g, '');
            amountInput.value = formatRupiah(rawInitial);
        }
        
        amountInput.addEventListener('input', function(e) {
            let rawValue = this.value.replace(/[^0-9]/g, '');
            this.value = rawValue ? formatRupiah(rawValue) : '';
        });
        
        // ========== FORM SUBMIT ==========
        const form = document.getElementById('transactionForm');
        form.addEventListener('submit', function(e) {
            let rawValue = getRawNumber(amountInput.value);
            amountInput.value = rawValue;
        });
    });
</script>
@endpush