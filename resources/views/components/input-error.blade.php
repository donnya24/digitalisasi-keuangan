@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'input-error']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-center">
                <i class="fas fa-exclamation-circle mr-1 text-xs"></i>
                {{ $message }}
            </li>
        @endforeach
    </ul>
@endif