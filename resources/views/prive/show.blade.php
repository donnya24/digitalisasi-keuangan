@extends('components.layout.app')

@section('title', 'Detail Prive')
@section('page-title', 'Detail Prive')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="px-4 sm:px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900">
                <i class="fas fa-money-bill-wave mr-2 text-purple-600"></i>
                Detail Prive
            </h3>
            <span class="px-3 py-1 text-xs rounded-full {{ $prive->is_approved == 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                <i class="fas fa-{{ $prive->is_approved == 'approved' ? 'check-circle' : 'clock' }} mr-1"></i>
                {{ $prive->is_approved == 'approved' ? 'Disetujui' : 'Pending' }}
            </span>
        </div>

        <!-- Content -->
        <div class="p-4 sm:p-6 space-y-4">
            <!-- Date -->
            <div class="bg-gray-50 p-3 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">
                    <i class="far fa-calendar-alt mr-1"></i> Tanggal
                </p>
                <p class="text-base font-medium">
                    {{ \Carbon\Carbon::parse($prive->prive_date)->translatedFormat('d F Y') }}
                </p>
            </div>

            <!-- Description -->
            <div class="bg-gray-50 p-3 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">
                    <i class="fas fa-align-left mr-1"></i> Deskripsi
                </p>
                <p class="text-base font-medium">{{ $prive->description }}</p>
            </div>

            <!-- Purpose -->
            @if($prive->purpose)
            <div class="bg-gray-50 p-3 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">
                    <i class="fas fa-tag mr-1"></i> Keperluan
                </p>
                <p class="text-base font-medium capitalize">{{ $prive->purpose }}</p>
            </div>
            @endif

            <!-- Amount Card -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-5 rounded-xl">
                <p class="text-xs text-gray-600 mb-1">
                    <i class="fas fa-coins mr-1"></i> Jumlah
                </p>
                <p class="text-2xl sm:text-3xl font-bold text-purple-600">
                    Rp {{ number_format($prive->amount, 0, ',', '.') }}
                </p>
            </div>

            <!-- Timestamps -->
            <div class="grid grid-cols-2 gap-4 text-xs text-gray-400 pt-4 border-t border-gray-200">
                <div>
                    <i class="far fa-clock mr-1"></i>
                    Dibuat: {{ $prive->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="text-right">
                    <i class="far fa-edit mr-1"></i>
                    Diupdate: {{ $prive->updated_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-4 sm:px-6 py-4 bg-gray-50 border-t flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
            <a href="{{ route('prive.index') }}" 
            class="w-full sm:w-auto px-4 py-3 sm:py-2 text-center border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <a href="{{ route('prive.edit', $prive->id) }}" 
            class="w-full sm:w-auto px-4 py-3 sm:py-2 text-center bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors flex items-center justify-center">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            
            <!-- DELETE BUTTON - Langsung dengan form -->
            <form action="{{ route('prive.destroy', $prive->id) }}" 
                method="POST" 
                onsubmit="return confirmDelete('prive', '{{ addslashes($prive->description) }}', {{ $prive->amount }})"
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
