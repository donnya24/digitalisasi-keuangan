<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Digitalisasi Keuangan UMKM') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Top Navigation -->
        <nav class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-30" x-data="{ open: false }">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex items-center flex-shrink-0">
                            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600">
                                KeuanganKu
                            </a>
                        </div>

                        <!-- Desktop Navigation -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                                Transaksi
                            </x-nav-link>
                            <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                                Laporan
                            </x-nav-link>
                            <x-nav-link :href="route('prive.index')" :active="request()->routeIs('prive.*')">
                                Prive
                            </x-nav-link>
                            <!-- Kategori link sudah dihapus -->
                        </div>
                    </div>

                    <!-- Notifications & Profile -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Notifications Dropdown -->
                        <div class="relative ml-3" x-data="{ open: false }">
                            <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-blue-600">
                                <i class="fas fa-bell text-lg sm:text-xl"></i>
                                @if(isset($unreadNotifications) && $unreadNotifications > 0)
                                    <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                                        {{ $unreadNotifications }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="open" @click.away="open = false"
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50">
                                <div class="px-4 py-2 text-sm text-gray-700 border-b flex justify-between items-center">
                                    <span class="font-semibold">Notifikasi</span>
                                    @if(isset($unreadNotifications) && $unreadNotifications > 0)
                                        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">
                                                Tandai semua dibaca
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    @forelse($notifications ?? [] as $notif)
                                        <div class="px-4 py-3 hover:bg-gray-50 border-b last:border-0">
                                            <p class="text-xs sm:text-sm font-medium">
                                                <i class="fas fa-{{ $notif['icon'] ?? 'bell' }} mr-1"></i>
                                                {{ $notif['title'] }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $notif['message'] }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $notif['time'] }}</p>
                                        </div>
                                    @empty
                                        <div class="px-4 py-8 text-center text-gray-500">
                                            <i class="fas fa-check-circle text-2xl mb-2"></i>
                                            <p class="text-xs sm:text-sm">Tidak ada notifikasi</p>
                                        </div>
                                    @endforelse
                                </div>
                                <div class="px-4 py-2 border-t text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-xs sm:text-sm text-blue-600 hover:underline">
                                        Lihat semua notifikasi
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative ml-3" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                                    <span>{{ Auth::user()->name }}</span>
                                    <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linecap="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>

                            <div x-show="open" @click.away="open = false"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-cog mr-2"></i> Pengaturan
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center -mr-2 sm:hidden">
                        <button @click="open = !open" class="p-2 text-gray-600 hover:text-blue-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div x-show="open" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="fas fa-home mr-2"></i> Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                        <i class="fas fa-exchange-alt mr-2"></i> Transaksi
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        <i class="fas fa-chart-bar mr-2"></i> Laporan
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('prive.index')" :active="request()->routeIs('prive.*')">
                        <i class="fas fa-money-bill-wave mr-2"></i> Prive
                    </x-responsive-nav-link>
                </div>

                <!-- Mobile Profile -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('profile.index')">
                            <i class="fas fa-user-cog mr-2"></i> Pengaturan
                        </x-responsive-nav-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="pt-16">
            <div class="py-6">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>

        <!-- Floating Action Button (hanya muncul di halaman tertentu) -->
        @hasSection('fab')
            @yield('fab')
        @else
            <div class="fixed bottom-6 right-6 z-20">
                <a href="{{ route('transactions.create') }}"
                   class="flex items-center justify-center w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" d="M12 4v16m8-8H4" />
                    </svg>
                </a>
            </div>
        @endif
    </div>

    <!-- Confirm Delete Function -->
    <script>
        function confirmDelete(type, description, amount = null) {
            let title = 'Hapus ' + (type === 'prive' ? 'Prive' : 'Transaksi');
            let text = '';
            
            if (type === 'prive') {
                text = `Apakah Anda yakin ingin menghapus prive sebesar Rp ${new Intl.NumberFormat('id-ID').format(amount)}?`;
            } else {
                text = `Apakah Anda yakin ingin menghapus transaksi "${description}"?`;
            }
            
            text += ' Data yang sudah dihapus tidak dapat dikembalikan.';
            
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    // Cari form terdekat dan submit
                    const button = event.target;
                    const form = button.closest('form');
                    if (form) {
                        form.submit();
                    }
                }
            });
            
            return false;
        }
    </script>

    <!-- Auto refresh notifikasi -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto refresh notifikasi setiap 30 detik
            setInterval(function() {
                fetch('{{ route("notifications.latest") }}')
                    .then(response => response.json())
                    .then(data => {
                        // Update badge notifikasi
                        const badge = document.querySelector('.fa-bell + span');
                        if (badge) {
                            if (data.unread_count > 0) {
                                badge.textContent = data.unread_count;
                                badge.classList.remove('hidden');
                            } else {
                                badge.classList.add('hidden');
                            }
                        }
                    })
                    .catch(error => console.error('Error fetching notifications:', error));
            }, 30000);
        });
    </script>

    @stack('scripts')
</body>
</html>