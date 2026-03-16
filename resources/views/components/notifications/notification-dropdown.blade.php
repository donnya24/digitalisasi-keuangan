@props(['notifications' => [], 'unread' => 0])

@php
    $displayNotifications = $shared_notifications ?? $notifications ?? [];
    $displayUnread = $shared_unread_count ?? $unread ?? 0;

    // Debug ke log
    \Log::info('NOTIFICATION DEBUG', [
        'shared_exists' => isset($shared_notifications),
        'shared_count' => count($shared_notifications ?? []),
        'props_count' => count($notifications),
        'display_count' => count($displayNotifications),
        'display_unread' => $displayUnread,
        'path' => request()->path(),
        'user_id' => Auth::id()
    ]);
@endphp

<div class="relative"
     x-data="{
         open: false,
         showAllModal: false,
         localNotifications: @js($displayNotifications),
         localUnreadCount: {{ $displayUnread }},

         init() {
             console.log('🚀 Component initialized', {
                 notifications: this.localNotifications,
                 unreadCount: this.localUnreadCount
             });

             // Test fetch langsung
             this.testFetch();
         },

         testFetch() {
             fetch('{{ route("notifications.latest") }}')
                 .then(res => res.json())
                 .then(data => {
                     console.log('📦 Fetch test response:', data);

                     // Update jika data kosong
                     if (data.notifications && data.notifications.length > 0) {
                         this.localNotifications = data.notifications;
                         this.localUnreadCount = data.unread_count;
                     }
                 })
                 .catch(err => console.error('❌ Fetch test error:', err));
         }
     }"
     x-init="init()">

    <!-- DEBUG VISUAL - Tampilkan status -->
    <div class="absolute top-0 right-0 text-xs bg-yellow-100 p-1 rounded z-50" style="margin-top: 40px;">
        <div>🔔 Count: <span x-text="localUnreadCount"></span></div>
        <div>📋 Total: <span x-text="localNotifications.length"></span></div>
    </div>

    <!-- Bell Icon -->
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-blue-600 focus:outline-none">
        <i class="fas fa-bell text-lg sm:text-xl"></i>
        <template x-if="localUnreadCount > 0">
            <span class="absolute top-0 right-0 w-3 h-3 sm:w-4 sm:h-4 bg-red-500 text-white text-[10px] sm:text-xs rounded-full flex items-center justify-center"
                  x-text="localUnreadCount">
            </span>
        </template>
    </button>

    <!-- Dropdown -->
    <div x-show="open"
         @click.away="open = false"
         x-cloak
         class="absolute right-0 mt-2 w-64 sm:w-80 bg-white rounded-lg shadow-xl py-2 z-50 border border-gray-200">

        <div class="px-4 py-2 border-b flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Notifikasi</h3>
        </div>

        <div class="max-h-96 overflow-y-auto">
            <template x-for="notif in localNotifications" :key="notif.id">
                <div class="px-4 py-3 hover:bg-gray-50 border-b">
                    <div class="flex gap-2">
                        <i class="fas fa-bell text-sm mt-1" :class="notif.is_read ? 'text-gray-400' : 'text-blue-600'"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium">
                                <span x-text="notif.title"></span>
                                <span x-show="!notif.is_read"
                                      class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">
                                    Baru
                                </span>
                            </p>
                            <p class="text-xs text-gray-500" x-text="notif.message"></p>
                            <p class="text-xs text-gray-400" x-text="notif.time"></p>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="localNotifications.length === 0"
                 class="text-center py-6 text-gray-500">
                <i class="fas fa-check-circle text-3xl mb-2 text-gray-300"></i>
                <p class="text-sm">Tidak ada notifikasi</p>
            </div>
        </div>

        <div class="px-4 py-2 border-t text-center">
            <button @click="showAllModal = true; open = false"
                    class="text-sm text-blue-600 hover:underline">
                Lihat semua notifikasi
            </button>
        </div>
    </div>

    <!-- MODAL (sederhanakan dulu) -->
    <div x-show="showAllModal"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50">
        <div class="bg-white w-full max-w-2xl p-4 rounded">
            <h2 class="font-bold text-lg">Semua Notifikasi</h2>
            <pre x-text="JSON.stringify(localNotifications, null, 2)"></pre>
            <button @click="showAllModal = false">Tutup</button>
        </div>
    </div>
</div>
