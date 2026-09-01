@props([
    'customers' => [],
    'model' => 'form.customer_id',
    'searchModel' => 'form.customer_search',
])

@php
    $customerList = collect($customers)->map(function ($c) {
        return is_array($c) ? $c : [
            'id' => $c->id,
            'name' => (string) $c->name,
            'number' => (string) ($c->number ?? ''),
            'email' => (string) ($c->email ?? ''),
            'phone' => (string) ($c->phone ?? $c->whatsapp ?? ''),
            'country' => (string) ($c->country ?? ''),
        ];
    })->values();
@endphp

<div class="relative group" x-data="{
    open: false,
    search: $wire.entangle('{{ $searchModel }}'),
    customers: {{ \Illuminate\Support\Js::from($customerList) }},
    get filtered() {
        if (! this.search || typeof this.search !== 'string') {
            return this.customers;
        }
        const q = this.search.toLowerCase().trim();
        return this.customers.filter(c => {
            const name = (c.name || '').toLowerCase();
            const num = (c.number || '').toLowerCase();
            const email = (c.email || '').toLowerCase();
            const phone = (c.phone || '').toLowerCase();
            return name.includes(q) || num.includes(q) || email.includes(q) || phone.includes(q);
        });
    },
    pick(c) {
        $wire.selectCustomer(c.id, c.name);
        this.search = c.name;
        this.open = false;
    },
    clear() {
        this.search = '';
        this.open = false;
    }
}">
    <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3.5 text-gray-400 group-focus-within:text-emerald-600 transition-colors">
            <i class="fa-solid fa-magnifying-glass text-xs"></i>
        </div>
        <input type="text"
               x-model="search"
               @focus="open = true"
               @input="open = true"
               @blur="setTimeout(() => open = false, 250)"
               placeholder="{{ __('Search customer by name, number, email, phone...') }}"
               autocomplete="off"
               class="w-full rounded-xl border border-gray-300 bg-white ps-10 pe-10 py-2.5 text-xs text-gray-900 placeholder:text-gray-400 transition-all duration-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 shadow-2xs">

        <button type="button"
                x-show="search && search.length > 0"
                x-cloak
                @click="clear()"
                class="absolute inset-y-0 end-0 flex items-center pe-3.5 text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fa-solid fa-circle-xmark text-xs"></i>
        </button>
    </div>

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute z-40 mt-1.5 max-h-64 w-full overflow-y-auto rounded-2xl border border-gray-200/90 bg-white/95 p-1.5 shadow-xl shadow-gray-200/70 backdrop-blur-md divide-y divide-gray-50">
        <template x-for="c in filtered" :key="c.id">
            <button type="button"
                    @mousedown.prevent="pick(c)"
                    class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2 text-left transition-all hover:bg-emerald-50/80 group/item">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-100 font-bold text-[11px] text-emerald-800 group-hover/item:bg-emerald-600 group-hover/item:text-white transition-colors"
                          x-text="c.name.charAt(0).toUpperCase()">
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-bold text-gray-900 group-hover/item:text-emerald-950" x-text="c.name"></p>
                        <div class="flex items-center gap-1.5 text-[10.5px] text-gray-400">
                            <span class="font-mono text-gray-500" x-text="c.number"></span>
                            <span x-show="c.phone">· <span x-text="c.phone"></span></span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <span x-show="c.country" class="rounded-md bg-gray-100 px-1.5 py-0.5 text-[9.5px] font-semibold uppercase text-gray-600" x-text="c.country"></span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover/item:text-emerald-600 transition-colors"></i>
                </div>
            </button>
        </template>
        <div x-show="filtered.length === 0" class="flex flex-col items-center justify-center py-6 text-center">
            <i class="fa-solid fa-user-slash text-xl text-gray-300 mb-1"></i>
            <p class="text-xs font-medium text-gray-500">{{ __('No customers found') }}</p>
        </div>
    </div>
</div>
