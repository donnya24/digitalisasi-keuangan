<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - KeuanganKu</title>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .password-strength { transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-600 rounded-xl mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Daftar Akun Baru</h1>
                <p class="text-gray-600 mt-1">Mulai kelola keuangan UMKM Anda</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
                <!-- Google Register Button -->
                <a href="{{ route('auth.google') }}" 
                   class="w-full flex items-center justify-center gap-3 border border-gray-300 rounded-lg px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors mb-6">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="font-medium">Daftar dengan Google</span>
                </a>

                <!-- Divider -->
                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">atau daftar manual</span>
                    </div>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm text-red-600">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Success Messages -->
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm text-green-600">{{ session('success') }}</p>
                    </div>
                @endif

                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    
                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user text-blue-500 mr-1"></i> Nama Lengkap
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="Masukkan nama lengkap"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               required>
                    </div>

                    <!-- Nama Bisnis -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-store text-blue-500 mr-1"></i> Nama Bisnis
                        </label>
                        <input type="text" 
                               name="business_name" 
                               value="{{ old('business_name') }}"
                               placeholder="Contoh: Warkop 96"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-envelope text-blue-500 mr-1"></i> Email
                        </label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                               required>
                    </div>

                    <!-- No Handphone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-phone text-blue-500 mr-1"></i> Nomor Handphone
                        </label>
                        <input type="tel" 
                               name="phone" 
                               value="{{ old('phone') }}"
                               placeholder="08xxxxxxxxxx"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Opsional, untuk keperluan konfirmasi</p>
                    </div>

                    <!-- Password -->
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-lock text-blue-500 mr-1"></i> Password
                        </label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" 
                                   name="password"
                                   id="password"
                                   placeholder="Minimal 8 karakter"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10 @error('password') border-red-500 @enderror"
                                   required>
                            <button type="button" 
                                    @click="show = !show"
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-lock text-blue-500 mr-1"></i> Konfirmasi Password
                        </label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" 
                                   name="password_confirmation"
                                   placeholder="Masukkan ulang password"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                                   required>
                            <button type="button" 
                                    @click="show = !show"
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Strength Indicator (Sederhana) -->
                    <div class="text-xs text-gray-600 bg-gray-50 p-3 rounded-lg" id="password-strength">
                        <p class="font-medium mb-1">Kriteria Password:</p>
                        <ul class="space-y-1" id="password-criteria">
                            <li id="length" class="text-gray-500">
                                <i class="fas fa-circle mr-1 text-xs"></i> Minimal 8 karakter
                            </li>
                            <li id="uppercase" class="text-gray-500">
                                <i class="fas fa-circle mr-1 text-xs"></i> Huruf besar (A-Z)
                            </li>
                            <li id="lowercase" class="text-gray-500">
                                <i class="fas fa-circle mr-1 text-xs"></i> Huruf kecil (a-z)
                            </li>
                            <li id="number" class="text-gray-500">
                                <i class="fas fa-circle mr-1 text-xs"></i> Angka (0-9)
                            </li>
                        </ul>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="flex items-start gap-2">
                        <input type="checkbox" 
                               name="terms" 
                               id="terms"
                               class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                               required>
                        <label for="terms" class="text-sm text-gray-600">
                            Saya menyetujui 
                            <a href="#" class="text-blue-600 hover:underline">Syarat & Ketentuan</a> 
                            dan 
                            <a href="#" class="text-blue-600 hover:underline">Kebijakan Privasi</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors mt-6">
                        <i class="fas fa-user-plus mr-2"></i> Daftar Sekarang
                    </button>

                    <!-- Login Link -->
                    <p class="text-center text-sm text-gray-600">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">
                            Masuk di sini
                        </a>
                    </p>
                </form>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-500 mt-4">
                © {{ date('Y') }} Digitalisasi Keuangan UMKM
            </p>
        </div>
    </div>

    <!-- Alpine.js untuk toggle password -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Password Strength Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    
                    // Cek panjang minimal 8
                    const lengthEl = document.getElementById('length');
                    if (password.length >= 8) {
                        lengthEl.className = 'text-green-600';
                        lengthEl.innerHTML = '<i class="fas fa-check-circle mr-1 text-xs"></i> Minimal 8 karakter';
                    } else {
                        lengthEl.className = 'text-gray-500';
                        lengthEl.innerHTML = '<i class="fas fa-circle mr-1 text-xs"></i> Minimal 8 karakter';
                    }
                    
                    // Cek huruf besar
                    const upperEl = document.getElementById('uppercase');
                    if (/[A-Z]/.test(password)) {
                        upperEl.className = 'text-green-600';
                        upperEl.innerHTML = '<i class="fas fa-check-circle mr-1 text-xs"></i> Huruf besar (A-Z)';
                    } else {
                        upperEl.className = 'text-gray-500';
                        upperEl.innerHTML = '<i class="fas fa-circle mr-1 text-xs"></i> Huruf besar (A-Z)';
                    }
                    
                    // Cek huruf kecil
                    const lowerEl = document.getElementById('lowercase');
                    if (/[a-z]/.test(password)) {
                        lowerEl.className = 'text-green-600';
                        lowerEl.innerHTML = '<i class="fas fa-check-circle mr-1 text-xs"></i> Huruf kecil (a-z)';
                    } else {
                        lowerEl.className = 'text-gray-500';
                        lowerEl.innerHTML = '<i class="fas fa-circle mr-1 text-xs"></i> Huruf kecil (a-z)';
                    }
                    
                    // Cek angka
                    const numberEl = document.getElementById('number');
                    if (/[0-9]/.test(password)) {
                        numberEl.className = 'text-green-600';
                        numberEl.innerHTML = '<i class="fas fa-check-circle mr-1 text-xs"></i> Angka (0-9)';
                    } else {
                        numberEl.className = 'text-gray-500';
                        numberEl.innerHTML = '<i class="fas fa-circle mr-1 text-xs"></i> Angka (0-9)';
                    }
                });
            }
        });
    </script>
</body>
</html>