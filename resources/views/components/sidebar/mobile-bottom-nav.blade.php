<nav class="mobile-nav lg:hidden bg-white border-t border-gray-200 fixed bottom-0 left-0 right-0 z-50 shadow-lg">
    <div class="flex items-stretch w-full">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex-1 flex flex-col items-center justify-center py-3 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
            <div class="relative">
                <i class="fas fa-home text-xl"></i>
                @if(request()->routeIs('dashboard'))
                    <span class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-600 rounded-full"></span>
                @endif
            </div>
            <span class="text-[11px] mt-1 font-medium {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-500' }}">
                Dashboard
            </span>
        </a>

        <!-- Transaksi -->
        <a href="{{ route('transactions.index') }}" 
           class="flex-1 flex flex-col items-center justify-center py-3 transition-all duration-200 {{ request()->routeIs('transactions.*') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
            <div class="relative">
                <i class="fas fa-exchange-alt text-xl"></i>
                @if(request()->routeIs('transactions.*'))
                    <span class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-600 rounded-full"></span>
                @endif
            </div>
            <span class="text-[11px] mt-1 font-medium {{ request()->routeIs('transactions.*') ? 'text-blue-600' : 'text-gray-500' }}">
                Transaksi
            </span>
        </a>

        <!-- Laporan -->
        <a href="{{ route('reports.index') }}" 
           class="flex-1 flex flex-col items-center justify-center py-3 transition-all duration-200 {{ request()->routeIs('reports.*') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
            <div class="relative">
                <i class="fas fa-chart-bar text-xl"></i>
                @if(request()->routeIs('reports.*'))
                    <span class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-600 rounded-full"></span>
                @endif
            </div>
            <span class="text-[11px] mt-1 font-medium {{ request()->routeIs('reports.*') ? 'text-blue-600' : 'text-gray-500' }}">
                Laporan
            </span>
        </a>

        <!-- Prive -->
        <a href="{{ route('prive.index') }}" 
           class="flex-1 flex flex-col items-center justify-center py-3 transition-all duration-200 {{ request()->routeIs('prive.*') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
            <div class="relative">
                <i class="fas fa-money-bill-wave text-xl"></i>
                @if(request()->routeIs('prive.*'))
                    <span class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-600 rounded-full"></span>
                @endif
            </div>
            <span class="text-[11px] mt-1 font-medium {{ request()->routeIs('prive.*') ? 'text-blue-600' : 'text-gray-500' }}">
                Prive
            </span>
        </a>

        <!-- Keperluan -->
        <a href="{{ route('prive-purposes.index') }}" 
           class="flex-1 flex flex-col items-center justify-center py-3 transition-all duration-200 {{ request()->routeIs('prive-purposes.*') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
            <div class="relative">
                <i class="fas fa-tags text-xl"></i>
                @if(request()->routeIs('prive-purposes.*'))
                    <span class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-600 rounded-full"></span>
                @endif
            </div>
            <span class="text-[11px] mt-1 font-medium {{ request()->routeIs('prive-purposes.*') ? 'text-blue-600' : 'text-gray-500' }}">
                Keperluan
            </span>
        </a>

    </div>
</nav>

<!-- Spacer supaya konten tidak tertutup navbar -->
<div class="lg:hidden h-16"></div>

<style>

/* Hilangkan padding tambahan */
.mobile-nav {
    padding: 0 !important;
}

/* Garis pemisah antar menu */
.mobile-nav a {
    border-right: 1px solid #f3f4f6;
}

/* Menu terakhir tanpa border */
.mobile-nav a:last-child {
    border-right: none;
}

/* Animasi hover */
.mobile-nav a {
    transform: scale(1);
    transition: all 0.2s ease;
}

.mobile-nav a:hover {
    background-color: #f9fafb;
}

/* Support safe area iPhone */
@@supports (padding-bottom: env(safe-area-inset-bottom)) {
    .mobile-nav {
        padding-bottom: env(safe-area-inset-bottom) !important;
    }

    .mobile-nav a {
        padding-bottom: calc(0.75rem + env(safe-area-inset-bottom));
    }
}

</style>