<div class="overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-xl shadow-emerald-100/30 ring-1 ring-black/5">
    @if ($sent)
        <div class="flex flex-col items-center px-6 py-12 text-center">
            <span class="relative flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-200 opacity-60"></span>
                <i class="fa-solid fa-check text-3xl relative text-emerald-600"></i>
            </span>
            <h3 class="mt-4 text-lg font-bold text-gray-900">{{ __('Request sent successfully') }}</h3>
            <p class="mt-1 max-w-sm text-sm text-gray-500">
                @if ($createdCount > 1)
                    {{ __('We received your request with :count products. We will contact you soon.', ['count' => $createdCount]) }}
                @else
                    {{ __('We will contact you soon with your quote.') }}
                @endif
            </p>
            <button type="button" wire:click="resetForm" class="btn-ghost mt-6">{{ __('Send another request') }}</button>
        </div>
    @else
        <div class="border-b border-gray-100 bg-gradient-to-r from-emerald-50 via-teal-50/60 to-white px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-200">
                        <i class="fa-solid fa-bag-shopping text-xl"></i>
                    </span>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ __('Send your request') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Add all the products you want to quote.') }}</p>
                    </div>
                </div>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                    {{ count($items) }} {{ count($items) === 1 ? __('Product') : __('Products') }}
                </span>
            </div>
        </div>

        <form wire:submit="submit" class="space-y-6 px-6 py-5">
            <div>
                <p class="mb-2 flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-gray-400">
                    <i class="fa-solid fa-user text-sm"></i>
                    {{ __('Your details') }}
                </p>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="label flex items-center gap-1.5" for="name-{{ $this->getId() }}">
                            <i class="fa-solid fa-user text-emerald-600 text-xs"></i>
                            <span>{{ __('Your full name') }} *</span>
                        </label>
                        <input id="name-{{ $this->getId() }}" name="name" type="text" wire:model="form.name" autocomplete="name"
                               class="input" placeholder="{{ __('Name') }}">
                        @error('form.name') <p class="helper-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label flex items-center gap-1.5" for="email-{{ $this->getId() }}">
                            <i class="fa-solid fa-envelope text-emerald-600 text-xs"></i>
                            <span>{{ __('Your email') }} *</span>
                        </label>
                        <input id="email-{{ $this->getId() }}" name="email" type="email" wire:model="form.email" autocomplete="email"
                               class="input" placeholder="you@example.com">
                        @error('form.email') <p class="helper-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label flex items-center gap-1.5" for="whatsapp-{{ $this->getId() }}">
                            <i class="fa-solid fa-phone text-emerald-600 text-xs"></i>
                            <span>{{ __('Your phone or WhatsApp') }}</span>
                        </label>
                        <input id="whatsapp-{{ $this->getId() }}" name="whatsapp" type="text" wire:model="form.whatsapp" autocomplete="tel"
                               class="input" placeholder="+502 5555 0000">
                    </div>
                </div>
            </div>

            <div>
                <div class="mb-3 flex items-center justify-between">
                    <p class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-gray-400">
                        <i class="fa-solid fa-box text-sm"></i>
                        {{ __('Products to quote') }}
                    </p>
                    <button type="button" wire:click="addItem"
                            class="inline-flex items-center gap-1 rounded-lg border border-emerald-300 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 transition-colors hover:bg-emerald-100 hover:text-emerald-800">
                        <i class="fa-solid fa-plus text-sm"></i>
                        {{ __('Add product') }}
                    </button>
                </div>

                <div class="space-y-4">
                    @foreach ($items as $index => $item)
                        <div wire:key="item-{{ $index }}" class="rounded-xl border border-gray-200/80 bg-gray-50/50 p-4 transition-all duration-200 hover:border-emerald-200">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                                    <i class="fa-solid fa-tag text-[10px]"></i>
                                    {{ __('Product') }} #{{ $index + 1 }}
                                </span>
                                @if (count($items) > 1)
                                    <button type="button" wire:click="removeItem({{ $index }})"
                                            class="inline-flex items-center gap-1 text-xs font-medium text-red-500 transition-colors hover:text-red-700">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                        {{ __('Remove') }}
                                    </button>
                                @endif
                            </div>

                            <div class="space-y-3">
                                <div class="grid gap-3 sm:grid-cols-4">
                                    <div class="sm:col-span-3">
                                        <label class="label text-xs flex items-center gap-1.5" for="product_name-{{ $index }}">
                                            <i class="fa-solid fa-cart-shopping text-emerald-600 text-xs"></i>
                                            <span>{{ __('Product name or description') }} *</span>
                                        </label>
                                        <input id="product_name-{{ $index }}" type="text" wire:model="items.{{ $index }}.product_name"
                                               class="input text-sm" placeholder="Ej. Nike Air Max 270, Zapatos Zara...">
                                        @error("items.{$index}.product_name") <p class="helper-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="label text-xs flex items-center gap-1.5" for="quantity-{{ $index }}">
                                            <i class="fa-solid fa-hashtag text-emerald-600 text-xs"></i>
                                            <span>{{ __('Quantity') }}</span>
                                        </label>
                                        <input id="quantity-{{ $index }}" type="number" min="1" max="999" wire:model="items.{{ $index }}.quantity"
                                               class="input text-sm text-center" placeholder="1">
                                        @error("items.{$index}.quantity") <p class="helper-error">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="label text-xs flex items-center gap-1.5" for="product_url-{{ $index }}">
                                        <i class="fa-solid fa-link text-emerald-600 text-xs"></i>
                                        <span>{{ __('Product link') }}</span>
                                    </label>
                                    <input id="product_url-{{ $index }}" type="url" wire:model="items.{{ $index }}.product_url"
                                           class="input text-sm" placeholder="https://amazon.com/... o tienda">
                                    @error("items.{$index}.product_url") <p class="helper-error">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="label text-xs flex items-center gap-1.5" for="description-{{ $index }}">
                                        <i class="fa-solid fa-circle-info text-emerald-600 text-xs"></i>
                                        <span>{{ __('Specific details') }}</span>
                                    </label>
                                    <input id="description-{{ $index }}" type="text" wire:model="items.{{ $index }}.description"
                                           class="input text-sm" placeholder="{{ __('Size, color, store, coupon or specific note...') }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 flex justify-center">
                    <button type="button" wire:click="addItem"
                            class="inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-emerald-300 bg-emerald-50/50 px-4 py-2.5 text-sm font-semibold text-emerald-700 transition-all hover:border-emerald-500 hover:bg-emerald-100/70 hover:text-emerald-900 w-full justify-center">
                        <i class="fa-solid fa-plus text-base"></i>
                        {{ __('Add another product to this quote') }}
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-3 text-base shadow-lg shadow-emerald-200">
                <i class="fa-solid fa-paper-plane text-lg"></i>
                {{ __('Send Request') }} ({{ count($items) }} {{ count($items) === 1 ? __('product') : __('products') }})
            </button>
            <p class="flex items-center justify-center gap-1.5 text-center text-xs text-gray-400">
                <i class="fa-solid fa-circle-check text-sm text-emerald-500"></i>
                {{ __('We quote before buying. No payment is required to request a quote.') }}
            </p>
        </form>
    @endif
</div>

