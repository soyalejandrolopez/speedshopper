<div>
    <x-slot name="header">{{ __('Customers') }}</x-slot>

    <!-- Customers Summary KPI Cards -->
    <div class="mb-4 grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3">
        <div class="rounded-xl border border-gray-200 bg-white p-3">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-medium text-gray-500">{{ __('Total Customers') }}</p>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 text-xs">
                    <i class="fa-solid fa-users"></i>
                </span>
            </div>
            <p class="mt-1 text-base font-bold text-gray-900 sm:text-lg">{{ $totalCustomers }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-medium text-gray-500">{{ __('Con Solicitudes') }}</p>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-100 text-amber-700 text-xs">
                    <i class="fa-solid fa-clipboard-list"></i>
                </span>
            </div>
            <p class="mt-1 text-base font-bold text-gray-900 sm:text-lg">{{ $customersWithRequests }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-medium text-gray-500">{{ __('Con Paquetes') }}</p>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-teal-100 text-teal-700 text-xs">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </span>
            </div>
            <p class="mt-1 text-base font-bold text-gray-900 sm:text-lg">{{ $customersWithPackages }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-medium text-gray-500">{{ __('Con Envíos') }}</p>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-blue-700 text-xs">
                    <i class="fa-solid fa-plane-departure"></i>
                </span>
            </div>
            <p class="mt-1 text-base font-bold text-gray-900 sm:text-lg">{{ $customersWithShipments }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-gray-200/80 p-4 sm:flex-row sm:items-center sm:justify-between">
            <x-search-input model="search" placeholder="{{ __('Buscar por nombre, código CUST, email, teléfono...') }}" class="w-full sm:max-w-md" />

            <button wire:click="openCreate" type="button"
                    class="inline-flex h-[34px] items-center gap-1.5 rounded-xl bg-emerald-600 px-4 text-xs font-semibold text-white shadow-2xs hover:bg-emerald-700 active:scale-95 transition-all shrink-0">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>{{ __('New Customer') }}</span>
            </button>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Number') }}</th>
                        <th class="px-4 py-3">{{ __('Name') }}</th>
                        <th class="px-4 py-3">{{ __('Email') }}</th>
                        <th class="px-4 py-3">{{ __('WhatsApp') }}</th>
                        <th class="px-4 py-3">{{ __('Country') }}</th>
                        <th class="px-4 py-3">{{ __('Balance Due') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">
                                <a href="{{ route('admin.customers.show', $customer) }}" wire:navigate class="font-medium text-emerald-600 hover:text-emerald-800">{{ $customer->number }}</a>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.customers.show', $customer) }}" wire:navigate class="font-medium text-emerald-600 hover:text-emerald-800">
                                    {{ $customer->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $customer->email ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs">{{ $customer->whatsapp ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs">{{ country_name($customer->country) }}</td>
                            <td class="px-4 py-3 text-xs {{ $customer->balance_due > 0 ? 'font-medium text-amber-600' : 'text-emerald-600' }}">
                                {{ money($customer->balance_due) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.customers.show', $customer) }}" wire:navigate
                                       class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Show') }}">
                                        <i class="fa-solid fa-eye text-base"></i>
                                    </a>
                                    <button wire:click="edit({{ $customer->id }})"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" title="{{ __('Edit') }}">
                                        <i class="fa-solid fa-pen-to-square text-base"></i>
                                    </button>
                                    <button @click="swalConfirmDelete(() => $wire.delete({{ $customer->id }}))"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600" title="{{ __('Delete') }}">
                                        <i class="fa-solid fa-trash text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
    <td colspan="99">
        <x-empty-state :message="__('No records found.')" icon="users" />
    </td>
</tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <ul class="divide-y divide-gray-100 md:hidden">
            @forelse ($customers as $customer)
                <li>
                    <a href="{{ route('admin.customers.show', $customer) }}" wire:navigate class="block px-4 py-4 transition-colors hover:bg-emerald-50/40">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-900">{{ $customer->name }}</p>
                                <p class="mt-0.5 truncate text-xs text-gray-500">
                                    <span class="font-mono text-gray-400">{{ $customer->number }}</span>
                                    @if ($customer->email)
                                        · {{ $customer->email }}
                                    @endif
                                </p>
                            </div>
                            <span class="shrink-0 text-xs {{ $customer->balance_due > 0 ? 'font-semibold text-amber-600' : 'font-medium text-emerald-600' }}">
                                {{ money($customer->balance_due) }}
                            </span>
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                            @if ($customer->whatsapp)
                                <span class="inline-flex items-center gap-1">
                                    <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                                    {{ $customer->whatsapp }}
                                </span>
                            @endif
                            <span>{{ country_name($customer->country) }}</span>
                            <span class="ms-auto inline-flex items-center gap-1 text-emerald-600">
                                {{ __('Show') }}
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </span>
                        </div>
                    </a>
                </li>
            @empty
                <li>
                    <x-empty-state :message="__('No records found.')" icon="users" />
                </li>
            @endforelse
        </ul>

        <div class="border-t border-gray-200 p-4">
            {{ $customers->links() }}
        </div>
    </div>

    <x-modal maxWidth="max-w-3xl">
        <form wire:submit="save">
                        <x-modal-header :title="$editingId ? __('Edit Customer') : __('New Customer')" />

                        <x-modal-body class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="name-{{ $this->getId() }}">{{ __('Name') }} *</label>
                                <input id="name-{{ $this->getId() }}" name="name" type="text" wire:model="form.name" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                                @error('form.name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="email-{{ $this->getId() }}">{{ __('Email') }}</label>
                                <input id="email-{{ $this->getId() }}" name="email" type="email" wire:model="form.email" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                                @error('form.email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="phone-{{ $this->getId() }}">{{ __('Phone') }}</label>
                                <input id="phone-{{ $this->getId() }}" name="phone" type="text" wire:model="form.phone" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="whatsapp-{{ $this->getId() }}">{{ __('WhatsApp') }}</label>
                                <input id="whatsapp-{{ $this->getId() }}" name="whatsapp" type="text" wire:model="form.whatsapp" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="country-{{ $this->getId() }}">{{ __('Country') }}</label>
                                <select id="country-{{ $this->getId() }}" name="country" wire:model="form.country" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                                    <option value="">—</option>
                                    @foreach (['MX', 'GT', 'HN', 'SV', 'NI', 'CR', 'PA', 'CO', 'EC', 'PE', 'CL', 'AR', 'US'] as $code)
                                        <option value="{{ $code }}">{{ country_name($code) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="address-{{ $this->getId() }}">{{ __('Address') }}</label>
                                <input id="address-{{ $this->getId() }}" name="address" type="text" wire:model="form.address" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="city-{{ $this->getId() }}">{{ __('City') }}</label>
                                <input id="city-{{ $this->getId() }}" name="city" type="text" wire:model="form.city" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="registered_at-{{ $this->getId() }}">{{ __('Date') }}</label>
                                <input id="registered_at-{{ $this->getId() }}" name="registered_at" type="date" wire:model="form.registered_at" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700" for="notes-{{ $this->getId() }}">{{ __('Notes') }}</label>
                                <textarea id="notes-{{ $this->getId() }}" name="notes" wire:model="form.notes" rows="3" class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"></textarea>
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
