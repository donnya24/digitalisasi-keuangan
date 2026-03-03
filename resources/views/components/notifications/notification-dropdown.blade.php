@props(['notifications' => [], 'unread' => 0])

<div class="relative" x-data="{ open: false, showAllModal: false }">
    <!-- Bell Icon -->
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-blue-600 focus:outline-none">
        <i class="fas fa-bell text-lg sm:text-xl"></i>
        @if($unread > 0)
            <span class="absolute top-0 right-0 w-3 h-3 sm:w-4 sm:h-4 bg-red-500 text-white text-[10px] sm:text-xs rounded-full flex items-center justify-center">
                {{ $unread }}
            </span>
        @endif
    </button>

    <!-- Dropdown Notifikasi -->
    <div x-show="open" 
         @click.away="open = false" 
         class="absolute right-0 mt-2 w-64 sm:w-80 bg-white rounded-lg shadow-xl py-2 z-50 border border-gray-200"
         x-cloak>
        
        <!-- Header Dropdown -->
        <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold text-sm sm:text-base text-gray-800">Notifikasi</h3>
            @if($unread > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 hover:underline">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>
        
        <!-- Daftar Notifikasi -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notif)
                <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0">
                    <div class="flex items-start gap-2">
                        <div class="flex-shrink-0 mt-0.5">
                            <i class="fas fa-{{ $notif['icon'] ?? 'bell' }} {{ $notif['text_color'] ?? 'text-gray-600' }} text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium {{ $notif['text_color'] ?? 'text-gray-800' }}">
                                {{ $notif['title'] ?? 'Notifikasi' }}
                                @if(!($notif['is_read'] ?? true))
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        Baru
                                    </span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $notif['message'] ?? '' }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notif['time'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-check-circle text-2xl sm:text-3xl mb-2 text-gray-400"></i>
                    <p class="text-xs sm:text-sm">Tidak ada notifikasi</p>
                </div>
            @endforelse
        </div>
        
        <!-- Footer Dropdown -->
        <div class="px-4 py-2 border-t border-gray-100 text-center">
            <button @click="showAllModal = true; open = false" 
                    class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 hover:underline w-full py-1">
                Lihat semua notifikasi
                <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>
    </div>

    <!-- MODAL LIHAT SEMUA NOTIFIKASI -->
<!-- MODAL LIHAT SEMUA NOTIFIKASI -->
<div x-show="showAllModal" 
     class="fixed inset-0 z-[100] overflow-y-auto" 
     x-cloak>
    
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
         @click="showAllModal = false"></div>

    <!-- Modal Panel - Centered -->
    <div class="min-h-screen px-3 sm:px-6 flex items-center justify-center">
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] sm:max-h-[85vh] flex flex-col my-6 sm:my-8"
             @click.stop>
            
            <!-- Header Modal - Fixed -->
            <div class="flex justify-between items-center p-4 sm:p-6 border-b border-gray-200 bg-white rounded-t-xl flex-shrink-0">
                <div>
                    <h2 class="text-base sm:text-xl font-bold text-gray-900">Semua Notifikasi</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5 sm:mt-1">
                        {{ $unread }} notifikasi belum dibaca
                    </p>
                </div>
                <button @click="showAllModal = false" 
                        class="text-gray-400 hover:text-gray-600 focus:outline-none p-1.5 sm:p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <i class="fas fa-times text-lg sm:text-xl"></i>
                </button>
            </div>

            <!-- Filter Tabs - Fixed -->
            <div class="px-4 sm:px-6 py-2 sm:py-3 border-b border-gray-200 bg-gray-50 flex-shrink-0">
                <div class="flex space-x-2 overflow-x-auto pb-1 no-scrollbar" x-data="{ filter: 'all' }">
                    <button @click="filter = 'all'" 
                            :class="filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                            class="px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm rounded-lg transition-colors whitespace-nowrap flex-shrink-0">
                        Semua
                    </button>
                    <button @click="filter = 'unread'" 
                            :class="filter === 'unread' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                            class="px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm rounded-lg transition-colors whitespace-nowrap flex-shrink-0">
                        Belum Dibaca
                    </button>
                    <button @click="filter = 'read'" 
                            :class="filter === 'read' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                            class="px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm rounded-lg transition-colors whitespace-nowrap flex-shrink-0">
                        Sudah Dibaca
                    </button>
                </div>
            </div>

            <!-- Content Modal - Scrollable -->
            <div class="overflow-y-auto flex-1 px-4 sm:px-6 py-3 sm:py-4" 
                 x-data="{
                     notifications: [],
                     loading: true,
                     filter: 'all',
                     init() {
                         this.loadNotifications();
                     },
                     loadNotifications() {
                         this.loading = true;
                         fetch('{{ route('notifications.latest') }}?limit=50')
                             .then(response => response.json())
                             .then(data => {
                                 this.notifications = data.notifications;
                                 this.loading = false;
                             })
                             .catch(error => {
                                 console.error('Error loading notifications:', error);
                                 this.loading = false;
                             });
                     },
                     get filteredNotifications() {
                         if (this.filter === 'all') return this.notifications;
                         if (this.filter === 'unread') return this.notifications.filter(n => !n.is_read);
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
                             this.loadNotifications();
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
                                 this.loadNotifications();
                             });
                         }
                     }
                 }"
                 x-init="init()">
                
                <!-- Loading State -->
                <div x-show="loading" class="flex justify-center items-center py-8 sm:py-12">
                    <div class="animate-spin rounded-full h-6 w-6 sm:h-8 sm:w-8 border-b-2 border-blue-600"></div>
                </div>

                <!-- Daftar Notifikasi -->
                <template x-if="!loading">
                    <div class="space-y-2 sm:space-y-3">
                        <template x-for="notif in filteredNotifications" :key="notif.id">
                            <div class="p-3 sm:p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors"
                                 :class="{ 'bg-blue-50 border-blue-100': !notif.is_read }">
                                <div class="flex items-start gap-2 sm:gap-3">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center"
                                             :class="notif.bg_color || 'bg-gray-100'">
                                            <i :class="'fas fa-' + (notif.icon || 'bell') + ' text-sm sm:text-base ' + (notif.text_color || 'text-gray-600')"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-1 sm:gap-2">
                                            <div class="flex-1">
                                                <div class="flex items-center flex-wrap gap-1 sm:gap-2">
                                                    <p class="text-xs sm:text-sm font-medium" :class="notif.text_color || 'text-gray-900'">
                                                        <span x-text="notif.title"></span>
                                                    </p>
                                                    <span x-show="!notif.is_read" 
                                                          class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] sm:text-xs font-medium bg-blue-100 text-blue-800 whitespace-nowrap">
                                                        Baru
                                                    </span>
                                                </div>
                                                <p class="text-xs sm:text-sm text-gray-600 mt-1 line-clamp-2" x-text="notif.message"></p>
                                                <p class="text-xs text-gray-400 mt-1 sm:mt-2">
                                                    <i class="far fa-clock mr-1"></i>
                                                    <span x-text="notif.time"></span>
                                                </p>
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                                                <template x-if="!notif.is_read">
                                                    <button @click="markAsRead(notif.id)" 
                                                            class="text-blue-600 hover:text-blue-800 p-1.5 hover:bg-blue-50 rounded-full transition-colors"
                                                            title="Tandai sudah dibaca">
                                                        <i class="fas fa-check-circle text-sm sm:text-base"></i>
                                                    </button>
                                                </template>
                                                <button @click="deleteNotification(notif.id)" 
                                                        class="text-red-600 hover:text-red-800 p-1.5 hover:bg-red-50 rounded-full transition-colors"
                                                        title="Hapus">
                                                    <i class="fas fa-trash-alt text-sm sm:text-base"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Empty State -->
                        <div x-show="filteredNotifications.length === 0" 
                             class="text-center py-8 sm:py-12 px-4">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-3 sm:mb-4">
                                <i class="fas fa-bell-slash text-xl sm:text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-sm sm:text-base text-gray-500">Tidak ada notifikasi</p>
                            <p class="text-xs sm:text-sm text-gray-400 mt-1">Semua notifikasi sudah dibaca</p>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer Modal - Fixed dengan padding yang tepat -->
            <div class="p-4 sm:p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl flex-shrink-0">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4">
                    <div class="text-xs sm:text-sm text-gray-600 order-2 sm:order-1">
                        <span x-text="filteredNotifications.length"></span> notifikasi
                    </div>
                    <div class="flex gap-2 order-1 sm:order-2 w-full sm:w-auto">
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="flex-1 sm:flex-none">
                            @csrf
                            <button type="submit" 
                                    class="w-full sm:w-auto px-4 py-2.5 sm:px-5 sm:py-2.5 text-xs sm:text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-check-double mr-1"></i> Tandai Semua
                            </button>
                        </form>
                        <button @click="showAllModal = false" 
                                class="flex-1 sm:flex-none px-4 py-2.5 sm:px-5 sm:py-2.5 text-xs sm:text-sm bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    /* Pastikan modal muncul di atas semuanya */
    .z-\[100\] {
        z-index: 100;
    }
    
    /* Hilangkan scrollbar di tab filter */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    /* Custom scrollbar untuk konten */
    .overflow-y-auto::-webkit-scrollbar {
        width: 4px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
    
    /* Line clamp untuk pesan */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>