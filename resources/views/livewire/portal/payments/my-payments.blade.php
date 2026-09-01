<div>
    <x-slot name="header">{{ __('My Payments') }}</x-slot>

    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-amber-800">{{ __('Tu Balance Pendiente') }}</p>
                <p class="mt-1 text-2xl font-semibold text-amber-900">{{ money($balanceDue) }}</p>
            </div>
            @php $wa = \App\Models\Setting::get('whatsapp_phone'); @endphp
            @if ($wa)
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $wa) }}?text={{ urlencode(__('Hi! I want to make a payment for my account.')) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                    {{ __('Pay via WhatsApp') }}
                </a>
            @endif
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="hidden md:block overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Number') }}</th>
                        <th class="px-4 py-3">{{ __('Invoice Total') }}</th>
                        <th class="px-4 py-3">{{ __('Amount Paid') }}</th>
                        <th class="px-4 py-3">{{ __('Balance Pendiente') }}</th>
                        <th class="px-4 py-3">{{ __('Payment Method') }}</th>
                        <th class="px-4 py-3">{{ __('Paid At') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">{{ $payment->number }}</td>
                            <td class="px-4 py-3 text-xs">{{ money($payment->invoice_total) }}</td>
                            <td class="px-4 py-3 text-xs text-emerald-600">{{ money($payment->amount_paid) }}</td>
                            <td class="px-4 py-3 text-xs {{ $payment->balance_due > 0 ? 'font-medium text-amber-600' : 'text-emerald-600' }}">
                                {{ money($payment->balance_due) }}
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $payment->payment_method?->label() ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs">{{ $payment->paid_at?->format('Y-m-d') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
    <td colspan="99">
        <x-empty-state :message="__('No records found.')" icon="card" />
    </td>
</tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <ul class="divide-y divide-gray-100 md:hidden">
            @forelse ($payments as $payment)
                <li class="px-4 py-4">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-mono text-xs text-gray-900">{{ $payment->number }}</p>
                        <span class="shrink-0 text-sm font-semibold {{ $payment->balance_due > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                            {{ $payment->balance_due > 0 ? money($payment->balance_due) . ' ' . __('due') : __('Paid') }}
                        </span>
                    </div>
                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                        <span>{{ __('Invoiced') }}: <span class="font-medium text-gray-700">{{ money($payment->invoice_total) }}</span></span>
                        <span>{{ __('Paid') }}: <span class="font-medium text-emerald-600">{{ money($payment->amount_paid) }}</span></span>
                        <span class="ms-auto">{{ $payment->payment_method?->label() ?? '—' }} · {{ $payment->paid_at?->format('Y-m-d') ?? '—' }}</span>
                    </div>
                </li>
            @empty
                <li>
                    <x-empty-state :message="__('No records found.')" icon="card" />
                </li>
            @endforelse
        </ul>

        <div class="border-t border-gray-200 p-4">
            {{ $payments->links() }}
        </div>
    </div>
</div>
