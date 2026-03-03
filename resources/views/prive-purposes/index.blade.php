@extends('components.layout.app')

@section('title', 'Keperluan Prive')
@section('page-title', 'Kelola Keperluan Prive')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header dengan tombol tambah -->
    <div class="bg-white rounded-lg shadow-sm p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-sm font-medium text-gray-700">DaftarOpsi Keperluan di Prive</h3>
            <p class="text-xs text-gray-500 mt-1">Kelola opsi keperluan untuk pengambilan uang pribadi</p>
        </div>
        <a href="{{ route('prive-purposes.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm">
            <i class="fas fa-plus mr-2"></i> Tambah Keperluan
        </a>
    </div>

    <!-- Daftar Keperluan -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($purposes as $purpose)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $purpose->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                {{ $purpose->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                {{ $purpose->sort_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-xs rounded-full {{ $purpose->is_active == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $purpose->is_active == 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Tombol Toggle Status -->
                                    <form action="{{ route('prive-purposes.toggle', $purpose->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="inline-flex items-center px-2 py-1 {{ $purpose->is_active == 'active' ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} rounded-md transition-colors duration-200 text-xs"
                                                title="{{ $purpose->is_active == 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="fas fa-{{ $purpose->is_active == 'active' ? 'ban' : 'check-circle' }} mr-1"></i>
                                            {{ $purpose->is_active == 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    
                                    <!-- Tombol Edit -->
                                    <a href="{{ route('prive-purposes.edit', $purpose->id) }}" 
                                       class="inline-flex items-center px-2 py-1 bg-yellow-50 text-yellow-600 rounded-md hover:bg-yellow-100 transition-colors duration-200 text-xs"
                                       title="Edit">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    
                                    <!-- Tombol Delete -->
                                    <form action="{{ route('prive-purposes.destroy', $purpose->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirmDelete('keperluan', '{{ addslashes($purpose->name) }}')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition-colors duration-200 text-xs"
                                                title="Hapus">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-purple-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-tags text-2xl text-purple-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 mb-1">Belum ada keperluan prive</p>
                                    <p class="text-xs text-gray-500 mb-4">Tambahkan opsi keperluan untuk pengambilan uang pribadi</p>
                                    <a href="{{ route('prive-purposes.create') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                        <i class="fas fa-plus mr-2"></i> Tambah Keperluan
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
            @forelse($purposes as $purpose)
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">{{ $purpose->name }}</h4>
                            <p class="text-xs text-gray-500">Urutan: {{ $purpose->sort_order }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $purpose->is_active == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $purpose->is_active == 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    
                    @if($purpose->description)
                        <p class="text-xs text-gray-600 mb-3">{{ $purpose->description }}</p>
                    @endif
                    
                    <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-100">
                        <!-- Tombol Toggle Status -->
                        <form action="{{ route('prive-purposes.toggle', $purpose->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-1.5 {{ $purpose->is_active == 'active' ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} rounded-lg transition-colors duration-200 text-xs">
                                <i class="fas fa-{{ $purpose->is_active == 'active' ? 'ban' : 'check-circle' }} mr-1"></i>
                                {{ $purpose->is_active == 'active' ? 'Nonaktif' : 'Aktif' }}
                            </button>
                        </form>
                        
                        <a href="{{ route('prive-purposes.edit', $purpose->id) }}" 
                           class="inline-flex items-center px-3 py-1.5 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors duration-200 text-xs">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        
                        <form action="{{ route('prive-purposes.destroy', $purpose->id) }}" 
                              method="POST" 
                              onsubmit="return confirmDelete('keperluan', '{{ addslashes($purpose->name) }}')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200 text-xs">
                                <i class="fas fa-trash-alt mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <div class="w-16 h-16 mx-auto bg-purple-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-tags text-2xl text-purple-400"></i>
                    </div>
                    <p class="text-gray-900 font-medium mb-1">Belum ada keperluan prive</p>
                    <p class="text-sm text-gray-500 mb-4">Tambahkan opsi keperluan untuk pengambilan uang pribadi</p>
                    <a href="{{ route('prive-purposes.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        <i class="fas fa-plus mr-2"></i> Tambah Keperluan
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection