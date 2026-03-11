@props(['user' => null])

@php
if (!$user) {
    $user = Auth::user();
}
@endphp

<aside id="desktopSidebar" class="desktop-sidebar bg-gradient-to-b from-blue-800 to-blue-900 text-white flex flex-col shadow-2xl h-screen fixed lg:sticky top-0 overflow-y-auto">
    <!-- Close button for mobile -->
    <div class="lg:hidden p-4 flex justify-end">
        <button onclick="toggleMobileMenu()" class="text-white hover:text-blue-200">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    
    <!-- Logo -->
    <div class="p-6 border-b border-blue-700">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center">
                <i class="fas fa-wallet text-blue-800 text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold">KeuanganKu</h1>
                <p class="text-xs text-blue-200">UMKM Digital</p>
            </div>
        </div>
    </div>

<!-- User Info -->
<div class="p-6 border-b border-blue-700">
    <div class="flex items-center space-x-3">
        <div class="w-12 h-12 rounded-full overflow-hidden bg-blue-600 flex items-center justify-center border-2 border-white flex-shrink-0">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar_url }}" 
                     alt="{{ Auth::user()->name }}" 
                     class="w-full h-full object-cover">
            @else
                <span class="text-xl font-bold text-white">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </span>
            @endif
        </div>
        <div class="min-w-0">
            <p class="font-semibold text-white truncate">{{ Auth::user()->name }}</p>
            <p class="text-xs text-blue-200 truncate">{{ Auth::user()->business->business_name ?? 'Warkop' }}</p>
        </div>
    </div>
</div>

    <!-- Desktop Navigation - Hanya muncul di layar besar -->
    <nav class="hidden lg:block flex-1 px-4 py-6 space-y-2">
        <x-sidebar.nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home">
            Dashboard
        </x-sidebar.nav-link>
        
        <x-sidebar.nav-link href="{{ route('transactions.index') }}" :active="request()->routeIs('transactions.*')" icon="exchange-alt">
            Transaksi
        </x-sidebar.nav-link>
        
        <x-sidebar.nav-link href="{{ route('reports.index') }}" :active="request()->routeIs('reports.*')" icon="chart-bar">
            Laporan
        </x-sidebar.nav-link>
        
        <x-sidebar.nav-link href="{{ route('prive.index') }}" :active="request()->routeIs('prive.*')" icon="money-bill-wave">
            Prive
        </x-sidebar.nav-link>

        <x-sidebar.nav-link href="{{ route('prive-purposes.index') }}" :active="request()->routeIs('prive-purposes.*')" icon="tags">
            Keperluan Prive
        </x-sidebar.nav-link>
    </nav>

    <!-- Mobile Navigation - Hanya menu yang tidak ada di bottom nav -->
    <nav class="lg:hidden flex-1 px-4 py-6 space-y-2">
        <x-sidebar.nav-link href="{{ route('setting.index') }}" :active="request()->routeIs('setting.*')" icon="user-cog">
            Pengaturan
        </x-sidebar.nav-link>
        
        <!-- Logout Button with Confirmation -->
        <button onclick="showLogoutModal()" class="w-full flex items-center space-x-3 px-4 py-3 text-blue-100 hover:bg-blue-700 rounded-xl transition">
            <i class="fas fa-sign-out-alt w-5"></i>
            <span>Logout</span>
        </button>
    </nav>

    <!-- Footer Navigation (Desktop only) -->
    <div class="p-4 border-t border-blue-700 hidden lg:block">
        <x-sidebar.nav-link href="{{ route('setting.index') }}" :active="request()->routeIs('setting.*')" icon="user-cog">
            Pengaturan
        </x-sidebar.nav-link>
        
        <!-- Logout Button with Confirmation -->
        <button onclick="showLogoutModal()" class="w-full flex items-center space-x-3 px-4 py-3 text-blue-100 hover:bg-blue-700 rounded-xl transition mt-2">
            <i class="fas fa-sign-out-alt w-5"></i>
            <span>Logout</span>
        </button>
    </div>
</aside>