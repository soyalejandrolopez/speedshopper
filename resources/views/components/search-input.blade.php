@props([
    'placeholder' => __('Search...'),
    'model' => 'search',
    'debounce' => '300ms',
    'buttonText' => __('Buscar'),
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-2 flex-1 max-w-md group']) }}
     x-data="{ query: @entangle($model) }">
    {{-- Clean Input with Clear Button --}}
    <div class="relative flex-1">
        <input type="search"
               wire:model.live.debounce.{{ $debounce }}="{{ $model }}"
               placeholder="{{ $placeholder }}"
               class="w-full rounded-xl border border-gray-200/90 bg-gray-50/70 py-2 px-3.5 pe-8 text-xs text-gray-900 placeholder:text-gray-400 transition-all duration-200 hover:border-gray-300 hover:bg-white focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/15 shadow-2xs">

        <button type="button"
                x-show="query && query.length > 0"
                x-cloak
                @click="query = ''; $wire.set('{{ $model }}', '')"
                class="absolute inset-y-0 end-0 flex items-center pe-2.5 text-gray-400 hover:text-gray-600 transition-colors"
                title="{{ __('Clear search') }}">
            <i class="fa-solid fa-circle-xmark text-xs"></i>
        </button>
    </div>

    {{-- Separate "Buscar" Button --}}
    <button type="button"
            wire:click="$refresh"
            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow-2xs hover:bg-emerald-700 active:scale-95 transition-all shrink-0 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
        <i wire:loading.remove wire:target="{{ $model }}" class="fa-solid fa-magnifying-glass text-xs"></i>
        <i wire:loading wire:target="{{ $model }}" class="fa-solid fa-circle-notch fa-spin text-xs"></i>
        <span>{{ $buttonText }}</span>
    </button>
</div>
