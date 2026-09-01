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
}" class="relative flex items-center">
    {{-- Mobile Search Icon Button (< md) --}}
    <button type="button"
            @click="open = true; $nextTick(() => $refs.modalSearchInput?.focus())"
            class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-gray-50/80 text-gray-500 transition-all hover:border-emerald-300 hover:bg-white hover:text-emerald-700 shadow-xs md:hidden"
            aria-label="{{ __('Search') }}"
            title="{{ __('Search') }} (⌘K)">
        <i class="fa-solid fa-magnifying-glass text-xs"></i>
    </button>

    {{-- Desktop Search Trigger (>= md) --}}
    <div class="hidden md:flex items-center gap-1.5 w-60 lg:w-72 xl:w-80">
        <button type="button"
                @click="open = true; $nextTick(() => $refs.modalSearchInput?.focus())"
                class="group flex flex-1 items-center justify-between gap-2 rounded-xl border border-gray-200/90 bg-gray-50/80 px-3.5 py-1.5 text-xs text-gray-400 transition-all duration-200 hover:border-emerald-300 hover:bg-white hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 shadow-xs">
            <span class="flex items-center gap-2 truncate">
                <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs group-hover:text-emerald-600 transition-colors"></i>
                <span class="truncate">{{ __('Search people, orders, packages...') }}</span>
            </span>
            <kbd class="hidden xl:inline-flex items-center gap-0.5 rounded-md border border-gray-200 bg-white px-1.5 py-0.5 font-mono text-[10px] font-semibold text-gray-400 shadow-2xs group-hover:text-gray-600">
                <span>⌘</span>K
            </kbd>
        </button>
    </div>

    {{-- Teleported Modal (Directly attached to <body> to avoid clipping by header backdrop-blur) --}}
    <template x-teleport="body">
        <div x-show="open"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] flex items-start justify-center overflow-y-auto bg-gray-950/60 p-3 sm:p-6 md:p-12 backdrop-blur-md">
            
            <div @click.outside="open = false"
                 x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 class="relative my-4 sm:my-8 w-full max-w-2xl transform overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl transition-all flex flex-col max-h-[85vh]">
                
                {{-- Search Header & Input --}}
                <div class="relative flex items-center gap-3 border-b border-gray-100 px-4 py-3 bg-white">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </span>

                    <input x-ref="modalSearchInput"
                           type="text"
                           wire:model.live.debounce.250ms="query"
                           placeholder="{{ __('Search people, customers, orders, packages, shipments, payments...') }}"
                           class="h-10 flex-1 border-0 bg-transparent px-1 text-sm sm:text-base text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-0">
                    
                    <div class="flex items-center gap-2 shrink-0">
                        <span wire:loading wire:target="query" class="text-xs text-emerald-600">
                            <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
                        </span>

                        @if (strlen(trim($query)) > 0)
                            <button type="button"
                                    wire:click="$set('query', '')"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
                                    title="{{ __('Clear search') }}">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        @endif

                        <button type="button"
                                @click="open = false"
                                class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-gray-50 px-2 py-1 text-[11px] font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                                title="{{ __('Close (Esc)') }}">
                            <kbd class="font-mono text-[10px]">ESC</kbd>
                        </button>
                    </div>
                </div>

                {{-- Category Filter Pills (When query is active) --}}
                @if (strlen(trim($query)) >= 2)
                    <div class="flex items-center gap-1.5 overflow-x-auto border-b border-gray-100 bg-gray-50/70 px-4 py-2 text-xs no-scrollbar">
                        <button type="button"
                                wire:click="setCategory('all')"
                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 font-medium transition-colors shrink-0 {{ $category === 'all' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200/80' }}">
                            <span>{{ __('All') }}</span>
                            <span class="rounded-full px-1.5 py-0.2 text-[10px] {{ $category === 'all' ? 'bg-emerald-700/80 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $totalResults }}</span>
                        </button>

                        <button type="button"
                                wire:click="setCategory('customers')"
                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 font-medium transition-colors shrink-0 {{ $category === 'customers' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200/80' }}">
                            <i class="fa-solid fa-users text-[10px]"></i>
                            <span>{{ __('Customers') }}</span>
                            <span class="rounded-full px-1.5 py-0.2 text-[10px] {{ $category === 'customers' ? 'bg-emerald-700/80 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $counts['customers'] }}</span>
                        </button>

                        <button type="button"
                                wire:click="setCategory('requests')"
                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 font-medium transition-colors shrink-0 {{ $category === 'requests' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200/80' }}">
                            <i class="fa-solid fa-clipboard-list text-[10px]"></i>
                            <span>{{ __('Orders / Requests') }}</span>
                            <span class="rounded-full px-1.5 py-0.2 text-[10px] {{ $category === 'requests' ? 'bg-emerald-700/80 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $counts['requests'] }}</span>
                        </button>

                        <button type="button"
                                wire:click="setCategory('packages')"
                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 font-medium transition-colors shrink-0 {{ $category === 'packages' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200/80' }}">
                            <i class="fa-solid fa-box text-[10px]"></i>
                            <span>{{ __('Packages') }}</span>
                            <span class="rounded-full px-1.5 py-0.2 text-[10px] {{ $category === 'packages' ? 'bg-emerald-700/80 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $counts['packages'] }}</span>
                        </button>

                        <button type="button"
                                wire:click="setCategory('shipments')"
                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 font-medium transition-colors shrink-0 {{ $category === 'shipments' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200/80' }}">
                            <i class="fa-solid fa-truck text-[10px]"></i>
                            <span>{{ __('Shipments') }}</span>
                            <span class="rounded-full px-1.5 py-0.2 text-[10px] {{ $category === 'shipments' ? 'bg-emerald-700/80 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $counts['shipments'] }}</span>
                        </button>

                        <button type="button"
                                wire:click="setCategory('payments')"
                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 font-medium transition-colors shrink-0 {{ $category === 'payments' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200/80' }}">
                            <i class="fa-solid fa-receipt text-[10px]"></i>
                            <span>{{ __('Payments / Invoices') }}</span>
                            <span class="rounded-full px-1.5 py-0.2 text-[10px] {{ $category === 'payments' ? 'bg-emerald-700/80 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $counts['payments'] }}</span>
                        </button>

                        <button type="button"
                                wire:click="setCategory('inquiries')"
                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 font-medium transition-colors shrink-0 {{ $category === 'inquiries' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200/80' }}">
                            <i class="fa-solid fa-envelope text-[10px]"></i>
                            <span>{{ __('Messages') }}</span>
                            <span class="rounded-full px-1.5 py-0.2 text-[10px] {{ $category === 'inquiries' ? 'bg-emerald-700/80 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $counts['inquiries'] }}</span>
                        </button>
                    </div>
                @endif

                {{-- Results Body --}}
                <div class="overflow-y-auto p-3 sm:p-4 divide-y divide-gray-100 space-y-3">
                    @if (strlen(trim($query)) < 2)
                        {{-- Quick Access Grid --}}
                        <div>
                            <p class="px-2 pb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">{{ __('Quick Access') }}</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                <a href="{{ route('admin.requests.index') }}" @click="open = false" wire:navigate class="group flex items-center gap-3 rounded-xl border border-gray-100 p-2.5 hover:border-blue-200 hover:bg-blue-50/50 transition-all">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-700 group-hover:scale-105 transition-transform"><i class="fa-solid fa-clipboard-list text-xs"></i></span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 group-hover:text-blue-900 truncate">{{ __('Purchase Requests') }}</p>
                                        <p class="text-[10px] text-gray-400 truncate">{{ __('Orders & quotes') }}</p>
                                    </div>
                                </a>
                                <a href="{{ route('admin.billing.index') }}" @click="open = false" wire:navigate class="group flex items-center gap-3 rounded-xl border border-gray-100 p-2.5 hover:border-emerald-200 hover:bg-emerald-50/50 transition-all">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 group-hover:scale-105 transition-transform"><i class="fa-solid fa-file-invoice-dollar text-xs"></i></span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 group-hover:text-emerald-900 truncate">{{ __('Billing & Invoices') }}</p>
                                        <p class="text-[10px] text-gray-400 truncate">{{ __('Rates & payments') }}</p>
                                    </div>
                                </a>
                                <a href="{{ route('admin.customers.index') }}" @click="open = false" wire:navigate class="group flex items-center gap-3 rounded-xl border border-gray-100 p-2.5 hover:border-purple-200 hover:bg-purple-50/50 transition-all">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 text-purple-700 group-hover:scale-105 transition-transform"><i class="fa-solid fa-users text-xs"></i></span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 group-hover:text-purple-900 truncate">{{ __('Customers') }}</p>
                                        <p class="text-[10px] text-gray-400 truncate">{{ __('Clients & balance') }}</p>
                                    </div>
                                </a>
                                <a href="{{ route('admin.packages.index') }}" @click="open = false" wire:navigate class="group flex items-center gap-3 rounded-xl border border-gray-100 p-2.5 hover:border-amber-200 hover:bg-amber-50/50 transition-all">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-700 group-hover:scale-105 transition-transform"><i class="fa-solid fa-box text-xs"></i></span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 group-hover:text-amber-900 truncate">{{ __('Warehouse Packages') }}</p>
                                        <p class="text-[10px] text-gray-400 truncate">{{ __('Received & tracking') }}</p>
                                    </div>
                                </a>
                                <a href="{{ route('admin.shipments.index') }}" @click="open = false" wire:navigate class="group flex items-center gap-3 rounded-xl border border-gray-100 p-2.5 hover:border-sky-200 hover:bg-sky-50/50 transition-all">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-100 text-sky-700 group-hover:scale-105 transition-transform"><i class="fa-solid fa-truck text-xs"></i></span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 group-hover:text-sky-900 truncate">{{ __('Shipments') }}</p>
                                        <p class="text-[10px] text-gray-400 truncate">{{ __('International delivery') }}</p>
                                    </div>
                                </a>
                                <a href="{{ route('admin.reports.index') }}" @click="open = false" wire:navigate class="group flex items-center gap-3 rounded-xl border border-gray-100 p-2.5 hover:border-teal-200 hover:bg-teal-50/50 transition-all">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-100 text-teal-700 group-hover:scale-105 transition-transform"><i class="fa-solid fa-chart-simple text-xs"></i></span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 group-hover:text-teal-900 truncate">{{ __('Reports & Analytics') }}</p>
                                        <p class="text-[10px] text-gray-400 truncate">{{ __('Financial insights') }}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @elseif ($totalResults === 0)
                        <div class="py-12 text-center text-gray-500">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 mb-3">
                                <i class="fa-solid fa-magnifying-glass text-2xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-gray-800">{{ __('No results found') }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ __('No records match ":query". Try searching by name, code, phone, or tracking number.', ['query' => $query]) }}</p>
                        </div>
                    @else
                        {{-- Customers / People --}}
                        @if ($results['customers']->isNotEmpty())
                            <div class="pt-2 first:pt-0">
                                <div class="flex items-center justify-between px-2 pb-1.5">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-1.5">
                                        <i class="fa-solid fa-users text-xs"></i>
                                        <span>{{ __('Customers & People') }}</span>
                                    </p>
                                    <span class="text-[10px] text-gray-400">{{ $results['customers']->count() }} {{ __('found') }}</span>
                                </div>
                                <div class="space-y-1">
                                    @foreach ($results['customers'] as $c)
                                        <a href="{{ route('admin.customers.show', $c) }}" @click="open = false" wire:navigate
                                           class="group flex items-center justify-between rounded-xl px-3 py-2 text-xs text-gray-700 hover:bg-emerald-50/80 hover:text-emerald-950 transition-all border border-transparent hover:border-emerald-200">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-teal-600 font-bold text-xs text-white shadow-xs">
                                                    {{ strtoupper(substr($c->name, 0, 1)) }}
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-gray-900 group-hover:text-emerald-900 truncate">{{ $c->name }}</p>
                                                    <p class="text-[11px] text-gray-400 truncate">
                                                        <span class="font-mono text-emerald-600 font-medium">{{ $c->number }}</span>
                                                        @if ($c->email) • {{ $c->email }} @endif
                                                        @if ($c->whatsapp || $c->phone) • {{ $c->whatsapp ?: $c->phone }} @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-emerald-600 transition-transform group-hover:translate-x-0.5"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Purchase Requests / Orders --}}
                        @if ($results['requests']->isNotEmpty())
                            <div class="pt-2 first:pt-0">
                                <div class="flex items-center justify-between px-2 pb-1.5">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-blue-700 flex items-center gap-1.5">
                                        <i class="fa-solid fa-clipboard-list text-xs"></i>
                                        <span>{{ __('Orders & Purchase Requests') }}</span>
                                    </p>
                                    <span class="text-[10px] text-gray-400">{{ $results['requests']->count() }} {{ __('found') }}</span>
                                </div>
                                <div class="space-y-1">
                                    @foreach ($results['requests'] as $r)
                                        <a href="{{ route('admin.requests.show', $r) }}" @click="open = false" wire:navigate
                                           class="group flex items-center justify-between rounded-xl px-3 py-2 text-xs text-gray-700 hover:bg-blue-50/80 hover:text-blue-950 transition-all border border-transparent hover:border-blue-200">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-700 font-mono text-[10px] font-bold">
                                                    <i class="fa-solid fa-cart-shopping"></i>
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-gray-900 group-hover:text-blue-900 truncate">
                                                        <span class="font-mono text-blue-600">{{ $r->number }}</span>
                                                        <span class="text-gray-400 font-normal">|</span>
                                                        {{ $r->product_name }}
                                                    </p>
                                                    <p class="text-[11px] text-gray-400 truncate">
                                                        {{ $r->customer?->name }}
                                                        @if ($r->store) • <span class="text-gray-600">{{ $r->store }}</span> @endif
                                                        @if ($r->unit_price) • <span class="font-medium text-emerald-600">${{ number_format($r->unit_price, 2) }}</span> @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <x-status-badge :status="$r->status" />
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Packages --}}
                        @if ($results['packages']->isNotEmpty())
                            <div class="pt-2 first:pt-0">
                                <div class="flex items-center justify-between px-2 pb-1.5">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-amber-700 flex items-center gap-1.5">
                                        <i class="fa-solid fa-box text-xs"></i>
                                        <span>{{ __('Warehouse Packages') }}</span>
                                    </p>
                                    <span class="text-[10px] text-gray-400">{{ $results['packages']->count() }} {{ __('found') }}</span>
                                </div>
                                <div class="space-y-1">
                                    @foreach ($results['packages'] as $pkg)
                                        <a href="{{ route('admin.packages.show', $pkg) }}" @click="open = false" wire:navigate
                                           class="group flex items-center justify-between rounded-xl px-3 py-2 text-xs text-gray-700 hover:bg-amber-50/80 hover:text-amber-950 transition-all border border-transparent hover:border-amber-200">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700 font-mono text-[10px] font-bold">
                                                    <i class="fa-solid fa-box-open"></i>
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-gray-900 group-hover:text-amber-900 truncate">
                                                        <span class="font-mono text-amber-700">{{ $pkg->number }}</span>
                                                        @if ($pkg->original_tracking)
                                                            <span class="text-gray-400 font-normal">|</span>
                                                            <span class="font-mono text-[11px] text-gray-600">{{ $pkg->original_tracking }}</span>
                                                        @endif
                                                    </p>
                                                    <p class="text-[11px] text-gray-400 truncate">
                                                        {{ $pkg->customer?->name }}
                                                        @if ($pkg->store) • {{ $pkg->store }} @endif
                                                        @if ($pkg->weight_lb) • {{ $pkg->weight_lb }} lbs @endif
                                                        @if ($pkg->location) • {{ __('Loc:') }} {{ $pkg->location }} @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <x-status-badge :status="$pkg->status" />
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Shipments --}}
                        @if ($results['shipments']->isNotEmpty())
                            <div class="pt-2 first:pt-0">
                                <div class="flex items-center justify-between px-2 pb-1.5">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-sky-700 flex items-center gap-1.5">
                                        <i class="fa-solid fa-truck text-xs"></i>
                                        <span>{{ __('Shipments & Deliveries') }}</span>
                                    </p>
                                    <span class="text-[10px] text-gray-400">{{ $results['shipments']->count() }} {{ __('found') }}</span>
                                </div>
                                <div class="space-y-1">
                                    @foreach ($results['shipments'] as $s)
                                        <a href="{{ route('admin.shipments.show', $s) }}" @click="open = false" wire:navigate
                                           class="group flex items-center justify-between rounded-xl px-3 py-2 text-xs text-gray-700 hover:bg-sky-50/80 hover:text-sky-950 transition-all border border-transparent hover:border-sky-200">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-700 font-mono text-[10px] font-bold">
                                                    <i class="fa-solid fa-plane-departure"></i>
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-gray-900 group-hover:text-sky-900 truncate">
                                                        <span class="font-mono text-sky-700">{{ $s->number }}</span>
                                                        @if ($s->carrier) • {{ $s->carrier }} @endif
                                                        @if ($s->tracking_number) • <span class="font-mono text-gray-600">{{ $s->tracking_number }}</span> @endif
                                                    </p>
                                                    <p class="text-[11px] text-gray-400 truncate">
                                                        {{ $s->customer?->name }}
                                                        @if ($s->destination_city) • {{ $s->destination_city }}, {{ $s->destination_country }} @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <x-status-badge :status="$s->status" />
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Payments / Invoices --}}
                        @if ($results['payments']->isNotEmpty())
                            <div class="pt-2 first:pt-0">
                                <div class="flex items-center justify-between px-2 pb-1.5">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-teal-700 flex items-center gap-1.5">
                                        <i class="fa-solid fa-receipt text-xs"></i>
                                        <span>{{ __('Payments & Transactions') }}</span>
                                    </p>
                                    <span class="text-[10px] text-gray-400">{{ $results['payments']->count() }} {{ __('found') }}</span>
                                </div>
                                <div class="space-y-1">
                                    @foreach ($results['payments'] as $p)
                                        <a href="{{ route('admin.payments.index') }}" @click="open = false" wire:navigate
                                           class="group flex items-center justify-between rounded-xl px-3 py-2 text-xs text-gray-700 hover:bg-teal-50/80 hover:text-teal-950 transition-all border border-transparent hover:border-teal-200">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-teal-100 text-teal-700 font-mono text-[10px] font-bold">
                                                    <i class="fa-solid fa-file-invoice-dollar"></i>
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-gray-900 group-hover:text-teal-900 truncate">
                                                        <span class="font-mono text-teal-700">{{ $p->number }}</span>
                                                        <span class="text-gray-400 font-normal">|</span>
                                                        <span class="font-bold text-emerald-600">${{ number_format($p->amount_paid, 2) }}</span>
                                                    </p>
                                                    <p class="text-[11px] text-gray-400 truncate">
                                                        {{ $p->customer?->name }}
                                                        @if ($p->payment_method) • {{ $p->payment_method->label() }} @endif
                                                        @if ($p->reference) • <span class="font-mono">{{ $p->reference }}</span> @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-teal-600 transition-transform group-hover:translate-x-0.5"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Contact Inquiries / Messages --}}
                        @if ($results['inquiries']->isNotEmpty())
                            <div class="pt-2 first:pt-0">
                                <div class="flex items-center justify-between px-2 pb-1.5">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-700 flex items-center gap-1.5">
                                        <i class="fa-solid fa-envelope text-xs"></i>
                                        <span>{{ __('Contact Messages & Inquiries') }}</span>
                                    </p>
                                    <span class="text-[10px] text-gray-400">{{ $results['inquiries']->count() }} {{ __('found') }}</span>
                                </div>
                                <div class="space-y-1">
                                    @foreach ($results['inquiries'] as $inq)
                                        <a href="{{ route('admin.inquiries.index') }}" @click="open = false" wire:navigate
                                           class="group flex items-center justify-between rounded-xl px-3 py-2 text-xs text-gray-700 hover:bg-indigo-50/80 hover:text-indigo-950 transition-all border border-transparent hover:border-indigo-200">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 font-mono text-[10px] font-bold">
                                                    <i class="fa-solid fa-comment-dots"></i>
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-gray-900 group-hover:text-indigo-900 truncate">
                                                        {{ $inq->name }}
                                                        @if ($inq->subject) • <span class="text-gray-600">{{ $inq->subject }}</span> @endif
                                                    </p>
                                                    <p class="text-[11px] text-gray-400 truncate">
                                                        {{ $inq->email }}
                                                        @if ($inq->phone) • {{ $inq->phone }} @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-medium {{ $inq->isUnread() ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $inq->isUnread() ? __('Unread') : __('Read') }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Command Bar Footer --}}
                <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/80 px-4 py-2.5 text-[11px] text-gray-500">
                    <div class="flex items-center gap-3">
                        <span class="hidden sm:inline-flex items-center gap-1">
                            <kbd class="rounded border border-gray-200 bg-white px-1.5 py-0.5 font-mono text-[10px] shadow-2xs">ESC</kbd>
                            <span>{{ __('to close') }}</span>
                        </span>
                        <span class="hidden sm:inline-flex items-center gap-1">
                            <kbd class="rounded border border-gray-200 bg-white px-1.5 py-0.5 font-mono text-[10px] shadow-2xs">⌘K</kbd>
                            <span>{{ __('shortcut') }}</span>
                        </span>
                    </div>

                    <div class="flex items-center gap-2 text-[11px] text-emerald-700 font-medium">
                        <i class="fa-solid fa-bolt text-xs"></i>
                        <span>{{ __('SpeedShopper Global Search') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

