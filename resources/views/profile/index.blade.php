@extends('components.layout.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan Akun')

@section('content')
<div class="space-y-6">
    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow-sm p-1 flex flex-wrap gap-1">
        <button type="button" 
                onclick="showTab('profile')"
                class="tab-button active px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                data-tab="profile">
            <i class="fas fa-user mr-2"></i> Profil Saya
        </button>
        <button type="button" 
                onclick="showTab('business')"
                class="tab-button px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                data-tab="business">
            <i class="fas fa-store mr-2"></i> Profil Usaha
        </button>
        <button type="button" 
                onclick="showTab('password')"
                class="tab-button px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                data-tab="password">
            <i class="fas fa-lock mr-2"></i> Ubah Password
        </button>
        <button type="button" 
                onclick="showTab('account')"
                class="tab-button px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                data-tab="account">
            <i class="fas fa-shield-alt mr-2"></i> Informasi Akun
        </button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            @foreach($errors->all() as $error)
                <p class="flex items-center text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Tab: Profil Saya (dari tabel users) -->
    <div id="tab-profile" class="tab-content">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                Informasi Pribadi
            </h3>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Avatar -->
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="relative">
                        <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-200">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-600 text-3xl font-bold">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <label for="avatar" class="absolute bottom-0 right-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-blue-700 transition-colors">
                            <i class="fas fa-camera text-white text-sm"></i>
                            <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*">
                        </label>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-600">Upload foto profil</p>
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks: 2MB</p>
                    </div>
                </div>

                <!-- Nama Lengkap (dari users) -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name"
                           value="{{ old('name', $user->name) }}"
                           required
                           class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Email (dari users) -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email"
                           value="{{ old('email', $user->email) }}"
                           required
                           class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- No HP (dari users) -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Nomor Handphone
                    </label>
                    <input type="tel" 
                           name="phone" 
                           id="phone"
                           value="{{ old('phone', $user->phone) }}"
                           placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Opsional, untuk keperluan konfirmasi</p>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab: Profil Usaha (dari tabel businesses) -->
    <div id="tab-business" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-store text-green-600 mr-2"></i>
                Informasi Usaha
            </h3>

            <form method="POST" action="{{ route('profile.update-business') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Logo Usaha -->
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="relative">
                        <div class="w-24 h-24 rounded-lg overflow-hidden bg-gray-100 border-2 border-gray-200">
                            @if($business->logo)
                                <img src="{{ Storage::url($business->logo) }}" alt="Logo" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-green-100 text-green-600 text-3xl">
                                    <i class="fas fa-store"></i>
                                </div>
                            @endif
                        </div>
                        <label for="logo" class="absolute bottom-0 right-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-green-700 transition-colors">
                            <i class="fas fa-camera text-white text-sm"></i>
                            <input type="file" name="logo" id="logo" class="hidden" accept="image/*">
                        </label>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-600">Upload logo usaha</p>
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks: 2MB</p>
                    </div>
                </div>

                <!-- Nama Usaha -->
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Usaha <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="business_name" 
                           id="business_name"
                           value="{{ old('business_name', $business->business_name ?? '') }}"
                           required
                           class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <!-- Jenis Usaha (text input, bukan dropdown) -->
                <div>
                    <label for="business_type" class="block text-sm font-medium text-gray-700 mb-1">
                        Jenis Usaha
                    </label>
                    <input type="text" 
                           name="business_type" 
                           id="business_type"
                           value="{{ old('business_type', $business->business_type ?? '') }}"
                           placeholder="Contoh: Warkop, Restoran, Toko Kelontong"
                           class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <!-- No Telepon Usaha -->
                <div>
                    <label for="business_phone" class="block text-sm font-medium text-gray-700 mb-1">
                        No Telepon Usaha
                    </label>
                    <input type="tel" 
                           name="phone" 
                           id="business_phone"
                           value="{{ old('phone', $business->phone ?? '') }}"
                           placeholder="021-xxxxxxx"
                           class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <!-- Alamat -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                        Alamat
                    </label>
                    <textarea name="address" 
                              id="address" 
                              rows="3"
                              class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('address', $business->address ?? '') }}</textarea>
                </div>

                <!-- Kota & Provinsi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <input type="text" 
                               name="city" 
                               id="city"
                               value="{{ old('city', $business->city ?? '') }}"
                               class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                        <input type="text" 
                               name="province" 
                               id="province"
                               value="{{ old('province', $business->province ?? '') }}"
                               class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Kode Pos -->
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                        Kode Pos
                    </label>
                    <input type="text" 
                           name="postal_code" 
                           id="postal_code"
                           value="{{ old('postal_code', $business->postal_code ?? '') }}"
                           class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab: Ubah Password -->
    <div id="tab-password" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-lock text-yellow-600 mr-2"></i>
                Ubah Password
            </h3>

            <!-- Saran Password -->
            <div class="mb-5 p-4 bg-blue-50 rounded-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-lightbulb text-blue-600 text-xl mt-1"></i>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Saran Password Kuat:</h4>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" 
                                    onclick="useSuggestedPassword('P@ssw0rd2024!')"
                                    class="px-3 py-1.5 bg-white text-blue-700 text-xs rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors">
                                P@ssw0rd2024!
                            </button>
                            <button type="button" 
                                    onclick="useSuggestedPassword('Kuat@123456')"
                                    class="px-3 py-1.5 bg-white text-blue-700 text-xs rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors">
                                Kuat@123456
                            </button>
                            <button type="button" 
                                    onclick="useSuggestedPassword('S4y4@B!s4')"
                                    class="px-3 py-1.5 bg-white text-blue-700 text-xs rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors">
                                S4y4@B!s4
                            </button>
                            <button type="button" 
                                    onclick="generateRandomPassword()"
                                    class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-random mr-1"></i> Acak
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.change-password') }}" class="space-y-5" id="passwordForm">
                @csrf
                @method('PUT')

                <!-- Password Saat Ini -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" 
                               name="current_password" 
                               id="current_password"
                               required
                               class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent pr-10">
                        <button type="button" 
                                @click="show = !show"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- Password Baru -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" 
                               name="new_password" 
                               id="new_password"
                               required
                               minlength="8"
                               class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent pr-10">
                        <button type="button" 
                                @click="show = !show"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Password Baru -->
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" 
                               name="new_password_confirmation" 
                               id="new_password_confirmation"
                               required
                               class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent pr-10">
                        <button type="button" 
                                @click="show = !show"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- Strength Indicator -->
                <div class="bg-gray-50 p-4 rounded-lg" x-data="passwordStrength()" x-init="init">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Kekuatan Password:</span>
                        <span :class="{
                            'text-gray-600': strength.color === 'gray',
                            'text-red-600': strength.color === 'red',
                            'text-yellow-600': strength.color === 'yellow',
                            'text-green-600': strength.color === 'green',
                            'text-blue-600': strength.color === 'blue'
                        }" x-text="strength.text" class="text-sm font-medium"></span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mb-3">
                        <div class="h-full transition-all duration-300" 
                             :style="{ width: strength.percentage + '%' }"
                             :class="{
                                 'bg-red-500': strength.color === 'red',
                                 'bg-yellow-500': strength.color === 'yellow',
                                 'bg-green-500': strength.color === 'green',
                                 'bg-blue-500': strength.color === 'blue'
                             }"></div>
                    </div>
                    
                    <!-- Kriteria Password -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        <div :class="criteria.minLength ? 'text-green-600' : 'text-gray-500'">
                            <i :class="criteria.minLength ? 'fas fa-check-circle' : 'fas fa-circle'" class="mr-1"></i>
                            Minimal 8 karakter
                        </div>
                        <div :class="criteria.hasUpper ? 'text-green-600' : 'text-gray-500'">
                            <i :class="criteria.hasUpper ? 'fas fa-check-circle' : 'fas fa-circle'" class="mr-1"></i>
                            Huruf besar (A-Z)
                        </div>
                        <div :class="criteria.hasLower ? 'text-green-600' : 'text-gray-500'">
                            <i :class="criteria.hasLower ? 'fas fa-check-circle' : 'fas fa-circle'" class="mr-1"></i>
                            Huruf kecil (a-z)
                        </div>
                        <div :class="criteria.hasNumber ? 'text-green-600' : 'text-gray-500'">
                            <i :class="criteria.hasNumber ? 'fas fa-check-circle' : 'fas fa-circle'" class="mr-1"></i>
                            Angka (0-9)
                        </div>
                        <div :class="criteria.hasSpecial ? 'text-green-600' : 'text-gray-500'" class="sm:col-span-2">
                            <i :class="criteria.hasSpecial ? 'fas fa-check-circle' : 'fas fa-circle'" class="mr-1"></i>
                            Karakter khusus (@$!%*?&)
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-key mr-2"></i> Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab: Informasi Akun (Read Only) -->
    <div id="tab-account" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-info-circle text-purple-600 mr-2"></i>
                Informasi Akun
            </h3>

            <div class="space-y-4">
                <!-- Status Akun -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Status Akun</p>
                            <p class="text-xs text-gray-500">Status keaktifan akun Anda</p>
                        </div>
                        <span class="px-3 py-1 text-xs rounded-full {{ $user->is_active == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                </div>

                <!-- Metode Login -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Metode Login</p>
                            <p class="text-xs text-gray-500">Cara Anda masuk ke aplikasi</p>
                        </div>
                        <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            @if($user->google_id)
                                <i class="fab fa-google mr-1"></i> Google
                            @else
                                <i class="fas fa-envelope mr-1"></i> Email
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Info Lainnya (Read Only) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">ID Akun</p>
                        <p class="text-sm font-mono text-gray-800 break-all">{{ $user->id }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Email Terverifikasi</p>
                        <p class="text-sm text-gray-800">{{ $user->email_verified_at ? $user->email_verified_at->translatedFormat('d F Y H:i') : 'Belum' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Terdaftar Sejak</p>
                        <p class="text-sm text-gray-800">{{ $user->created_at->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Terakhir Login</p>
                        <p class="text-sm text-gray-800">{{ $user->last_login ? $user->last_login->translatedFormat('d F Y H:i') : '-' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Terakhir Update</p>
                        <p class="text-sm text-gray-800">{{ $user->updated_at->translatedFormat('d F Y') }}</p>
                    </div>
                </div>

                <!-- Hapus Akun -->
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <h4 class="text-sm font-medium text-red-600 mb-3">Zona Berbahaya</h4>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <p class="text-sm font-medium text-red-800">Hapus Akun</p>
                                <p class="text-xs text-red-600">Setelah dihapus, semua data (transaksi, prive, kategori) tidak dapat dikembalikan</p>
                            </div>
                            <button type="button"
                                    onclick="confirmDeleteAccount()"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm whitespace-nowrap">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Hapus Akun
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteAccountModal" class="fixed inset-0 z-[100] overflow-y-auto hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="hideDeleteModal()"></div>
    <div class="min-h-screen px-4 flex items-center justify-center">
        <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <div class="text-center mb-4">
                <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Hapus Akun Permanen</h3>
                <p class="text-sm text-gray-500 mt-2">
                    Tindakan ini akan menghapus semua data Anda secara permanen termasuk:
                </p>
                <ul class="text-xs text-gray-500 mt-2 text-left list-disc list-inside">
                    <li>Data profil dan usaha</li>
                    <li>Semua transaksi pemasukan & pengeluaran</li>
                    <li>Semua data prive</li>
                    <li>Semua kategori yang pernah dibuat</li>
                </ul>
                <p class="text-sm text-red-600 font-medium mt-2">Data yang sudah dihapus tidak dapat dikembalikan!</p>
            </div>

            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('DELETE')
                
                <div>
                    <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Masukkan Password untuk Konfirmasi
                    </label>
                    <input type="password" 
                           name="password" 
                           id="delete_password"
                           required
                           class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button"
                            onclick="hideDeleteModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Hapus Permanen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Tab switching
    function showTab(tabName) {
        // Update tab buttons
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-700');
        });
        
        const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
        if (activeBtn) {
            activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
            activeBtn.classList.add('active', 'bg-blue-600', 'text-white');
        }
        
        // Show selected tab content
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        const selectedTab = document.getElementById(`tab-${tabName}`);
        if (selectedTab) {
            selectedTab.classList.remove('hidden');
        }
    }

    // Delete account modal
    function confirmDeleteAccount() {
        document.getElementById('deleteAccountModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideDeleteModal() {
        document.getElementById('deleteAccountModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Password strength indicator
    function passwordStrength() {
        return {
            password: '',
            strength: { text: 'Ketik password', color: 'gray', percentage: 0 },
            criteria: {
                minLength: false,
                hasUpper: false,
                hasLower: false,
                hasNumber: false,
                hasSpecial: false
            },
            init() {
                const newPassword = document.getElementById('new_password');
                if (newPassword) {
                    newPassword.addEventListener('input', (e) => {
                        this.password = e.target.value;
                        this.checkStrength();
                    });
                }
            },
            checkStrength() {
                const pwd = this.password;
                
                // Update criteria
                this.criteria.minLength = pwd.length >= 8;
                this.criteria.hasUpper = /[A-Z]/.test(pwd);
                this.criteria.hasLower = /[a-z]/.test(pwd);
                this.criteria.hasNumber = /[0-9]/.test(pwd);
                this.criteria.hasSpecial = /[@$!%*?&]/.test(pwd);
                
                // Calculate strength
                let score = 0;
                if (this.criteria.minLength) score += 1;
                if (this.criteria.hasUpper) score += 1;
                if (this.criteria.hasLower) score += 1;
                if (this.criteria.hasNumber) score += 1;
                if (this.criteria.hasSpecial) score += 2;
                
                // Determine strength level
                if (pwd.length === 0) {
                    this.strength = { text: 'Ketik password', color: 'gray', percentage: 0 };
                } else if (score < 3) {
                    this.strength = { text: 'Lemah', color: 'red', percentage: 25 };
                } else if (score < 5) {
                    this.strength = { text: 'Sedang', color: 'yellow', percentage: 50 };
                } else if (score < 6) {
                    this.strength = { text: 'Kuat', color: 'green', percentage: 75 };
                } else {
                    this.strength = { text: 'Sangat Kuat', color: 'blue', percentage: 100 };
                }
            }
        };
    }

    // Use suggested password
    function useSuggestedPassword(password) {
        document.getElementById('new_password').value = password;
        document.getElementById('new_password_confirmation').value = password;
        
        // Trigger strength check
        const event = new Event('input');
        document.getElementById('new_password').dispatchEvent(event);
    }

    // Generate random password
    function generateRandomPassword() {
        const length = 12;
        const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const lowercase = 'abcdefghijklmnopqrstuvwxyz';
        const numbers = '0123456789';
        const special = '@$!%*?&';
        
        const all = uppercase + lowercase + numbers + special;
        
        let password = '';
        password += uppercase[Math.floor(Math.random() * uppercase.length)];
        password += lowercase[Math.floor(Math.random() * lowercase.length)];
        password += numbers[Math.floor(Math.random() * numbers.length)];
        password += special[Math.floor(Math.random() * special.length)];
        
        for (let i = 4; i < length; i++) {
            password += all[Math.floor(Math.random() * all.length)];
        }
        
        // Shuffle password
        password = password.split('').sort(() => 0.5 - Math.random()).join('');
        
        useSuggestedPassword(password);
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideDeleteModal();
        }
    });
</script>

<style>
    .tab-button.active {
        background-color: #2563eb;
        color: white;
    }
    .tab-button:not(.active) {
        background-color: #f3f4f6;
        color: #374151;
    }
    .tab-button:not(.active):hover {
        background-color: #e5e7eb;
    }
</style>
@endsection