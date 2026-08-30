<div>
    <x-slot name="header">{{ __('My Account') }}</x-slot>

    <div class="relative mb-6 overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-600 to-teal-500 p-6 text-white shadow-lg shadow-emerald-200 animate-fade-up sm:p-8">
        <div class="pointer-events-none absolute -end-16 -top-16 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-20 -start-10 h-40 w-40 rounded-full bg-teal-300/20 blur-2xl"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(255,255,255,0.12)_0%,transparent_55%)]"></div>

        <div class="relative flex flex-wrap items-center justify-between gap-6">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-emerald-50 backdrop-blur">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                    {{ __('Client Portal') }}
                </span>
                <h2 class="mt-3 text-2xl font-bold sm:text-3xl">{{ __('Hello') }}, {{ auth()->user()->name }}</h2>
                <p class="mt-1 text-sm text-emerald-100">{{ __('Track your requests, packages and shipments in real time.') }}</p>
            </div>

            <a href="{{ route('portal.requests.create') }}" wire:navigate
               class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-emerald-700 shadow-lg transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl">
                <i class="fa-solid fa-plus text-lg"></i>
                {{ __('New Request') }}
            </a>
        </div>
    </div>

    <div class="relative mb-6 overflow-hidden rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm animate-fade-up" style="animation-delay: 80ms">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ __('Outstanding Balance') }}</p>
                <p class="mt-1 text-3xl font-extrabold tracking-tight text-gray-900">
                    <span data-count="{{ $balanceDue }}" data-prefix="$">{{ money($balanceDue) }}</span>
                </p>
            </div>
            <div class="grid w-full grid-cols-3 gap-3 sm:w-auto sm:flex sm:flex-wrap sm:gap-6">
                <div class="rounded-xl bg-gray-50/80 p-3 sm:border-s sm:border-gray-100 sm:bg-transparent sm:ps-4 sm:p-0">
                    <p class="text-xs font-medium text-gray-500">{{ __('Open Requests') }}</p>
                    <p class="mt-0.5 text-xl font-bold text-gray-900"><span data-count="{{ $openRequestsCount }}">{{ $openRequestsCount }}</span></p>
                </div>
                <div class="rounded-xl bg-gray-50/80 p-3 sm:border-s sm:border-gray-100 sm:bg-transparent sm:ps-4 sm:p-0">
                    <p class="text-xs font-medium text-gray-500">{{ __('Packages') }}</p>
                    <p class="mt-0.5 text-xl font-bold text-gray-900"><span data-count="{{ $totalPackages }}">{{ $totalPackages }}</span></p>
                </div>
                <div class="rounded-xl bg-gray-50/80 p-3 sm:border-s sm:border-gray-100 sm:bg-transparent sm:ps-4 sm:p-0">
                    <p class="text-xs font-medium text-gray-500">{{ __('In Transit') }}</p>
                    <p class="mt-0.5 text-xl font-bold text-gray-900"><span data-count="{{ $inTransitCount }}">{{ $inTransitCount }}</span></p>
                </div>
            </div>
            <a href="{{ route('portal.payments.index') }}" wire:navigate
               class="btn-primary">
                <i class="fa-solid fa-credit-card text-base"></i>
                {{ __('Pay Balance') }}
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card animate-fade-up" style="animation-delay: 140ms">
            <div class="flex items-center justify-between border-b border-gray-100/60 px-5 py-3">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Your orders') }}</h3>
                <a href="{{ route('portal.requests.index') }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 transition-colors hover:text-emerald-900">{{ __('View All') }}
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </a>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse ($requests as $request)
                    <li class="group flex items-center justify-between gap-3 px-5 py-3.5 transition-colors hover:bg-gradient-to-r hover:from-emerald-50/50 hover:to-transparent">
                        <div class="min-w-0">
                            <p class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                                <span class="font-mono text-xs text-gray-400">{{ $request->number }}</span>
                                <span class="truncate">{{ $request->product_name }}</span>
                                @if ($request->unit_price !== null)
                                    <span class="shrink-0 text-xs font-medium text-gray-500">— {{ money($request->unit_price) }}</span>
                                @endif
                            </p>
                        </div>
                        <x-status-badge :status="$request->status" />
                    </li>
                @empty
                    <li>
                        <x-empty-state :message="__('No records found.')" icon="inbox">
                            <a href="{{ route('portal.requests.create') }}" wire:navigate class="btn-primary mt-4 px-4 py-2 text-sm">{{ __('Create a request') }}</a>
                        </x-empty-state>
                    </li>
                @endforelse
            </ul>
        </div>

        <div class="space-y-6">
            <div class="card animate-fade-up" style="animation-delay: 200ms">
                <div class="flex items-center justify-between border-b border-gray-100/60 px-5 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Your boxes') }}</h3>
                    <a href="{{ route('portal.shipments.index') }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 transition-colors hover:text-emerald-900">{{ __('View All') }}
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </a>
                </div>
                <ul class="divide-y divide-gray-100">
                    @forelse ($shipments as $shipment)
                        <li class="flex items-center justify-between gap-3 px-5 py-3.5 transition-colors hover:bg-gradient-to-r hover:from-emerald-50/50 hover:to-transparent">
                            <div class="min-w-0">
                                <p class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                        <i class="fa-solid fa-box text-lg"></i>
                                    </span>
                                    <span class="truncate font-mono text-xs">{{ $shipment->number }}</span>
                                </p>
                                <p class="mt-0.5 truncate text-xs text-gray-500">
                                    {{ $shipment->packages_count }} {{ __('Packages') }}
                                    @if ($shipment->final_weight_lb) · {{ $shipment->final_weight_lb }} lb @endif
                                    @if ($shipment->shipping_cost) · {{ __('Shipping') }} {{ money($shipment->shipping_cost) }} @endif
                                </p>
                                @if ($shipment->international_tracking)
                                    <button type="button" data-copy="{{ $shipment->international_tracking }}"
                                            data-title="{{ __('International Tracking') }}" data-copied="{{ __('Copied') }}"
                                            class="group/track mt-0.5 inline-flex items-center gap-1.5 font-mono text-xs text-gray-400 transition-colors hover:text-emerald-600">
                                        <i class="fa-solid fa-link text-sm"></i>
                                        {{ $shipment->international_tracking }}
                                    </button>
                                @endif
                            </div>
                            <x-status-badge :status="$shipment->status" />
                        </li>
                    @empty
                        <li><x-empty-state :message="__('No records found.')" icon="box" /></li>
                    @endforelse
                </ul>
            </div>

            @if ($packages->isNotEmpty())
                <div class="card animate-fade-up" style="animation-delay: 260ms">
                    <div class="flex items-center justify-between border-b border-gray-100/60 px-5 py-3">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Recent packages') }}</h3>
                        <a href="{{ route('portal.packages.index') }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 transition-colors hover:text-emerald-900">{{ __('View All') }}
                            <i class="fa-solid fa-chevron-right text-sm"></i>
                        </a>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @foreach ($packages as $package)
                            <li class="flex items-center justify-between gap-3 px-5 py-3 transition-colors hover:bg-gradient-to-r hover:from-emerald-50/50 hover:to-transparent">
                                <div class="flex min-w-0 items-center gap-3">
                                    @if ($package->photo_path)
                                        <img src="{{ asset('storage/' . $package->photo_path) }}" alt="{{ __('Photo of package') }}"
                                             data-lightbox="{{ asset('storage/' . $package->photo_path) }}"
                                             class="h-12 w-12 shrink-0 cursor-zoom-in rounded-lg border border-gray-200 object-cover">
                                    @else
                                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-gray-50 text-gray-300">
                                            <i class="fa-solid fa-box text-xl"></i>
                                        </span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="truncate font-mono text-xs text-gray-900">{{ $package->number }}</p>
                                        <p class="truncate text-xs text-gray-500">{{ $package->store ?? __('Package') }}</p>
                                    </div>
                                </div>
                                <x-status-badge :status="$package->status" />
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
