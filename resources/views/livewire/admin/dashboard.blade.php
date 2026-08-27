<div>
    <x-slot name="header">{{ __('Dashboard') }}</x-slot>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <a href="{{ route('admin.customers.index') }}" wire:navigate class="stat-card group text-emerald-600 animate-fade-up">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">{{ __('Total Customers') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 transition-transform duration-200 group-hover:scale-110">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-gray-900">
                <span data-count="{{ $totalCustomers }}">{{ $totalCustomers }}</span>
            </p>
        </a>

        <a href="{{ route('admin.requests.index') }}" wire:navigate class="stat-card group text-amber-600 animate-fade-up" style="animation-delay: 60ms">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">{{ __('Open Requests') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600 transition-transform duration-200 group-hover:scale-110">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-gray-900">
                <span data-count="{{ $openRequests }}">{{ $openRequests }}</span>
            </p>
        </a>

        <a href="{{ route('admin.packages.index') }}" wire:navigate class="stat-card group text-blue-600 animate-fade-up" style="animation-delay: 120ms">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">{{ __('Packages received today') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition-transform duration-200 group-hover:scale-110">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-gray-900">
                <span data-count="{{ $packagesReceivedToday }}">{{ $packagesReceivedToday }}</span>
            </p>
        </a>

        <a href="{{ route('admin.payments.index') }}" wire:navigate class="stat-card group text-emerald-600 animate-fade-up" style="animation-delay: 180ms">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">{{ __('Total balance due') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 transition-transform duration-200 group-hover:scale-110">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-gray-900">
                <span data-count="{{ $totalBalanceDue }}" data-prefix="$">{{ money($totalBalanceDue) }}</span>
            </p>
        </a>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 mt-4">
        <a href="{{ route('admin.packages.index') }}" wire:navigate class="stat-card group text-teal-600 animate-fade-up" style="animation-delay: 220ms">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">{{ __('Stored Packages') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-50 text-teal-600 transition-transform duration-200 group-hover:scale-110">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-gray-900">
                <span data-count="{{ $storedPackages }}">{{ $storedPackages }}</span>
            </p>
        </a>
        <a href="{{ route('admin.shipments.index') }}" wire:navigate class="stat-card group text-purple-600 animate-fade-up" style="animation-delay: 260ms">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">{{ __('Shipments in transit') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-purple-50 text-purple-600 transition-transform duration-200 group-hover:scale-110">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-gray-900">
                <span data-count="{{ $shipmentsInTransit }}">{{ $shipmentsInTransit }}</span>
            </p>
        </a>
        <a href="{{ route('admin.packages.index') }}" wire:navigate class="stat-card group text-cyan-600 animate-fade-up" style="animation-delay: 300ms">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">{{ __('Packages ready to ship') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600 transition-transform duration-200 group-hover:scale-110">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-gray-900">
                <span data-count="{{ $readyShipments }}">{{ $readyShipments }}</span>
            </p>
        </a>
        <a href="{{ route('admin.requests.index') }}" wire:navigate class="stat-card group text-sky-600 animate-fade-up" style="animation-delay: 340ms">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">{{ __('Open Requests') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600 transition-transform duration-200 group-hover:scale-110">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                    </svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-gray-900">
                <span data-count="{{ $openRequests }}">{{ $openRequests }}</span>
            </p>
        </a>
    </div>

    <div class="grid gap-6 mt-6 lg:grid-cols-2">
        <div class="card animate-fade-up" style="animation-delay: 360ms">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Requests by status') }}</h2>
                <a href="{{ route('admin.requests.index') }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 transition-colors hover:text-emerald-800">
                    {{ __('View All') }}
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
            <div class="space-y-4 p-5">
                @forelse ($requestsByStatus as $label => $total)
                    @php
                        $pct = round(($total / $maxRequests) * 100);
                        $colors = ['bg-emerald-500', 'bg-amber-500', 'bg-emerald-500', 'bg-purple-500', 'bg-cyan-500', 'bg-red-500', 'bg-blue-500'];
                        $color = $colors[$loop->index % count($colors)];
                    @endphp
                    <a href="{{ route('admin.requests.index') }}" wire:navigate class="group block">
                        <div class="mb-1.5 flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700 group-hover:text-emerald-700">{{ $label }}</span>
                            <span class="font-semibold text-gray-900">{{ $total }}</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill {{ $color }}" style="width: 0%" data-bar="{{ $pct }}"></div>
                        </div>
                    </a>
                @empty
                    <li><x-empty-state :message="__('No records found.')" icon="inbox"  /></li>
                @endforelse
            </div>
        </div>

        <div class="card animate-fade-up" style="animation-delay: 420ms">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Shipments by carrier') }}</h2>
                <a href="{{ route('admin.shipments.index') }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 transition-colors hover:text-emerald-800">
                    {{ __('View All') }}
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
            <div class="space-y-4 p-5">
                @forelse ($carriers as $carrier => $total)
                    @php
                        $pct = round(($total / $maxCarrier) * 100);
                        $colors = ['bg-emerald-500', 'bg-emerald-500', 'bg-amber-500', 'bg-purple-500', 'bg-cyan-500'];
                        $color = $colors[$loop->index % count($colors)];
                    @endphp
                    <a href="{{ route('admin.shipments.index') }}" wire:navigate class="group block">
                        <div class="mb-1.5 flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700 group-hover:text-emerald-700">{{ $carrier }}</span>
                            <span class="font-semibold text-gray-900">{{ $total }}</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill {{ $color }}" style="width: 0%" data-bar="{{ $pct }}"></div>
                        </div>
                    </a>
                @empty
                    <li><x-empty-state :message="__('No records found.')" icon="ship"  /></li>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid gap-6 mt-6 lg:grid-cols-3">
        <div class="card animate-fade-up" style="animation-delay: 380ms">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Recent Requests') }}</h2>
                <a href="{{ route('admin.requests.index') }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 transition-colors hover:text-emerald-800">
                    {{ __('View All') }}
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse ($recentRequests as $request)
                    <li>
                        <a href="{{ route('admin.requests.show', $request) }}" wire:navigate class="group flex items-center justify-between gap-3 px-5 py-3 transition-colors hover:bg-emerald-50/40">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900 group-hover:text-emerald-700">{{ $request->number }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $request->customer->name }} · {{ $request->product_name }}</p>
                            </div>
                            <x-status-badge :status="$request->status" />
                        </a>
                    </li>
                @empty
                    <li><x-empty-state :message="__('No records found.')" icon="inbox"  /></li>
                @endforelse
            </ul>
        </div>

        <div class="card animate-fade-up" style="animation-delay: 440ms">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Recent Packages') }}</h2>
                <a href="{{ route('admin.packages.index') }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 transition-colors hover:text-emerald-800">
                    {{ __('View All') }}
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse ($recentPackages as $package)
                    <li>
                        <a href="{{ route('admin.packages.show', $package) }}" wire:navigate class="flex items-center justify-between gap-3 px-5 py-3 transition-colors hover:bg-emerald-50/40">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900">{{ $package->number }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $package->customer->name }} · {{ $package->store }}</p>
                            </div>
                            <span class="shrink-0 text-xs font-medium text-gray-500">{{ $package->weight_lb ? $package->weight_lb . ' lb' : '—' }}</span>
                        </a>
                    </li>
                @empty
                    <li><x-empty-state :message="__('No records found.')" icon="inbox"  /></li>
                @endforelse
            </ul>
        </div>

        <div class="card animate-fade-up" style="animation-delay: 500ms">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Recent Payments') }}</h2>
                <a href="{{ route('admin.payments.index') }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 transition-colors hover:text-emerald-800">
                    {{ __('View All') }}
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse ($recentPayments as $payment)
                    <li>
                        <a href="{{ route('admin.payments.index') }}" wire:navigate class="flex items-center justify-between gap-3 px-5 py-3 transition-colors hover:bg-emerald-50/40">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900">{{ $payment->customer->name }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $payment->number }}</p>
                            </div>
                            <span class="shrink-0 text-xs font-medium {{ $payment->balance_due > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                                {{ $payment->balance_due > 0 ? money($payment->balance_due) : '✓' }}
                            </span>
                        </a>
                    </li>
                @empty
                    <li><x-empty-state :message="__('No records found.')" icon="inbox"  /></li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
