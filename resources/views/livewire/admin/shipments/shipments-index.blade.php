<div>
    <x-slot name="header">{{ __('Shipments') }}</x-slot>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-gray-200/80 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center flex-1 min-w-0">
                <x-search-input model="search" placeholder="{{ __('Buscar por N° envío, transportista, tracking, cliente...') }}" class="w-full sm:max-w-md" />

                <select name="status" wire:model.live="status" aria-label="{{ __('Filter by status') }}"
                        class="h-[34px] rounded-xl border border-gray-200/90 bg-gray-50/70 py-1.5 px-3 text-xs font-medium text-gray-700 transition-all hover:border-gray-300 hover:bg-white focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/15 shadow-2xs">
                    <option value="all">{{ __('All statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <button wire:click="openCreate" type="button"
                    class="inline-flex h-[34px] items-center gap-1.5 rounded-xl bg-emerald-600 px-4 text-xs font-semibold text-white shadow-2xs hover:bg-emerald-700 active:scale-95 transition-all shrink-0">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>{{ __('New Shipment') }}</span>
            </button>
        </div>

        <div class="hidden md:block overflow-x-auto">
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
                                        <i class="fa-solid fa-eye text-base"></i>
                                    </a>
                                    <button wire:click="edit({{ $shipment->id }})"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Edit') }}">
                                        <i class="fa-solid fa-pen-to-square text-base"></i>
                                    </button>
                                    <button @click="swalConfirmDelete(() => $wire.delete({{ $shipment->id }}))"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600" title="{{ __('Delete') }}">
                                        <i class="fa-solid fa-trash text-base"></i>
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

        <ul class="divide-y divide-gray-100 md:hidden">
            @forelse ($shipments as $shipment)
                <li>
                    <a href="{{ route('admin.shipments.show', $shipment) }}" wire:navigate class="block px-4 py-4 transition-colors hover:bg-emerald-50/40">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-900">
                                    <span class="font-mono text-xs">{{ $shipment->number }}</span>
                                    <span class="ms-2">{{ $shipment->carrier ?? __('Shipment') }}</span>
                                </p>
                                <p class="mt-0.5 truncate text-xs text-gray-500">{{ $shipment->customer?->name ?? __('Unknown') }} · {{ country_name($shipment->destination_country) }}</p>
                            </div>
                            <x-status-badge :status="$shipment->status" />
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 font-medium text-gray-700">{{ $shipment->packages_count }} {{ __('packages') }}</span>
                            @if ($shipment->international_tracking)
                                <span class="font-mono text-gray-400">{{ $shipment->international_tracking }}</span>
                            @endif
                            <span class="ms-auto font-semibold text-gray-900">{{ $shipment->shipping_cost !== null ? money($shipment->shipping_cost) : '—' }}</span>
                        </div>
                    </a>
                </li>
            @empty
                <li>
                    <x-empty-state :message="__('No records found.')" icon="ship" />
                </li>
            @endforelse
        </ul>

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
                                    @foreach (['VE', 'CO', 'EC', 'PE', 'CL', 'CR', 'PA', 'DO', 'SV', 'HN', 'MX', 'US'] as $code)
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
