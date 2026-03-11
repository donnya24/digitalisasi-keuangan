@props(['user'])

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
                    <p class="text-sm font-medium text-gray-700">
                        <i class="fas fa-shield-alt text-gray-500 mr-2"></i>
                        Status Akun
                    </p>
                    <p class="text-xs text-gray-500">Status keaktifan akun Anda</p>
                </div>
                <span class="px-3 py-1 text-xs rounded-full {{ $user->is_active == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    <i class="fas fa-{{ $user->is_active == 'active' ? 'check-circle' : 'exclamation-circle' }} mr-1"></i>
                    {{ $user->is_active == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>
        </div>

        <!-- Metode Login -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">
                        <i class="fas fa-sign-in-alt text-gray-500 mr-2"></i>
                        Metode Login
                    </p>
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
                <p class="text-xs text-gray-500 mb-1">
                    <i class="fas fa-id-card text-gray-400 mr-1"></i>
                    ID Akun
                </p>
                <p class="text-sm font-mono text-gray-800 break-all">{{ $user->id }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">
                    <i class="fas fa-envelope text-gray-400 mr-1"></i>
                    Email Terverifikasi
                </p>
                <p class="text-sm text-gray-800">
                    @if($user->email_verified_at)
                        <span class="flex items-center text-green-600">
                            <i class="fas fa-check-circle mr-1"></i>
                            {{ $user->email_verified_at->translatedFormat('d F Y H:i') }}
                        </span>
                    @else
                        <span class="text-yellow-600">Belum terverifikasi</span>
                    @endif
                </p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">
                    <i class="fas fa-calendar-plus text-gray-400 mr-1"></i>
                    Terdaftar Sejak
                </p>
                <p class="text-sm text-gray-800">{{ $user->created_at->translatedFormat('d F Y') }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">
                    <i class="fas fa-history text-gray-400 mr-1"></i>
                    Terakhir Login
                </p>
                <p class="text-sm text-gray-800">{{ $user->last_login ? $user->last_login->translatedFormat('d F Y H:i') : '-' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">
                    <i class="fas fa-sync-alt text-gray-400 mr-1"></i>
                    Terakhir Update
                </p>
                <p class="text-sm text-gray-800">{{ $user->updated_at->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <!-- Hapus Akun -->
        <div class="border-t border-gray-200 pt-4 mt-4">
            <h4 class="text-sm font-medium text-red-600 mb-3 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Zona Berbahaya
            </h4>
            <div class="bg-red-50 p-4 rounded-lg">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <p class="text-sm font-medium text-red-800">Hapus Akun</p>
                        <p class="text-xs text-red-600">Setelah dihapus, semua data tidak dapat dikembalikan</p>
                    </div>
                    <button type="button"
                            onclick="confirmDeleteAccount()"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm whitespace-nowrap font-medium">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus Akun
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteAccountModal" class="fixed inset-0 z-[100] overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="hideDeleteModal()"></div>

    <div class="min-h-screen px-4 flex items-center justify-center">
        <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6 transform transition-all">
            <!-- Icon Warning -->
            <div class="text-center mb-4">
                <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900" id="modal-title">Hapus Akun Permanen</h3>
                <p class="text-sm text-gray-500 mt-2">
                    Tindakan ini akan menghapus semua data Anda secara permanen termasuk:
                </p>

                <!-- Daftar data yang akan dihapus -->
                <ul class="text-xs text-gray-500 mt-3 text-left bg-gray-50 p-3 rounded-lg space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-user text-red-400 mr-2 mt-0.5"></i>
                        <span>Data profil dan usaha</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-exchange-alt text-red-400 mr-2 mt-0.5"></i>
                        <span>Semua transaksi pemasukan & pengeluaran</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-money-bill-wave text-red-400 mr-2 mt-0.5"></i>
                        <span>Semua data prive</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-tags text-red-400 mr-2 mt-0.5"></i>
                        <span>Semua kategori yang pernah dibuat</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-image text-red-400 mr-2 mt-0.5"></i>
                        <span>Foto profil dan logo usaha</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-bell text-red-400 mr-2 mt-0.5"></i>
                        <span>Semua notifikasi</span>
                    </li>
                </ul>

                <p class="text-sm text-red-600 font-medium mt-3 bg-red-50 p-2 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Data yang sudah dihapus tidak dapat dikembalikan!
                </p>
            </div>

            <!-- Form Konfirmasi -->
            <form method="POST" action="{{ route('setting.account.destroy') }}" class="space-y-4">
                @csrf
                @method('DELETE')

                <div>
                    <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Masukkan Password untuk Konfirmasi
                    </label>
                    <div class="relative">
                        <input type="password"
                               name="password"
                               id="delete_password"
                               required
                               placeholder="••••••••"
                               class="w-full px-4 py-3 text-sm border rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent pr-10 @error('password') border-red-500 @else border-gray-300 @enderror">
                        <button type="button"
                                onclick="togglePasswordVisibility('delete_password')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="delete_password_icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Aksi -->
                <div class="flex gap-3 pt-2">
                    <button type="button"
                            onclick="hideDeleteModal()"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm font-medium">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus Permanen
                    </button>
                </div>
            </form>

            <!-- Catatan Keamanan -->
            <p class="text-xs text-gray-400 text-center mt-4">
                <i class="fas fa-shield-alt mr-1"></i>
                Tindakan ini dicatat untuk keamanan akun Anda
            </p>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk toggle password visibility di modal
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId + '_icon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
