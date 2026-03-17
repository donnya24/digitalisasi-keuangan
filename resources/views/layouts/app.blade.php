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

    <!-- ALPINE STORE GLOBAL untuk NOTIFIKASI -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('notification', {
                notifications: [],
                unreadCount: 0,
                filter: 'all',

                init() {
                    this.loadNotifications();

                    // Auto refresh setiap 30 detik
                    setInterval(() => {
                        this.loadNotifications();
                    }, 30000);
                },

                loadNotifications() {
                    fetch('{{ route("notifications.latest") }}')
                        .then(response => response.json())
                        .then(data => {
                            this.notifications = data.notifications || [];
                            this.unreadCount = data.unread_count || 0;
                            console.log('Notifikasi dimuat:', this.notifications.length);
                        })
                        .catch(error => console.error('Error loading notifications:', error));
                },

                get filteredNotifications() {
                    if (!this.notifications) return [];
                    if (this.filter === 'all') return this.notifications;
                    if (this.filter === 'unread') {
                        return this.notifications.filter(n => !n.is_read);
                    }
                    return this.notifications.filter(n => n.is_read);
                },

                markAsRead(id) {
                    fetch(`/notifications/${id}/mark-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        const notif = this.notifications.find(n => n.id === id);
                        if (notif) {
                            notif.is_read = true;
                            this.unreadCount = this.notifications.filter(n => !n.is_read).length;
                        }
                    });
                },

                markAllAsRead() {
                    fetch('{{ route("notifications.mark-all-read") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        this.notifications.forEach(n => n.is_read = true);
                        this.unreadCount = 0;
                    });
                },

                deleteNotification(id) {
                    if (confirm('Hapus notifikasi ini?')) {
                        fetch(`/notifications/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        }).then(() => {
                            this.notifications = this.notifications.filter(n => n.id !== id);
                            this.unreadCount = this.notifications.filter(n => !n.is_read).length;
                        });
                    }
                }
            });

            // Inisialisasi store
            Alpine.store('notification').init();
        });
    </script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')
    <!-- Custom Mobile Styles -->
<style>
    /* Pastikan spinner terlihat di mobile */
    @media (max-width: 640px) {
        #loading-spinner > div {
            max-width: 80vw;
            margin: 0 20px;
        }

        #loading-spinner .animate-spin {
            width: 48px;
            height: 48px;
            border-width: 4px;
        }
    }

    /* Animasi fade untuk spinner */
    #loading-spinner {
        transition: opacity 0.2s ease-in-out;
        opacity: 1;
    }

    #loading-spinner.hidden {
        display: none !important;
    }
</style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <!-- Loading Spinner -->
    <x-loading-spinner />

    <!-- Mobile Menu Overlay -->
    <div id="mobileMenuOverlay" class="mobile-menu-overlay" onclick="toggleMobileMenu()"></div>

    <div class="min-h-screen">
        <!-- Top Navigation -->
        <nav class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-30" x-data="{ open: false }">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center">
                            <i class="fas fa-wallet text-blue-600 text-xl mr-2"></i>
                            <span class="font-bold text-gray-800">{{ config('app.name') }}</span>
                        </a>
                    </div>

                    <!-- Right Navigation -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        @auth
                            <x-notification-dropdown />
                        @endauth

                        <!-- User Menu -->
                        @auth
                            <div class="relative">
                                <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                                    <span class="text-sm">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>

                                <!-- Dropdown -->
                                <div x-show="open"
                                     @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="{{ route('setting.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i> Profil
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endauth
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
    </div>

    <!-- Floating Action Button -->
    @yield('fab')

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

    <!-- Spinner Script - Optimized for Mobile -->
    <script>
        (function() {
            // Spinner elements
            let spinner = document.getElementById('loading-spinner');
            let timeoutId = null;

            // Fungsi untuk menampilkan spinner dengan delay minimal
            function showSpinner() {
                // Hapus timeout jika ada
                if (timeoutId) clearTimeout(timeoutId);

                if (spinner) {
                    spinner.classList.remove('hidden');
                    // Sembunyikan otomatis setelah 10 detik (fallback)
                    timeoutId = setTimeout(() => {
                        hideSpinner();
                    }, 10000);
                }
            }

            // Fungsi untuk menyembunyikan spinner
            function hideSpinner() {
                if (spinner) {
                    spinner.classList.add('hidden');
                    if (timeoutId) {
                        clearTimeout(timeoutId);
                        timeoutId = null;
                    }
                }
            }

            // Handler untuk semua klik - support mobile dan desktop
            document.addEventListener('click', function(e) {
                // Cari elemen link atau button
                const link = e.target.closest('a');
                const button = e.target.closest('button[type="submit"]');
                const form = e.target.closest('form');

                // Skip untuk link dengan target _blank atau download
                if (link) {
                    if (link.target === '_blank') return;
                    if (link.hasAttribute('download')) return;
                    if (link.hostname && link.hostname !== window.location.hostname) return;
                    if (link.hash && link.href === window.location.href) return;

                    showSpinner();
                }

                // Untuk submit button dalam form
                if (button && button.type === 'submit') {
                    showSpinner();
                }

                // Untuk form submission via JavaScript
                if (form && !button) {
                    // Cek apakah form akan di-submit
                    if (form.method && form.method.toLowerCase() === 'post') {
                        showSpinner();
                    }
                }
            });

            // Handle form submission via JavaScript
            document.addEventListener('submit', function(e) {
                showSpinner();
            });

            // Handle touch events untuk mobile
            document.addEventListener('touchstart', function(e) {
                const link = e.target.closest('a');
                const button = e.target.closest('button[type="submit"]');

                if (link || button) {
                    // Delay sebentar untuk memastikan bukan scroll
                    setTimeout(() => {
                        showSpinner();
                    }, 50);
                }
            }, { passive: true });

            // Sembunyikan spinner saat halaman selesai dimuat
            window.addEventListener('load', hideSpinner);
            window.addEventListener('pageshow', hideSpinner);

            // Sembunyikan spinner jika terjadi error
            window.addEventListener('error', hideSpinner);

            // Override fetch untuk AJAX requests
            const originalFetch = window.fetch;
            window.fetch = function() {
                showSpinner();
                return originalFetch.apply(this, arguments)
                    .finally(() => hideSpinner());
            };

            // Override XMLHttpRequest untuk AJAX lama
            const originalXHROpen = XMLHttpRequest.prototype.open;
            const originalXHRSend = XMLHttpRequest.prototype.send;

            XMLHttpRequest.prototype.open = function() {
                this._url = arguments[1];
                return originalXHROpen.apply(this, arguments);
            };

            XMLHttpRequest.prototype.send = function() {
                showSpinner();
                this.addEventListener('loadend', () => hideSpinner());
                return originalXHRSend.apply(this, arguments);
            };
        })();
    </script>
    @stack('scripts')
</body>
</html>
