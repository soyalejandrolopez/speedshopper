@props([
    'maxWidth' => 'max-w-2xl',
])

<div
    x-data="{ open: @entangle('showForm').live }"
    x-cloak
    x-show="open"
    x-effect="
        document.body.classList.toggle('overflow-hidden', open);
        if (open) $nextTick(() => $refs.panel?.querySelector('input:not([type=hidden]), select, textarea')?.focus());
    "
    @keydown.escape.window="open = false; $wire.closeForm()"
    class="fixed inset-0 z-50"
    role="dialog"
    aria-modal="true"
>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false; $wire.closeForm()"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-[2px]"
    ></div>

    <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
            <div
                x-ref="panel"
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                class="relative w-full max-w-[calc(100vw-1.5rem)] {{ $maxWidth }} max-h-[calc(100vh-1.5rem)] overflow-y-auto overscroll-contain rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 sm:max-h-[calc(100vh-4rem)]"
                style="scrollbar-width: thin; scrollbar-color: rgb(156 163 175 / 0.6) transparent;"
            >
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
