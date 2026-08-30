<div>
    <x-slot name="header">{{ __('Facturación') }}</x-slot>

    {{-- Welcome banner --}}
    <div class="relative mb-6 overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 p-5 sm:p-6 text-white shadow-lg shadow-emerald-200">
        <div class="pointer-events-none absolute -end-10 -top-10 h-36 w-36 rounded-full bg-white/10 blur-xl"></div>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-emerald-50 backdrop-blur">
                    <i class="fa-solid fa-file-invoice-dollar text-xs"></i>
                    {{ __('Portal de Facturación') }}
                </span>
                <h1 class="mt-2 text-xl sm:text-2xl font-bold">{{ __('Mis Facturas y Cotizaciones') }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-emerald-100">{{ __('Consulta, imprime o descarga tus cotizaciones oficiales, comprobantes de pago y recibos de envío.') }}</p>
            </div>

            <a href="{{ route('portal.requests.create') }}" wire:navigate
               class="inline-flex items-center gap-1.5 rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-emerald-800 shadow-md transition-all hover:bg-emerald-50 hover:shadow-lg active:scale-95">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>{{ __('Nueva Solicitud') }}</span>
            </a>
        </div>
    </div>

    {{-- Financial Summary KPIs --}}
    @php
        $balance = $customer ? $customer->balance_due : 0;
        $totalPaid = $customer ? (float) $customer->payments()->sum('amount_paid') : 0;
        $totalQuotes = $quotes->count() + $shipments->count();
    @endphp
    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-gray-200/80 bg-white p-4 sm:p-5 shadow-2xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Balance Pendiente') }}</p>
                    <p class="mt-1 text-xl sm:text-2xl font-bold {{ $balance > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ money($balance) }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-clock text-base"></i>
                </div>
            </div>
            @if ($balance > 0)
                <div class="mt-3">
                    <a href="{{ route('portal.payments.index') }}" wire:navigate
                       class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 hover:text-emerald-700">
                        {{ __('Ir a Pagar Balance') }} &rarr;
                    </a>
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white p-4 sm:p-5 shadow-2xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Total Pagado') }}</p>
                    <p class="mt-1 text-xl sm:text-2xl font-bold text-emerald-600">{{ money($totalPaid) }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-circle-check text-base"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white p-4 sm:p-5 shadow-2xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Documentos / Recibos') }}</p>
                    <p class="mt-1 text-xl sm:text-2xl font-bold text-gray-900">{{ $totalQuotes }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-600">
                    <i class="fa-solid fa-file-lines text-base"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- My Quotes / Invoices Section --}}
    <div class="rounded-2xl border border-gray-200/80 bg-white p-5 sm:p-6 shadow-sm">
        <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">{{ __('Historial de Facturas y Cotizaciones') }}</h2>
                <p class="text-xs text-gray-500">{{ __('Descarga o imprime tus cotizaciones oficiales y recibos con código QR de verificación.') }}</p>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Documento') }}</th>
                        <th class="px-4 py-3">{{ __('Descripción / Destino') }}</th>
                        <th class="px-4 py-3">{{ __('Fecha') }}</th>
                        <th class="px-4 py-3">{{ __('Total') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse ($quotes as $quote)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono font-bold text-emerald-700">
                                {{ $quote->number }}
                                <span class="block text-[10px] font-normal text-gray-400">{{ __('Cotización de Compra') }}</span>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $quote->product_name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $quote->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ money($quote->total_cost ?? 0) }}</td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('requests.print', $quote) }}" target="_blank"
                                   class="inline-flex items-center gap-1 rounded-lg border border-emerald-300 bg-emerald-50 px-2.5 py-1 font-semibold text-emerald-700 hover:bg-emerald-100">
                                    <i class="fa-solid fa-print text-xs"></i>
                                    <span>{{ __('Imprimir / QR') }}</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        @forelse ($shipments as $shipment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono font-bold text-purple-700">
                                    {{ $shipment->number }}
                                    <span class="block text-[10px] font-normal text-gray-400">{{ __('Recibo de Envío') }}</span>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $shipment->destination_country ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $shipment->created_at->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ money($shipment->shipping_cost ?? 0) }}</td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('shipments.print', $shipment) }}" target="_blank"
                                       class="inline-flex items-center gap-1 rounded-lg border border-purple-300 bg-purple-50 px-2.5 py-1 font-semibold text-purple-700 hover:bg-purple-100">
                                        <i class="fa-solid fa-print text-xs"></i>
                                        <span>{{ __('Imprimir / QR') }}</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">
                                    <i class="fa-solid fa-file-invoice text-3xl mb-2 text-gray-300"></i>
                                    <p>{{ __('No hay cotizaciones o recibos registrados todavía.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
