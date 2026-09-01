<div>
    <x-slot name="header">{{ __('Purchase Requests') }}</x-slot>

    <!-- Requests Quick Filter Badges -->
    <div class="mb-4 grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3">
        <button type="button" wire:click="setStatus('all')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition-all {{ $status === 'all' ? 'border-emerald-500 bg-emerald-50/50 ring-1 ring-emerald-500' : 'border-gray-200 bg-white hover:border-gray-300' }}">
            <div>
                <p class="text-[11px] font-medium text-gray-500">{{ __('Total Requests') }}</p>
                <p class="text-base font-bold text-gray-900 sm:text-lg">{{ $totalCount }}</p>
            </div>
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 text-xs">
                <i class="fa-solid fa-list-check"></i>
            </span>
        </button>

        <button type="button" wire:click="setStatus('open')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition-all {{ $status === 'open' ? 'border-amber-500 bg-amber-50/50 ring-1 ring-amber-500' : 'border-gray-200 bg-white hover:border-gray-300' }}">
            <div>
                <p class="text-[11px] font-medium text-gray-500">{{ __('Open Requests') }}</p>
                <p class="text-base font-bold text-gray-900 sm:text-lg">{{ $openCount }}</p>
            </div>
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-100 text-amber-700 text-xs">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </span>
        </button>

        <button type="button" wire:click="setStatus('delivered')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition-all {{ $status === 'delivered' ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-500' : 'border-gray-200 bg-white hover:border-gray-300' }}">
            <div>
                <p class="text-[11px] font-medium text-gray-500">{{ __('Delivered') }}</p>
                <p class="text-base font-bold text-gray-900 sm:text-lg">{{ $deliveredCount }}</p>
            </div>
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-blue-700 text-xs">
                <i class="fa-solid fa-circle-check"></i>
            </span>
        </button>

        <button type="button" wire:click="setStatus('cancelled')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition-all {{ $status === 'cancelled' ? 'border-red-500 bg-red-50/50 ring-1 ring-red-500' : 'border-gray-200 bg-white hover:border-gray-300' }}">
            <div>
                <p class="text-[11px] font-medium text-gray-500">{{ __('Cancelled') }}</p>
                <p class="text-base font-bold text-gray-900 sm:text-lg">{{ $cancelledCount }}</p>
            </div>
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-100 text-red-700 text-xs">
                <i class="fa-solid fa-ban"></i>
            </span>
        </button>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-gray-200/80 p-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center flex-1 min-w-0">
                <x-search-input model="search" placeholder="{{ __('Buscar por N° orden, producto, cliente, tienda...') }}" class="flex-1 max-w-lg" />

                <div class="flex items-center gap-2 shrink-0">
                    <select name="status" wire:model.live="status" aria-label="{{ __('Filter by status') }}"
                            class="filter-select border border-gray-200/90 bg-gray-50/70 font-medium text-gray-700 transition-all hover:border-gray-300 hover:bg-white focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/15 shadow-2xs cursor-pointer">
                        <option value="all">{{ __('All statuses') }}</option>
                        <option value="open">{{ __('Open Requests') }}</option>
                        @foreach ($statuses as $statusOption)
                            <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                        @endforeach
                    </select>

                    @if ($customer)
                        <span class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50/80 px-3 py-2 text-xs font-semibold text-emerald-800 shadow-2xs">
                            <i class="fa-solid fa-user text-[10px] text-emerald-600"></i>
                            <span class="truncate max-w-[140px]">{{ $customers->firstWhere('id', $customer)?->name }}</span>
                            <button wire:click="$set('customer', null)" class="text-emerald-500 hover:text-emerald-700 transition-colors" title="{{ __('Quitar filtro') }}">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </span>
                    @endif
                </div>
            </div>

            <button wire:click="openCreate" type="button"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow-2xs hover:bg-emerald-700 active:scale-95 transition-all shrink-0">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>{{ __('New Request') }}</span>
            </button>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Number') }}</th>
                        <th class="px-4 py-3">{{ __('Customer') }}</th>
                        <th class="px-4 py-3">{{ __('Product') }}</th>
                        <th class="px-4 py-3">{{ __('Store') }}</th>
                        <th class="px-4 py-3">{{ __('Presupuesto') }}</th>
                        <th class="px-4 py-3">{{ __('Total') }}</th>
                        <th class="px-4 py-3">{{ __('Ganancia') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $request)
                        @php
                            $productCost = (float) $request->costItems->where('type', \App\Enums\CostType::ProductCost)->sum('amount');
                            if ($productCost == 0.0 && $request->unit_price) {
                                $productCost = (float) $request->unit_price * max(1, $request->quantity);
                            }
                            $earnings = (float) $request->costItems->where('type', '!=', \App\Enums\CostType::ProductCost)->sum('amount');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">
                                <a href="{{ route('admin.requests.show', $request) }}" wire:navigate class="font-medium text-emerald-600 hover:text-emerald-800">{{ $request->number }}</a>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $request->customer?->name ?? __('Unknown') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.requests.show', $request) }}" wire:navigate class="font-medium text-emerald-600 hover:text-emerald-800">
                                    {{ $request->product_name }}
                                </a>
                                @if ($request->size_color)
                                    <span class="block text-xs text-gray-400">{{ $request->size_color }} · ×{{ $request->quantity }}</span>
                                @endif
                                @if (! empty($request->services))
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach (service_definitions() as $sKey => $sDef)
                                            @if (in_array($sKey, $request->services, true))
                                                <span class="inline-flex items-center gap-1 rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-medium text-gray-700">
                                                    <i class="fa-solid {{ $sDef['icon'] }} text-[9px] text-gray-500"></i>
                                                    {{ $sDef['title'] }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $request->store ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs font-medium text-gray-600">{{ $productCost > 0 ? money($productCost) : '—' }}</td>
                            <td class="px-4 py-3 text-xs font-bold text-gray-900">{{ money($request->total_cost) }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="inline-flex items-center gap-1 font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 shadow-2xs">
                                    💰 {{ money($earnings) }}
                                </span>
                            </td>
                            <td class="px-4 py-3"><x-status-badge :status="$request->status" /></td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.requests.show', $request) }}" wire:navigate
                                       class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Show') }}">
                                        <i class="fa-solid fa-eye text-base"></i>
                                    </a>
                                    <button wire:click="edit({{ $request->id }})"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Edit') }}">
                                        <i class="fa-solid fa-pen-to-square text-base"></i>
                                    </button>
                                    <button @click="swalConfirmDelete(() => $wire.delete({{ $request->id }}))"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600" title="{{ __('Delete') }}">
                                        <i class="fa-solid fa-trash text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
    <td colspan="99">
        <x-empty-state :message="__('No records found.')" icon="inbox" />
    </td>
</tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <ul class="divide-y divide-gray-100 md:hidden">
            @forelse ($requests as $request)
                @php
                    $productCost = (float) $request->costItems->where('type', \App\Enums\CostType::ProductCost)->sum('amount');
                    if ($productCost == 0.0 && $request->unit_price) {
                        $productCost = (float) $request->unit_price * max(1, $request->quantity);
                    }
                    $earnings = (float) $request->costItems->where('type', '!=', \App\Enums\CostType::ProductCost)->sum('amount');
                @endphp
                <li>
                    <a href="{{ route('admin.requests.show', $request) }}" wire:navigate class="block px-4 py-4 transition-colors hover:bg-emerald-50/40">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-900">{{ $request->product_name }}</p>
                                <p class="mt-0.5 truncate text-xs text-gray-500">
                                    <span class="font-mono text-gray-400">{{ $request->number }}</span>
                                    · {{ $request->customer?->name ?? __('Unknown') }}
                                </p>
                            </div>
                            <x-status-badge :status="$request->status" />
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                            @if ($request->store)
                                <span class="inline-flex items-center gap-1"><i class="fa-solid fa-store text-xs"></i> {{ $request->store }}</span>
                            @endif
                            @if ($productCost > 0)
                                <span class="text-gray-600 font-medium">{{ __('Presupuesto:') }} {{ money($productCost) }}</span>
                            @endif
                            <span class="text-emerald-700 font-bold">💰 {{ __('Ganancia:') }} {{ money($earnings) }}</span>
                            <span class="ms-auto font-bold text-gray-900">{{ money($request->total_cost) }}</span>
                        </div>
                    </a>
                </li>
            @empty
                <li>
                    <x-empty-state :message="__('No records found.')" icon="inbox" />
                </li>
            @endforelse
        </ul>

        <div class="border-t border-gray-200 p-4">
            {{ $requests->links() }}
        </div>
    </div>

    <x-modal maxWidth="max-w-3xl">
        <form wire:submit="save">
                        <x-modal-header :title="$editingId ? __('Edit') : __('New Request')" />

                        <x-modal-body class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="customer_id-{{ $this->getId() }}">{{ __('Customer') }} *</label>
                                <x-customer-search :customers="$customers" />
                                @error('form.customer_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="store-{{ $this->getId() }}">{{ __('Store') }}</label>
                                <input id="store-{{ $this->getId() }}" name="store" type="text" wire:model="form.store" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="product_name-{{ $this->getId() }}">{{ __('Product Name') }} *</label>
                                <input id="product_name-{{ $this->getId() }}" name="product_name" type="text" wire:model="form.product_name" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                                @error('form.product_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="product_url-{{ $this->getId() }}">{{ __('Product URL') }}</label>
                                <input id="product_url-{{ $this->getId() }}" name="product_url" type="url" wire:model="form.product_url" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                                @error('form.product_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="size_color-{{ $this->getId() }}">{{ __('Size/Color') }}</label>
                                <input id="size_color-{{ $this->getId() }}" name="size_color" type="text" wire:model="form.size_color" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="quantity-{{ $this->getId() }}">{{ __('Quantity') }}</label>
                                <input id="quantity-{{ $this->getId() }}" name="quantity" type="number" min="1" wire:model="form.quantity" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="unit_price-{{ $this->getId() }}">{{ __('Unit Price') }} (USD)</label>
                                <input id="unit_price-{{ $this->getId() }}" name="unit_price" type="number" step="0.01" min="0" wire:model="form.unit_price" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="discount_found-{{ $this->getId() }}">{{ __('Discount Found') }} (USD)</label>
                                <input id="discount_found-{{ $this->getId() }}" name="discount_found" type="number" step="0.01" min="0" wire:model="form.discount_found" class="w-full rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="description-{{ $this->getId() }}">{{ __('Description') }}</label>
                                <textarea id="description-{{ $this->getId() }}" name="description" wire:model="form.description" rows="2" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"></textarea>
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
