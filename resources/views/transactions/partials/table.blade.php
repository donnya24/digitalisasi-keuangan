<!-- Desktop Table View -->
<div class="hidden sm:block overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($transactions as $transaction)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full {{ $transaction->type == 'pemasukan' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="flex items-center">
                            @if($transaction->category && $transaction->category->icon)
                                <i class="fas fa-{{ $transaction->category->icon }} mr-2" style="color: {{ $transaction->category->color ?? '#6B7280' }}"></i>
                            @else
                                <i class="fas fa-tag mr-2 text-gray-400"></i>
                            @endif
                            <span>{{ $transaction->category->name ?? 'Tanpa Kategori' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                        {{ $transaction->description }}
                        @if($transaction->notes)
                            <i class="fas fa-paperclip text-gray-400 ml-1" title="{{ $transaction->notes }}"></i>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->type == 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $transaction->type == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $transaction->payment_method ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <!-- Tombol Detail -->
                            <a href="{{ route('transactions.show', $transaction->id) }}" 
                               class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 rounded-md hover:bg-blue-100 transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye text-sm"></i>
                                <span class="sr-only md:not-sr-only md:ml-1 text-xs">Detail</span>
                            </a>
                            
                            <!-- Tombol Edit -->
                            <a href="{{ route('transactions.edit', $transaction->id) }}" 
                               class="inline-flex items-center px-2 py-1 bg-yellow-50 text-yellow-600 rounded-md hover:bg-yellow-100 transition-colors duration-200"
                               title="Edit Transaksi">
                                <i class="fas fa-edit text-sm"></i>
                                <span class="sr-only md:not-sr-only md:ml-1 text-xs">Edit</span>
                            </a>
                            
                            <!-- DELETE BUTTON - Langsung dengan form -->
                            <form action="{{ route('transactions.destroy', $transaction->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirmDelete('transaksi', '{{ addslashes($transaction->description) }}', {{ $transaction->amount }})"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 rounded-md hover:bg-red-100 hover:text-red-700 transition-colors duration-200"
                                        title="Hapus Transaksi">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                    <span class="sr-only md:not-sr-only md:ml-1 text-xs">Hapus</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-exchange-alt text-4xl mb-3"></i>
                        <p class="text-sm">Belum ada transaksi</p>
                        <a href="{{ route('transactions.create') }}" 
                           class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                            <i class="fas fa-plus mr-1"></i> Tambah Transaksi
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Mobile Card View -->
<div class="block sm:hidden">
    @forelse($transactions as $transaction)
        <div class="bg-white rounded-lg shadow-sm mb-3 p-4 border border-gray-100">
            <!-- Header -->
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs rounded-full {{ $transaction->type == 'pemasukan' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($transaction->type) }}
                    </span>
                    <span class="text-xs text-gray-500">
                        <i class="far fa-calendar-alt mr-1"></i>
                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}
                    </span>
                </div>
            </div>
            
            <!-- Category -->
            <div class="flex items-center mb-3">
                @if($transaction->category && $transaction->category->icon)
                    <i class="fas fa-{{ $transaction->category->icon }} mr-2 text-sm" style="color: {{ $transaction->category->color ?? '#6B7280' }}"></i>
                @else
                    <i class="fas fa-tag mr-2 text-gray-400 text-sm"></i>
                @endif
                <span class="text-sm font-medium">{{ $transaction->category->name ?? 'Tanpa Kategori' }}</span>
            </div>
            
            <!-- Description -->
            <p class="text-sm text-gray-700 mb-3">{{ $transaction->description }}</p>
            
            @if($transaction->notes)
                <div class="mt-2 text-xs text-gray-500 bg-gray-50 p-2 rounded mb-3">
                    <i class="fas fa-sticky-note mr-1"></i> {{ $transaction->notes }}
                </div>
            @endif
            
            <!-- Footer -->
            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                <div class="flex items-center">
                    <span class="text-xs text-gray-500 mr-2">
                        <i class="fas fa-credit-card mr-1"></i>{{ $transaction->payment_method ?? 'Tunai' }}
                    </span>
                </div>
                <span class="text-base font-bold {{ $transaction->type == 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $transaction->type == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                </span>
            </div>
            
            <!-- MOBILE ACTION BUTTONS -->
            <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-100">
                <!-- Tombol Detail -->
                <a href="{{ route('transactions.show', $transaction->id) }}" 
                   class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-xs">
                    <i class="fas fa-eye mr-1"></i> Detail
                </a>
                
                <!-- Tombol Edit -->
                <a href="{{ route('transactions.edit', $transaction->id) }}" 
                   class="inline-flex items-center px-3 py-1.5 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors duration-200 text-xs">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                
                <!-- DELETE BUTTON - Langsung dengan form -->
                <form action="{{ route('transactions.destroy', $transaction->id) }}" 
                      method="POST" 
                      onsubmit="return confirmDelete('transaksi', '{{ addslashes($transaction->description) }}', {{ $transaction->amount }})"
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200 text-xs">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-exchange-alt text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Transaksi</h3>
            <p class="text-sm text-gray-500 mb-4">Mulai catat pemasukan dan pengeluaran usaha Anda</p>
            <a href="{{ route('transactions.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> Tambah Transaksi
            </a>
        </div>
    @endforelse
</div>