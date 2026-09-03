<div>
    <x-slot name="header">{{ __('Reports') }}</x-slot>

    <div class="card animate-fade-up">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Generador de Reportes') }}</h2>
                <p class="mt-0.5 text-xs text-gray-500">{{ __('Descarga el reporte financiero consolidado de facturación, ganancias por servicios y pagos en PDF, Excel o CSV.') }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                <i class="fa-solid fa-calendar-days text-sm"></i>
                {{ $reportPeriod['label'] }}
            </span>
        </div>

        <div class="space-y-4 p-5">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex overflow-hidden rounded-lg border border-gray-300">
                    <button type="button" wire:click="$set('period', 'monthly')"
                            class="px-4 py-2 text-sm font-semibold transition-colors {{ $period === 'monthly' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                        {{ __('Monthly') }}
                    </button>
                    <button type="button" wire:click="$set('period', 'yearly')"
                            class="border-s border-gray-300 px-4 py-2 text-sm font-semibold transition-colors {{ $period === 'yearly' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                        {{ __('Yearly') }}
                    </button>
                    <button type="button" wire:click="$set('period', 'custom')"
                            class="border-s border-gray-300 px-4 py-2 text-sm font-semibold transition-colors {{ $period === 'custom' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                        {{ __('Custom') }}
                    </button>
                </div>

                @if ($period === 'monthly')
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">{{ __('Month') }}</label>
                        <input type="month" wire:model.live="month" class="rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                @elseif ($period === 'yearly')
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">{{ __('Year') }}</label>
                        <input type="number" min="2000" max="2100" wire:model.live="year" class="w-28 rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                @else
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">{{ __('From') }}</label>
                        <input type="date" wire:model.live="from" class="rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">{{ __('To') }}</label>
                        <input type="date" wire:model.live="to" class="rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-2xs">
                    <p class="text-xs font-medium text-gray-500">{{ __('Total Facturado') }}</p>
                    <p class="mt-0.5 text-lg font-bold text-gray-900">{{ money($reportPeriod['invoiced']) }}</p>
                </div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/70 p-3 shadow-2xs">
                    <p class="text-xs font-bold text-emerald-800">{{ __('Ganancia Servicios') }}</p>
                    <p class="mt-0.5 text-lg font-extrabold text-emerald-800">💰 {{ money($reportPeriod['earnings']) }}</p>
                </div>
                <div class="rounded-xl border border-teal-200 bg-teal-50/70 p-3 shadow-2xs">
                    <p class="text-xs font-medium text-teal-800">{{ __('Total Pagado por Clientes') }}</p>
                    <p class="mt-0.5 text-lg font-bold text-teal-700">{{ money($reportPeriod['collected']) }}</p>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-3 shadow-2xs">
                    <p class="text-xs font-medium text-amber-800">{{ __('Saldo por Cobrar') }}</p>
                    <p class="mt-0.5 text-lg font-bold {{ $reportPeriod['balance'] > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ money($reportPeriod['balance']) }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">{{ __('New Customers') }}</p>
                    <p class="mt-0.5 text-lg font-bold text-gray-900">{{ $reportPeriod['newCustomers'] }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">{{ __('Requests') }}</p>
                    <p class="mt-0.5 text-lg font-bold text-gray-900">{{ $reportPeriod['requests'] }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">{{ __('Packages') }}</p>
                    <p class="mt-0.5 text-lg font-bold text-gray-900">{{ $reportPeriod['packages'] }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">{{ __('Shipments') }}</p>
                    <p class="mt-0.5 text-lg font-bold text-gray-900">{{ $reportPeriod['shipments'] }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">{{ __('Payments') }}</p>
                    <p class="mt-0.5 text-lg font-bold text-gray-900">{{ $reportPeriod['payments']->count() }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <button wire:click="exportReportPdf" type="button" class="btn-primary">
                    <i class="fa-solid fa-file-lines text-base"></i>
                    {{ __('Download PDF') }}
                </button>
                <button wire:click="exportReportExcel" type="button" class="btn-ghost">
                    <i class="fa-solid fa-qrcode text-base"></i>
                    {{ __('Download Excel') }}
                </button>
                <button wire:click="exportReportCsv" type="button" class="btn-ghost">
                    <i class="fa-solid fa-download text-base"></i>
                    {{ __('Download CSV') }}
                </button>
            </div>
        </div>
    </div>

    {{-- TABLA 1: LO FACTURADO --}}
    <div class="card mt-6 overflow-hidden animate-fade-up" style="animation-delay: 150ms">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4 bg-gray-50/70">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <i class="fa-solid fa-file-invoice"></i>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-bold text-gray-900">{{ __('Lo Facturado (Facturas del Período)') }}</h2>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200">
                            {{ count($reportPeriod['invoicesList']) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500">{{ __('Detalle independiente de facturas emitidas en el período seleccionado.') }}</p>
                </div>
            </div>
            <div class="rounded-xl bg-white border border-emerald-200 px-3.5 py-1.5 shadow-2xs">
                <span class="text-xs text-gray-500">{{ __('Total Facturado') }}:</span>
                <span class="ml-1 text-sm font-extrabold text-gray-900">{{ money($reportPeriod['invoiced']) }}</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3">{{ __('Number') }}</th>
                        <th class="px-4 py-3">{{ __('Customer') }}</th>
                        <th class="px-4 py-3">{{ __('Date') }}</th>
                        <th class="px-4 py-3">{{ __('Details') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Total Facturado') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($reportPeriod['invoicesList'] as $inv)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-2.5 font-mono font-bold text-gray-900 whitespace-nowrap">
                                <a href="{{ route('admin.requests.show', $inv['id']) }}" wire:navigate class="hover:text-emerald-700 hover:underline">
                                    {{ $inv['number'] }}
                                </a>
                            </td>
                            <td class="px-4 py-2.5 font-medium text-gray-900 whitespace-nowrap">
                                @if ($inv['customer_id'])
                                    <a href="{{ route('admin.customers.show', $inv['customer_id']) }}" wire:navigate class="hover:text-emerald-700">
                                        {{ $inv['customer'] }}
                                    </a>
                                @else
                                    {{ $inv['customer'] }}
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-gray-500 whitespace-nowrap">
                                {{ $inv['date'] }}
                            </td>
                            <td class="px-4 py-2.5 text-gray-600 max-w-xs truncate">
                                {{ $inv['details'] ?: '—' }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-700">
                                    {{ $inv['status'] }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono font-bold text-gray-900 whitespace-nowrap">
                                {{ money($inv['invoice_total']) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center">
                                <x-empty-state :message="__('No records found.')" icon="card" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($reportPeriod['invoicesList']) > 0)
                    <tfoot class="bg-gray-50 border-t-2 border-gray-300 font-bold text-gray-900">
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-right uppercase tracking-wider text-[11px] text-gray-600">
                                {{ __('Total Facturado') }}:
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-sm font-extrabold text-emerald-800">
                                {{ money($reportPeriod['invoiced']) }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- TABLA 2: PAGOS POR CLIENTE --}}
    <div class="card mt-6 overflow-hidden animate-fade-up" style="animation-delay: 200ms">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4 bg-gray-50/70">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-100 text-teal-700">
                    <i class="fa-solid fa-money-check-dollar"></i>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-bold text-gray-900">{{ __('Pagos por Cliente (Cobrado en el Período)') }}</h2>
                        <span class="inline-flex items-center rounded-full bg-teal-50 px-2 py-0.5 text-xs font-semibold text-teal-700 border border-teal-200">
                            {{ count($reportPeriod['customerPaymentsList']) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500">{{ __('Total de pagos efectivamente cobrados agrupados por cada cliente.') }}</p>
                </div>
            </div>
            <div class="rounded-xl bg-white border border-teal-200 px-3.5 py-1.5 shadow-2xs">
                <span class="text-xs text-gray-500">{{ __('Total Pagado por Clientes') }}:</span>
                <span class="ml-1 text-sm font-extrabold text-teal-700">{{ money($reportPeriod['collected']) }}</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3">{{ __('Customer') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Number of Payments') }}</th>
                        <th class="px-4 py-3">{{ __('Method') }}</th>
                        <th class="px-4 py-3">{{ __('Last Payment Date') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Total Pagado') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($reportPeriod['customerPaymentsList'] as $cp)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-2.5 font-medium text-gray-900 whitespace-nowrap">
                                @if ($cp['customer_id'])
                                    <a href="{{ route('admin.customers.show', $cp['customer_id']) }}" wire:navigate class="hover:text-teal-700 font-semibold">
                                        {{ $cp['customer'] }}
                                    </a>
                                @else
                                    <span class="font-semibold">{{ $cp['customer'] }}</span>
                                @endif
                                @if (!empty($cp['customer_whatsapp']))
                                    <span class="block text-[11px] text-gray-400 font-mono">{{ $cp['customer_whatsapp'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-bold text-gray-700">
                                    {{ $cp['payments_count'] }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-gray-600 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 rounded bg-teal-50 px-2 py-0.5 text-xs font-medium text-teal-700 border border-teal-100">
                                    {{ $cp['methods'] ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-gray-500 whitespace-nowrap">
                                {{ $cp['latest_date'] }}
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono font-bold text-teal-700 whitespace-nowrap">
                                {{ money($cp['total_paid']) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center">
                                <x-empty-state :message="__('No records found.')" icon="card" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($reportPeriod['customerPaymentsList']) > 0)
                    <tfoot class="bg-gray-50 border-t-2 border-gray-300 font-bold text-gray-900">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right uppercase tracking-wider text-[11px] text-gray-600">
                                {{ __('Total Pagado por Clientes') }}:
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-sm font-extrabold text-teal-700">
                                {{ money($reportPeriod['collected']) }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div class="grid gap-6 mt-6 lg:grid-cols-2">
        <div class="card animate-fade-up" style="animation-delay: 220ms">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Ingresos por Período') }}</h2>
            </div>
            <div class="space-y-4 p-5">
                @php $maxRevenue = max($reportPeriod['revenue'] ? max(array_column($reportPeriod['revenue'], 'total')) : 0, 1); @endphp
                @forelse ($reportPeriod['revenue'] as $row)
                    @php $pct = round(($row['total'] / $maxRevenue) * 100); @endphp
                    <div>
                        <div class="mb-1.5 flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700">{{ $row['label'] }}</span>
                            <span class="font-semibold text-gray-900">{{ money($row['total']) }}</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill bg-emerald-500" style="width: 0%" data-bar="{{ $pct }}"></div>
                        </div>
                    </div>
                @empty
                    <x-empty-state :message="__('No records found.')" icon="card" />
                @endforelse
            </div>
        </div>

        <div class="card animate-fade-up" style="animation-delay: 280ms">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Balance Pendiente por Cliente') }}</h2>
            </div>
            <div class="space-y-4 p-5">
                @forelse ($balanceByCustomer as $row)
                    @php
                        $pct = round(($row['balance'] / $maxCustomerBalance) * 100);
                    @endphp
                    <div>
                        <div class="mb-1.5 flex items-center justify-between text-sm">
                            <span class="truncate font-medium text-gray-700">{{ $row['name'] }}</span>
                            <span class="font-semibold text-amber-600">{{ money($row['balance']) }}</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill bg-amber-500" style="width: 0%" data-bar="{{ $pct }}"></div>
                        </div>
                    </div>
                @empty
                    <x-empty-state :message="__('No balances pending.')" icon="card" />
                @endforelse
            </div>
        </div>
    </div>

    <div class="card mt-6 animate-fade-up" style="animation-delay: 340ms">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Export Data (CSV)') }}</h2>
                <p class="mt-0.5 text-xs text-gray-500">{{ __('Download a snapshot of your records for Excel, Sheets or accounting.') }}</p>
            </div>
        </div>
        <div class="grid gap-3 p-5 sm:grid-cols-2 lg:grid-cols-5">
            <button wire:click="exportCustomers" type="button"
                    class="btn-ghost justify-center text-xs">
                <i class="fa-solid fa-download text-base"></i>
                {{ __('Customers') }} ({{ $customersCount }})
            </button>
            <button wire:click="exportRequests" type="button"
                    class="btn-ghost justify-center text-xs">
                <i class="fa-solid fa-download text-base"></i>
                {{ __('Requests') }} ({{ $requestsCount }})
            </button>
            <button wire:click="exportPackages" type="button"
                    class="btn-ghost justify-center text-xs">
                <i class="fa-solid fa-download text-base"></i>
                {{ __('Packages') }} ({{ $packagesCount }})
            </button>
            <button wire:click="exportShipments" type="button"
                    class="btn-ghost justify-center text-xs">
                <i class="fa-solid fa-download text-base"></i>
                {{ __('Shipments') }} ({{ $shipmentsCount }})
            </button>
            <button wire:click="exportPayments" type="button"
                    class="btn-ghost justify-center text-xs">
                <i class="fa-solid fa-download text-base"></i>
                {{ __('Payments') }} ({{ $paymentsCount }})
            </button>
        </div>
    </div>
</div>
