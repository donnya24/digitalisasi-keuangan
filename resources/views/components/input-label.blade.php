@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'input-label']) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-red-500 ml-1">*</span>
    @endif
</label>