<div>
    <x-slot name="header">{{ __('Shipments') }}</x-slot>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-gray-200 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-2 sm:flex-row">
<div class="relative">
                    <svg class="pointer-events-none absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input name="search"
                        wire:model.live.debounce.300ms="search" data-shortcut-search aria-label="{{ __('Search') }}"
                        type="search"
                        placeholder="{{ __('Search') }}..."
                        class="w-full rounded-lg border border-gray-300 text-sm ps-9 sm:w-64 focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <select name="status" wire:model.live="status" aria-label="{{ __('Filter by status') }}" class="rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="all">{{ __('All statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <button wire:click="openCreate" type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('New Shipment') }}
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Number') }}</th>
                        <th class="px-4 py-3">{{ __('Customer') }}</th>
                        <th class="px-4 py-3">{{ __('Packages') }}</th>
                        <th class="px-4 py-3">{{ __('Carrier') }}</th>
                        <th class="px-4 py-3">{{ __('Destination Country') }}</th>
                        <th class="px-4 py-3">{{ __('Shipping Cost') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($shipments as $shipment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">
                                <a href="{{ route('admin.shipments.show', $shipment) }}" wire:navigate class="font-medium text-emerald-600 hover:text-emerald-800">{{ $shipment->number }}</a>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $shipment->customer?->name ?? __('Unknown') }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 font-medium text-gray-700">{{ $shipment->packages_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $shipment->carrier ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs">{{ country_name($shipment->destination_country) }}</td>
                            <td class="px-4 py-3 text-xs">{{ $shipment->shipping_cost !== null ? money($shipment->shipping_cost) : '—' }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$shipment->status" /></td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.shipments.show', $shipment) }}" wire:navigate
                                       class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Show') }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>
                                    <button wire:click="edit({{ $shipment->id }})"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Edit') }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $shipment->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this record?') }}"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600" title="{{ __('Delete') }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
    <td colspan="99">
        <x-empty-state :message="__('No records found.')" icon="ship" />
    </td>
</tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 p-4">
            {{ $shipments->links() }}
        </div>
    </div>

    <x-modal maxWidth="max-w-3xl">
        <form wire:submit="save">
                        <x-modal-header :title="$editingId ? __('Edit Shipment') : __('New Shipment')" />

                        <x-modal-body class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="customer_id-{{ $this->getId() }}">{{ __('Customer') }} *</label>
                                <x-customer-search :customers="$customers" />
                                @error('form.customer_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="carrier-{{ $this->getId() }}">{{ __('Carrier') }}</label>
                                <select id="carrier-{{ $this->getId() }}" name="carrier" wire:model="form.carrier" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">—</option>
                                    <option value="DHL Express">DHL Express</option>
                                    <option value="FedEx">FedEx</option>
                                    <option value="UPS">UPS</option>
                                    <option value="USPS">USPS</option>
                                    <option value="Other">{{ __('Other') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="destination_country-{{ $this->getId() }}">{{ __('Destination Country') }}</label>
                                <select id="destination_country-{{ $this->getId() }}" name="destination_country" wire:model="form.destination_country" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">—</option>
                                    @foreach (['MX', 'GT', 'HN', 'SV', 'NI', 'CR', 'PA', 'CO', 'EC', 'PE', 'CL', 'AR', 'US'] as $code)
                                        <option value="{{ $code }}">{{ country_name($code) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="final_weight_lb-{{ $this->getId() }}">{{ __('Final Weight (lb)') }}</label>
                                <input id="final_weight_lb-{{ $this->getId() }}" name="final_weight_lb" type="number" step="0.01" min="0" wire:model="form.final_weight_lb" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="dimensions-{{ $this->getId() }}">{{ __('Dimensions') }}</label>
                                <input id="dimensions-{{ $this->getId() }}" name="dimensions" type="text" wire:model="form.dimensions" placeholder="12x10x8 in" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="international_tracking-{{ $this->getId() }}">{{ __('International Tracking') }}</label>
                                <input id="international_tracking-{{ $this->getId() }}" name="international_tracking" type="text" wire:model="form.international_tracking" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="shipping_cost-{{ $this->getId() }}">{{ __('Shipping Cost') }} (USD)</label>
                                <input id="shipping_cost-{{ $this->getId() }}" name="shipping_cost" type="number" step="0.01" min="0" wire:model="form.shipping_cost" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="shipped_at-{{ $this->getId() }}">{{ __('Shipped At') }}</label>
                                <input id="shipped_at-{{ $this->getId() }}" name="shipped_at" type="date" wire:model="form.shipped_at" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="delivered_at-{{ $this->getId() }}">{{ __('Delivered At') }}</label>
                                <input id="delivered_at-{{ $this->getId() }}" name="delivered_at" type="date" wire:model="form.delivered_at" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Consolidate Packages') }}</label>
                                <div class="max-h-48 overflow-y-auto rounded-lg border border-gray-200">
                                    @forelse ($availablePackages as $package)
                                        <label class="flex cursor-pointer items-center gap-3 px-4 py-2.5 hover:bg-gray-50" for="package_ids-{{ $package->id }}">
                                            <input id="package_ids-{{ $package->id }}" name="package_ids" type="checkbox" wire:model.live="form.package_ids" value="{{ $package->id }}"
                                                   class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                            <span class="font-mono text-xs">{{ $package->number }}</span>
                                            <span class="flex-1 truncate text-sm">{{ $package->store ?? '—' }}</span>
                                            <span class="text-xs text-gray-400">{{ $package->weight_lb ? $package->weight_lb . ' lb' : '' }}</span>
                                        </label>
                                    @empty
                                        <p class="px-4 py-6 text-center text-sm text-gray-500">
                                            {{ $form['customer_id'] ? __('No packages assigned.') : __('Select a customer first.') }}
                                        </p>
                                    @endforelse
                                </div>
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
