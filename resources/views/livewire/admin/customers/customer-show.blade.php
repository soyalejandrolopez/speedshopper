<div>
    <x-slot name="header">{{ $customer->name }}</x-slot>

    <div class="mb-4 flex items-center justify-between no-print">
        <a href="{{ route('admin.customers.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-800">
            <i class="fa-solid fa-arrow-left text-base"></i>
            {{ __('Back') }}
        </a>
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:bg-gray-50 no-print">
                <i class="fa-solid fa-print text-sm"></i>
                {{ __('Print') }}
            </button>
    </div>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Customer Details') }}</h2>
                <span class="rounded-md bg-gray-100 px-2 py-0.5 font-mono text-xs text-gray-600">{{ $customer->number }}</span>
            </div>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500">{{ __('Email') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $customer->email ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500">{{ __('Phone') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $customer->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500">{{ __('WhatsApp') }}</dt>
                    <dd class="font-medium text-gray-900">
                        @if ($customer->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $customer->whatsapp) }}" target="_blank" class="text-emerald-600 hover:underline">
                                {{ $customer->whatsapp }}
                            </a>
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500">{{ __('Address') }}</dt>
                    <dd class="text-end font-medium text-gray-900">{{ $customer->address ? $customer->address . ', ' : '' }}{{ $customer->city ?? '' }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500">{{ __('Country') }}</dt>
                    <dd class="font-medium text-gray-900">{{ country_name($customer->country) }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500">{{ __('Date') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $customer->registered_at?->format('Y-m-d') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-gray-500">{{ __('Balance Due') }}</dt>
                    <dd class="font-semibold {{ $customer->balance_due > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ money($customer->balance_due) }}</dd>
                </div>
            </dl>
            @if ($customer->notes)
                <div class="mt-4 rounded-lg bg-gray-50 p-3 text-xs text-gray-600">{{ $customer->notes }}</div>
            @endif
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Purchase Requests') }}</h3>
                    <a href="{{ route('admin.requests.index', ['customer' => $customer->id]) }}" wire:navigate class="text-xs font-medium text-emerald-600">{{ __('View All') }}</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($requests as $request)
                                <tr>
                                    <td class="px-5 py-2.5 font-mono text-xs">{{ $request->number }}</td>
                                    <td class="px-5 py-2.5">{{ $request->product_name }}</td>
                                    <td class="px-5 py-2.5"><x-status-badge :status="$request->status" /></td>
                                </tr>
                            @empty
                                <x-empty-state :message="__('No records found.')" icon="inbox" />
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="border-b border-gray-200 px-5 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Packages') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($packages as $package)
                                <tr>
                                    <td class="px-5 py-2.5 font-mono text-xs">{{ $package->number }}</td>
                                    <td class="px-5 py-2.5">{{ $package->store ?? '—' }}</td>
                                    <td class="px-5 py-2.5"><x-status-badge :status="$package->status" /></td>
                                </tr>
                            @empty
                                <x-empty-state :message="__('No records found.')" icon="inbox" />
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="border-b border-gray-200 px-5 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Payments') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($payments as $payment)
                                <tr>
                                    <td class="px-5 py-2.5 font-mono text-xs">{{ $payment->number }}</td>
                                    <td class="px-5 py-2.5">{{ $payment->paid_at?->format('Y-m-d') ?? '—' }}</td>
                                    <td class="px-5 py-2.5 text-end">{{ money($payment->invoice_total) }}</td>
                                    <td class="px-5 py-2.5 text-end font-medium {{ $payment->balance_due > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ money($payment->balance_due) }}</td>
                                </tr>
                            @empty
                                <x-empty-state :message="__('No records found.')" icon="inbox" />
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
