<x-print-layout
    :doc-title="__('Receipt')"
    :doc-number="$shipment->number"
    :back-url="route('admin.shipments.show', $shipment)"
    :auto-print="true">

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ __('Bill To') }}</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $shipment->customer?->name ?? __('Unknown') }}</p>
            <p class="text-sm text-gray-500">{{ $shipment->customer?->email ?? '' }}</p>
            @if ($shipment->customer?->whatsapp)
                <p class="text-sm text-gray-500">WhatsApp: {{ $shipment->customer->whatsapp }}</p>
            @endif
            @if ($shipment->customer?->country)
                <p class="text-sm text-gray-500">{{ country_name($shipment->customer->country) }}</p>
            @endif
        </div>
        <div class="sm:text-end">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ __('Shipment') }}</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $shipment->carrier ?? __('Shipment') }}</p>
            <p class="text-sm text-gray-500">{{ __('Destination') }}: {{ country_name($shipment->destination_country) }}</p>
            @if ($shipment->international_tracking)
                <p class="font-mono text-xs text-gray-500">{{ __('Tracking') }}: {{ $shipment->international_tracking }}</p>
            @endif
        </div>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-x-6 gap-y-2 rounded-xl bg-gray-50 p-4 text-sm sm:grid-cols-4">
        <div>
            <p class="text-xs text-gray-400">{{ __('Status') }}</p>
            <p class="font-semibold text-gray-900">{{ $shipment->status->label() }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400">{{ __('Packages') }}</p>
            <p class="font-semibold text-gray-900">{{ $shipment->packages->count() }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400">{{ __('Weight') }}</p>
            <p class="font-semibold text-gray-900">{{ $shipment->final_weight_lb ? $shipment->final_weight_lb . ' lb' : '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400">{{ __('Dimensions') }}</p>
            <p class="font-semibold text-gray-900">{{ $shipment->dimensions ?? '—' }}</p>
        </div>
    </div>

    @if ($shipment->packages->isNotEmpty())
        <h3 class="mt-6 text-sm font-semibold text-gray-900">{{ __('Consolidated Packages') }}</h3>
        <ul class="mt-2 space-y-1.5 text-sm">
            @foreach ($shipment->packages as $package)
                <li class="flex items-center justify-between border-b border-dashed border-gray-100 pb-1.5">
                    <span class="font-mono text-xs text-gray-700">{{ $package->number }}</span>
                    <span class="text-xs text-gray-500">{{ $package->store ?? '' }} · {{ $package->weight_lb ? $package->weight_lb . ' lb' : '' }}</span>
                </li>
            @endforeach
        </ul>
    @endif

    <h3 class="mt-6 text-sm font-semibold text-gray-900">{{ __('Charges') }}</h3>
    <table class="mt-2 w-full text-sm">
        <tbody class="divide-y divide-gray-100">
            @forelse ($shipment->costItems as $cost)
                <tr>
                    <td class="py-2.5 font-medium text-gray-900">{{ $cost->type->label() }}</td>
                    <td class="py-2.5 text-xs text-gray-500">{{ $cost->description }}</td>
                    <td class="py-2.5 text-end font-medium text-gray-900">{{ money($cost->amount) }}</td>
                </tr>
            @empty
                @if ($shipment->shipping_cost)
                    <tr>
                        <td class="py-2.5 font-medium text-gray-900">{{ __('International Shipping') }}</td>
                        <td class="py-2.5 text-xs text-gray-500">{{ $shipment->carrier }}</td>
                        <td class="py-2.5 text-end font-medium text-gray-900">{{ money($shipment->shipping_cost) }}</td>
                    </tr>
                @endif
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 flex items-center justify-between rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white">
        <span>{{ __('Total') }}</span>
        <span>{{ money($shipment->total_cost ?: $shipment->shipping_cost) }}</span>
    </div>

    <p class="mt-6 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-500">
        {{ __('Thank you for shopping with us. Contact us by WhatsApp for any question about your shipment.') }}
    </p>
</x-print-layout>
