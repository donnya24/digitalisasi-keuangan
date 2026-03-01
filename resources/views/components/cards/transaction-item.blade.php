@props(['transaction'])

<div class="flex items-center justify-between p-2 sm:p-3 hover:bg-gray-50 rounded-lg">
    <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center flex-shrink-0"
             style="background-color: {{ $transaction['type'] == 'pemasukan' ? '#10b98120' : '#ef444420' }}">
            <i class="fas fa-{{ $transaction['icon'] }} {{ $transaction['type'] == 'pemasukan' ? 'text-green-600' : 'text-red-600' }} text-xs sm:text-sm"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs sm:text-sm font-medium truncate">{{ $transaction['description'] }}</p>
            <p class="text-xs text-gray-500">{{ $transaction['time_ago'] }}</p>
        </div>
    </div>
    <span class="text-xs sm:text-sm font-semibold flex-shrink-0 {{ $transaction['type'] == 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
        {{ $transaction['type'] == 'pemasukan' ? '+' : '-' }}Rp {{ number_format($transaction['amount'], 0, ',', '.') }}
    </span>
</div>