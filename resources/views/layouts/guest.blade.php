<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Digitalisasi Keuangan') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 (untuk konsistensi) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <!-- Loading Spinner Component -->
    <x-loading-spinner />

    <div class="guest-layout">
        <div class="auth-card">
            {{ $slot }}
        </div>
        
        <!-- Footer -->
        <p class="mt-4 text-xs text-center text-gray-500">
            © {{ date('Y') }} Digitalisasi Keuangan UMKM. All rights reserved.
        </p>
    </div>

    <!-- Script untuk menangani spinner di guest pages -->
    <script>
        (function() {
            // Fungsi untuk menampilkan spinner
            function showSpinner() {
                const spinner = document.getElementById('loading-spinner');
                if (spinner) {
                    spinner.classList.remove('hidden');
                }
            }

            // Fungsi untuk menyembunyikan spinner
            function hideSpinner() {
                const spinner = document.getElementById('loading-spinner');
                if (spinner) {
                    spinner.classList.add('hidden');
                }
            }

            // Tampilkan spinner saat link diklik
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                
                // Jika bukan link atau link ke external, ignore
                if (!link) return;
                if (link.target === '_blank') return;
                if (link.hasAttribute('download')) return;
                if (link.hash && link.href === window.location.href) return;
                
                // Tampilkan spinner
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

            // Untuk AJAX requests (jika ada)
            const originalFetch = window.fetch;
            window.fetch = function() {
                showSpinner();
                return originalFetch.apply(this, arguments)
                    .finally(() => hideSpinner());
            };
        })();
    </script>
    
    @stack('scripts')
</body>
</html>