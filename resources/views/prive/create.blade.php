@extends('components.layout.app')

@section('title', 'Tambah Prive')
@section('page-title', 'Tambah Prive')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-xl shadow-sm p-5">
        <form method="POST" action="{{ route('prive.store') }}" class="space-y-5" id="priveForm">
            @csrf
            
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
                           class="w-full pl-14 pr-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('amount') border-red-500 @enderror">
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
                       placeholder="Contoh: Ambil uang untuk kebutuhan pribadi"
                       required
                       class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('description') border-red-500 @enderror">
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Prive Date -->
            <div>
                <label for="prive_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       name="prive_date" 
                       id="prive_date"
                       value="{{ old('prive_date', date('Y-m-d')) }}"
                       required
                       class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('prive_date') border-red-500 @enderror">
                @error('prive_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Purpose -->
            <div>
                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">
                    Keperluan (Opsional)
                </label>
                <select name="purpose" id="purpose"
                        class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Pilih Keperluan</option>
                    <option value="kebutuhan pribadi" {{ old('purpose') == 'kebutuhan pribadi' ? 'selected' : '' }}>Kebutuhan Pribadi</option>
                    <option value="belanja bulanan" {{ old('purpose') == 'belanja bulanan' ? 'selected' : '' }}>Belanja Bulanan</option>
                    <option value="pendidikan" {{ old('purpose') == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                    <option value="kesehatan" {{ old('purpose') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                    <option value="transportasi" {{ old('purpose') == 'transportasi' ? 'selected' : '' }}>Transportasi</option>
                    <option value="hiburan" {{ old('purpose') == 'hiburan' ? 'selected' : '' }}>Hiburan</option>
                </select>
                @error('purpose')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" 
                        class="w-full px-4 py-4 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium">
                    <i class="fas fa-save mr-2"></i> Simpan Prive
                </button>
                <a href="{{ route('prive.index') }}" 
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
        
        const form = document.getElementById('priveForm');
        form.addEventListener('submit', function(e) {
            let rawValue = getRawNumber(amountInput.value);
            amountInput.value = rawValue;
        });
    });
</script>
@endpush