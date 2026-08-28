<div>
    <x-slot name="header">{{ __('Reports') }}</x-slot>

    <div class="card animate-fade-up">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Report Generator') }}</h2>
                <p class="mt-0.5 text-xs text-gray-500">{{ __('Choose a period and download the financial report in PDF, Excel or CSV with your company logo.') }}</p>
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

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">{{ __('Invoiced') }}</p>
                    <p class="mt-0.5 text-lg font-bold text-gray-900">{{ money($reportPeriod['invoiced']) }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">{{ __('Collected') }}</p>
                    <p class="mt-0.5 text-lg font-bold text-gray-900">{{ money($reportPeriod['collected']) }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">{{ __('Balance') }}</p>
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

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="stat-card text-emerald-600 animate-fade-up">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">{{ __('Total Invoiced') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-money-bill text-xl"></i>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ money($totalInvoiced) }}</p>
        </div>

        <div class="stat-card text-teal-600 animate-fade-up" style="animation-delay: 60ms">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">{{ __('Total Collected') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-50 text-teal-600">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ money($totalCollected) }}</p>
        </div>

        <div class="stat-card text-amber-600 animate-fade-up" style="animation-delay: 120ms">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">{{ __('Balance Due') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-clock text-xl"></i>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ money($balanceDue) }}</p>
        </div>

        <div class="stat-card text-sky-600 animate-fade-up" style="animation-delay: 180ms">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-500">{{ __('Revenue This Month') }}</p>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                    <i class="fa-solid fa-chart-simple text-xl"></i>
                </span>
            </div>
            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ money($revenueThisMonth) }}</p>
        </div>
    </div>

    <div class="grid gap-6 mt-6 lg:grid-cols-2">
        <div class="card animate-fade-up" style="animation-delay: 220ms">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Revenue by Period') }}</h2>
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
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Balance by Customer') }}</h2>
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
