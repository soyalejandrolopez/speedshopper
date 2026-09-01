<div class="space-y-4 sm:space-y-6 min-w-0 w-full">
    <x-slot name="header">{{ __('Dashboard') }}</x-slot>

    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4 min-w-0">
        <a href="{{ route('admin.customers.index') }}" wire:navigate class="stat-card group text-emerald-600 animate-fade-up min-w-0 !p-3.5 sm:!p-5">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">{{ __('Total Customers') }}</p>
                <span class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 transition-transform duration-200 group-hover:scale-110">
                    <i class="fa-solid fa-users text-base sm:text-xl"></i>
                </span>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-bold tracking-tight text-gray-900 truncate">
                <span data-count="{{ $totalCustomers }}">{{ $totalCustomers }}</span>
            </p>
        </a>

        <a href="{{ route('admin.requests.index', ['status' => 'open']) }}" wire:navigate class="stat-card group text-amber-600 animate-fade-up min-w-0 !p-3.5 sm:!p-5" style="animation-delay: 60ms">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">{{ __('Open Requests') }}</p>
                <span class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 transition-transform duration-200 group-hover:scale-110">
                    <i class="fa-solid fa-clipboard-list text-base sm:text-xl"></i>
                </span>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-bold tracking-tight text-gray-900 truncate">
                <span data-count="{{ $openRequests }}">{{ $openRequests }}</span>
            </p>
        </a>

        <a href="{{ route('admin.packages.index', ['filter' => 'today']) }}" wire:navigate class="stat-card group text-blue-600 animate-fade-up min-w-0 !p-3.5 sm:!p-5" style="animation-delay: 120ms">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">{{ __('Packages received today') }}</p>
                <span class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition-transform duration-200 group-hover:scale-110">
                    <i class="fa-solid fa-box text-base sm:text-xl"></i>
                </span>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-bold tracking-tight text-gray-900 truncate">
                <span data-count="{{ $packagesReceivedToday }}">{{ $packagesReceivedToday }}</span>
            </p>
        </a>

        <a href="{{ route('admin.payments.index') }}" wire:navigate class="stat-card group text-emerald-600 animate-fade-up min-w-0 !p-3.5 sm:!p-5" style="animation-delay: 180ms">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">{{ __('Balance') }}</p>
                <span class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 transition-transform duration-200 group-hover:scale-110">
                    <i class="fa-solid fa-money-bill text-base sm:text-xl"></i>
                </span>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-bold tracking-tight text-gray-900 truncate">
                <span data-count="{{ $totalBalanceDue }}" data-prefix="$">{{ money($totalBalanceDue) }}</span>
            </p>
        </a>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4 min-w-0">
        <a href="{{ route('admin.packages.index', ['filter' => 'stored']) }}" wire:navigate class="stat-card group text-teal-600 animate-fade-up min-w-0 !p-3.5 sm:!p-5" style="animation-delay: 220ms">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">{{ __('Stored Packages') }}</p>
                <span class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-teal-600 transition-transform duration-200 group-hover:scale-110">
                    <i class="fa-solid fa-box text-base sm:text-xl"></i>
                </span>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-bold tracking-tight text-gray-900 truncate">
                <span data-count="{{ $storedPackages }}">{{ $storedPackages }}</span>
            </p>
        </a>
        <a href="{{ route('admin.shipments.index', ['status' => 'in_transit']) }}" wire:navigate class="stat-card group text-purple-600 animate-fade-up min-w-0 !p-3.5 sm:!p-5" style="animation-delay: 260ms">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">{{ __('Shipments in transit') }}</p>
                <span class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-lg bg-purple-50 text-purple-600 transition-transform duration-200 group-hover:scale-110">
                    <i class="fa-solid fa-truck-fast text-base sm:text-xl"></i>
                </span>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-bold tracking-tight text-gray-900 truncate">
                <span data-count="{{ $shipmentsInTransit }}">{{ $shipmentsInTransit }}</span>
            </p>
        </a>
        <a href="{{ route('admin.packages.index', ['filter' => 'ready']) }}" wire:navigate class="stat-card group text-cyan-600 animate-fade-up min-w-0 !p-3.5 sm:!p-5" style="animation-delay: 300ms">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">{{ __('Packages ready to ship') }}</p>
                <span class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600 transition-transform duration-200 group-hover:scale-110">
                    <i class="fa-solid fa-shield-halved text-base sm:text-xl"></i>
                </span>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-bold tracking-tight text-gray-900 truncate">
                <span data-count="{{ $readyShipments }}">{{ $readyShipments }}</span>
            </p>
        </a>
        <a href="{{ route('admin.inquiries.index', ['status' => 'unread']) }}" wire:navigate class="stat-card group text-indigo-600 animate-fade-up min-w-0 !p-3.5 sm:!p-5" style="animation-delay: 340ms">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">{{ __('Mensajes de Contacto') }}</p>
                <span class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 transition-transform duration-200 group-hover:scale-110">
                    <i class="fa-solid fa-envelope text-base sm:text-xl"></i>
                </span>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-bold tracking-tight text-gray-900 flex items-center gap-2 truncate">
                <span data-count="{{ $unreadInquiriesCount }}">{{ $unreadInquiriesCount }}</span>
                @if ($unreadInquiriesCount > 0)
                    <span class="text-[10px] sm:text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full shrink-0">{{ __('sin leer') }}</span>
                @endif
            </p>
        </a>
    </div>

    <!-- Contact Inquiries Section in Dashboard -->
    <div class="card animate-fade-up min-w-0" style="animation-delay: 350ms">
        <div class="flex items-center justify-between gap-2 border-b border-gray-100/60 px-4 py-3.5 sm:px-5 sm:py-4">
            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                    <i class="fa-solid fa-envelope text-sm sm:text-base"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="text-sm font-bold text-gray-900 truncate">{{ __('Mensajes de Contacto Recientes') }}</h2>
                    <p class="text-xs text-gray-500 truncate hidden sm:block">{{ __('Consultas enviadas desde la página de contacto pública') }}</p>
                </div>
            </div>
            <a href="{{ route('admin.inquiries.index') }}" wire:navigate class="shrink-0 inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 transition-colors hover:text-emerald-800">
                <span>{{ __('Ver todos') }}</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/50 text-[11px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3">{{ __('Nombre') }}</th>
                        <th class="px-4 py-3">{{ __('Email') }}</th>
                        <th class="px-4 py-3">{{ __('Tel/WhatsApp') }}</th>
                        <th class="px-4 py-3">{{ __('País') }}</th>
                        <th class="px-4 py-3">{{ __('Mensaje') }}</th>
                        <th class="px-4 py-3">{{ __('Fecha') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Estado') }}</th>
                        <th class="px-5 py-3 text-end">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($recentInquiries as $inquiry)
                        <tr class="transition-colors hover:bg-gray-50/80 {{ $inquiry->isUnread() ? 'bg-emerald-50/20 font-medium' : '' }}">
                            <td class="px-5 py-3">
                                <span class="font-bold text-gray-900">{{ $inquiry->name }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <a href="mailto:{{ $inquiry->email }}" class="text-emerald-700 hover:underline">
                                    {{ $inquiry->email }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if ($inquiry->phone)
                                    @php $phoneRaw = preg_replace('/\D+/', '', $inquiry->phone); @endphp
                                    <a href="https://wa.me/{{ $phoneRaw }}" target="_blank" class="text-gray-600 hover:text-emerald-700 inline-flex items-center gap-1">
                                        <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                                        {{ $inquiry->phone }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if ($inquiry->country)
                                    <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[11px] font-medium text-gray-700">
                                        {{ country_name($inquiry->country) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs max-w-xs truncate text-gray-600" title="{{ $inquiry->message }}">
                                {{ $inquiry->message }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                {{ $inquiry->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if ($inquiry->status === 'unread')
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                                        {{ __('Nuevo') }}
                                    </span>
                                @elseif ($inquiry->status === 'contacted')
                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold text-blue-800">
                                        {{ __('Atendido') }}
                                    </span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600">
                                        {{ __('Leído') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-end whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if ($inquiry->isUnread())
                                        <button type="button" wire:click="markInquiryRead({{ $inquiry->id }})"
                                                class="rounded p-1 text-xs text-emerald-600 hover:bg-emerald-50"
                                                title="{{ __('Marcar como leído') }}">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    @endif
                                    @if ($inquiry->phone)
                                        @php $phoneRaw = preg_replace('/\D+/', '', $inquiry->phone); @endphp
                                        <a href="https://wa.me/{{ $phoneRaw }}?text={{ urlencode('Hola '.$inquiry->name.', te contactamos desde '.config('app.name').':') }}"
                                           target="_blank"
                                           class="rounded p-1 text-xs text-emerald-600 hover:bg-emerald-50"
                                           title="{{ __('WhatsApp') }}">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    @endif
                                    <button type="button"
                                            @click="swalConfirmDelete(() => $wire.deleteInquiry({{ $inquiry->id }}))"
                                            class="rounded p-1 text-xs text-gray-400 hover:bg-red-50 hover:text-red-600"
                                            title="{{ __('Eliminar') }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-xs text-gray-400">
                                {{ __('No hay mensajes de contacto recientes.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <ul class="divide-y divide-gray-100 md:hidden">
            @forelse ($recentInquiries as $inquiry)
                <li class="px-4 py-3.5 {{ $inquiry->isUnread() ? 'bg-emerald-50/20' : '' }}">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="truncate text-sm font-bold text-gray-900">{{ $inquiry->name }}</p>
                                @if ($inquiry->status === 'unread')
                                    <span class="shrink-0 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">{{ __('Nuevo') }}</span>
                                @elseif ($inquiry->status === 'contacted')
                                    <span class="shrink-0 rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold text-blue-800">{{ __('Atendido') }}</span>
                                @else
                                    <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600">{{ __('Leído') }}</span>
                                @endif
                            </div>
                            <p class="mt-0.5 truncate text-xs text-gray-500">{{ $inquiry->email }}</p>
                        </div>
                        <span class="shrink-0 text-xs text-gray-400">{{ $inquiry->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-1.5 line-clamp-2 text-xs text-gray-600">{{ $inquiry->message }}</p>
                    <div class="mt-2 flex items-center gap-1.5">
                        @if ($inquiry->isUnread())
                            <button type="button" wire:click="markInquiryRead({{ $inquiry->id }})"
                                    class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-1.5 text-xs font-medium text-emerald-700">
                                <i class="fa-solid fa-check text-sm"></i>
                                {{ __('Marcar leído') }}
                            </button>
                        @endif
                        @if ($inquiry->phone)
                            @php $phoneRaw = preg_replace('/\D+/', '', $inquiry->phone); @endphp
                            <a href="https://wa.me/{{ $phoneRaw }}?text={{ urlencode('Hola '.$inquiry->name.', te contactamos desde '.config('app.name').':') }}"
                               target="_blank"
                               class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-50">
                                <i class="fa-brands fa-whatsapp"></i>
                            </a>
                        @endif
                        <button type="button"
                                @click="swalConfirmDelete(() => $wire.deleteInquiry({{ $inquiry->id }}))"
                                class="ms-auto inline-flex h-7 w-7 items-center justify-center rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </li>
            @empty
                <li>
                    <x-empty-state :message="__('No hay mensajes de contacto recientes.')" icon="inbox" />
                </li>
            @endforelse
        </ul>
    </div>

    <div class="grid gap-4 sm:gap-6 mt-4 sm:mt-6 lg:grid-cols-2 min-w-0">
        <div class="card animate-fade-up min-w-0" style="animation-delay: 360ms">
            <div class="flex items-center justify-between gap-2 border-b border-gray-100/60 px-4 py-3.5 sm:px-5 sm:py-4">
                <h2 class="text-sm font-semibold text-gray-900 truncate">{{ __('Requests by status') }}</h2>
                <a href="{{ route('admin.requests.index') }}" wire:navigate class="shrink-0 inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 transition-colors hover:text-emerald-800">
                    <span>{{ __('View All') }}</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>
            <div class="space-y-4 p-4 sm:p-5">
                @forelse ($requestsByStatus as $label => $total)
                    @php
                        $pct = round(($total / $maxRequests) * 100);
                        $colors = ['bg-emerald-500', 'bg-amber-500', 'bg-emerald-500', 'bg-purple-500', 'bg-cyan-500', 'bg-red-500', 'bg-blue-500'];
                        $color = $colors[$loop->index % count($colors)];
                    @endphp
                    <a href="{{ route('admin.requests.index') }}" wire:navigate class="group block">
                        <div class="mb-1.5 flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700 group-hover:text-emerald-700 truncate pr-2">{{ $label }}</span>
                            <span class="font-semibold text-gray-900 shrink-0">{{ $total }}</span>
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

        <div class="card animate-fade-up min-w-0" style="animation-delay: 420ms">
            <div class="flex items-center justify-between gap-2 border-b border-gray-100/60 px-4 py-3.5 sm:px-5 sm:py-4">
                <h2 class="text-sm font-semibold text-gray-900 truncate">{{ __('Shipments by carrier') }}</h2>
                <a href="{{ route('admin.shipments.index') }}" wire:navigate class="shrink-0 inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 transition-colors hover:text-emerald-800">
                    <span>{{ __('View All') }}</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>
            <div class="space-y-4 p-4 sm:p-5">
                @forelse ($carriers as $carrier => $total)
                    @php
                        $pct = round(($total / $maxCarrier) * 100);
                        $colors = ['bg-emerald-500', 'bg-emerald-500', 'bg-amber-500', 'bg-purple-500', 'bg-cyan-500'];
                        $color = $colors[$loop->index % count($colors)];
                    @endphp
                    <a href="{{ route('admin.shipments.index') }}" wire:navigate class="group block">
                        <div class="mb-1.5 flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700 group-hover:text-emerald-700 truncate pr-2">{{ $carrier }}</span>
                            <span class="font-semibold text-gray-900 shrink-0">{{ $total }}</span>
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

    <div class="grid gap-4 sm:gap-6 mt-4 sm:mt-6 lg:grid-cols-3 min-w-0">
        <div class="card animate-fade-up min-w-0" style="animation-delay: 380ms">
            <div class="flex items-center justify-between gap-2 border-b border-gray-100/60 px-4 py-3.5 sm:px-5 sm:py-4">
                <h2 class="text-sm font-semibold text-gray-900 truncate">{{ __('Recent Requests') }}</h2>
                <a href="{{ route('admin.requests.index') }}" wire:navigate class="shrink-0 inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 transition-colors hover:text-emerald-800">
                    <span>{{ __('View All') }}</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse ($recentRequests as $request)
                    <li>
                        <a href="{{ route('admin.requests.show', $request) }}" wire:navigate class="group flex items-center justify-between gap-3 px-4 py-3 sm:px-5 transition-colors hover:bg-gradient-to-r hover:from-emerald-50/50 hover:to-transparent">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900 group-hover:text-emerald-700">{{ $request->number }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $request->customer?->name ?? __('Unknown') }} · {{ $request->product_name }}</p>
                            </div>
                            <x-status-badge :status="$request->status" />
                        </a>
                    </li>
                @empty
                    <li><x-empty-state :message="__('No records found.')" icon="inbox"  /></li>
                @endforelse
            </ul>
        </div>

        <div class="card animate-fade-up min-w-0" style="animation-delay: 440ms">
            <div class="flex items-center justify-between gap-2 border-b border-gray-100/60 px-4 py-3.5 sm:px-5 sm:py-4">
                <h2 class="text-sm font-semibold text-gray-900 truncate">{{ __('Recent Packages') }}</h2>
                <a href="{{ route('admin.packages.index') }}" wire:navigate class="shrink-0 inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 transition-colors hover:text-emerald-800">
                    <span>{{ __('View All') }}</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse ($recentPackages as $package)
                    <li>
                        <a href="{{ route('admin.packages.show', $package) }}" wire:navigate class="flex items-center justify-between gap-3 px-4 py-3 sm:px-5 transition-colors hover:bg-gradient-to-r hover:from-emerald-50/50 hover:to-transparent">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900">{{ $package->number }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $package->customer?->name ?? __('Unknown') }} · {{ $package->store }}</p>
                            </div>
                            <span class="shrink-0 text-xs font-medium text-gray-500">{{ $package->weight_lb ? $package->weight_lb . ' lb' : '—' }}</span>
                        </a>
                    </li>
                @empty
                    <li><x-empty-state :message="__('No records found.')" icon="inbox"  /></li>
                @endforelse
            </ul>
        </div>

        <div class="card animate-fade-up min-w-0" style="animation-delay: 500ms">
            <div class="flex items-center justify-between gap-2 border-b border-gray-100/60 px-4 py-3.5 sm:px-5 sm:py-4">
                <h2 class="text-sm font-semibold text-gray-900 truncate">{{ __('Recent Payments') }}</h2>
                <a href="{{ route('admin.payments.index') }}" wire:navigate class="shrink-0 inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 transition-colors hover:text-emerald-800">
                    <span>{{ __('View All') }}</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse ($recentPayments as $payment)
                    <li>
                        <a href="{{ route('admin.payments.index') }}" wire:navigate class="flex items-center justify-between gap-3 px-4 py-3 sm:px-5 transition-colors hover:bg-gradient-to-r hover:from-emerald-50/50 hover:to-transparent">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900">{{ $payment->customer?->name ?? __('Unknown') }}</p>
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
