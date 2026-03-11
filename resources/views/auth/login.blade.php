<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - KeuanganKu</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .input-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }
        .input-success {
            border-color: #10b981 !important;
        }
        .btn-disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl mx-auto mb-4 flex items-center justify-center shadow-lg">
                    <i class="fas fa-wallet text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">KeuanganKu</h1>
                <p class="text-gray-600 mt-1">Kelola keuangan UMKM dengan mudah</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
                <!-- Google Button -->
                <a href="{{ route('auth.google') }}"
                   class="w-full flex items-center justify-center gap-3 border border-gray-300 rounded-lg px-4 py-3 text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all mb-6">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="font-medium">Lanjutkan dengan Google</span>
                </a>

                <!-- Divider -->
                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">atau masuk dengan email</span>
                    </div>
                </div>

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

                <!-- Google Login Error Message (Khusus untuk error dari Google) -->
                @if(session('google_error'))
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                        <div class="flex items-start">
                            <i class="fab fa-google text-red-500 mt-0.5 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-red-800">Login Google Gagal</p>
                                <p class="text-sm text-red-700 mt-1">{{ session('google_error') }}</p>
                                <a href="{{ route('register') }}" class="inline-flex items-center mt-2 text-xs text-red-600 hover:text-red-800 font-medium">
                                    <i class="fas fa-user-plus mr-1"></i>
                                    Registrasi Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- General Error Message -->
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5" id="loginForm" novalidate>
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-envelope text-blue-500 mr-1"></i> Email
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               required
                               autofocus
                               oninput="validateEmail(this)">
                        <div id="emailError" class="mt-1 text-xs text-red-600 hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <span id="emailErrorMessage"></span>
                        </div>
                        <div id="emailSuccess" class="mt-1 text-xs text-green-600 hidden">
                            <i class="fas fa-check-circle mr-1"></i>
                            Format email valid
                        </div>
                    </div>

                    <!-- Password dengan Eye Icon -->
                    <div x-data="{ show: false }">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-lock text-blue-500 mr-1"></i> Password
                        </label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'"
                                   id="password"
                                   name="password"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-12 transition"
                                   required
                                   minlength="8"
                                   oninput="validatePassword(this)">

                            <!-- Eye Button -->
                            <button type="button"
                                    @click="show = !show"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 focus:outline-none transition-colors"
                                    :class="{ 'text-blue-600': show }">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linecap="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linecap="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>

                                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linecap="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <div id="passwordError" class="mt-1 text-xs text-red-600 hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <span id="passwordErrorMessage"></span>
                        </div>
                        <div id="passwordSuccess" class="mt-1 text-xs text-green-600 hidden">
                            <i class="fas fa-check-circle mr-1"></i>
                            Password valid (minimal 8 karakter)
                        </div>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox"
                                   name="remember"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Ingat saya</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline transition-colors">
                            Lupa password?
                        </a>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            id="submitButton"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-2.5 rounded-lg font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-[1.02]">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk
                    </button>

                    <!-- Register -->
                    <p class="text-center text-sm text-gray-600">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                            Daftar sekarang
                            <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </a>
                    </p>
                </form>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-500 mt-4">
                <i class="far fa-copyright mr-1"></i>
                {{ date('Y') }} KeuanganKu.
                <a href="{{ route('terms') }}" class="hover:underline">Syarat</a> |
                <a href="{{ route('privacy') }}" class="hover:underline">Privasi</a>
            </p>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
    // Shortcut Enter untuk submit form login
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input');

        // Submit form saat tekan Enter di input terakhir (password)
        inputs.forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    form.submit();
                }
            });
        });

        // Focus otomatis ke input email
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.focus();
        }
    });
</script>

    <!-- Validation Script -->
   <script>
    function validateEmail(input) {
        const email = input.value;
        const emailError = document.getElementById('emailError');
        const emailSuccess = document.getElementById('emailSuccess');
        const emailErrorMessage = document.getElementById('emailErrorMessage');

        // Regex untuk validasi email STANDAR
        // Mengizinkan: huruf, angka, titik, underscore, @, plus, minus, persen
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        // Karakter yang TIDAK DIIZINKAN (simbol berbahaya)
        // @ justru DIIZINKAN, jadi tidak masuk daftar ini
        const invalidCharsRegex = /[;:\"\'\(\)\[\]\{\}\!\#\$\%\^\&\*\+\=\~\`\|\<\>\,\?\/]/g;

        if (email.length === 0) {
            input.classList.remove('input-error', 'input-success');
            emailError.classList.add('hidden');
            emailSuccess.classList.add('hidden');
            checkFormValidity();
            return;
        }

        // Cek karakter tidak valid (simbol berbahaya)
        if (invalidCharsRegex.test(email)) {
            input.classList.add('input-error');
            input.classList.remove('input-success');
            emailError.classList.remove('hidden');
            emailSuccess.classList.add('hidden');

            // Tampilkan karakter apa yang tidak valid
            const invalidChars = email.match(invalidCharsRegex).join(' ');
            emailErrorMessage.innerText = `Email mengandung karakter tidak valid: ${invalidChars}`;
            checkFormValidity();
            return;
        }

        // Cek apakah ada @ (WAJIB)
        if (!email.includes('@')) {
            input.classList.add('input-error');
            input.classList.remove('input-success');
            emailError.classList.remove('hidden');
            emailSuccess.classList.add('hidden');
            emailErrorMessage.innerText = 'Email harus mengandung @';
            checkFormValidity();
            return;
        }

        // Cek format email dengan regex
        if (!emailRegex.test(email)) {
            input.classList.add('input-error');
            input.classList.remove('input-success');
            emailError.classList.remove('hidden');
            emailSuccess.classList.add('hidden');

            if (!email.includes('.')) {
                emailErrorMessage.innerText = 'Email harus mengandung domain (contoh: .com, .id, .co.id)';
            } else {
                emailErrorMessage.innerText = 'Format email tidak valid. Contoh yang benar: nama@domain.com';
            }
            checkFormValidity();
            return;
        }

        // Email valid
        input.classList.remove('input-error');
        input.classList.add('input-success');
        emailError.classList.add('hidden');
        emailSuccess.classList.remove('hidden');
        checkFormValidity();
    }

    function validatePassword(input) {
        const password = input.value;
        const passwordError = document.getElementById('passwordError');
        const passwordSuccess = document.getElementById('passwordSuccess');
        const passwordErrorMessage = document.getElementById('passwordErrorMessage');

        if (password.length === 0) {
            input.classList.remove('input-error', 'input-success');
            passwordError.classList.add('hidden');
            passwordSuccess.classList.add('hidden');
            checkFormValidity();
            return;
        }

        if (password.length < 8) {
            input.classList.add('input-error');
            input.classList.remove('input-success');
            passwordError.classList.remove('hidden');
            passwordSuccess.classList.add('hidden');
            passwordErrorMessage.innerText = 'Password minimal 8 karakter';
            checkFormValidity();
            return;
        }

        // Password valid
        input.classList.remove('input-error');
        input.classList.add('input-success');
        passwordError.classList.add('hidden');
        passwordSuccess.classList.remove('hidden');
        checkFormValidity();
    }

    function checkFormValidity() {
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const submitButton = document.getElementById('submitButton');

        const emailValid = email.classList.contains('input-success');
        const passwordValid = password.classList.contains('input-success');

        if (emailValid && passwordValid) {
            submitButton.classList.remove('btn-disabled');
            submitButton.disabled = false;
        } else {
            submitButton.classList.add('btn-disabled');
            submitButton.disabled = true;
        }
    }

    // Inisialisasi validasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        const email = document.getElementById('email');
        const password = document.getElementById('password');

        if (email.value) {
            validateEmail(email);
        }

        if (password.value) {
            validatePassword(password);
        }

        checkFormValidity();
    });
</script>
</body>
</html>
