@props(['active', 'href', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center space-x-3 px-4 py-3 bg-blue-700 rounded-xl text-white'
            : 'flex items-center space-x-3 px-4 py-3 text-blue-100 hover:bg-blue-700 rounded-xl transition';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    <i class="fas fa-{{ $icon }} w-5"></i>
    <span>{{ $slot }}</span>
</a>