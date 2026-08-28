<div>
    <x-slot name="header">{{ __('Packages') }}</x-slot>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-gray-200 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-2 sm:flex-row">
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

                <select name="status" wire:model.live="status" aria-label="{{ __('Filter by status') }}" class="rounded-lg border border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="all">{{ __('All statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <button wire:click="openCreate" type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                <i class="fa-solid fa-plus text-base"></i>
                {{ __('New Package') }}
            </button>
        </div>

        <div class="overflow-x-auto">
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
                                    <button wire:click="delete({{ $package->id }})"
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
        <x-empty-state :message="__('No records found.')" icon="box" />
    </td>
</tr>
                    @endforelse
                </tbody>
            </table>
        </div>

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
                                    <img src="{{ $photo->temporaryUrl() }}" class="mt-2 h-24 w-24 rounded-lg object-cover">
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
