<div>
    <x-slot name="header">{{ __('My Shipments') }}</x-slot>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="hidden md:block overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Number') }}</th>
                        <th class="px-4 py-3">{{ __('Packages') }}</th>
                        <th class="px-4 py-3">{{ __('Carrier') }}</th>
                        <th class="px-4 py-3">{{ __('Destination Country') }}</th>
                        <th class="px-4 py-3">{{ __('Final Weight (lb)') }}</th>
                        <th class="px-4 py-3">{{ __('Shipping Cost') }}</th>
                        <th class="px-4 py-3">{{ __('International Tracking') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($shipments as $shipment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">{{ $shipment->number }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 font-medium text-gray-700">{{ $shipment->packages_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $shipment->carrier ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs">{{ country_name($shipment->destination_country) }}</td>
                            <td class="px-4 py-3 text-xs">{{ $shipment->final_weight_lb ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs">{{ $shipment->shipping_cost !== null ? money($shipment->shipping_cost) : '—' }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $shipment->international_tracking ?? '—' }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$shipment->status" /></td>
                        </tr>
                    @empty
                        <tr>
    <td colspan="99">
        <x-empty-state :message="__('No records found.')" icon="ship" />
    </td>
</tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <ul class="divide-y divide-gray-100 md:hidden">
            @forelse ($shipments as $shipment)
                <li class="px-4 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-mono text-xs text-gray-900">{{ $shipment->number }}</p>
                            <p class="mt-0.5 truncate text-xs text-gray-500">{{ $shipment->carrier ?? __('Shipment') }} · {{ country_name($shipment->destination_country) }}</p>
                        </div>
                        <x-status-badge :status="$shipment->status" />
                    </div>
                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 font-medium text-gray-700">{{ $shipment->packages_count }} {{ __('packages') }}</span>
                        @if ($shipment->final_weight_lb)
                            <span>{{ $shipment->final_weight_lb }} lb</span>
                        @endif
                        @if ($shipment->international_tracking)
                            <button type="button" data-copy="{{ $shipment->international_tracking }}"
                                    data-title="{{ __('International Tracking') }}" data-copied="{{ __('Copied') }}"
                                    class="group/track inline-flex items-center gap-1 font-mono text-gray-400 transition-colors hover:text-emerald-600">
                                <i class="fa-solid fa-link text-xs"></i>
                                {{ $shipment->international_tracking }}
                            </button>
                        @endif
                        <span class="ms-auto font-semibold text-gray-900">{{ $shipment->shipping_cost !== null ? money($shipment->shipping_cost) : '—' }}</span>
                    </div>
                </li>
            @empty
                <li>
                    <x-empty-state :message="__('No records found.')" icon="ship" />
                </li>
            @endforelse
        </ul>

        <div class="border-t border-gray-200 p-4">
            {{ $shipments->links() }}
        </div>
    </div>
</div>
