<x-guest-layout>
    <!-- Header -->
    <div class="auth-header">
        <div class="mx-auto w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg mb-4">
            <i class="fas fa-envelope-open-text text-white text-2xl"></i>
        </div>
        <h2 class="auth-title">Verifikasi Email</h2>
        <p class="auth-subtitle">Hampir selesai! Verifikasi email Anda</p>
    </div>

    <!-- Info -->
    <div class="mt-6 text-sm text-gray-600 bg-blue-50 p-4 rounded-lg">
        <div class="flex">
            <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
            <div>
                <p class="mb-2">
                    Terima kasih telah mendaftar! Sebelum memulai, kami perlu memverifikasi alamat email Anda.
                </p>
                <p>
                    Kami telah mengirimkan tautan verifikasi ke <strong>{{ auth()->user()->email }}</strong>.
                </p>
            </div>
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert-success mt-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                <p class="text-sm text-green-700">
                    Tautan verifikasi baru telah dikirim ke email Anda.
                </p>
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="mt-6 space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary">
                <i class="fas fa-paper-plane mr-2"></i>
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-center text-sm text-gray-600 hover:text-gray-900">
                <i class="fas fa-sign-out-alt mr-1"></i>
                Logout
            </button>
        </form>
    </div>
</x-guest-layout>