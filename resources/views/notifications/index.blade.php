@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
<div class="space-y-6">
    <!-- Header dengan Statistik -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Semua Notifikasi</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Anda memiliki <span class="font-medium text-blue-600">{{ $unreadCount }}</span> notifikasi belum dibaca
                </p>
            </div>
            
            <div class="flex flex-wrap gap-2">
                <!-- Filter Buttons -->
                <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
                class="px-4 py-2 text-sm rounded-lg {{ request('filter') == 'all' || !request('filter') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Semua
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
                class="px-4 py-2 text-sm rounded-lg {{ request('filter') == 'unread' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Belum Dibaca
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'read']) }}" 
                class="px-4 py-2 text-sm rounded-lg {{ request('filter') == 'read' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Sudah Dibaca
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'archived']) }}" 
                class="px-4 py-2 text-sm rounded-lg {{ request('filter') == 'archived' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Diarsipkan
                </a>
            </div>
            
            <div class="flex gap-2">
                <!-- Action Buttons -->
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-check-double mr-1"></i> Tandai Semua Dibaca
                    </button>
                </form>
                
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                    
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-10">
                        <form action="{{ route('notifications.destroy-read') }}" method="POST" class="block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-trash-alt mr-2 text-red-500"></i> Hapus yang sudah dibaca
                            </button>
                        </form>
                        <form action="{{ route('notifications.destroy-all') }}" method="POST" class="block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="return confirm('Yakin ingin menghapus SEMUA notifikasi?')">
                                <i class="fas fa-trash-alt mr-2 text-red-500"></i> Hapus semua
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Notifikasi -->
    <div class="bg-white rounded-xl shadow-sm">
        @forelse($notifications as $notification)
            <div class="p-4 border-b last:border-0 hover:bg-gray-50 {{ !$notification->is_read ? 'bg-blue-50/30' : '' }}">
                <div class="flex items-start gap-4">
                    <!-- Icon -->
                    <div class="w-10 h-10 rounded-lg {{ $notification->bg_color }} flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-{{ $notification->icon }} {{ $notification->text_color }}"></i>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4 class="text-sm font-semibold {{ !$notification->is_read ? 'text-gray-900' : 'text-gray-600' }}">
                                    {{ $notification->title }}
                                    @if(!$notification->is_read)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Baru
                                        </span>
                                    @endif
                                </h4>
                                <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                <p class="text-xs text-gray-400 mt-2">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $notification->formatted_time }}
                                </p>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                @if(!$notification->is_read)
                                    <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-600 hover:text-blue-800" title="Tandai sudah dibaca">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" 
                                      onsubmit="return confirm('Hapus notifikasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-bell-slash text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak Ada Notifikasi</h3>
                <p class="text-sm text-gray-500">Belum ada notifikasi untuk ditampilkan</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div>
@endsection