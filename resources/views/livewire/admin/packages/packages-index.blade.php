<div>
    <x-slot name="header">{{ __('Packages') }}</x-slot>

    <!-- Packages Quick Filter Badges -->
    <div class="mb-4 grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3">
        <button type="button" wire:click="setFilter('all')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition-all {{ $filter === 'all' && $status === 'all' ? 'border-emerald-500 bg-emerald-50/50 ring-1 ring-emerald-500' : 'border-gray-200 bg-white hover:border-gray-300' }}">
            <div>
                <p class="text-[11px] font-medium text-gray-500">{{ __('Total Packages') }}</p>
                <p class="text-base font-bold text-gray-900 sm:text-lg">{{ $totalCount }}</p>
            </div>
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 text-xs">
                <i class="fa-solid fa-boxes-stacked"></i>
            </span>
        </button>

        <button type="button" wire:click="setFilter('today')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition-all {{ $filter === 'today' ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-500' : 'border-gray-200 bg-white hover:border-gray-300' }}">
            <div>
                <p class="text-[11px] font-medium text-gray-500">{{ __('Received today') }}</p>
                <p class="text-base font-bold text-gray-900 sm:text-lg">{{ $receivedTodayCount }}</p>
            </div>
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-blue-700 text-xs">
                <i class="fa-solid fa-calendar-day"></i>
            </span>
        </button>

        <button type="button" wire:click="setFilter('stored')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition-all {{ $filter === 'stored' ? 'border-teal-500 bg-teal-50/50 ring-1 ring-teal-500' : 'border-gray-200 bg-white hover:border-gray-300' }}">
            <div>
                <p class="text-[11px] font-medium text-gray-500">{{ __('Stored Packages') }}</p>
                <p class="text-base font-bold text-gray-900 sm:text-lg">{{ $storedCount }}</p>
            </div>
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-teal-100 text-teal-700 text-xs">
                <i class="fa-solid fa-warehouse"></i>
            </span>
        </button>

        <button type="button" wire:click="setFilter('ready')"
                class="flex items-center justify-between rounded-xl border p-3 text-left transition-all {{ $filter === 'ready' || $status === 'ready' ? 'border-amber-500 bg-amber-50/50 ring-1 ring-amber-500' : 'border-gray-200 bg-white hover:border-gray-300' }}">
            <div>
                <p class="text-[11px] font-medium text-gray-500">{{ __('Ready to ship') }}</p>
                <p class="text-base font-bold text-gray-900 sm:text-lg">{{ $readyCount }}</p>
            </div>
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-100 text-amber-700 text-xs">
                <i class="fa-solid fa-box-check"></i>
            </span>
        </button>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-gray-200/80 p-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center flex-1 min-w-0">
                <x-search-input model="search" placeholder="{{ __('Buscar por N° paquete, tracking, cliente, tienda...') }}" class="flex-1 max-w-lg" />

                <select name="status" wire:model.live="status" aria-label="{{ __('Filter by status') }}"
                        class="filter-select border border-gray-200/90 bg-gray-50/70 font-medium text-gray-700 transition-all hover:border-gray-300 hover:bg-white focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/15 shadow-2xs cursor-pointer">
                    <option value="all">{{ __('All statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <button wire:click="openCreate" type="button"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow-2xs hover:bg-emerald-700 active:scale-95 transition-all shrink-0">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>{{ __('New Package') }}</span>
            </button>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Number') }}</th>
                        <th class="px-4 py-3">{{ __('Customer') }}</th>
                        <th class="px-4 py-3">{{ __('Store') }}</th>
                        <th class="px-4 py-3">{{ __('Original Tracking') }}</th>
                        <th class="px-4 py-3">{{ __('Received At') }}</th>
                        <th class="px-4 py-3">{{ __('Weight (lb)') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($packages as $package)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">
                                <a href="{{ route('admin.packages.show', $package) }}" wire:navigate class="font-medium text-emerald-600 hover:text-emerald-800">{{ $package->number }}</a>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $package->customer?->name ?? __('Unknown') }}</td>
                            <td class="px-4 py-3 text-xs">{{ $package->store ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-xs">
                                @if ($package->original_tracking)
                                    <button type="button" data-copy="{{ $package->original_tracking }}"
                                            data-title="{{ __('Original Tracking') }}" data-copied="{{ __('Copied') }}"
                                            class="group/track inline-flex items-center gap-1.5 hover:text-emerald-600">
                                        {{ $package->original_tracking }}
                                        <i class="fa-solid fa-copy text-sm opacity-0 transition-opacity group-hover/track:opacity-100"></i>
                                    </button>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $package->received_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs">{{ $package->weight_lb ?? '—' }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$package->status" /></td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.packages.show', $package) }}" wire:navigate
                                       class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Show') }}">
                                        <i class="fa-solid fa-eye text-base"></i>
                                    </a>
                                    <button wire:click="edit({{ $package->id }})"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Edit') }}">
                                        <i class="fa-solid fa-pen-to-square text-base"></i>
                                    </button>
                                    <button @click="swalConfirmDelete(() => $wire.delete({{ $package->id }}))"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600" title="{{ __('Delete') }}">
                                        <i class="fa-solid fa-trash text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
    <td colspan="99">
        <x-empty-state :message="__('No records found.')" icon="box" />
    </td>
</tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <ul class="divide-y divide-gray-100 md:hidden">
            @forelse ($packages as $package)
                <li>
                    <a href="{{ route('admin.packages.show', $package) }}" wire:navigate class="block px-4 py-4 transition-colors hover:bg-emerald-50/40">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-900">{{ $package->store ?? __('Package') }}</p>
                                <p class="mt-0.5 truncate text-xs text-gray-500">
                                    <span class="font-mono text-gray-400">{{ $package->number }}</span>
                                    · {{ $package->customer?->name ?? __('Unknown') }}
                                </p>
                            </div>
                            <x-status-badge :status="$package->status" />
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                            @if ($package->original_tracking)
                                <button type="button" data-copy="{{ $package->original_tracking }}"
                                        data-title="{{ __('Original Tracking') }}" data-copied="{{ __('Copied') }}"
                                        class="group/track inline-flex items-center gap-1 font-mono text-emerald-700">
                                    <i class="fa-solid fa-copy text-xs opacity-60"></i>
                                    {{ $package->original_tracking }}
                                </button>
                            @endif
                            <span>{{ $package->received_at?->format('Y-m-d') ?? '—' }}</span>
                            @if ($package->weight_lb)
                                <span class="ms-auto font-semibold text-gray-900">{{ $package->weight_lb }} lb</span>
                            @endif
                        </div>
                    </a>
                </li>
            @empty
                <li>
                    <x-empty-state :message="__('No records found.')" icon="box" />
                </li>
            @endforelse
        </ul>

        <div class="border-t border-gray-200 p-4">
            {{ $packages->links() }}
        </div>
    </div>

    <x-modal maxWidth="max-w-3xl">
        <form wire:submit="save">
                        <x-modal-header :title="$editingId ? __('Edit Package') : __('New Package')" />

                        <x-modal-body class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="customer_id-{{ $this->getId() }}">{{ __('Customer') }} *</label>
                                <x-customer-search :customers="$customers" />
                                @error('form.customer_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="purchase_request_id-{{ $this->getId() }}">{{ __('Request') }}</label>
                                <select id="purchase_request_id-{{ $this->getId() }}" name="purchase_request_id" wire:model="form.purchase_request_id" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                                    <option value="">—</option>
                                    @php $reqs = $form['customer_id'] ? \App\Models\PurchaseRequest::where('customer_id', $form['customer_id'])->latest()->get() : collect(); @endphp
                                    @foreach ($reqs as $req)
                                        <option value="{{ $req->id }}">{{ $req->number }} — {{ $req->product_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="store-{{ $this->getId() }}">{{ __('Store') }}</label>
                                <input id="store-{{ $this->getId() }}" name="store" type="text" wire:model="form.store" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="original_tracking-{{ $this->getId() }}">{{ __('Original Tracking') }}</label>
                                <input id="original_tracking-{{ $this->getId() }}" name="original_tracking" type="text" wire:model="form.original_tracking" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="received_at-{{ $this->getId() }}">{{ __('Received At') }}</label>
                                <input id="received_at-{{ $this->getId() }}" name="received_at" type="date" wire:model="form.received_at" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="weight_lb-{{ $this->getId() }}">{{ __('Weight (lb)') }}</label>
                                <input id="weight_lb-{{ $this->getId() }}" name="weight_lb" type="number" step="0.01" min="0" wire:model="form.weight_lb" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="location-{{ $this->getId() }}">{{ __('Location') }}</label>
                                <input id="location-{{ $this->getId() }}" name="location" type="text" wire:model="form.location" placeholder="Estante A-1" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="photo-{{ $this->getId() }}">{{ __('Photo') }}</label>
                                <input id="photo-{{ $this->getId() }}" name="photo" type="file" accept="image/*" wire:model="photo" class="block w-full text-sm text-gray-500 file:me-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-emerald-700 hover:file:bg-emerald-100">
                                @error('photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                @if ($photo)
                                    <img src="{{ $photo->temporaryUrl() }}" alt="{{ __('Preview') }}" class="mt-2 h-24 w-24 rounded-lg object-cover">
                                @endif
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
