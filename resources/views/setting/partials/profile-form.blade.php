@props(['user'])

<div class="bg-white rounded-xl shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-user-circle text-blue-600 mr-2"></i>
        Informasi Pribadi
    </h3>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('setting.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Avatar -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
            <div class="flex items-center space-x-6">
                <div class="relative">
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-300">
                        @if($user->avatar)
                            <img src="{{ supabase_asset($user->avatar, 'avatars') }}" 
                                 alt="Avatar" 
                                 class="w-full h-full object-cover"
                                 id="avatar-preview">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-600 text-2xl font-bold"
                                 id="avatar-placeholder">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <img src="" alt="Avatar" class="w-full h-full object-cover hidden" id="avatar-preview">
                        @endif
                    </div>
                    <label for="avatar" 
                           class="absolute bottom-0 right-0 w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-blue-700">
                        <i class="fas fa-camera text-white text-xs"></i>
                        <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </label>
                </div>
                <div class="text-sm text-gray-500">
                    <p>Format: JPG, PNG, GIF, WEBP</p>
                    <p>Maks: 5MB</p>
                </div>
            </div>
        </div>

        <!-- Nama -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" 
                   name="name" 
                   value="{{ old('name', $user->name) }}"
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" 
                   value="{{ $user->email }}"
                   readonly
                   disabled
                   class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg">
        </div>

        <!-- Phone -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Handphone</label>
            <input type="text" 
                   name="phone" 
                   value="{{ old('phone', $user->phone) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                Simpan
            </button>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('avatar-preview');
    const placeholder = document.getElementById('avatar-placeholder');
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) {
                placeholder.classList.add('hidden');
            }
        }
        reader.readAsDataURL(file);
    }
}
</script>