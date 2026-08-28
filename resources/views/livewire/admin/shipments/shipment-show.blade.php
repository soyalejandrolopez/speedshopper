<div>
    <x-slot name="header">{{ $shipment->number }}</x-slot>

    <div class="mb-4 flex items-center justify-between no-print">
        <a href="{{ route('admin.shipments.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-800">
            <i class="fa-solid fa-arrow-left text-base"></i>
            {{ __('Back') }}
        </a>
            <a href="{{ route('admin.shipments.print', $shipment) }}" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:bg-gray-50 no-print">
                <i class="fa-solid fa-print text-sm"></i>
                {{ __('Print') }}
            </a>
    </div>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-gray-200 border-t-4 border-t-emerald-500 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $shipment->carrier ?? __('Shipment') }}</h2>
                        @if ($shipment->international_tracking)
                            <button type="button" data-copy="{{ $shipment->international_tracking }}"
                                    data-title="{{ __('International Tracking') }}" data-copied="{{ __('Copied') }}"
                                    class="group/track mt-1 inline-flex items-center gap-1.5 font-mono text-xs text-gray-500 transition-colors hover:text-emerald-600">
                                {{ $shipment->international_tracking }}
                                <i class="fa-solid fa-copy text-sm opacity-0 transition-opacity group-hover/track:opacity-100"></i>
                            </button>
                        @endif
                    </div>
                    <x-status-badge :status="$shipment->status" />
                </div>

                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Customer') }}</dt>
                        <dd class="font-medium text-gray-900">
                            <a href="{{ route('admin.customers.show', $shipment->customer) }}" wire:navigate class="text-emerald-600 hover:underline">
                                {{ $shipment->customer?->name ?? __('Unknown') }}
                            </a>
                        </dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Destination Country') }}</dt>
                        <dd class="font-medium text-gray-900">{{ country_name($shipment->destination_country) }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Final Weight (lb)') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $shipment->final_weight_lb ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Dimensions') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $shipment->dimensions ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Shipped At') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $shipment->shipped_at?->format('Y-m-d') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Delivered At') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $shipment->delivered_at?->format('Y-m-d') ?? '—' }}</dd>
                    </div>
                </dl>

                @if ($shipment->notes)
                    <div class="mt-4 rounded-lg bg-gray-50 p-3 text-xs text-gray-600">{{ $shipment->notes }}</div>
                @endif
            </div>

            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Costs') }}</h3>
                    <span class="text-sm font-semibold text-gray-900">{{ money($shipment->total_cost) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($shipment->costItems as $cost)
                                <tr>
                                    <td class="px-5 py-2.5">{{ $cost->type->label() }}</td>
                                    <td class="px-5 py-2.5 text-xs text-gray-500">{{ $cost->description }}</td>
                                    <td class="px-5 py-2.5 text-end font-medium">{{ money($cost->amount) }}</td>
                                    <td class="px-5 py-2.5 text-end">
                                        <button wire:click="removeCost({{ $cost->id }})"
                                                wire:confirm="{{ __('Are you sure you want to delete this record?') }}"
                                                class="text-gray-400 hover:text-red-600">
                                            <i class="fa-solid fa-xmark text-base"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td><li><x-empty-state :message="__('No records found.')" icon="inbox" /></li></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <form wire:submit="addCost" class="flex flex-col gap-2 border-t border-gray-200 p-4 sm:flex-row sm:items-start">
                    <select wire:model="costForm.type" class="rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach ($costTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    <input type="text" wire:model="costForm.description" placeholder="{{ __('Description') }}"
                           class="flex-1 rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <input type="number" step="0.01" min="0" wire:model="costForm.amount" placeholder="0.00"
                           class="w-32 rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <button type="submit" class="rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                        {{ __('Add Cost') }}
                    </button>
                    @error('costForm.amount') <p class="w-full text-xs text-red-600">{{ $message }}</p> @enderror
                </form>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="border-b border-gray-200 px-5 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Packages in this shipment') }}</h3>
                </div>
                <ul class="divide-y divide-gray-100">
                    @forelse ($shipment->packages as $package)
                        <li class="flex items-center justify-between px-5 py-3 text-sm">
                            <span class="font-mono text-xs">{{ $package->number }}</span>
                            <span class="flex-1 px-4 text-xs text-gray-500">{{ $package->store }} · {{ $package->weight_lb }} lb</span>
                            <x-status-badge :status="$package->status" />
                            <a href="{{ route('admin.packages.show', $package) }}" wire:navigate class="ms-3 text-xs font-medium text-emerald-600">{{ __('Show') }}</a>
                        </li>
                    @empty
                        <li class="px-5 py-6 text-center text-sm text-gray-500">{{ __('No packages assigned.') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="space-y-6">
            @if ($shipment->status->nextStatuses())
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-5">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Change Status') }}</h3>
                    <form wire:submit="transitionStatus" class="mt-3 space-y-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Change To') }}</label>
                            <select wire:model="newStatus" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">—</option>
                                @foreach ($shipment->status->nextStatuses() as $next)
                                    <option value="{{ $next }}">{{ \App\Enums\ShipmentStatus::from($next)->label() }}</option>
                                @endforeach
                            </select>
                            @error('newStatus') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Transition Note') }}</label>
                            <textarea wire:model="transitionNote" rows="2" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
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
                    @forelse ($shipment->statusHistory as $history)
                        <li class="mb-4 ms-4">
                            @php
        $current = \App\Enums\ShipmentStatus::from($history->to);
        $dotColor = $current->color();
        $dotHex = match ($dotColor) {
            'gray' => '#6b7280', 'blue' => '#3b82f6', 'amber' => '#f59e0b',
            'indigo' => '#6366f1', 'purple' => '#a855f7', 'green' => '#10b981',
            'red' => '#ef4444', 'cyan' => '#06b6d4', default => '#6b7280',
        };
    @endphp
                        <span class="timeline-dot" style="background-color: {{ $dotHex }};"></span>
                            <p class="text-xs font-semibold text-gray-900">
                                {{ $history->from ? \App\Enums\ShipmentStatus::from($history->from)->label() . ' → ' : '' }}{{ \App\Enums\ShipmentStatus::from($history->to)->label() }}
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
