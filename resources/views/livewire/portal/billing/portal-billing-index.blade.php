<div>
    <x-slot name="header">{{ __('Facturación y Tarifario') }}</x-slot>

    {{-- Welcome banner --}}
    <div class="relative mb-6 overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 p-6 text-white shadow-lg shadow-emerald-200">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-emerald-50 backdrop-blur">
                    <i class="fa-solid fa-file-invoice-dollar text-xs"></i>
                    {{ __('Tarifas y Facturación') }}
                </span>
                <h2 class="mt-2 text-2xl font-bold">{{ __('Guía Oficial de Precios y Facturas') }}</h2>
                <p class="mt-1 text-xs text-emerald-100">{{ __('Consulta nuestras tarifas oficiales, descarga la guía en PDF y revisa tus cotizaciones.') }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.rates.pdf', ['lang' => 'es']) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-emerald-800 shadow-md transition-transform hover:scale-105">
                    <i class="fa-solid fa-file-pdf text-sm text-emerald-600"></i>
                    <span>{{ __('Descargar PDF (Español)') }}</span>
                </a>
                <a href="{{ route('admin.rates.pdf', ['lang' => 'en']) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-white/90 px-4 py-2.5 text-xs font-bold text-gray-800 shadow-md transition-transform hover:scale-105">
                    <i class="fa-solid fa-file-pdf text-sm text-blue-600"></i>
                    <span>{{ __('Download PDF (English)') }}</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Official Rate Grid --}}
    <div class="mb-8 grid gap-6 lg:grid-cols-2">
        {{-- Personal Shopper Tiers --}}
        <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-sm font-bold text-emerald-700">1</div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Personal Shopper (Compras Físicas)') }}</h3>
                    <p class="text-[11px] text-gray-500">{{ __('Porcentaje de comisión y beneficios según el rango de compra') }}</p>
                </div>
            </div>

            <div class="mt-4 space-y-3">
                @foreach ($rates['shopper_tiers'] ?? [] as $tier)
                    <div class="flex items-center justify-between rounded-xl border border-emerald-100 bg-emerald-50/40 p-3.5">
                        <div>
                            <p class="text-xs font-bold text-gray-900">
                                @if (empty($tier['max']))
                                    ${{ number_format($tier['min']) }} {{ __('o más') }}
                                @else
                                    ${{ number_format($tier['min']) }} – ${{ number_format($tier['max']) }}
                                @endif
                            </p>
                            <p class="text-[11px] text-gray-500">
                                {{ $tier['stores'] }} {{ __('tiendas') }} &bull; {{ $tier['hours'] }} {{ __('horas') }}
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="inline-block rounded-lg bg-emerald-600 px-2.5 py-1 text-xs font-extrabold text-white">
                                {{ $tier['percent'] }}%
                            </span>
                        </div>
                    </div>
                @endforeach

                <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs text-gray-700">
                    <span>{{ __('Visitar una tienda adicional:') }}</span>
                    <strong class="font-bold text-gray-900">${{ number_format($rates['extra_store_fee'] ?? 20, 2) }} USD</strong>
                </div>
            </div>
        </div>

        {{-- Repackaging & Storage --}}
        <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-100 text-sm font-bold text-teal-700">2</div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Reempaques y Almacén') }}</h3>
                    <p class="text-[11px] text-gray-500">{{ __('Cajas Heavy Duty y tarifas de recepción') }}</p>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                    <p class="text-[11px] font-medium text-gray-500">{{ __('Caja Small') }}</p>
                    <p class="mt-1 text-lg font-bold text-emerald-700">${{ number_format($rates['box_small_heavy_duty'] ?? 15) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                    <p class="text-[11px] font-medium text-gray-500">{{ __('Caja Mediana') }}</p>
                    <p class="mt-1 text-lg font-bold text-emerald-700">${{ number_format($rates['box_medium_heavy_duty'] ?? 20) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                    <p class="text-[11px] font-medium text-gray-500">{{ __('Caja Larga') }}</p>
                    <p class="mt-1 text-lg font-bold text-emerald-700">${{ number_format($rates['box_large_heavy_duty'] ?? 25) }}</p>
                </div>
            </div>

            <div class="mt-4 space-y-2 text-xs">
                <div class="flex items-center justify-between rounded-xl bg-gray-50 p-3">
                    <span class="text-gray-600">{{ __('Comisión por compras online recibidas:') }}</span>
                    <strong class="font-bold text-gray-900">{{ $rates['warehouse_percent'] ?? 15 }}%</strong>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-gray-50 p-3">
                    <span class="text-gray-600">{{ __('Llevar caja al almacén:') }}</span>
                    <strong class="font-bold text-gray-900">${{ number_format($rates['warehouse_delivery_fee'] ?? 20, 2) }}</strong>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-gray-50 p-3">
                    <span class="text-gray-600">{{ __('Almacenaje (más de 30 días):') }}</span>
                    <strong class="font-bold text-gray-900">${{ number_format($rates['monthly_storage_fee'] ?? 15, 2) }}/mes</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- My Quotes / Invoices Section --}}
    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm">
        <div class="border-b border-gray-100 pb-3">
            <h3 class="text-sm font-bold text-gray-900">{{ __('Mis Facturas y Cotizaciones') }}</h3>
            <p class="text-xs text-gray-500">{{ __('Descarga o imprime tus cotizaciones y recibos oficiales con código QR.') }}</p>
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
                                <span class="block text-[10px] font-normal text-gray-400">{{ __('Cotización') }}</span>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $quote->product_name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $quote->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ money($quote->total_cost ?? 0) }}</td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('admin.requests.print', $quote) }}" target="_blank"
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
                                    <a href="{{ route('admin.shipments.print', $shipment) }}" target="_blank"
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
