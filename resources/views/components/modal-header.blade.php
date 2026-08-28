@props(['title'])

<div class="flex items-center justify-between border-b border-gray-100/60 px-6 py-5">
    <h3 class="text-lg font-bold tracking-tight text-gray-900">{{ $title }}</h3>
    <button type="button" wire:click="closeForm" class="rounded-full p-2 text-gray-400 transition-colors hover:bg-gray-100/80 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
