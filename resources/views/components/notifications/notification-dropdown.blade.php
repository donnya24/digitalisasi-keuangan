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

    <!-- Dropdown -->
    <div x-show="open"
         @click.away="open = false"
         x-cloak
         class="absolute right-0 mt-2 w-64 sm:w-80 bg-white rounded-lg shadow-xl py-2 z-50 border border-gray-200">

        <div class="px-4 py-2 border-b flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Notifikasi</h3>

            @if($unread > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button class="text-xs text-blue-600 hover:underline">
                    Tandai semua
                </button>
            </form>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notif)
            <div class="px-4 py-3 hover:bg-gray-50 border-b">
                <div class="flex gap-2">
                    <i class="fas fa-{{ $notif['icon'] ?? 'bell' }} text-sm"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium">
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
                Tidak ada notifikasi
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

    <!-- MODAL SEMUA NOTIFIKASI -->
    <div x-show="showAllModal"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50"
         @click.self="showAllModal = false">

        <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-xl shadow-xl flex flex-col"
             x-data="{
                 notifications: [],
                 loading: true,
                 filter: 'all',
                 
                 init() {
                     this.loadNotifications()
                 },
                 
                 loadNotifications() {
                     this.loading = true
                     fetch('{{ route('notifications.latest') }}?limit=50')
                         .then(res => res.json())
                         .then(data => {
                             this.notifications = data.notifications
                             this.loading = false
                         })
                         .catch(e => {
                             console.error(e)
                             this.loading = false
                         })
                 },
                 
                 get filteredNotifications() {
                     if (!this.notifications) return []
                     if (this.filter === 'all') return this.notifications
                     if (this.filter === 'unread') return this.notifications.filter(n => !n.is_read)
                     return this.notifications.filter(n => n.is_read)
                 },
                 
                 markAsRead(id) {
                     fetch(`/notifications/${id}/mark-read`, {
                         method: 'POST',
                         headers: {
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                             'Content-Type': 'application/json'
                         }
                     }).then(() => this.loadNotifications())
                 },
                 
                 deleteNotification(id) {
                     if (!confirm('Hapus notifikasi ini?')) return
                     fetch(`/notifications/${id}`, {
                         method: 'DELETE',
                         headers: {
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                             'Content-Type': 'application/json'
                         }
                     }).then(() => this.loadNotifications())
                 }
             }"
             x-init="init()"
             @click.away="showAllModal = false">

            <!-- HEADER -->
            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="font-bold text-lg">Semua Notifikasi</h2>
                <button @click="showAllModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- FILTER -->
            <div class="flex gap-2 p-3 border-b">
                <button @click="filter = 'all'"
                        :class="filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                        class="px-3 py-1 rounded text-sm transition-colors">
                    Semua
                </button>
                <button @click="filter = 'unread'"
                        :class="filter === 'unread' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                        class="px-3 py-1 rounded text-sm transition-colors">
                    Belum Dibaca
                </button>
                <button @click="filter = 'read'"
                        :class="filter === 'read' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                        class="px-3 py-1 rounded text-sm transition-colors">
                    Sudah Dibaca
                </button>
            </div>

            <!-- CONTENT -->
            <div class="flex-1 overflow-y-auto p-4">
                <!-- Loading State -->
                <div x-show="loading" class="text-center py-6">
                    <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <p class="text-sm text-gray-500 mt-2">Memuat notifikasi...</p>
                </div>

                <!-- Daftar Notifikasi -->
                <template x-if="!loading">
                    <div>
                        <template x-for="notif in filteredNotifications" :key="notif.id">
                            <div class="border rounded-lg p-3 mb-2"
                                 :class="{ 'bg-blue-50 border-blue-200': !notif.is_read, 'border-gray-200': notif.is_read }">
                                <div class="flex justify-between items-start gap-2">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="font-medium text-sm" x-text="notif.title"></p>
                                            <span x-show="!notif.is_read" 
                                                  class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">
                                                Baru
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1" x-text="notif.message"></p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            <i class="far fa-clock mr-1"></i>
                                            <span x-text="notif.time"></span>
                                        </p>
                                    </div>
                                    
                                    <div class="flex gap-1">
                                        <button x-show="!notif.is_read"
                                                @click="markAsRead(notif.id)"
                                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-full transition-colors"
                                                title="Tandai sudah dibaca">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                        <button @click="deleteNotification(notif.id)"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-full transition-colors"
                                                title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Empty State -->
                        <div x-show="filteredNotifications.length === 0" 
                             class="text-center text-gray-500 py-8">
                            <i class="fas fa-bell-slash text-3xl text-gray-300 mb-2"></i>
                            <p class="text-sm">Tidak ada notifikasi</p>
                        </div>
                    </div>
                </template>
            </div>

            <!-- FOOTER -->
            <div class="p-4 border-t flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    <span x-text="filteredNotifications.length"></span> notifikasi
                </div>
                <button @click="showAllModal = false"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] {
    display: none !important;
}
</style>