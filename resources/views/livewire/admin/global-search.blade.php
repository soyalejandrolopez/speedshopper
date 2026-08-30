<div x-data="{
    open: @entangle('isOpen'),
    init() {
        window.addEventListener('keydown', (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                this.open = true;
                $nextTick(() => this.$refs.modalSearchInput?.focus());
            }
            if (e.key === 'Escape' && this.open) {
                this.open = false;
            }
        });
    }
}" class="relative flex-1 max-w-xs sm:max-w-sm md:max-w-md mx-2 sm:mx-4">
    {{-- Trigger Input and Separated Buscar Button in Header --}}
    <div class="flex items-center gap-1.5 w-full">
        <button type="button"
                @click="open = true; $nextTick(() => $refs.modalSearchInput?.focus())"
                class="group flex flex-1 items-center justify-between gap-2 rounded-xl border border-gray-200/90 bg-gray-50/70 px-3.5 py-1.5 text-xs text-gray-400 transition-all duration-200 hover:border-gray-300 hover:bg-white hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 shadow-2xs">
            <span class="truncate">{{ __('Buscar clientes, órdenes, paquetes...') }}</span>
            <kbd class="hidden sm:inline-flex items-center gap-0.5 rounded-md border border-gray-200 bg-white px-1.5 py-0.5 font-mono text-[10px] font-semibold text-gray-400 shadow-2xs group-hover:text-gray-600">
                <span>⌘</span>K
            </kbd>
        </button>

        <button type="button"
                @click="open = true; $nextTick(() => $refs.modalSearchInput?.focus())"
                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-2xs hover:bg-emerald-700 active:scale-95 transition-all shrink-0">
            <i class="fa-solid fa-magnifying-glass text-xs"></i>
            <span class="hidden sm:inline">{{ __('Buscar') }}</span>
        </button>
    </div>

    {{-- Modal Backdrop & Command Palette --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/40 p-4 sm:p-6 md:p-20 backdrop-blur-sm">
        
        <div @click.outside="open = false"
             x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="mx-auto max-w-xl transform overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl transition-all">
            
            {{-- Search Input in Modal with Separate Button --}}
            <div class="relative flex items-center gap-2 border-b border-gray-100 px-4 py-1.5">
                <input x-ref="modalSearchInput"
                       type="text"
                       wire:model.live.debounce.250ms="query"
                       placeholder="{{ __('Escribe para buscar clientes, solicitudes, facturas, paquetes...') }}"
                       class="h-11 flex-1 border-0 bg-transparent px-2 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-0">
                
                <div class="flex items-center gap-2 shrink-0">
                    <span wire:loading wire:target="query" class="text-xs text-emerald-600">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                    </span>
                    <button type="button"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-2xs hover:bg-emerald-700 transition-all">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        <span>{{ __('Buscar') }}</span>
                    </button>
                    <button type="button" @click="open = false" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Cerrar') }}">
                        <kbd class="rounded bg-gray-100 px-1.5 py-0.5 font-mono text-[10px] text-gray-500">ESC</kbd>
                    </button>
                </div>
            </div>

            {{-- Results Container --}}
            <div class="max-h-96 overflow-y-auto p-2">
                @if (strlen(trim($query)) < 2)
                    {{-- Quick Navigation / Suggestions --}}
                    <div class="p-3 text-xs">
                        <p class="font-semibold uppercase tracking-wider text-gray-400 text-[10px] mb-2">{{ __('Acceso Rápido') }}</p>
                        <div class="grid grid-cols-2 gap-1.5">
                            <a href="{{ route('admin.billing.index') }}" @click="open = false" wire:navigate class="flex items-center gap-2 rounded-xl p-2 text-gray-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700"><i class="fa-solid fa-file-invoice-dollar text-xs"></i></span>
                                <span class="font-medium truncate">{{ __('Facturación') }}</span>
                            </a>
                            <a href="{{ route('admin.requests.index') }}" @click="open = false" wire:navigate class="flex items-center gap-2 rounded-xl p-2 text-gray-700 hover:bg-blue-50 hover:text-blue-800 transition-colors">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-blue-700"><i class="fa-solid fa-clipboard-list text-xs"></i></span>
                                <span class="font-medium truncate">{{ __('Solicitudes') }}</span>
                            </a>
                            <a href="{{ route('admin.customers.index') }}" @click="open = false" wire:navigate class="flex items-center gap-2 rounded-xl p-2 text-gray-700 hover:bg-purple-50 hover:text-purple-800 transition-colors">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-purple-100 text-purple-700"><i class="fa-solid fa-users text-xs"></i></span>
                                <span class="font-medium truncate">{{ __('Clientes') }}</span>
                            </a>
                            <a href="{{ route('admin.reports.index') }}" @click="open = false" wire:navigate class="flex items-center gap-2 rounded-xl p-2 text-gray-700 hover:bg-teal-50 hover:text-teal-800 transition-colors">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-teal-100 text-teal-700"><i class="fa-solid fa-chart-simple text-xs"></i></span>
                                <span class="font-medium truncate">{{ __('Reportes') }}</span>
                            </a>
                        </div>
                    </div>
                @elseif ($totalResults === 0)
                    <div class="py-10 text-center text-xs text-gray-500">
                        <i class="fa-solid fa-magnifying-glass text-2xl text-gray-300 mb-2"></i>
                        <p class="font-medium">{{ __('No se encontraron resultados para ":query"', ['query' => $query]) }}</p>
                    </div>
                @else
                    {{-- Customers --}}
                    @if ($results['customers']->isNotEmpty())
                        <div class="mb-2">
                            <p class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Clientes') }}</p>
                            @foreach ($results['customers'] as $c)
                                <a href="{{ route('admin.customers.show', $c) }}" @click="open = false" wire:navigate
                                   class="flex items-center justify-between rounded-xl px-3 py-2 text-xs text-gray-700 hover:bg-emerald-50 hover:text-emerald-900 transition-colors">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-100 font-bold text-[10px] text-emerald-800">{{ strtoupper(substr($c->name, 0, 1)) }}</span>
                                        <span class="font-medium truncate">{{ $c->name }}</span>
                                        <span class="font-mono text-[10.5px] text-gray-400">{{ $c->number }}</span>
                                    </div>
                                    @if ($c->whatsapp || $c->phone)
                                        <span class="text-[10px] text-gray-400">{{ $c->whatsapp ?? $c->phone }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Requests / Orders --}}
                    @if ($results['requests']->isNotEmpty())
                        <div class="mb-2">
                            <p class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Solicitudes y Facturas') }}</p>
                            @foreach ($results['requests'] as $r)
                                <a href="{{ route('admin.requests.show', $r) }}" @click="open = false" wire:navigate
                                   class="flex items-center justify-between rounded-xl px-3 py-2 text-xs text-gray-700 hover:bg-blue-50 hover:text-blue-900 transition-colors">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="font-mono font-bold text-blue-600">{{ $r->number }}</span>
                                        <span class="truncate">{{ $r->product_name }}</span>
                                    </div>
                                    <span class="text-[10px] text-gray-400">{{ $r->customer?->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Packages --}}
                    @if ($results['packages']->isNotEmpty())
                        <div class="mb-2">
                            <p class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Paquetes') }}</p>
                            @foreach ($results['packages'] as $pkg)
                                <a href="{{ route('admin.packages.show', $pkg) }}" @click="open = false" wire:navigate
                                   class="flex items-center justify-between rounded-xl px-3 py-2 text-xs text-gray-700 hover:bg-sky-50 hover:text-sky-900 transition-colors">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="font-mono font-bold text-sky-600">{{ $pkg->number }}</span>
                                        <span class="truncate">{{ $pkg->description ?: $pkg->tracking_number }}</span>
                                    </div>
                                    <span class="text-[10px] text-gray-400">{{ $pkg->customer?->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Shipments --}}
                    @if ($results['shipments']->isNotEmpty())
                        <div class="mb-2">
                            <p class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Envíos') }}</p>
                            @foreach ($results['shipments'] as $s)
                                <a href="{{ route('admin.shipments.show', $s) }}" @click="open = false" wire:navigate
                                   class="flex items-center justify-between rounded-xl px-3 py-2 text-xs text-gray-700 hover:bg-purple-50 hover:text-purple-900 transition-colors">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="font-mono font-bold text-purple-600">{{ $s->number }}</span>
                                        <span class="truncate">{{ $s->carrier }}</span>
                                    </div>
                                    <span class="text-[10px] text-gray-400">{{ $s->customer?->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Payments --}}
                    @if ($results['payments']->isNotEmpty())
                        <div class="mb-2">
                            <p class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Pagos') }}</p>
                            @foreach ($results['payments'] as $p)
                                <a href="{{ route('admin.payments.index') }}" @click="open = false" wire:navigate
                                   class="flex items-center justify-between rounded-xl px-3 py-2 text-xs text-gray-700 hover:bg-teal-50 hover:text-teal-900 transition-colors">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="font-mono font-bold text-teal-600">{{ $p->number }}</span>
                                        <span class="font-bold text-teal-700">${{ number_format($p->amount_paid, 2) }}</span>
                                    </div>
                                    <span class="text-[10px] text-gray-400">{{ $p->customer?->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
