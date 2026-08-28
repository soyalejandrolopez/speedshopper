<div>
    <x-slot name="header">{{ $purchaseRequest->number }}</x-slot>

    <div class="mb-4 flex items-center justify-between no-print">
        <a href="{{ route('admin.requests.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            {{ __('Back') }}
        </a>
            <a href="{{ route('admin.requests.print', $purchaseRequest) }}" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:bg-gray-50 no-print">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
                </svg>
                {{ __('Print') }}
            </a>
    </div>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-gray-200 border-t-4 border-t-emerald-500 bg-white p-5 shadow-sm">
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
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        @foreach (service_options() as $key => $label)
                            @if (in_array($key, $purchaseRequest->services, true))
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    {{ $label }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                @endif

                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Customer') }}</dt>
                        <dd class="font-medium text-gray-900">
                            <a href="{{ route('admin.customers.show', $purchaseRequest->customer) }}" wire:navigate class="text-emerald-600 hover:underline">
                                {{ $purchaseRequest->customer?->name ?? __('Unknown') }}
                            </a>
                        </dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Store') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $purchaseRequest->store ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Size/Color') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $purchaseRequest->size_color ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Quantity') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $purchaseRequest->quantity }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Unit Price') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $purchaseRequest->unit_price !== null ? money($purchaseRequest->unit_price) : '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Discount Found') }}</dt>
                        <dd class="font-medium text-emerald-600">{{ $purchaseRequest->discount_found !== null ? money($purchaseRequest->discount_found) : '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2 sm:col-span-2">
                        <dt class="text-gray-500">{{ __('Description') }}</dt>
                        <dd class="text-start font-medium whitespace-pre-line text-gray-900">{{ $purchaseRequest->description ?? '—' }}</dd>
                    </div>
                    @if ($purchaseRequest->notes)
                        <div class="rounded-lg bg-gray-50 p-3 text-xs text-gray-600 sm:col-span-2">{{ $purchaseRequest->notes }}</div>
                    @endif
                </dl>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Costs') }}</h3>
                    <span class="text-sm font-semibold text-gray-900">{{ money($purchaseRequest->total_cost) }}</span>
                </div>
                <table class="table-base">
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($purchaseRequest->costItems as $cost)
                            <tr>
                                <td class="px-5 py-2.5">{{ $cost->type->label() }}</td>
                                <td class="px-5 py-2.5 text-xs text-gray-500">{{ $cost->description }}</td>
                                <td class="px-5 py-2.5 text-end font-medium">{{ money($cost->amount) }}</td>
                                <td class="px-5 py-2.5 text-end">
                                    <button wire:click="removeCost({{ $cost->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this record?') }}"
                                            class="text-gray-400 hover:text-red-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td><li><x-empty-state :message="__('No records found.')" icon="inbox" /></li></td></tr>
                        @endforelse
                    </tbody>
                </table>
                <form wire:submit="addCost" class="flex flex-col gap-2 border-t border-gray-200 p-4 sm:flex-row sm:items-start">
                    <select wire:model="costForm.type" class="rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach ($costTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    <input type="text" wire:model="costForm.description" placeholder="{{ __('Description') }}"
                           class="flex-1 rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <input type="number" step="0.01" min="0" wire:model="costForm.amount" placeholder="0.00"
                           class="w-32 rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <button type="submit" class="rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                        {{ __('Add Cost') }}
                    </button>
                    @error('costForm.amount') <p class="w-full text-xs text-red-600">{{ $message }}</p> @enderror
                </form>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Related Packages') }}</h3>
                <ul class="mt-3 divide-y divide-gray-100">
                    @forelse ($purchaseRequest->packages as $package)
                        <li class="flex items-center justify-between py-2.5 text-sm">
                            <span class="font-mono text-xs">{{ $package->number }}</span>
                            <x-status-badge :status="$package->status" />
                            <a href="{{ route('admin.packages.show', $package) }}" wire:navigate class="text-xs font-medium text-emerald-600">{{ __('Show') }}</a>
                        </li>
                    @empty
                        <li class="py-4 text-center text-sm text-gray-500">{{ __('No packages assigned.') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="space-y-6">
            @if ($purchaseRequest->status->nextStatuses())
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-5">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Change Status') }}</h3>
                    <form wire:submit="transitionStatus" class="mt-3 space-y-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Change To') }}</label>
                            <select wire:model="newStatus" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">—</option>
                                @foreach ($purchaseRequest->status->nextStatuses() as $next)
                                    <option value="{{ $next }}">{{ \App\Enums\RequestStatus::from($next)->label() }}</option>
                                @endforeach
                            </select>
                            @error('newStatus') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Transition Note') }}</label>
                            <textarea wire:model="transitionNote" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                            {{ __('Update Status') }}
                        </button>
                    </form>
                </div>
            @endif

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
                            <p class="text-xs text-gray-500">{{ $history->created_at?->diffForHumans() }}
                                @if ($history->user)
                                    · {{ $history->user->name }}
                                @endif
                            </p>
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
</div>
