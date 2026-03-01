@props([
    'title', 
    'value', 
    'change' => null, 
    'changeArrow' => 'up', 
    'changeColor' => 'green',
    'icon', 
    'iconBg' => 'green',
    'borderColor' => 'green',
    'note' => null
])

@php
$borderColors = [
    'green' => 'border-green-500',
    'red' => 'border-red-500',
    'blue' => 'border-blue-500',
    'purple' => 'border-purple-500',
    'yellow' => 'border-yellow-500',
];

$iconBgColors = [
    'green' => 'bg-green-100',
    'red' => 'bg-red-100',
    'blue' => 'bg-blue-100',
    'purple' => 'bg-purple-100',
    'yellow' => 'bg-yellow-100',
];

$iconColors = [
    'green' => 'text-green-600',
    'red' => 'text-red-600',
    'blue' => 'text-blue-600',
    'purple' => 'text-purple-600',
    'yellow' => 'text-yellow-600',
];

$changeTextColors = [
    'green' => 'text-green-500',
    'red' => 'text-red-500',
];
@endphp

<div class="bg-white rounded-lg sm:rounded-xl shadow-sm p-3 sm:p-6 card-hover border-l-4 {{ $borderColors[$borderColor] }}">
    <div class="flex justify-between items-start">
        <div class="min-w-0">
            <p class="text-xs sm:text-sm text-gray-500 mb-1 truncate">{{ $title }}</p>
            <h3 class="text-base sm:text-xl lg:text-2xl font-bold text-gray-800 truncate">Rp {{ number_format($value, 0, ',', '.') }}</h3>
            
            @if($change !== null)
                <p class="text-xs {{ $changeColor === 'green' ? 'text-green-500' : 'text-red-500' }} mt-1 sm:mt-2">
                    <i class="fas fa-arrow-{{ $changeArrow }} mr-1"></i>
                    {{ abs($change) }}%
                </p>
            @endif
            
            @if($note)
                <p class="text-xs text-gray-500 mt-1 sm:mt-2">{{ $note }}</p>
            @endif
        </div>
        <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 {{ $iconBgColors[$iconBg] }} rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-{{ $icon }} {{ $iconColors[$iconBg] }} text-sm sm:text-base lg:text-xl"></i>
        </div>
    </div>
</div>