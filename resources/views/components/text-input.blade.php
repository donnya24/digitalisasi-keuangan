@props(['disabled' => false, 'type' => 'text', 'icon' => null])

<div class="relative">
    @if($icon)
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-{{ $icon }} text-gray-400"></i>
        </div>
    @endif
    
    <input 
        {{ $disabled ? 'disabled' : '' }} 
        {!! $attributes->merge(['class' => 'input-field ' . ($icon ? 'pl-10' : '')]) !!} 
        type="{{ $type }}"
    />
</div>