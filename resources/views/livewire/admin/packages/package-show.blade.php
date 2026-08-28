<div>
    <x-slot name="header">{{ $package->number }}</x-slot>

    <div class="mb-4 flex items-center justify-between no-print">
        <a href="{{ route('admin.packages.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-800">
            <i class="fa-solid fa-arrow-left text-base"></i>
            {{ __('Back') }}
        </a>
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:bg-gray-50 no-print">
                <i class="fa-solid fa-print text-sm"></i>
                {{ __('Print') }}
            </button>
    </div>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-gray-200 border-t-4 border-t-emerald-500 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $package->store ?? __('Package') }}</h2>
                        @if ($package->original_tracking)
                            <button type="button" data-copy="{{ $package->original_tracking }}"
                                    data-title="{{ __('Original Tracking') }}" data-copied="{{ __('Copied') }}"
                                    class="group/track mt-1 inline-flex items-center gap-1.5 font-mono text-xs text-gray-500 transition-colors hover:text-emerald-600">
                                {{ $package->original_tracking }}
                                <i class="fa-solid fa-copy text-sm opacity-0 transition-opacity group-hover/track:opacity-100"></i>
                            </button>
                        @endif
                    </div>
                    <x-status-badge :status="$package->status" />
                </div>

                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Customer') }}</dt>
                        <dd class="font-medium text-gray-900">
                            <a href="{{ route('admin.customers.show', $package->customer) }}" wire:navigate class="text-emerald-600 hover:underline">
                                {{ $package->customer?->name ?? __('Unknown') }}
                            </a>
                        </dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Received At') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $package->received_at?->format('Y-m-d') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Weight (lb)') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $package->weight_lb ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Location') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $package->location ?? '—' }}</dd>
                    </div>
                    @if ($package->purchaseRequest)
                        <div class="flex justify-between gap-2">
                            <dt class="text-gray-500">{{ __('Request') }}</dt>
                            <dd class="font-medium text-gray-900">
                                <a href="{{ route('admin.requests.show', $package->purchaseRequest) }}" wire:navigate class="text-emerald-600 hover:underline">
                                    {{ $package->purchaseRequest->number }}
                                </a>
                            </dd>
                        </div>
                    @endif
                </dl>

                @if ($package->notes)
                    <div class="mt-4 rounded-lg bg-gray-50 p-3 text-xs text-gray-600">{{ $package->notes }}</div>
                @endif

                @if ($package->photo_path)
                    <div class="mt-4">
                        <img src="{{ asset('storage/' . $package->photo_path) }}" alt="{{ __('Photo of package') }}"
                             data-lightbox="{{ asset('storage/' . $package->photo_path) }}"
                             class="max-h-80 cursor-zoom-in rounded-lg border border-gray-200 object-cover transition-transform duration-200 hover:scale-[1.01]">
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="border-b border-gray-200 px-5 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Shipments') }}</h3>
                </div>
                <ul class="divide-y divide-gray-100">
                    @forelse ($package->shipments as $shipment)
                        <li class="flex items-center justify-between px-5 py-3 text-sm">
                            <span class="font-mono text-xs">{{ $shipment->number }}</span>
                            <x-status-badge :status="$shipment->status" />
                            <a href="{{ route('admin.shipments.show', $shipment) }}" wire:navigate class="text-xs font-medium text-emerald-600">{{ __('Show') }}</a>
                        </li>
                    @empty
                        <li><x-empty-state :message="__('No records found.')" icon="box" /></li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="space-y-6">
            @if ($package->status->nextStatuses())
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-5">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Change Status') }}</h3>
                    <form wire:submit="transitionStatus" class="mt-3 space-y-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">{{ __('Change To') }}</label>
                            <select wire:model="newStatus" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">—</option>
                                @foreach ($package->status->nextStatuses() as $next)
                                    <option value="{{ $next }}">{{ \App\Enums\PackageStatus::from($next)->label() }}</option>
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
                    @forelse ($package->statusHistory as $history)
                        <li class="mb-4 ms-4">
                            @php
        $current = \App\Enums\PackageStatus::from($history->to);
        $dotColor = $current->color();
        $dotHex = match ($dotColor) {
            'gray' => '#6b7280', 'blue' => '#3b82f6', 'amber' => '#f59e0b',
            'indigo' => '#6366f1', 'purple' => '#a855f7', 'green' => '#10b981',
            'red' => '#ef4444', 'cyan' => '#06b6d4', default => '#6b7280',
        };
    @endphp
                        <span class="timeline-dot" style="background-color: {{ $dotHex }};"></span>
                            <p class="text-xs font-semibold text-gray-900">
                                {{ $history->from ? \App\Enums\PackageStatus::from($history->from)->label() . ' → ' : '' }}{{ \App\Enums\PackageStatus::from($history->to)->label() }}
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
