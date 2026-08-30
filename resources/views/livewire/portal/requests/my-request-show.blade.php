<div>
    <x-slot name="header">{{ $purchaseRequest->number }}</x-slot>

    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('portal.requests.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-800">
            <i class="fa-solid fa-arrow-left text-base"></i>
            {{ __('Back') }}
        </a>

        <a href="{{ route('requests.print', $purchaseRequest) }}" target="_blank"
           class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50 px-3.5 py-1.5 text-xs font-bold text-emerald-700 shadow-2xs transition-all hover:bg-emerald-100 hover:shadow-xs active:scale-95">
            <i class="fa-solid fa-print text-sm"></i>
            <span>{{ __('Imprimir Cotización con QR') }}</span>
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $purchaseRequest->product_name }}</h2>
                        @if ($purchaseRequest->product_url)
                            <a href="{{ $purchaseRequest->product_url }}" target="_blank" class="mt-1 block truncate text-sm text-emerald-600 hover:underline">
                                {{ $purchaseRequest->product_url }}
                            </a>
                        @endif
                    </div>
                    <x-status-badge :status="$purchaseRequest->status" />
                </div>

                @if (! empty($purchaseRequest->services))
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        @foreach (service_definitions() as $key => $svc)
                            @if (in_array($key, $purchaseRequest->services, true))
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    <i class="fa-solid {{ $svc['icon'] }} text-xs"></i>
                                    {{ $svc['title'] }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                @endif

                @php
                    $isPaid = ($purchaseRequest->total_cost > 0 && ($purchaseRequest->total_cost - (float) $purchaseRequest->costItems()->sum('amount')) <= 0 && $purchaseRequest->status === \App\Enums\RequestStatus::Purchased) || $purchaseRequest->status === \App\Enums\RequestStatus::Purchased;
                @endphp

                @if ($isPaid)
                    <div class="mt-4 rounded-xl border border-emerald-300 bg-emerald-50/90 p-3.5 shadow-2xs">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-white shrink-0 mt-0.5">
                                <i class="fa-solid fa-location-dot text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-emerald-900">{{ __('Dirección de Entrega y Almacén (USA)') }}</p>
                                <p class="text-sm font-black text-gray-900 mt-0.5 font-mono select-all">7835 Wood Hollow Dr Baytown Tx 77521</p>
                                <p class="text-[11px] text-emerald-700 mt-0.5">{{ __('Tus compras pagadas son recibidas y preparadas en nuestra dirección principal de almacén.') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Store') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $purchaseRequest->store ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Quantity') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $purchaseRequest->quantity }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Size/Color') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $purchaseRequest->size_color ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Unit Price') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $purchaseRequest->unit_price !== null ? money($purchaseRequest->unit_price) : '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2 sm:col-span-2">
                        <dt class="text-gray-500">{{ __('Description') }}</dt>
                        <dd class="text-end font-medium text-gray-900">{{ $purchaseRequest->description ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Costs') }}</h3>
                    <span class="text-sm font-semibold text-gray-900">{{ money($purchaseRequest->total_cost) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-start text-sm text-gray-700">
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($purchaseRequest->costItems as $cost)
                                <tr>
                                    <td class="px-5 py-2.5">{{ $cost->type->label() }}</td>
                                    <td class="px-5 py-2.5 text-xs text-gray-500">{{ $cost->description }}</td>
                                    <td class="px-5 py-2.5 text-end font-medium">{{ money($cost->amount) }}</td>
                                </tr>
                            @empty
                                <tr><td><li><x-empty-state :message="__('No records found.')" icon="inbox" /></li></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-5 py-3">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Status History') }}</h3>
            </div>
            <ol class="relative m-4 border-s border-gray-200">
                @forelse ($purchaseRequest->statusHistory as $history)
                    <li class="mb-4 ms-4">
                        @php
        $current = \App\Enums\RequestStatus::from($history->to);
        $dotColor = $current->color();
        $dotHex = match ($dotColor) {
            'gray' => '#6b7280', 'blue' => '#3b82f6', 'amber' => '#f59e0b',
            'indigo' => '#6366f1', 'purple' => '#a855f7', 'green' => '#10b981',
            'red' => '#ef4444', 'cyan' => '#06b6d4', default => '#6b7280',
        };
    @endphp
                        <span class="timeline-dot" style="background-color: {{ $dotHex }};"></span>
                        <p class="text-xs font-semibold text-gray-900">
                            {{ $history->from ? \App\Enums\RequestStatus::from($history->from)->label() . ' → ' : '' }}{{ \App\Enums\RequestStatus::from($history->to)->label() }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $history->created_at?->diffForHumans() }}</p>
                        @if ($history->note)
                            <p class="mt-1 text-xs text-gray-600">{{ $history->note }}</p>
                        @endif
                    </li>
                @empty
                    <li><x-empty-state :message="__('No records found.')" icon="inbox" /></li>
                @endforelse
            </ol>
        </div>
    </div>
</div>
