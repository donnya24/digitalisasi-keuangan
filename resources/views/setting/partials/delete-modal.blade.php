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
                <ul class="text-xs text-gray-500 mt-3 text-left bg-gray-50 p-3 rounded-lg space-y-1">
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
                    <input type="password"
                           name="password"
                           id="delete_password"
                           required
                           placeholder="••••••••"
                           class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('password') border-red-500 @enderror">
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
