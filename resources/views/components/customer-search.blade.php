@props([
    'customers' => [],
    'model' => 'form.customer_id',
    'searchModel' => 'form.customer_search',
])

<div class="relative" x-data="{
    open: false,
    search: @entangle($searchModel),
    pick(id, name) {
        $wire.selectCustomer(id, name);
        this.search = name;
        this.open = false;
    }
}">
    <input type="text"
           x-model="search"
           @focus="open = true"
           @input="open = true"
           @blur="setTimeout(() => open = false, 200)"
           placeholder="{{ __('Search customer by name or number...') }}"
           autocomplete="off"
           class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">

    <div x-show="open" x-cloak
         x-transition
         class="absolute z-30 mt-1 max-h-60 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white py-1 shadow-xl shadow-gray-200/60">
        @forelse ($customers as $c)
            <button type="button"
                    @mousedown.prevent="pick({{ $c->id }}, '{{ addslashes($c->name) }}')"
                    class="block w-full px-3 py-2 text-left text-sm text-gray-700 transition-colors hover:bg-emerald-50"
                    x-show="! search || ('{{ strtolower($c->name) }} {{ $c->number }} {{ strtolower($c->email ?? '') }}'.includes(search.toLowerCase()))">
                <span class="font-medium">{{ $c->name }}</span>
                <span class="ms-1 text-xs text-gray-400">{{ $c->number }}</span>
                @if ($c->email)
                    <span class="ms-1 text-xs text-gray-400">{{ $c->email }}</span>
                @endif
            </button>
        @empty
            <div class="px-3 py-2 text-sm text-gray-400">{{ __('No customers available') }}</div>
        @endforelse
    </div>
</div>
