@props(['notifications' => [], 'unread' => 0])

<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-blue-600">
        <i class="fas fa-bell text-lg sm:text-xl"></i>
        @if($unread > 0)
            <span class="absolute top-0 right-0 w-3 h-3 sm:w-4 sm:h-4 bg-red-500 text-white text-[10px] sm:text-xs rounded-full flex items-center justify-center">
                {{ $unread }}
            </span>
        @endif
    </button>

    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-64 sm:w-80 bg-white rounded-lg shadow-lg py-2 z-20" x-cloak>
        <div class="px-4 py-2 border-b">
            <h3 class="font-semibold text-sm sm:text-base">Notifikasi</h3>
        </div>
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notif)
                <div class="px-4 py-3 hover:bg-gray-50 {{ $notif['bg_color'] ?? '' }}">
                    <p class="text-xs sm:text-sm font-medium {{ $notif['text_color'] ?? 'text-gray-800' }}">
                        <i class="fas fa-{{ $notif['icon'] }} mr-1"></i>
                        {{ $notif['title'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">{{ $notif['message'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $notif['time'] }}</p>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-check-circle text-2xl sm:text-3xl mb-2"></i>
                    <p class="text-xs sm:text-sm">Tidak ada notifikasi</p>
                </div>
            @endforelse
        </div>
        <div class="px-4 py-2 border-t text-center">
            <a href="#" class="text-xs sm:text-sm text-blue-600 hover:underline">Lihat semua</a>
        </div>
    </div>
</div>