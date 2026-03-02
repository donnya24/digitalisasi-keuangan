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

                <!-- Success Messages -->
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-4" id="registerForm" novalidate>
                    @csrf
                    
                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user text-blue-500 mr-1"></i> Nama Lengkap
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name"
                               value="{{ old('name') }}"
                               placeholder="Masukkan nama lengkap"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required
                               oninput="validateName(this)">
                        <div id="nameError" class="mt-1 text-xs text-red-600 hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <span id="nameErrorMessage"></span>
                        </div>
                    </div>

                    <!-- Nama Bisnis -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-store text-blue-500 mr-1"></i> Nama Bisnis
                        </label>
                        <input type="text" 
                               name="business_name" 
                               id="business_name"
                               value="{{ old('business_name') }}"
                               placeholder="Contoh: Warkop 96"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required
                               oninput="validateBusinessName(this)">
                        <div id="businessNameError" class="mt-1 text-xs text-red-600 hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <span id="businessNameErrorMessage"></span>
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-envelope text-blue-500 mr-1"></i> Email
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email"
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required
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

                    <!-- No Handphone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-phone text-blue-500 mr-1"></i> Nomor Handphone
                        </label>
                        <input type="tel" 
                               name="phone" 
                               id="phone"
                               value="{{ old('phone') }}"
                               placeholder="08xxxxxxxxxx"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               oninput="validatePhone(this)">
                        <div id="phoneError" class="mt-1 text-xs text-red-600 hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <span id="phoneErrorMessage"></span>
                        </div>
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
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                                   required
                                   minlength="8"
                                   oninput="validatePassword(this)">
                            <button type="button" 
                                    @click="show = !show"
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        <div id="passwordError" class="mt-1 text-xs text-red-600 hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <span id="passwordErrorMessage"></span>
                        </div>
                        <div id="passwordSuccess" class="mt-1 text-xs text-green-600 hidden">
                            <i class="fas fa-check-circle mr-1"></i>
                            Password valid
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
                                   id="password_confirmation"
                                   placeholder="Masukkan ulang password"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                                   required
                                   oninput="validatePasswordMatch(this)">
                            <button type="button" 
                                    @click="show = !show"
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        <div id="passwordMatchError" class="mt-1 text-xs text-red-600 hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <span id="passwordMatchErrorMessage"></span>
                        </div>
                        <div id="passwordMatchSuccess" class="mt-1 text-xs text-green-600 hidden">
                            <i class="fas fa-check-circle mr-1"></i>
                            Password cocok
                        </div>
                    </div>

                    <!-- Password Strength Indicator -->
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
                            required
                            onchange="validateTerms(this)">
                        <label for="terms" class="text-sm text-gray-600">
                            Saya menyetujui 
                            <a href="{{ route('terms') }}" target="_blank" class="text-blue-600 hover:underline">Syarat & Ketentuan</a> 
                            dan 
                            <a href="{{ route('privacy') }}" target="_blank" class="text-blue-600 hover:underline">Kebijakan Privasi</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            id="submitButton"
                            class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors mt-6 btn-disabled"
                            disabled>
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
    <script>
    // Shortcut Enter untuk submit form register
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input');
        const termsCheckbox = document.getElementById('terms');
        
        // Submit form saat tekan Enter di input terakhir (konfirmasi password)
        inputs.forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    
                    // Cek apakah checkbox terms sudah dicentang
                    if (termsCheckbox && !termsCheckbox.checked) {
                        alert('Anda harus menyetujui Syarat & Ketentuan terlebih dahulu.');
                        termsCheckbox.focus();
                        return;
                    }
                    
                    form.submit();
                }
            });
        });
        
        // Focus otomatis ke input nama
        const nameInput = document.getElementById('name');
        if (nameInput) {
            nameInput.focus();
        }
        
        // Juga handle Enter di checkbox (agar bisa submit dari checkbox)
        if (termsCheckbox) {
            termsCheckbox.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    form.submit();
                }
            });
        }
    });
</script>
    
    <!-- Validation Script -->
    <script>
        // Validasi Nama
        function validateName(input) {
            const name = input.value;
            const errorDiv = document.getElementById('nameError');
            const errorMsg = document.getElementById('nameErrorMessage');
            
            if (name.length === 0) {
                input.classList.remove('input-success');
                errorDiv.classList.add('hidden');
                checkFormValidity();
                return;
            }
            
            if (name.length < 3) {
                input.classList.add('input-error');
                input.classList.remove('input-success');
                errorDiv.classList.remove('hidden');
                errorMsg.innerText = 'Nama minimal 3 karakter';
            } else {
                input.classList.remove('input-error');
                input.classList.add('input-success');
                errorDiv.classList.add('hidden');
            }
            checkFormValidity();
        }

        // Validasi Nama Bisnis
        function validateBusinessName(input) {
            const name = input.value;
            const errorDiv = document.getElementById('businessNameError');
            const errorMsg = document.getElementById('businessNameErrorMessage');
            
            if (name.length === 0) {
                input.classList.remove('input-success');
                errorDiv.classList.add('hidden');
                checkFormValidity();
                return;
            }
            
            if (name.length < 3) {
                input.classList.add('input-error');
                input.classList.remove('input-success');
                errorDiv.classList.remove('hidden');
                errorMsg.innerText = 'Nama bisnis minimal 3 karakter';
            } else {
                input.classList.remove('input-error');
                input.classList.add('input-success');
                errorDiv.classList.add('hidden');
            }
            checkFormValidity();
        }

        // Validasi Email (sama dengan halaman login)
        function validateEmail(input) {
            const email = input.value;
            const errorDiv = document.getElementById('emailError');
            const successDiv = document.getElementById('emailSuccess');
            const errorMsg = document.getElementById('emailErrorMessage');
            
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            const invalidCharsRegex = /[;:\"\'\(\)\[\]\{\}\!\#\$\%\^\&\*\+\=\~\`\|\<\>\,\?\/]/g;
            
            if (email.length === 0) {
                input.classList.remove('input-error', 'input-success');
                errorDiv.classList.add('hidden');
                successDiv.classList.add('hidden');
                checkFormValidity();
                return;
            }
            
            if (invalidCharsRegex.test(email)) {
                input.classList.add('input-error');
                input.classList.remove('input-success');
                errorDiv.classList.remove('hidden');
                successDiv.classList.add('hidden');
                errorMsg.innerText = 'Email mengandung karakter tidak valid';
                checkFormValidity();
                return;
            }
            
            if (!email.includes('@')) {
                input.classList.add('input-error');
                input.classList.remove('input-success');
                errorDiv.classList.remove('hidden');
                successDiv.classList.add('hidden');
                errorMsg.innerText = 'Email harus mengandung @';
                checkFormValidity();
                return;
            }
            
            if (!emailRegex.test(email)) {
                input.classList.add('input-error');
                input.classList.remove('input-success');
                errorDiv.classList.remove('hidden');
                successDiv.classList.add('hidden');
                errorMsg.innerText = 'Format email tidak valid';
                checkFormValidity();
                return;
            }
            
            input.classList.remove('input-error');
            input.classList.add('input-success');
            errorDiv.classList.add('hidden');
            successDiv.classList.remove('hidden');
            checkFormValidity();
        }

        // Validasi No HP
        function validatePhone(input) {
            const phone = input.value;
            const errorDiv = document.getElementById('phoneError');
            const errorMsg = document.getElementById('phoneErrorMessage');
            
            if (phone.length === 0) {
                input.classList.remove('input-error', 'input-success');
                errorDiv.classList.add('hidden');
                checkFormValidity();
                return;
            }
            
            const phoneRegex = /^[0-9+\-\s]+$/;
            
            if (!phoneRegex.test(phone)) {
                input.classList.add('input-error');
                input.classList.remove('input-success');
                errorDiv.classList.remove('hidden');
                errorMsg.innerText = 'No HP hanya boleh angka, +, -, dan spasi';
            } else {
                input.classList.remove('input-error');
                input.classList.add('input-success');
                errorDiv.classList.add('hidden');
            }
            checkFormValidity();
        }

        // Validasi Password
        function validatePassword(input) {
            const password = input.value;
            const errorDiv = document.getElementById('passwordError');
            const successDiv = document.getElementById('passwordSuccess');
            const errorMsg = document.getElementById('passwordErrorMessage');
            
            const lengthEl = document.getElementById('length');
            const upperEl = document.getElementById('uppercase');
            const lowerEl = document.getElementById('lowercase');
            const numberEl = document.getElementById('number');
            
            // Update strength indicator
            if (password.length >= 8) {
                lengthEl.className = 'text-green-600';
                lengthEl.innerHTML = '<i class="fas fa-check-circle mr-1 text-xs"></i> Minimal 8 karakter';
            } else {
                lengthEl.className = 'text-gray-500';
                lengthEl.innerHTML = '<i class="fas fa-circle mr-1 text-xs"></i> Minimal 8 karakter';
            }
            
            if (/[A-Z]/.test(password)) {
                upperEl.className = 'text-green-600';
                upperEl.innerHTML = '<i class="fas fa-check-circle mr-1 text-xs"></i> Huruf besar (A-Z)';
            } else {
                upperEl.className = 'text-gray-500';
                upperEl.innerHTML = '<i class="fas fa-circle mr-1 text-xs"></i> Huruf besar (A-Z)';
            }
            
            if (/[a-z]/.test(password)) {
                lowerEl.className = 'text-green-600';
                lowerEl.innerHTML = '<i class="fas fa-check-circle mr-1 text-xs"></i> Huruf kecil (a-z)';
            } else {
                lowerEl.className = 'text-gray-500';
                lowerEl.innerHTML = '<i class="fas fa-circle mr-1 text-xs"></i> Huruf kecil (a-z)';
            }
            
            if (/[0-9]/.test(password)) {
                numberEl.className = 'text-green-600';
                numberEl.innerHTML = '<i class="fas fa-check-circle mr-1 text-xs"></i> Angka (0-9)';
            } else {
                numberEl.className = 'text-gray-500';
                numberEl.innerHTML = '<i class="fas fa-circle mr-1 text-xs"></i> Angka (0-9)';
            }
            
            if (password.length === 0) {
                input.classList.remove('input-error', 'input-success');
                errorDiv.classList.add('hidden');
                successDiv.classList.add('hidden');
                checkFormValidity();
                return;
            }
            
            if (password.length < 8) {
                input.classList.add('input-error');
                input.classList.remove('input-success');
                errorDiv.classList.remove('hidden');
                successDiv.classList.add('hidden');
                errorMsg.innerText = 'Password minimal 8 karakter';
            } else {
                input.classList.remove('input-error');
                input.classList.add('input-success');
                errorDiv.classList.add('hidden');
                successDiv.classList.remove('hidden');
            }
            
            // Trigger validasi password match
            const confirmInput = document.getElementById('password_confirmation');
            if (confirmInput.value) {
                validatePasswordMatch(confirmInput);
            }
            
            checkFormValidity();
        }

        // Validasi Kecocokan Password
        function validatePasswordMatch(input) {
            const password = document.getElementById('password').value;
            const confirmPassword = input.value;
            const errorDiv = document.getElementById('passwordMatchError');
            const successDiv = document.getElementById('passwordMatchSuccess');
            const errorMsg = document.getElementById('passwordMatchErrorMessage');
            
            if (confirmPassword.length === 0) {
                input.classList.remove('input-error', 'input-success');
                errorDiv.classList.add('hidden');
                successDiv.classList.add('hidden');
                checkFormValidity();
                return;
            }
            
            if (password !== confirmPassword) {
                input.classList.add('input-error');
                input.classList.remove('input-success');
                errorDiv.classList.remove('hidden');
                successDiv.classList.add('hidden');
                errorMsg.innerText = 'Password tidak cocok';
            } else {
                input.classList.remove('input-error');
                input.classList.add('input-success');
                errorDiv.classList.add('hidden');
                successDiv.classList.remove('hidden');
            }
            checkFormValidity();
        }

        // Validasi Terms
        function validateTerms(checkbox) {
            const errorDiv = document.getElementById('termsError');
            
            if (!checkbox.checked) {
                errorDiv.classList.remove('hidden');
            } else {
                errorDiv.classList.add('hidden');
            }
            checkFormValidity();
        }

        // Cek validitas seluruh form
        function checkFormValidity() {
            const name = document.getElementById('name');
            const businessName = document.getElementById('business_name');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');
            const terms = document.getElementById('terms');
            const submitButton = document.getElementById('submitButton');
            
            const nameValid = name.classList.contains('input-success');
            const businessNameValid = businessName.classList.contains('input-success');
            const emailValid = email.classList.contains('input-success');
            const passwordValid = password.classList.contains('input-success');
            const confirmValid = confirmPassword.classList.contains('input-success');
            const termsChecked = terms.checked;
            
            if (nameValid && businessNameValid && emailValid && passwordValid && confirmValid && termsChecked) {
                submitButton.classList.remove('btn-disabled');
                submitButton.disabled = false;
            } else {
                submitButton.classList.add('btn-disabled');
                submitButton.disabled = true;
            }
        }

        // Inisialisasi
        document.addEventListener('DOMContentLoaded', function() {
            const fields = ['name', 'business_name', 'email', 'phone', 'password', 'password_confirmation'];
            
            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field && field.value) {
                    if (fieldId === 'email') validateEmail(field);
                    else if (fieldId === 'phone') validatePhone(field);
                    else if (fieldId === 'password') validatePassword(field);
                    else if (fieldId === 'password_confirmation') validatePasswordMatch(field);
                    else if (fieldId === 'name') validateName(field);
                    else if (fieldId === 'business_name') validateBusinessName(field);
                }
            });
            
            checkFormValidity();
        });
    </script>
</body>
</html>