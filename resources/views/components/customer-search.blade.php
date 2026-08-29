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
        ];
    })->values();
@endphp

<div class="relative" x-data="{
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
            return name.includes(q) || num.includes(q) || email.includes(q);
        });
    },
    pick(c) {
        $wire.selectCustomer(c.id, c.name);
        this.search = c.name;
        this.open = false;
    }
}">
    <input type="text"
           x-model="search"
           @focus="open = true"
           @input="open = true"
           @blur="setTimeout(() => open = false, 250)"
           placeholder="{{ __('Search customer by name or number...') }}"
           autocomplete="off"
           class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">

    <div x-show="open" x-cloak
         x-transition
         class="absolute z-30 mt-1 max-h-60 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white py-1 shadow-xl shadow-gray-200/60">
        <template x-for="c in filtered" :key="c.id">
            <button type="button"
                    @mousedown.prevent="pick(c)"
                    class="block w-full px-3 py-2 text-left text-sm text-gray-700 transition-colors hover:bg-emerald-50">
                <span class="font-medium" x-text="c.name"></span>
                <span class="ms-1 text-xs text-gray-400" x-text="c.number"></span>
                <span class="ms-1 text-xs text-gray-400" x-show="c.email" x-text="c.email"></span>
            </button>
        </template>
        <div x-show="filtered.length === 0" class="px-3 py-2 text-sm text-gray-400">
            {{ __('No customers found') }}
        </div>
    </div>
</div>
