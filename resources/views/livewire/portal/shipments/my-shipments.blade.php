<div>
    <x-slot name="header">{{ __('My Shipments') }}</x-slot>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
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

        <div class="border-t border-gray-200 p-4">
            {{ $shipments->links() }}
        </div>
    </div>
</div>
