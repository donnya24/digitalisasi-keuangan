@props(['notifications' => [], 'unread' => 0])

<div class="relative" x-data="{ open: false, showAllModal: false }">
    <!-- Bell Icon -->
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-blue-600">
        <i class="fas fa-bell text-lg sm:text-xl"></i>
        @if($unread > 0)
            <span class="absolute top-0 right-0 w-3 h-3 sm:w-4 sm:h-4 bg-red-500 text-white text-[10px] sm:text-xs rounded-full flex items-center justify-center">
                {{ $unread }}
            </span>
        @endif
    </button>

    <!-- Dropdown Notifikasi -->
    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-64 sm:w-80 bg-white rounded-lg shadow-lg py-2 z-20" x-cloak>
        <div class="px-4 py-2 border-b flex justify-between items-center">
            <h3 class="font-semibold text-sm sm:text-base">Notifikasi</h3>
            @if($unread > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notif)
                <div class="px-4 py-3 hover:bg-gray-50 {{ $notif['bg_color'] ?? '' }} border-b last:border-0">
                    <div class="flex items-start gap-2">
                        <div class="flex-shrink-0">
                            <i class="fas fa-{{ $notif['icon'] }} {{ $notif['text_color'] ?? 'text-gray-600' }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium {{ $notif['text_color'] ?? 'text-gray-800' }}">
                                {{ $notif['title'] }}
                                @if(!($notif['is_read'] ?? true))
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        Baru
                                    </span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $notif['message'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notif['time'] }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-check-circle text-2xl sm:text-3xl mb-2"></i>
                    <p class="text-xs sm:text-sm">Tidak ada notifikasi</p>
                </div>
            @endforelse
        </div>
        
        <div class="px-4 py-2 border-t text-center">
            <button @click="showAllModal = true; open = false" class="text-xs sm:text-sm text-blue-600 hover:underline w-full">
                Lihat semua notifikasi
                <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>
    </div>

    <!-- MODAL LIHAT SEMUA NOTIFIKASI -->
    <div x-show="showAllModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
             @click="showAllModal = false"></div>

        <!-- Modal Panel -->
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden"
                 @click.stop>
                
                <!-- Header Modal -->
                <div class="flex justify-between items-center p-6 border-b">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Semua Notifikasi</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $unread }} notifikasi belum dibaca
                        </p>
                    </div>
                    <button @click="showAllModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Filter Tabs -->
                <div class="px-6 py-3 border-b bg-gray-50">
                    <div class="flex space-x-2" x-data="{ filter: 'all' }">
                        <button @click="filter = 'all'" 
                                :class="filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                                class="px-4 py-2 text-sm rounded-lg transition-colors">
                            Semua
                        </button>
                        <button @click="filter = 'unread'" 
                                :class="filter === 'unread' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                                class="px-4 py-2 text-sm rounded-lg transition-colors">
                            Belum Dibaca
                        </button>
                        <button @click="filter = 'read'" 
                                :class="filter === 'read' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                                class="px-4 py-2 text-sm rounded-lg transition-colors">
                            Sudah Dibaca
                        </button>
                    </div>
                </div>

                <!-- Content Modal dengan Loading -->
                <div class="overflow-y-auto" style="max-height: 400px;" 
                     x-data="{
                         notifications: [],
                         loading: true,
                         filter: 'all',
                         init() {
                             this.loadNotifications();
                         },
                         loadNotifications() {
                             this.loading = true;
                             fetch('{{ route("notifications.latest") }}?limit=50')
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
                     x-init="init()"
                     @filter-changed.window="filter = $event.detail">
                    
                    <!-- Loading State -->
                    <div x-show="loading" class="flex justify-center items-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>

                    <!-- Daftar Notifikasi -->
                    <template x-if="!loading">
                        <div>
                            <template x-for="notif in filteredNotifications" :key="notif.id">
                                <div class="p-4 border-b hover:bg-gray-50 transition-colors"
                                     :class="{ 'bg-blue-50': !notif.is_read }">
                                    <div class="flex items-start gap-3">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                                 :class="notif.bg_color || 'bg-gray-100'">
                                                <i :class="'fas fa-' + (notif.icon || 'bell') + ' ' + (notif.text_color || 'text-gray-600')"></i>
                                            </div>
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <div>
                                                    <p class="text-sm font-medium" :class="notif.text_color || 'text-gray-900'">
                                                        <span x-text="notif.title"></span>
                                                        <span x-show="!notif.is_read" 
                                                              class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                            Baru
                                                        </span>
                                                    </p>
                                                    <p class="text-sm text-gray-600 mt-1" x-text="notif.message"></p>
                                                    <p class="text-xs text-gray-400 mt-2">
                                                        <i class="far fa-clock mr-1"></i>
                                                        <span x-text="notif.time"></span>
                                                    </p>
                                                </div>
                                                
                                                <!-- Actions -->
                                                <div class="flex items-center gap-2">
                                                    <template x-if="!notif.is_read">
                                                        <button @click="markAsRead(notif.id)" 
                                                                class="text-xs text-blue-600 hover:text-blue-800"
                                                                title="Tandai sudah dibaca">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </template>
                                                    <button @click="deleteNotification(notif.id)" 
                                                            class="text-xs text-red-600 hover:text-red-800"
                                                            title="Hapus">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Empty State -->
                            <div x-show="filteredNotifications.length === 0" 
                                 class="text-center py-12">
                                <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-bell-slash text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500">Tidak ada notifikasi</p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer Modal -->
                <div class="p-4 border-t bg-gray-50 flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        <span x-text="filteredNotifications.length"></span> notifikasi ditampilkan
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-check-double mr-1"></i> Tandai Semua Dibaca
                            </button>
                        </form>
                        <button @click="showAllModal = false" 
                                class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>