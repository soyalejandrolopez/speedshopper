@if (session('success') || session('error'))
    <div x-data="{ show: false }" x-init="requestAnimationFrame(() => show = true); setTimeout(() => show = false, 4000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-8 opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-8 opacity-0"
         class="fixed top-20 right-4 z-[60] flex w-80 items-center gap-3 rounded-xl border p-4 shadow-2xl backdrop-blur-md {{ session('success') ? 'border-emerald-200 bg-emerald-50/95' : 'border-red-200 bg-red-50/95' }}">
        @if (session('success'))
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </span>
            <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
        @else
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </span>
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        @endif
        <button @click="show = false" class="ms-auto shrink-0 rounded-lg p-1 text-gray-400 transition-colors hover:bg-white/60 hover:text-gray-600">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif
