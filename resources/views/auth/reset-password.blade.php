<x-guest-layout>
    <!-- Header -->
    <div class="auth-header">
        <div class="mx-auto w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg mb-4">
            <i class="fas fa-lock-open text-white text-2xl"></i>
        </div>
        <h2 class="auth-title">Reset Password</h2>
        <p class="auth-subtitle">Buat password baru untuk akun Anda</p>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert-error mt-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mr-2 mt-0.5"></i>
                <div class="text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Reset Password Form -->
    <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-6">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email -->
        <div class="input-group">
            <x-input-label for="email" value="Email" required />
            <x-text-input 
                id="email" 
                type="email" 
                name="email" 
                :value="old('email', $request->email)" 
                required 
                autofocus 
                autocomplete="username"
                icon="envelope"
                readonly
                class="bg-gray-50"
            />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="input-group" x-data="{ show: false }">
            <x-input-label for="password" value="Password Baru" required />
            <div class="relative">
                <input 
                    id="password"
                    :type="show ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                    class="input-field pr-10"
                />
                <button 
                    type="button"
                    @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none"
                >
                    <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="input-group" x-data="{ show: false }">
            <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" required />
            <div class="relative">
                <input 
                    id="password_confirmation"
                    :type="show ? 'text' : 'password'"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Masukkan ulang password"
                    class="input-field pr-10"
                />
                <button 
                    type="button"
                    @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none"
                >
                    <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <!-- Password Strength Indicator -->
        <div class="text-xs text-gray-600 bg-gray-50 p-3 rounded-lg" x-data="{ 
            password: '',
            getStrength() {
                if(this.password.length === 0) return { text: 'Ketik password', color: 'gray' };
                if(this.password.length < 8) return { text: 'Terlalu pendek', color: 'red' };
                
                let strength = 0;
                if(this.password.match(/[a-z]+/)) strength++;
                if(this.password.match(/[A-Z]+/)) strength++;
                if(this.password.match(/[0-9]+/)) strength++;
                if(this.password.match(/[$@#&!]+/)) strength++;
                
                if(strength < 2) return { text: 'Lemah', color: 'red' };
                if(strength < 3) return { text: 'Sedang', color: 'yellow' };
                if(strength < 4) return { text: 'Kuat', color: 'green' };
                return { text: 'Sangat Kuat', color: 'blue' };
            }
        }">
            <input type="hidden" x-model="password" x-init="$watch('password', value => document.getElementById('password').value = value)" />
            <div class="flex items-center justify-between mb-2">
                <span class="font-medium">Kekuatan Password:</span>
                <span :class="{
                    'text-gray-600': getStrength().color === 'gray',
                    'text-red-600': getStrength().color === 'red',
                    'text-yellow-600': getStrength().color === 'yellow',
                    'text-green-600': getStrength().color === 'green',
                    'text-blue-600': getStrength().color === 'blue'
                }" x-text="getStrength().text"></span>
            </div>
            <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full transition-all duration-300" 
                     :style="{ width: (password.length * 4) + '%' }"
                     :class="{
                         'bg-red-500': getStrength().color === 'red',
                         'bg-yellow-500': getStrength().color === 'yellow',
                         'bg-green-500': getStrength().color === 'green',
                         'bg-blue-500': getStrength().color === 'blue'
                     }"></div>
            </div>
            <ul class="mt-2 space-y-1 text-gray-500">
                <li :class="{ 'text-green-600': password.length >= 8 }">
                    <i :class="password.length >= 8 ? 'fas fa-check-circle' : 'fas fa-circle'" class="w-4 mr-1"></i>
                    Minimal 8 karakter
                </li>
                <li :class="{ 'text-green-600': password.match(/[A-Z]/) }">
                    <i :class="password.match(/[A-Z]/) ? 'fas fa-check-circle' : 'fas fa-circle'" class="w-4 mr-1"></i>
                    Huruf besar (A-Z)
                </li>
                <li :class="{ 'text-green-600': password.match(/[a-z]/) }">
                    <i :class="password.match(/[a-z]/) ? 'fas fa-check-circle' : 'fas fa-circle'" class="w-4 mr-1"></i>
                    Huruf kecil (a-z)
                </li>
                <li :class="{ 'text-green-600': password.match(/[0-9]/) }">
                    <i :class="password.match(/[0-9]/) ? 'fas fa-check-circle' : 'fas fa-circle'" class="w-4 mr-1"></i>
                    Angka (0-9)
                </li>
                <li :class="{ 'text-green-600': password.match(/[$@#&!]/) }">
                    <i :class="password.match(/[$@#&!]/) ? 'fas fa-check-circle' : 'fas fa-circle'" class="w-4 mr-1"></i>
                    Karakter khusus ($@#&!)
                </li>
            </ul>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-primary">
            <i class="fas fa-sync-alt mr-2"></i>
            Reset Password
        </button>
    </form>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        // Sync password field with Alpine
        document.getElementById('password').addEventListener('input', function(e) {
            this.dispatchEvent(new CustomEvent('input', { detail: e.target.value }));
        });
    </script>
    @endpush
</x-guest-layout>