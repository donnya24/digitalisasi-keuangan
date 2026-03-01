<x-guest-layout>
    <!-- Header -->
    <div class="auth-header">
        <div class="mx-auto w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg mb-4">
            <i class="fas fa-key text-white text-2xl"></i>
        </div>
        <h2 class="auth-title">Lupa Password?</h2>
        <p class="auth-subtitle">Tenang, kami akan bantu Anda mereset password</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert-success mt-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                <p class="text-sm text-green-700">{{ session('status') }}</p>
            </div>
        </div>
    @endif

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

    <!-- Info Text -->
    <div class="mt-6 text-sm text-gray-600 bg-blue-50 p-4 rounded-lg">
        <div class="flex">
            <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
            <p>
                Masukkan alamat email Anda yang terdaftar. Kami akan mengirimkan tautan untuk mereset password Anda.
            </p>
        </div>
    </div>

    <!-- Forgot Password Form -->
    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-6">
        @csrf

        <!-- Email -->
        <div class="input-group">
            <x-input-label for="email" value="Email" required />
            <x-text-input 
                id="email" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus
                icon="envelope"
                placeholder="nama@email.com"
            />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-primary">
            <i class="fas fa-paper-plane mr-2"></i>
            Kirim Tautan Reset Password
        </button>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                <i class="fas fa-arrow-left mr-1"></i>
                Kembali ke halaman login
            </a>
        </div>
    </form>
</x-guest-layout>