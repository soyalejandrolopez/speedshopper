@props([
    'placeholder' => __('Search...'),
    'model' => 'search',
    'debounce' => '300ms',
])

<div {{ $attributes->merge(['class' => 'relative flex-1 max-w-md group']) }}
     x-data="{ query: @entangle($model) }">
    {{-- Search Icon & Live Spinner --}}
    <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3.5 text-gray-400 group-focus-within:text-emerald-600 transition-colors">
        <i wire:loading.remove wire:target="{{ $model }}" class="fa-solid fa-magnifying-glass text-xs transition-transform duration-200 group-focus-within:scale-110"></i>
        <i wire:loading wire:target="{{ $model }}" class="fa-solid fa-circle-notch fa-spin text-xs text-emerald-600"></i>
    </div>

    {{-- Input --}}
    <input type="search"
           wire:model.live.debounce.{{ $debounce }}="{{ $model }}"
           placeholder="{{ $placeholder }}"
           class="w-full rounded-xl border border-gray-200/90 bg-gray-50/70 py-2.5 ps-9 pe-9 text-xs text-gray-900 placeholder:text-gray-400 transition-all duration-200 hover:border-gray-300 hover:bg-white focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/15 shadow-2xs">

    {{-- Clear Button --}}
    <button type="button"
            x-show="query && query.length > 0"
            x-cloak
            @click="query = ''; $wire.set('{{ $model }}', '')"
            class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-400 hover:text-gray-600 transition-colors"
            title="{{ __('Clear search') }}">
        <i class="fa-solid fa-circle-xmark text-xs"></i>
    </button>
</div>
