@php
    $paidAmount = (float) \App\Models\Payment::where('billable_type', \App\Models\PurchaseRequest::class)
        ->where('billable_id', $request->id)
        ->sum('amount_paid');
    $totalCost = (float) $request->total_cost;
    $balance = max(0, $totalCost - $paidAmount);
    $isQuoted = $request->status === \App\Enums\RequestStatus::Quoted;
    $isPaid = ($totalCost > 0 && $balance <= 0 && ! $isQuoted) || $request->status === \App\Enums\RequestStatus::Purchased;
    $isPending = ! $isPaid && ! $isQuoted;
    $docTitle = $isPaid ? __('Official Invoice') : __('Quote');
@endphp

<x-print-layout
    :doc-title="$docTitle"
    :doc-number="$request->number"
    :back-url="route('admin.requests.show', $request)"
    :auto-print="true">

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ __('Bill To') }}</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $request->customer?->name ?? __('Unknown') }}</p>
            <p class="text-sm text-gray-500">{{ $request->customer?->email ?? '' }}</p>
            @if ($request->customer?->phone || $request->customer?->whatsapp)
                <p class="text-sm text-gray-500">Tel / WhatsApp: {{ $request->customer->whatsapp ?: $request->customer->phone }}</p>
            @endif
            @if ($request->customer?->country)
                <p class="text-sm text-gray-500">{{ country_name($request->customer->country) }}</p>
            @endif
        </div>
        <div class="sm:text-end">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ __('Document') }}</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $request->product_name }}</p>
            @if ($request->store)
                <p class="text-sm text-gray-500">{{ __('Store') }}: {{ $request->store }}</p>
            @endif
            <div class="mt-2">
                @if ($isQuoted)
                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-800">
                        {{ __('Quote') }}
                    </span>
                @elseif ($isPending)
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                        {{ __('Pending Payment') }}
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                        {{ __('Paid') }}
                    </span>
                @endif
            </div>
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
                    <td class="py-2.5 text-sm text-gray-500" colspan="3">{{ __('No costs recorded yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Resumen de Totales --}}
    <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50/50 p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
            <div>
                <span class="text-xs text-gray-500 uppercase font-semibold">{{ __('Total Facturado') }}:</span>
                <span class="font-bold text-gray-900 ms-1">{{ money($totalCost) }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 uppercase font-semibold">{{ __('Monto Pagado') }}:</span>
                <span class="font-bold text-emerald-700 ms-1">{{ money($paidAmount) }}</span>
            </div>
            <div class="rounded-lg bg-emerald-600 px-4 py-2 text-white font-bold text-base">
                <span>{{ __('Saldo por Pagar') }}:</span>
                <span class="ms-1">{{ money($balance) }}</span>
            </div>
        </div>
    </div>

    {{-- Métodos de Pago (Zelle y PayPal @speedingshopper) --}}
    @if ($isQuoted || $isPending || $balance > 0)
        <div class="mt-6 rounded-2xl border-2 border-emerald-300 bg-gradient-to-br from-emerald-50/90 to-teal-50/60 p-4 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="space-y-2 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-600 text-white text-xs">
                            <i class="fa-solid fa-credit-card text-xs"></i>
                        </span>
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-emerald-950">
                            {{ __('MÉTODOS DE PAGO DISPONIBLES') }}
                        </h4>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div class="rounded-xl bg-white border border-emerald-200 p-3 shadow-2xs">
                            <p class="text-[10px] font-bold uppercase text-emerald-800">Zelle</p>
                            <p class="text-xs font-extrabold text-gray-900 font-mono select-all mt-0.5">Gomez.Lilibeth1977@gmail.com</p>
                        </div>

                        <div class="rounded-xl bg-white border border-emerald-200 p-3 shadow-2xs">
                            <p class="text-[10px] font-bold uppercase text-emerald-800">PayPal</p>
                            <p class="text-xs font-extrabold text-gray-900 font-mono select-all mt-0.5">@speedingshopper</p>
                        </div>
                    </div>

                    <p class="text-[11px] text-emerald-900/80 italic mt-1">
                        • {{ __('Por favor enviar comprobante de pago indicando el número de factura #') }}{{ $request->number }}
                    </p>
                </div>

                {{-- Thumbnail de Imagen.png --}}
                @if (file_exists(public_path('images/Imagen.png')) || file_exists(public_path('Imagen.png')))
                    <div class="shrink-0 flex flex-col items-center">
                        <div class="rounded-xl border border-emerald-200 bg-white p-1.5 shadow-xs">
                            <img src="{{ asset(file_exists(public_path('images/Imagen.png')) ? 'images/Imagen.png' : 'Imagen.png') }}"
                                 alt="Información de Pago" class="h-20 w-auto rounded-lg object-contain">
                        </div>
                        <span class="text-[9px] text-emerald-700 font-semibold mt-0.5">{{ __('Info Pago') }}</span>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="mt-4 rounded-xl border border-emerald-500 bg-emerald-50/90 p-4">
            <div class="text-center text-sm font-bold text-emerald-800">
                ✓ {{ __('FACTURA PAGADA EN SU TOTALIDAD - ¡GRACIAS POR SU PAGO!') }}
            </div>
            <div class="mt-3 border-t border-emerald-200 pt-3 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs">
                <span class="font-bold text-emerald-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-location-dot text-emerald-600"></i>
                    {{ __('Dirección de Entrega / Almacén (USA):') }}
                </span>
                <span class="font-mono font-bold text-gray-900 bg-white px-2.5 py-1 rounded-md border border-emerald-200 shadow-2xs">
                    7835 Wood Hollow Dr Baytown Tx 77521
                </span>
            </div>
        </div>
    @endif

    <p class="mt-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
        {{ __('Gracias por tu preferencia. El envío internacional se calcula por caja según peso y dimensiones en el momento del despacho.') }}
    </p>
</x-print-layout>
