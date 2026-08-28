<div>
    <x-slot name="header">{{ __('My Requests') }}</x-slot>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="flex items-center justify-between border-b border-gray-200 p-4">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Purchase Requests') }}</h2>
            <button wire:click="openForm" type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                <i class="fa-solid fa-plus text-base"></i>
                {{ __('New Request') }}
            </button>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Number') }}</th>
                        <th class="px-4 py-3">{{ __('Product') }}</th>
                        <th class="px-4 py-3">{{ __('Store') }}</th>
                        <th class="px-4 py-3">{{ __('Total') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">{{ $request->number }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $request->product_name }}</td>
                            <td class="px-4 py-3 text-xs">{{ $request->store ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs">{{ money($request->total_cost) }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$request->status" /></td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('portal.requests.show', $request) }}" wire:navigate class="text-xs font-medium text-emerald-600 hover:text-emerald-800">
                                    {{ __('Show') }}
                                </a>
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
                <li>
                    <a href="{{ route('portal.requests.show', $request) }}" wire:navigate class="block px-4 py-4 transition-colors hover:bg-emerald-50/40">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-900">{{ $request->product_name }}</p>
                                <p class="mt-0.5 font-mono text-xs text-gray-400">{{ $request->number }}</p>
                            </div>
                            <x-status-badge :status="$request->status" />
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                            @if ($request->store)
                                <span class="inline-flex items-center gap-1"><i class="fa-solid fa-store text-xs"></i> {{ $request->store }}</span>
                            @endif
                            <span class="ms-auto font-semibold text-gray-900">{{ money($request->total_cost) }}</span>
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
                        <x-modal-header :title="__('New Purchase Request')" />

                        <x-modal-body class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="product_name-{{ $this->getId() }}">{{ __('Product Name') }} *</label>
                                <input id="product_name-{{ $this->getId() }}" name="product_name" type="text" wire:model="form.product_name" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                                @error('form.product_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="product_url-{{ $this->getId() }}">{{ __('Product URL') }}</label>
                                <input id="product_url-{{ $this->getId() }}" name="product_url" type="url" wire:model="form.product_url" placeholder="https://..." class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                                @error('form.product_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="store-{{ $this->getId() }}">{{ __('Store') }}</label>
                                <input id="store-{{ $this->getId() }}" name="store" type="text" wire:model="form.store" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
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
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="description-{{ $this->getId() }}">{{ __('Description') }}</label>
                                <textarea id="description-{{ $this->getId() }}" name="description" wire:model="form.description" rows="3" placeholder="{{ __('Send us the links of the products you want') }}..." class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"></textarea>
                            </div>
                        </x-modal-body>

                        <x-modal-footer>
                            <button type="button" wire:click="closeForm"
                                    class="rounded-xl border border-gray-200/80 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-200/50">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                    class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/30">
                                {{ __('Send Request') }}
                            </button>
                        </x-modal-footer>
        </form>
    </x-modal>
</div>
