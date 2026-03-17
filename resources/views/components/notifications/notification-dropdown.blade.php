@props(['notifications' => [], 'unread' => 0])

@php
    // PASTIKAN PAKAI SHARED DATA DARI APPSERVICEPROVIDER
    $displayNotifications = $shared_notifications ?? [];
    $displayUnread = $shared_unread_count ?? 0;

    // DEBUG VISUAL - TAMPILKAN DI POJOK
    $debugInfo = [
        'from' => 'AppServiceProvider',
        'notif_count' => count($displayNotifications),
        'unread' => $displayUnread,
        'path' => request()->path()
    ];
@endphp

<!-- DEBUG INFO - Tampilkan di pojok kanan atas -->
<div style="position:fixed; top:60px; right:10px; background:black; color:white; padding:10px; border-radius:5px; z-index:9999; font-size:12px; opacity:0.9;">
    <div style="font-weight:bold; color:#FFD700;">🔔 NOTIF DEBUG (AppServiceProvider)</div>
    <div>📊 Count: {{ count($displayNotifications) }}</div>
    <div>👁️ Unread: {{ $displayUnread }}</div>
    <div>📍 Path: {{ request()->path() }}</div>
    @if(count($displayNotifications) > 0)
        <div style="color:#90EE90; margin-top:5px;">
            ✅ Data: {{ $displayNotifications[0]['title'] ?? '' }}
        </div>
    @else
        <div style="color:#FF6B6B; margin-top:5px;">
            ❌ TIDAK ADA DATA
        </div>
    @endif
</div>

<div class="relative" x-data="{ open: false, showAllModal: false }">
    <!-- Bell Icon -->
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-blue-600 focus:outline-none">
        <i class="fas fa-bell text-lg sm:text-xl"></i>
        @if($displayUnread > 0)
        <span class="absolute top-0 right-0 w-3 h-3 sm:w-4 sm:h-4 bg-red-500 text-white text-[10px] sm:text-xs rounded-full flex items-center justify-center">
            {{ $displayUnread }}
        </span>
        @else
        <span class="absolute top-0 right-0 w-3 h-3 sm:w-4 sm:h-4 bg-gray-400 text-white text-[10px] sm:text-xs rounded-full flex items-center justify-center">
            0
        </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open"
         @click.away="open = false"
         x-cloak
         class="absolute right-0 mt-2 w-64 sm:w-80 bg-white rounded-lg shadow-xl py-2 z-50 border border-gray-200">

        <div class="px-4 py-2 border-b flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Notifikasi</h3>
            @if($displayUnread > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button class="text-xs text-blue-600 hover:underline">
                    Tandai semua
                </button>
            </form>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($displayNotifications as $notif)
            <div class="px-4 py-3 hover:bg-gray-50 border-b">
                <div class="flex gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $notif['bg_color'] ?? 'bg-gray-100' }}">
                        <i class="fas fa-{{ $notif['icon'] ?? 'bell' }} text-sm {{ $notif['text_color'] ?? 'text-gray-600' }}"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium {{ $notif['text_color'] ?? 'text-gray-800' }}">
                            {{ $notif['title'] ?? 'Notifikasi' }}
                            @if(!($notif['is_read'] ?? true))
                            <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">
                                Baru
                            </span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500">{{ $notif['message'] ?? '' }}</p>
                        <p class="text-xs text-gray-400">{{ $notif['time'] ?? '' }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-6 text-gray-500">
                <i class="fas fa-check-circle text-3xl mb-2 text-gray-300"></i>
                <p class="text-sm">Tidak ada notifikasi</p>
            </div>
            @endforelse
        </div>

        <div class="px-4 py-2 border-t text-center">
            <button @click="showAllModal = true; open = false"
                    class="text-sm text-blue-600 hover:underline">
                Lihat semua notifikasi
            </button>
        </div>
    </div>

    <!-- MODAL SEMUA NOTIFIKASI (SEDERHANA UNTUK TEST) -->
    <div x-show="showAllModal"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50"
         @click.self="showAllModal = false">
        <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-xl shadow-xl flex flex-col">
            <div class="p-4 border-b flex justify-between">
                <h2 class="text-lg font-bold">Semua Notifikasi</h2>
                <button @click="showAllModal = false">✕</button>
            </div>
            <div class="flex-1 overflow-y-auto p-4">
                @forelse($displayNotifications as $notif)
                <div class="border-b py-2">
                    <p class="font-medium">{{ $notif['title'] }}</p>
                    <p class="text-sm text-gray-600">{{ $notif['message'] }}</p>
                    <p class="text-xs text-gray-400">{{ $notif['time'] }}</p>
                </div>
                @empty
                <p class="text-center text-gray-500">Tidak ada notifikasi</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] {
    display: none !important;
}
</style>
