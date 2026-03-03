@extends('components.layout.app')

@section('title', 'Prive')
@section('page-title', 'Prive')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
            <p class="text-xs text-gray-500 mb-1">
                <i class="fas fa-calendar-alt mr-1 text-purple-500"></i>
                Total Prive Bulan Ini
            </p>
            <p class="text-lg sm:text-xl font-bold text-purple-600">Rp {{ number_format($totalPriveBulanIni, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-indigo-500">
            <p class="text-xs text-gray-500 mb-1">
                <i class="fas fa-database mr-1 text-indigo-500"></i>
                Total Semua Prive
            </p>
            <p class="text-lg sm:text-xl font-bold text-indigo-600">Rp {{ number_format($totalAllPrive, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" action="{{ route('prive.index') }}" id="filterForm">
            <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        <i class="fas fa-calendar mr-1"></i> Pilih Bulan
                    </label>
                    <select name="month" id="month" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Semua Bulan</option>
                        @foreach($months as $month)
                            <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        <i class="fas fa-filter mr-1"></i> Status
                    </label>
                    <select name="status" id="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Semua Status</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('prive.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm flex items-center">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm flex items-center">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Prive List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-calendar mr-1"></i> Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-align-left mr-1"></i> Deskripsi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-tag mr-1"></i> Keperluan
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-coins mr-1"></i> Jumlah
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-check-circle mr-1"></i> Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-cog mr-1"></i> Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($prives as $prive)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($prive->prive_date)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                {{ $prive->description }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($prive->purpose)
                                    <span class="capitalize">{{ $prive->purpose }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-purple-600 text-right">
                                Rp {{ number_format($prive->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-xs rounded-full {{ $prive->is_approved == 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    <i class="fas fa-{{ $prive->is_approved == 'approved' ? 'check-circle' : 'clock' }} mr-1"></i>
                                    {{ $prive->is_approved == 'approved' ? 'Disetujui' : 'Pending' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                <!-- DESKTOP ACTION BUTTONS -->
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Tombol Detail -->
                                    <a href="{{ route('prive.show', $prive->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 rounded-md hover:bg-blue-100 transition-colors duration-200"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                        <span class="sr-only md:not-sr-only md:ml-1 text-xs">Detail</span>
                                    </a>

                                    <!-- Tombol Edit -->
                                    <a href="{{ route('prive.edit', $prive->id) }}"
                                       class="inline-flex items-center px-2 py-1 bg-yellow-50 text-yellow-600 rounded-md hover:bg-yellow-100 transition-colors duration-200"
                                       title="Edit Prive">
                                        <i class="fas fa-edit text-sm"></i>
                                        <span class="sr-only md:not-sr-only md:ml-1 text-xs">Edit</span>
                                    </a>

                                    <!-- Tombol Delete - Langsung dengan form -->
                                    <form action="{{ route('prive.destroy', $prive->id) }}" 
                                        method="POST" 
                                        onsubmit="return confirmDelete('prive', '{{ addslashes($prive->description) }}', {{ $prive->amount }})"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 rounded-md hover:bg-red-100 hover:text-red-700 transition-colors duration-200"
                                                title="Hapus Prive">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                            <span class="sr-only md:not-sr-only md:ml-1 text-xs">Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-purple-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-money-bill-wave text-3xl text-purple-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 mb-1">Belum ada data prive</p>
                                    <p class="text-xs text-gray-500 mb-4">Mulai catat pengambilan uang pribadi Anda</p>
                                    <a href="{{ route('prive.create') }}"
                                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i> Tambah Prive
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="block sm:hidden space-y-3 p-4">
            @forelse($prives as $prive)
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <!-- Header with Date and Status -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center text-xs text-gray-500">
                            <i class="far fa-calendar-alt mr-1"></i>
                            {{ \Carbon\Carbon::parse($prive->prive_date)->format('d/m/Y') }}
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $prive->is_approved == 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            <i class="fas fa-{{ $prive->is_approved == 'approved' ? 'check-circle' : 'clock' }} mr-1"></i>
                            {{ $prive->is_approved == 'approved' ? 'Disetujui' : 'Pending' }}
                        </span>
                    </div>

                    <!-- Description -->
                    <p class="text-sm font-medium text-gray-900 mb-2">{{ $prive->description }}</p>

                    <!-- Purpose -->
                    @if($prive->purpose)
                        <p class="text-xs text-gray-500 mb-3">
                            <i class="fas fa-tag mr-1"></i>
                            <span class="capitalize">{{ $prive->purpose }}</span>
                        </p>
                    @endif

                    <!-- Amount -->
                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-coins mr-1"></i> Jumlah
                        </span>
                        <span class="text-base font-bold text-purple-600">
                            Rp {{ number_format($prive->amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- MOBILE ACTION BUTTONS -->
                    <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-100">
                        <!-- Tombol Detail -->
                        <a href="{{ route('prive.show', $prive->id) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-xs">
                            <i class="fas fa-eye mr-1"></i> Detail
                        </a>

                        <!-- Tombol Edit -->
                        <a href="{{ route('prive.edit', $prive->id) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors duration-200 text-xs">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>

                        <!-- Tombol Delete - Langsung dengan form -->
                        <form action="{{ route('prive.destroy', $prive->id) }}" 
                            method="POST" 
                            onsubmit="return confirmDelete('prive', '{{ addslashes($prive->description) }}', {{ $prive->amount }})"
                            class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200 text-xs">
                                <i class="fas fa-trash-alt mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <div class="w-20 h-20 mx-auto bg-purple-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-money-bill-wave text-3xl text-purple-400"></i>
                    </div>
                    <p class="text-gray-900 font-medium mb-1">Belum ada data prive</p>
                    <p class="text-sm text-gray-500 mb-4">Mulai catat pengambilan uang pribadi Anda</p>
                    <a href="{{ route('prive.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Tambah Prive
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if(method_exists($prives, 'links') && $prives->hasPages())
    <div class="mt-4">
        {{ $prives->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@section('fab')
    <div class="floating-action-btn fixed bottom-20 lg:bottom-6 right-4 z-50">
        <a href="{{ route('prive.create') }}"
           class="w-14 h-14 bg-purple-600 text-white rounded-full shadow-lg hover:bg-purple-700 flex items-center justify-center transition-all hover:scale-110">
            <i class="fas fa-plus text-xl"></i>
        </a>
    </div>
@endsection
