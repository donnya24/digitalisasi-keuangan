<nav class="mobile-nav lg:hidden bg-white border-t border-gray-200 py-2 px-4">
    <div class="flex justify-around items-center">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center px-3 py-1 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
            <i class="fas fa-home text-lg"></i>
            <span class="text-xs mt-1">Dashboard</span>
        </a>
        <a href="{{ route('transactions.index') }}" class="flex flex-col items-center px-3 py-1 {{ request()->routeIs('transactions.*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
            <i class="fas fa-exchange-alt text-lg"></i>
            <span class="text-xs mt-1">Transaksi</span>
        </a>
        <a href="{{ route('categories.index') }}" class="flex flex-col items-center px-3 py-1 {{ request()->routeIs('categories.*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
            <i class="fas fa-tags text-lg"></i>
            <span class="text-xs mt-1">Kategori</span>
        </a>
        <a href="{{ route('reports.index') }}" class="flex flex-col items-center px-3 py-1 {{ request()->routeIs('reports.*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
            <i class="fas fa-chart-bar text-lg"></i>
            <span class="text-xs mt-1">Laporan</span>
        </a>
        <a href="{{ route('prive.index') }}" class="flex flex-col items-center px-3 py-1 {{ request()->routeIs('prive.*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
            <i class="fas fa-money-bill-wave text-lg"></i>
            <span class="text-xs mt-1">Prive</span>
        </a>
    </div>
</nav>