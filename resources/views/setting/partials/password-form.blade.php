@props([])

<div class="bg-white rounded-xl shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-lock text-yellow-600 mr-2"></i>
        Ubah Password
    </h3>

    <form method="POST" action="{{ route('setting.password.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <!-- Password Baru -->
        <div x-data="{ show: false }">
            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                Password Baru <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'"
                       name="new_password"
                       id="new_password"
                       required
                       minlength="8"
                       class="w-full px-4 py-3 pr-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('new_password') border-red-500 @enderror"
                       placeholder="Minimal 8 karakter">
                <button type="button"
                        @click="show = !show"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-yellow-600 focus:outline-none">
                    <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>
            @error('new_password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Konfirmasi Password Baru -->
        <div x-data="{ show: false }">
            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                Konfirmasi Password Baru <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'"
                       name="new_password_confirmation"
                       id="new_password_confirmation"
                       required
                       class="w-full px-4 py-3 pr-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('new_password_confirmation') border-red-500 @enderror"
                       placeholder="Ulangi password baru">
                <button type="button"
                        @click="show = !show"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-yellow-600 focus:outline-none">
                    <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>
            @error('new_password_confirmation')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit"
                    class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-medium">
                <i class="fas fa-key mr-2"></i> Ubah Password
            </button>
        </div>
    </form>
</div>