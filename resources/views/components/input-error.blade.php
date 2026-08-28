@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'flex items-start gap-1.5 text-xs text-red-600']) }}>
        <i class="fa-solid fa-triangle-exclamation text-sm mt-px"></i>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
