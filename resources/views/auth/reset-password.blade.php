<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - KeuanganKu</title>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl mx-auto mb-4 flex items-center justify-center shadow-lg">
                    <i class="fas fa-key text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Reset Password</h1>
                <p class="text-gray-600 mt-1">Buat password baru untuk akun Anda</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p class="text-sm text-red-700">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    
                    <!-- Token -->
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-envelope text-blue-500 mr-1"></i> Email
                        </label>
                        <input type="email" 
                               id="email"
                               name="email" 
                               value="{{ old('email', $email) }}"
                               class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                               readonly>
                    </div>

                    <!-- Password Baru -->
                    <div x-data="{ show: false }">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-lock text-blue-500 mr-1"></i> Password Baru
                        </label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" 
                                   id="password"
                                   name="password"
                                   placeholder="Minimal 8 karakter"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-12"
                                   required>
                            <button type="button" 
                                    @click="show = !show"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
                                <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div x-data="{ show: false }">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-lock text-blue-500 mr-1"></i> Konfirmasi Password
                        </label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" 
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   placeholder="Ulangi password baru"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-12"
                                   required>
                            <button type="button" 
                                    @click="show = !show"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
                                <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Strength Info -->
                    <div class="text-xs text-gray-500 bg-gray-50 p-3 rounded-lg">
                        <i class="fas fa-info-circle mr-1"></i>
                        Password harus minimal 8 karakter.
                    </div>

                    <!-- Submit -->
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-2.5 rounded-lg font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-[1.02]">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Reset Password
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-500 mt-4">
                <i class="far fa-copyright mr-1"></i>
                {{ date('Y') }} KeuanganKu. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>