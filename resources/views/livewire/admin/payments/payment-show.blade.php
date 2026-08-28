<div>
    <x-slot name="header">{{ $payment->number }}</x-slot>

    <div class="mb-4 flex items-center justify-between no-print">
        <a href="{{ route('admin.payments.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-800">
            <i class="fa-solid fa-arrow-left text-base"></i>
            {{ __('Back') }}
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-gray-200 border-t-4 border-t-emerald-500 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $payment->number }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Payment') }} · {{ $payment->paid_at?->format('Y-m-d') ?? '—' }}</p>
                    </div>
                    @if ($payment->balance_due > 0)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">
                            {{ __('Balance due') }}: {{ money($payment->balance_due) }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                            {{ __('Paid in full') }}
                        </span>
                    @endif
                </div>

                <dl class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Customer') }}</dt>
                        <dd class="font-medium text-gray-900">
                            <a href="{{ route('admin.customers.show', $payment->customer) }}" wire:navigate class="text-emerald-600 hover:underline">
                                {{ $payment->customer?->name ?? __('Unknown') }}
                            </a>
                        </dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Payment method') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $payment->payment_method?->label() ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Invoice total') }}</dt>
                        <dd class="font-medium text-gray-900">{{ money($payment->invoice_total) }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Amount paid') }}</dt>
                        <dd class="font-medium text-emerald-600">{{ money($payment->amount_paid) }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Balance due') }}</dt>
                        <dd class="font-medium {{ $payment->balance_due > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ money($payment->balance_due) }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500">{{ __('Paid at') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $payment->paid_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                    </div>
                </dl>

                @if ($payment->notes)
                    <div class="mt-4 rounded-lg bg-gray-50 p-3 text-xs text-gray-600">{{ $payment->notes }}</div>
                @endif
            </div>

            @if ($payment->billable)
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Related') }}</h3>
                    <div class="mt-3 flex items-center justify-between rounded-xl bg-gray-50 p-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">
                                @if ($payment->billable_type === \App\Models\PurchaseRequest::class)
                                    {{ __('Purchase Request') }} — {{ $payment->billable->number ?? '' }}
                                @elseif ($payment->billable_type === \App\Models\Shipment::class)
                                    {{ __('Shipment') }} — {{ $payment->billable->number ?? '' }}
                                @else
                                    {{ $payment->billable_type }}
                                @endif
                            </p>
                            @if ($payment->billable_type === \App\Models\PurchaseRequest::class && $payment->billable->product_name)
                                <p class="text-xs text-gray-500">{{ $payment->billable->product_name }}</p>
                            @endif
                        </div>
                        @if ($payment->billable_type === \App\Models\PurchaseRequest::class)
                            <a href="{{ route('admin.requests.show', $payment->billable) }}" wire:navigate class="text-xs font-medium text-emerald-600 hover:underline">{{ __('View') }}</a>
                        @elseif ($payment->billable_type === \App\Models\Shipment::class)
                            <a href="{{ route('admin.shipments.show', $payment->billable) }}" wire:navigate class="text-xs font-medium text-emerald-600 hover:underline">{{ __('View') }}</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Summary') }}</h3>
                <dl class="mt-3 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">{{ __('Invoiced') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ money($payment->invoice_total) }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">{{ __('Paid') }}</dt>
                        <dd class="font-semibold text-emerald-600">{{ money($payment->amount_paid) }}</dd>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-100 pt-3">
                        <dt class="text-gray-500">{{ __('Balance') }}</dt>
                        <dd class="font-semibold {{ $payment->balance_due > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ money($payment->balance_due) }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
