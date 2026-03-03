<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'KeuanganKu') }}</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); }
        .chart-container { position: relative; height: 200px; width: 100%; }

        /* Mobile bottom navigation */
        .mobile-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 50;
            padding-bottom: env(safe-area-inset-bottom, 0);
        }

        /* Desktop sidebar */
        .desktop-sidebar {
            width: 260px;
            transition: transform 0.3s ease;
        }

        /* Mobile menu overlay */
        .mobile-menu-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 40;
        }

        .mobile-menu-overlay.active {
            display: block;
        }

        /* Responsive breakpoints */
        @media (max-width: 1023px) {
            .desktop-sidebar {
                position: fixed;
                left: -260px;
                height: 100vh;
                z-index: 45;
                transition: left 0.3s ease;
            }

            .desktop-sidebar.mobile-open {
                left: 0;
            }

            .mobile-nav {
                display: flex;
            }

            main {
                padding-bottom: 70px;
            }

            .floating-action-btn {
                bottom: 80px;
            }
        }

        @media (max-width: 480px) {
            .chart-container {
                height: 180px;
            }
        }

        /* Safe area for notched phones */
        .mobile-nav {
            padding-bottom: env(safe-area-inset-bottom, 0);
        }

            main {
                padding-bottom: calc(70px + env(safe-area-inset-bottom));
            }

            .floating-action-btn {
                bottom: calc(80px + env(safe-area-inset-bottom));
            }
        }

        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Mobile Menu Overlay -->
    <div id="mobileMenuOverlay" class="mobile-menu-overlay" onclick="toggleMobileMenu()"></div>

    <div class="min-h-screen flex">
        <!-- Desktop Sidebar Component -->
        <x-sidebar.desktop-sidebar />

        <!-- Main Content -->
        <main class="flex-1 lg:ml-0">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm sticky top-0 z-10">
                <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                    <!-- Mobile Menu Button -->
                    <button onclick="toggleMobileMenu()" class="lg:hidden p-2 text-gray-600 hover:text-blue-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <h2 class="text-xl lg:text-2xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>

                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <!-- Notifications Component -->
                        <x-notifications.notification-dropdown :notifications="$notifications ?? collect()" :unread="$unreadNotifications ?? 0" />
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile Bottom Navigation Component -->
    <x-sidebar.mobile-bottom-nav />

    <!-- Floating Action Button -->
    @hasSection('fab')
        @yield('fab')
    @else
        <div class="floating-action-btn fixed bottom-20 lg:bottom-6 right-4 sm:right-6">
            <a href="{{ route('transactions.create') }}"
               class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 flex items-center justify-center transition-all hover:scale-110">
                <i class="fas fa-plus text-base sm:text-xl"></i>
            </a>
        </div>
    @endif

    <!-- Logout Confirmation Modal -->
    <x-modals.logout-confirmation />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Mobile Menu Toggle Script -->
    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('desktopSidebar');
            const overlay = document.getElementById('mobileMenuOverlay');

            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');

            if (sidebar.classList.contains('mobile-open')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                const sidebar = document.getElementById('desktopSidebar');
                const overlay = document.getElementById('mobileMenuOverlay');
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
