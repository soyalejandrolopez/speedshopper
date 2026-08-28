<x-print-layout
    :doc-title="__('Quote')"
    :doc-number="$request->number"
    :back-url="route('admin.requests.show', $request)"
    :auto-print="true">

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ __('Bill To') }}</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $request->customer?->name ?? __('Unknown') }}</p>
            <p class="text-sm text-gray-500">{{ $request->customer?->email ?? '' }}</p>
            @if ($request->customer?->whatsapp)
                <p class="text-sm text-gray-500">WhatsApp: {{ $request->customer->whatsapp }}</p>
            @endif
            @if ($request->customer?->country)
                <p class="text-sm text-gray-500">{{ country_name($request->customer->country) }}</p>
            @endif
        </div>
        <div class="sm:text-end">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ __('Purchase Request') }}</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $request->product_name }}</p>
            <p class="text-sm text-gray-500">{{ $request->store ?? '' }}</p>
            @if ($request->product_url)
                <p class="break-all text-xs text-gray-400">{{ $request->product_url }}</p>
            @endif
        </div>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-x-6 gap-y-2 rounded-xl bg-gray-50 p-4 text-sm sm:grid-cols-4">
        <div>
            <p class="text-xs text-gray-400">{{ __('Status') }}</p>
            <p class="font-semibold text-gray-900">{{ $request->status->label() }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400">{{ __('Quantity') }}</p>
            <p class="font-semibold text-gray-900">{{ $request->quantity }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400">{{ __('Size / Color') }}</p>
            <p class="font-semibold text-gray-900">{{ $request->size_color ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400">{{ __('Unit Price') }}</p>
            <p class="font-semibold text-gray-900">{{ $request->unit_price !== null ? money($request->unit_price) : '—' }}</p>
        </div>
    </div>

    <h3 class="mt-6 text-sm font-semibold text-gray-900">{{ __('Cost Breakdown') }}</h3>
    <table class="mt-2 w-full text-sm">
        <tbody class="divide-y divide-gray-100">
            @forelse ($request->costItems as $cost)
                <tr>
                    <td class="py-2.5 font-medium text-gray-900">{{ $cost->type->label() }}</td>
                    <td class="py-2.5 text-xs text-gray-500">{{ $cost->description }}</td>
                    <td class="py-2.5 text-end font-medium text-gray-900">{{ money($cost->amount) }}</td>
                </tr>
            @empty
                <tr>
                    <td class="py-2.5 text-sm text-gray-500">{{ __('No costs recorded yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 flex items-center justify-between rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white">
        <span>{{ __('Estimated Total') }}</span>
        <span>{{ money($request->total_cost) }}</span>
    </div>

    <p class="mt-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
        {{ __('This is a quote. Nothing is purchased until you confirm and pay. International shipping is calculated per box based on weight and destination.') }}
    </p>
</x-print-layout>
