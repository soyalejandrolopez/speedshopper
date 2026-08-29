<div>
    <x-slot name="header">{{ __('Payments') }}</x-slot>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-gray-200 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:w-64">
                <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input name="search"
                    wire:model.live.debounce.300ms="search" data-shortcut-search aria-label="{{ __('Search') }}"
                    type="search"
                    placeholder="{{ __('Search') }}..."
                    class="input ps-8 text-xs">
            </div>

            <button wire:click="openCreate" type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                <i class="fa-solid fa-plus text-base"></i>
                {{ __('New Payment') }}
            </button>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Number') }}</th>
                        <th class="px-4 py-3">{{ __('Customer') }}</th>
                        <th class="px-4 py-3">{{ __('Invoice Total') }}</th>
                        <th class="px-4 py-3">{{ __('Amount Paid') }}</th>
                        <th class="px-4 py-3">{{ __('Balance Due') }}</th>
                        <th class="px-4 py-3">{{ __('Payment Method') }}</th>
                        <th class="px-4 py-3">{{ __('Paid At') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">
                                <a href="{{ route('admin.payments.show', $payment) }}" wire:navigate class="font-medium text-emerald-600 hover:text-emerald-800">{{ $payment->number }}</a>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $payment->customer?->name ?? __('Unknown') }}</td>
                            <td class="px-4 py-3 text-xs">{{ money($payment->invoice_total) }}</td>
                            <td class="px-4 py-3 text-xs text-emerald-600">{{ money($payment->amount_paid) }}</td>
                            <td class="px-4 py-3 text-xs {{ $payment->balance_due > 0 ? 'font-medium text-amber-600' : 'text-emerald-600' }}">
                                {{ money($payment->balance_due) }}
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $payment->payment_method?->label() ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs">{{ $payment->paid_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    <button wire:click="edit({{ $payment->id }})"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Edit') }}">
                                        <i class="fa-solid fa-pen-to-square text-base"></i>
                                    </button>
                                    <button wire:click="delete({{ $payment->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this record?') }}"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600" title="{{ __('Delete') }}">
                                        <i class="fa-solid fa-trash text-base"></i>
                                    </button>
                                </div>
                            </td>
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
                <li>
                    <div class="block px-4 py-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-900">{{ $payment->customer?->name ?? __('Unknown') }}</p>
                                <p class="mt-0.5 font-mono text-xs text-gray-400">{{ $payment->number }}</p>
                            </div>
                            <span class="shrink-0 text-sm font-semibold {{ $payment->balance_due > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                                {{ $payment->balance_due > 0 ? money($payment->balance_due) . ' ' . __('due') : __('Paid') }}
                            </span>
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                            <span>{{ __('Invoiced') }}: <span class="font-medium text-gray-700">{{ money($payment->invoice_total) }}</span></span>
                            <span>{{ __('Paid') }}: <span class="font-medium text-emerald-600">{{ money($payment->amount_paid) }}</span></span>
                            <span class="ms-auto">{{ $payment->payment_method?->label() ?? '—' }} · {{ $payment->paid_at?->format('Y-m-d') ?? '—' }}</span>
                        </div>
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

    <x-modal maxWidth="max-w-3xl">
                    <form wire:submit="save">
                        <x-modal-header :title="$editingId ? __('Edit Payment') : __('New Payment')" />

                        <x-modal-body class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="customer_id-{{ $this->getId() }}">{{ __('Customer') }} *</label>
                                <x-customer-search :customers="$customers" />
                                @error('form.customer_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="payment_method-{{ $this->getId() }}">{{ __('Payment Method') }}</label>
                                <select id="payment_method-{{ $this->getId() }}" name="payment_method" wire:model="form.payment_method" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">—</option>
                                    @foreach ($methods as $method)
                                        <option value="{{ $method->value }}">{{ $method->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($pendingBalance)
                                <div class="sm:col-span-2">
                                    <div class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100">
                                            <i class="fa-solid fa-triangle-exclamation text-sm text-amber-600"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-medium text-amber-800">{{ __('Pending balance') }}</p>
                                            <p class="text-sm font-semibold text-amber-900">{{ money($pendingBalance) }}</p>
                                        </div>
                                        <span class="ms-auto shrink-0 rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">
                                            {{ __('Invoice Total assumed') }}
                                        </span>
                                    </div>
                                    {{-- Hidden input keeps the bound value so validation passes --}}
                                    <input type="hidden" wire:model="form.invoice_total">
                                </div>
                            @else
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700" for="invoice_total-{{ $this->getId() }}">{{ __('Invoice Total') }} *</label>
                                    <input id="invoice_total-{{ $this->getId() }}" name="invoice_total" type="number" step="0.01" min="0" wire:model="form.invoice_total" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                                    @error('form.invoice_total') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            @endif
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="amount_paid-{{ $this->getId() }}">{{ __('Amount Paid') }}</label>
                                <input id="amount_paid-{{ $this->getId() }}" name="amount_paid" type="number" step="0.01" min="0" wire:model="form.amount_paid" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                                @error('form.amount_paid') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="paid_at-{{ $this->getId() }}">{{ __('Paid At') }}</label>
                                <input id="paid_at-{{ $this->getId() }}" name="paid_at" type="date" wire:model="form.paid_at" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="reference-{{ $this->getId() }}">{{ __('Related to') }}</label>
                                <input id="reference-{{ $this->getId() }}" name="reference" type="text" wire:model="form.reference"
                                    placeholder="{{ __('e.g. Request #, invoice, shipment…') }}"
                                    class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="notes-{{ $this->getId() }}">{{ __('Notes') }}</label>
                                <textarea id="notes-{{ $this->getId() }}" name="notes" wire:model="form.notes" rows="2" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"></textarea>
                            </div>
                        </x-modal-body>

                        <x-modal-footer>
                            <button type="button" wire:click="closeForm"
                                    class="rounded-xl border border-gray-200/80 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-200/50">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                    class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/30">
                                {{ __('Save') }}
                            </button>
                        </x-modal-footer>
        </form>
    </x-modal>
</div>
