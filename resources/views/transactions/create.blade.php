@extends('components.layout.app')

@section('title', 'Tambah Transaksi')
@section('page-title', 'Tambah Transaksi')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-xl shadow-sm p-5">
        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-5" id="transactionForm">
            @csrf
            
            <!-- Type Selection - Fixed -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Transaksi</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition-all type-option {{ old('type', $type) == 'pemasukan' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-200' }}"
                           data-type="pemasukan">
                        <input type="radio" name="type" value="pemasukan" class="hidden type-radio" 
                               {{ old('type', $type) == 'pemasukan' ? 'checked' : '' }}>
                        <span class="flex items-center">
                            <i class="fas fa-arrow-down text-green-600 mr-2"></i>
                            <span class="text-sm font-medium">Pemasukan</span>
                        </span>
                    </label>
                    <label class="flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition-all type-option {{ old('type', $type) == 'pengeluaran' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-red-200' }}"
                           data-type="pengeluaran">
                        <input type="radio" name="type" value="pengeluaran" class="hidden type-radio"
                               {{ old('type', $type) == 'pengeluaran' ? 'checked' : '' }}>
                        <span class="flex items-center">
                            <i class="fas fa-arrow-up text-red-600 mr-2"></i>
                            <span class="text-sm font-medium">Pengeluaran</span>
                        </span>
                    </label>
                </div>
                @error('type')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="category_name" 
                       id="category_name"
                       value="{{ old('category_name') }}"
                       placeholder="Contoh: Kopi, Makanan, Bahan Baku"
                       required
                       class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category_name') border-red-500 @enderror">
                @error('category_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Kategori akan otomatis tersimpan dan bisa dipakai lagi
                </p>
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
                           value="{{ old('amount') }}"
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
                       value="{{ old('description') }}"
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
                       value="{{ old('transaction_date', date('Y-m-d')) }}"
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
                    <option value="tunai" {{ old('payment_method') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="gopay" {{ old('payment_method') == 'gopay' ? 'selected' : '' }}>GoPay</option>
                    <option value="ovo" {{ old('payment_method') == 'ovo' ? 'selected' : '' }}>OVO</option>
                    <option value="dana" {{ old('payment_method') == 'dana' ? 'selected' : '' }}>DANA</option>
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
                          class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" 
                        class="w-full px-4 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-save mr-2"></i> Simpan Transaksi
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
        // ========== TYPE SWITCHING ==========
        const typeOptions = document.querySelectorAll('.type-option');
        const typeRadios = document.querySelectorAll('.type-radio');
        const categoryInput = document.getElementById('category_name');
        
        // Function to update type styles
        function updateTypeStyles(selectedType) {
            typeOptions.forEach(option => {
                const optionType = option.dataset.type;
                if (optionType === selectedType) {
                    if (optionType === 'pemasukan') {
                        option.classList.add('border-green-500', 'bg-green-50');
                        option.classList.remove('border-gray-200', 'hover:border-green-200', 'border-red-500', 'bg-red-50');
                    } else {
                        option.classList.add('border-red-500', 'bg-red-50');
                        option.classList.remove('border-gray-200', 'hover:border-red-200', 'border-green-500', 'bg-green-50');
                    }
                } else {
                    if (optionType === 'pemasukan') {
                        option.classList.add('border-gray-200', 'hover:border-green-200');
                        option.classList.remove('border-green-500', 'bg-green-50');
                    } else {
                        option.classList.add('border-gray-200', 'hover:border-red-200');
                        option.classList.remove('border-red-500', 'bg-red-50');
                    }
                }
            });
        }
        
        // Add click event to type options
        typeOptions.forEach(option => {
            option.addEventListener('click', function() {
                const selectedType = this.dataset.type;
                const radio = this.querySelector('.type-radio');
                
                // Check the radio
                radio.checked = true;
                
                // Update styles
                updateTypeStyles(selectedType);
                
                // Optional: Update category placeholder based on type
                if (selectedType === 'pemasukan') {
                    categoryInput.placeholder = 'Contoh: Kopi, Makanan, Minuman';
                } else {
                    categoryInput.placeholder = 'Contoh: Bahan Baku, Gaji, Listrik';
                }
            });
        });
        
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
            // Clean amount
            let rawValue = getRawNumber(amountInput.value);
            amountInput.value = rawValue;
        });
    });
</script>
@endpush