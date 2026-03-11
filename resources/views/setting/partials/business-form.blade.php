@props(['business'])

@php
    $projectRef = env('SUPABASE_PROJECT_REF');
    $logoUrl = $business && $business->logo ? "https://{$projectRef}.supabase.co/storage/v1/object/public/logos/{$business->logo}" : null;
@endphp

<div class="bg-white rounded-xl shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-store text-green-600 mr-2"></i>
        Informasi Usaha
    </h3>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            @foreach($errors->all() as $error)
                <p class="text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('setting.business.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Logo Usaha -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Logo Usaha</label>
            <div class="flex items-center space-x-6">
                <div class="relative">
                    <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 border-2 border-gray-300">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" 
                                 alt="Logo" 
                                 class="w-full h-full object-cover"
                                 id="logo-preview">
                            <div id="logo-placeholder" class="hidden"></div>
                        @else
                            <div id="logo-placeholder" 
                                 class="w-full h-full flex items-center justify-center bg-green-100 text-green-600 text-2xl">
                                <i class="fas fa-store"></i>
                            </div>
                            <img src="" alt="Logo" class="hidden" id="logo-preview">
                        @endif
                    </div>
                    <label for="logo" 
                           class="absolute bottom-0 right-0 w-6 h-6 bg-green-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-green-700">
                        <i class="fas fa-camera text-white text-xs"></i>
                        <input type="file" name="logo" id="logo" class="hidden" accept="image/*" onchange="previewLogo(this)">
                    </label>
                </div>
                <div class="text-sm text-gray-500">
                    <p>Format: JPG, PNG, GIF, WEBP | Maks: 5MB</p>
                </div>
            </div>
        </div>

        <!-- Nama Usaha -->
        <div class="mb-4">
            <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Usaha</label>
            <input type="text" 
                   name="business_name" 
                   id="business_name"
                   value="{{ old('business_name', $business->business_name ?? '') }}"
                   required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            @error('business_name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Jenis Usaha -->
        <div class="mb-4">
            <label for="business_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Usaha</label>
            <input type="text" 
                   name="business_type" 
                   id="business_type"
                   value="{{ old('business_type', $business->business_type ?? '') }}"
                   placeholder="Contoh: Warkop, Restoran"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            @error('business_type')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- No Telepon -->
        <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No Telepon</label>
            <input type="text" 
                   name="phone" 
                   id="phone"
                   value="{{ old('phone', $business->phone ?? '') }}"
                   placeholder="021-1234567"
                   oninput="this.value = this.value.replace(/[^0-9+\-\s]/g, '')"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            @error('phone')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Alamat -->
        <div class="mb-4">
            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
            <textarea name="address" 
                      id="address" 
                      rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">{{ old('address', $business->address ?? '') }}</textarea>
            @error('address')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Kota & Provinsi -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                <input type="text" 
                       name="city" 
                       id="city"
                       value="{{ old('city', $business->city ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                @error('city')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                <input type="text" 
                       name="province" 
                       id="province"
                       value="{{ old('province', $business->province ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                @error('province')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Kode Pos -->
        <div class="mb-6">
            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
            <input type="text" 
                   name="postal_code" 
                   id="postal_code"
                   value="{{ old('postal_code', $business->postal_code ?? '') }}"
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                   maxlength="10"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            @error('postal_code')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit" 
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
function previewLogo(input) {
    const preview = document.getElementById('logo-preview');
    const placeholder = document.getElementById('logo-placeholder');
    const file = input.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
}
</script>