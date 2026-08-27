<div>
    <x-slot name="header">{{ __('Packages') }}</x-slot>

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
                        class="w-full rounded-lg border-gray-300 text-sm ps-9 sm:w-64 focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <select name="status" wire:model.live="status" aria-label="{{ __('Filter by status') }}" class="rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
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
                            <td class="px-4 py-3 text-xs">{{ $package->customer->name }}</td>
                            <td class="px-4 py-3 text-xs">{{ $package->store ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-xs">
                                @if ($package->original_tracking)
                                    <button type="button" data-copy="{{ $package->original_tracking }}"
                                            data-title="{{ __('Original Tracking') }}" data-copied="{{ __('Copied') }}"
                                            class="group/track inline-flex items-center gap-1.5 hover:text-emerald-600">
                                        {{ $package->original_tracking }}
                                        <svg class="h-3.5 w-3.5 opacity-0 transition-opacity group-hover/track:opacity-100" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                                        </svg>
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
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>
                                    <button wire:click="edit({{ $package->id }})"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Edit') }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $package->id }})"
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

    <x-modal maxWidth="max-w-2xl">
        <form wire:submit="save">
                        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                            <h3 class="text-base font-semibold text-gray-900">{{ $editingId ? __('Edit Package') : __('New Package') }}</h3>
                            <button type="button" wire:click="closeForm" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="grid gap-4 p-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="customer_id-{{ $this->getId() }}">{{ __('Customer') }} *</label>
                                <select id="customer_id-{{ $this->getId() }}" name="customer_id" wire:model.live="form.customer_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">—</option>
                                    @foreach ($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->number }})</option>
                                    @endforeach
                                </select>
                                @error('form.customer_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="purchase_request_id-{{ $this->getId() }}">{{ __('Request') }}</label>
                                <select id="purchase_request_id-{{ $this->getId() }}" name="purchase_request_id" wire:model="form.purchase_request_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">—</option>
                                    @php $reqs = $form['customer_id'] ? \App\Models\PurchaseRequest::where('customer_id', $form['customer_id'])->latest()->get() : collect(); @endphp
                                    @foreach ($reqs as $req)
                                        <option value="{{ $req->id }}">{{ $req->number }} — {{ $req->product_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="store-{{ $this->getId() }}">{{ __('Store') }}</label>
                                <input id="store-{{ $this->getId() }}" name="store" type="text" wire:model="form.store" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="original_tracking-{{ $this->getId() }}">{{ __('Original Tracking') }}</label>
                                <input id="original_tracking-{{ $this->getId() }}" name="original_tracking" type="text" wire:model="form.original_tracking" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="received_at-{{ $this->getId() }}">{{ __('Received At') }}</label>
                                <input id="received_at-{{ $this->getId() }}" name="received_at" type="date" wire:model="form.received_at" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="weight_lb-{{ $this->getId() }}">{{ __('Weight (lb)') }}</label>
                                <input id="weight_lb-{{ $this->getId() }}" name="weight_lb" type="number" step="0.01" min="0" wire:model="form.weight_lb" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="location-{{ $this->getId() }}">{{ __('Location') }}</label>
                                <input id="location-{{ $this->getId() }}" name="location" type="text" wire:model="form.location" placeholder="Estante A-1" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
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
                                <textarea id="notes-{{ $this->getId() }}" name="notes" wire:model="form.notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 border-t border-gray-200 px-5 py-4">
                            <button type="button" wire:click="closeForm"
                                    class="rounded-lg border border-gray-300 px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                    class="rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                                {{ __('Save') }}
                            </button>
                        </div>
        </form>
    </x-modal>
</div>
