@extends('components.layout.app')

@section('title', 'Edit Keperluan Prive')
@section('page-title', 'Edit Keperluan Prive')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-xl shadow-sm p-5">
        <form method="POST" action="{{ route('prive-purposes.update', $privePurpose->id) }}" class="space-y-5">
            @csrf
            @method('PUT')
            
            <!-- Nama -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Keperluan <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name"
                       value="{{ old('name', $privePurpose->name) }}"
                       placeholder="Contoh: Kebutuhan Pribadi, Belanja Bulanan"
                       required
                       class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sort Order -->
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                    Urutan
                </label>
                <input type="number" 
                       name="sort_order" 
                       id="sort_order"
                       value="{{ old('sort_order', $privePurpose->sort_order) }}"
                       min="0"
                       class="w-32 px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="is_active" value="active" 
                               {{ old('is_active', $privePurpose->is_active) == 'active' ? 'checked' : '' }}
                               class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-700">Aktif</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="is_active" value="inactive" 
                               {{ old('is_active', $privePurpose->is_active) == 'inactive' ? 'checked' : '' }}
                               class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-700">Nonaktif</span>
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="3"
                          placeholder="Deskripsi keperluan (opsional)"
                          class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('description', $privePurpose->description) }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col gap-2 pt-4">
                <button type="submit" 
                        class="w-full px-4 py-4 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium">
                    <i class="fas fa-save mr-2"></i> Update Keperluan
                </button>
                <a href="{{ route('prive-purposes.index') }}" 
                   class="w-full px-4 py-4 text-center border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection