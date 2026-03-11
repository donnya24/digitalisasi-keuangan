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
    <!-- Loading Spinner - CUKUP SATU KALI -->
    <x-loading-spinner />

    <!-- Mobile Menu Overlay -->
    <div id="mobileMenuOverlay" class="mobile-menu-overlay" onclick="toggleMobileMenu()"></div>

    <div class="min-h-screen">
        <!-- Top Navigation -->
        <nav class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-30" x-data="{ open: false }">
            <!-- ... navigasi tetap sama ... -->
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

    <!-- SPINNER SCRIPT - PASTIKAN HANYA SATU KALI -->
    <script>
        (function() {
            // Fungsi untuk menampilkan spinner
            function showSpinner() {
                const spinner = document.getElementById('loading-spinner');
                if (spinner) {
                    spinner.classList.remove('hidden');
                    console.log('Spinner ditampilkan');
                } else {
                    console.error('Spinner element tidak ditemukan!');
                }
            }

            // Fungsi untuk menyembunyikan spinner
            function hideSpinner() {
                const spinner = document.getElementById('loading-spinner');
                if (spinner) {
                    spinner.classList.add('hidden');
                    console.log('Spinner disembunyikan');
                }
            }

            // Tampilkan spinner saat link diklik
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                
                if (!link) return;
                if (link.target === '_blank') return;
                if (link.hasAttribute('download')) return;
                if (link.hash && link.href === window.location.href) return;
                
                // Cek apakah link internal
                if (link.hostname && link.hostname !== window.location.hostname) return;
                
                showSpinner();
            });

            // Tampilkan spinner saat form disubmit
            document.addEventListener('submit', function(e) {
                showSpinner();
            });

            // Sembunyikan spinner saat halaman selesai dimuat
            window.addEventListener('load', function() {
                hideSpinner();
            });

            // Sembunyikan spinner jika terjadi error
            window.addEventListener('error', function() {
                hideSpinner();
            });

            // Override fetch untuk AJAX requests
            const originalFetch = window.fetch;
            window.fetch = function() {
                showSpinner();
                return originalFetch.apply(this, arguments)
                    .finally(() => hideSpinner());
            };
        })();
    </script>

    <!-- Auto refresh notifikasi -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setInterval(function() {
                fetch('{{ route("notifications.latest") }}')
                    .then(response => response.json())
                    .then(data => {
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