<div class="overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-xl shadow-emerald-100/30 ring-1 ring-black/5">
    @if ($sent)
        <div class="flex flex-col items-center px-6 py-10 text-center">
            <span class="relative flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 shadow-sm">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-200 opacity-60"></span>
                <i class="fa-solid fa-check text-3xl relative text-emerald-600"></i>
            </span>
            <h3 class="mt-4 text-xl font-extrabold text-gray-900">{{ __('Request sent successfully') }}</h3>
            <p class="mt-1 max-w-sm text-sm text-gray-500">
                @if ($createdCount > 1)
                    {{ __('We received your request with :count products. We will contact you soon.', ['count' => $createdCount]) }}
                @else
                    {{ __('We will contact you soon with your quote.') }}
                @endif
            </p>

            @auth
                <div class="mt-6 flex flex-col sm:flex-row items-center gap-3">
                    <a href="{{ route('portal.dashboard') }}" wire:navigate class="btn-primary">
                        <i class="fa-solid fa-gauge text-base"></i>
                        {{ __('Ir a mi Portal de Clientes') }}
                    </a>
                    <button type="button" wire:click="resetForm" class="btn-ghost">
                        <i class="fa-solid fa-plus text-sm"></i>
                        {{ __('Send another request') }}
                    </button>
                </div>
            @else
                <button type="button" wire:click="resetForm" class="btn-ghost mt-6">{{ __('Send another request') }}</button>
            @endauth
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

                {{-- Sección opcional para crear cuenta de usuario --}}
                @guest
                    <div class="mt-4 rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50/80 to-teal-50/40 p-4 shadow-xs">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" wire:model.live="form.create_account" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-sm font-bold text-gray-900">{{ __('Crear mi cuenta de usuario para acceder al Portal') }}</span>
                                <p class="text-xs text-gray-500 mt-0.5">{{ __('Podrás iniciar sesión en cualquier momento para ver tus compras, cotizaciones, paquetes y facturas.') }}</p>
                            </div>
                        </label>

                        @if (! empty($form['create_account']))
                            <div class="mt-4 grid gap-3 sm:grid-cols-2 pt-3 border-t border-emerald-100" x-data="{ showPass: false, showConfirm: false }">
                                <div>
                                    <label class="label flex items-center gap-1.5" for="form_password_pub">
                                        <i class="fa-solid fa-lock text-emerald-600 text-xs"></i>
                                        <span>{{ __('Password') }} *</span>
                                    </label>
                                    <div class="relative">
                                        <input id="form_password_pub" name="password" :type="showPass ? 'text' : 'password'" wire:model="form.password"
                                               class="input pe-10" placeholder="••••••••">
                                        <button type="button" @click="showPass = ! showPass" class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-400 hover:text-gray-600">
                                            <i x-show="! showPass" class="fa-solid fa-eye text-xs"></i>
                                            <i x-show="showPass" x-cloak class="fa-solid fa-eye-slash text-xs"></i>
                                        </button>
                                    </div>
                                    @error('form.password') <p class="helper-error">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="label flex items-center gap-1.5" for="form_password_confirmation_pub">
                                        <i class="fa-solid fa-shield text-emerald-600 text-xs"></i>
                                        <span>{{ __('Confirm Password') }} *</span>
                                    </label>
                                    <div class="relative">
                                        <input id="form_password_confirmation_pub" name="password_confirmation" :type="showConfirm ? 'text' : 'password'" wire:model="form.password_confirmation"
                                               class="input pe-10" placeholder="••••••••">
                                        <button type="button" @click="showConfirm = ! showConfirm" class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-400 hover:text-gray-600">
                                            <i x-show="! showConfirm" class="fa-solid fa-eye text-xs"></i>
                                            <i x-show="showConfirm" x-cloak class="fa-solid fa-eye-slash text-xs"></i>
                                        </button>
                                    </div>
                                    @error('form.password_confirmation') <p class="helper-error">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                @endguest
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
                                <div class="grid gap-3 sm:grid-cols-6">
                                    <div class="sm:col-span-3">
                                        <label class="label text-xs flex items-center gap-1.5" for="product_name-{{ $index }}">
                                            <i class="fa-solid fa-cart-shopping text-emerald-600 text-xs"></i>
                                            <span>{{ __('Product name or description') }} *</span>
                                        </label>
                                        <input id="product_name-{{ $index }}" name="product_name_{{ $index }}" autocomplete="off" type="text" wire:model="items.{{ $index }}.product_name"
                                               class="input text-sm" placeholder="Ej. Nike Air Max 270, Zapatos Zara...">
                                        @error("items.{$index}.product_name") <p class="helper-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="label text-xs flex items-center gap-1.5" for="unit_price-{{ $index }}">
                                            <i class="fa-solid fa-dollar-sign text-emerald-600 text-xs"></i>
                                            <span>{{ __('Precio / Presupuesto ($)') }}</span>
                                        </label>
                                        <div class="flex">
                                            <span class="inline-flex items-center px-3 text-xs font-bold text-gray-500 bg-gray-100 border border-e-0 border-gray-300 rounded-s-lg">$</span>
                                            <input id="unit_price-{{ $index }}" name="unit_price_{{ $index }}" autocomplete="off" type="number" step="0.01" min="0" wire:model="items.{{ $index }}.unit_price"
                                                   class="input rounded-none rounded-e-lg text-sm" placeholder="0.00">
                                        </div>
                                        @error("items.{$index}.unit_price") <p class="helper-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="sm:col-span-1">
                                        <label class="label text-xs flex items-center gap-1.5" for="quantity-{{ $index }}">
                                            <i class="fa-solid fa-hashtag text-emerald-600 text-xs"></i>
                                            <span>{{ __('Quantity') }}</span>
                                        </label>
                                        <input id="quantity-{{ $index }}" name="quantity_{{ $index }}" autocomplete="off" type="number" min="1" max="999" wire:model="items.{{ $index }}.quantity"
                                               class="input text-sm text-center" placeholder="1">
                                        @error("items.{$index}.quantity") <p class="helper-error">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="label text-xs flex items-center gap-1.5" for="product_url-{{ $index }}">
                                        <i class="fa-solid fa-link text-emerald-600 text-xs"></i>
                                        <span>{{ __('Product link') }}</span>
                                    </label>
                                    <input id="product_url-{{ $index }}" name="product_url_{{ $index }}" autocomplete="off" type="url" wire:model="items.{{ $index }}.product_url"
                                           class="input text-sm" placeholder="https://amazon.com/... o tienda">
                                    @error("items.{$index}.product_url") <p class="helper-error">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="label text-xs flex items-center gap-1.5" for="description-{{ $index }}">
                                        <i class="fa-solid fa-circle-info text-emerald-600 text-xs"></i>
                                        <span>{{ __('Specific details') }}</span>
                                    </label>
                                    <input id="description-{{ $index }}" name="description_{{ $index }}" autocomplete="off" type="text" wire:model="items.{{ $index }}.description"
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

            {{-- Sección de Embalaje y Cajas Heavy Duty --}}
            <div class="rounded-2xl border-2 border-teal-200 bg-gradient-to-br from-teal-50/90 via-emerald-50/50 to-white p-4 shadow-sm">
                <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-600 text-white shadow-xs">
                            <i class="fa-solid fa-boxes-packing text-sm"></i>
                        </span>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-teal-950">
                                {{ __('Servicio de Embalaje / Cajas Heavy Duty (Opcional)') }}
                            </h4>
                            <p class="text-[11px] text-teal-700">{{ __('Selecciona las cajas si requieres reempaque o consolidación.') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 rounded-full bg-teal-100 px-3 py-1 text-xs font-extrabold text-teal-900">
                        <i class="fa-solid fa-calculator text-teal-600"></i>
                        <span>{{ __('Total Embalaje:') }}</span>
                        <span class="text-sm font-black text-teal-700">{{ money($this->packagingTotal) }}</span>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    {{-- 1. Caja Small --}}
                    <div class="rounded-xl border {{ $boxes_small > 0 ? 'border-teal-500 bg-teal-50/80 ring-1 ring-teal-400' : 'border-gray-200 bg-white' }} p-3 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-900">{{ __('Caja Small') }}</span>
                            <span class="rounded bg-teal-100 px-2 py-0.5 text-xs font-extrabold text-teal-800">
                                ${{ number_format($this->rates['box_small_heavy_duty'] ?? 15, 2) }}
                            </span>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-0.5">Heavy Duty</p>

                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" wire:click="decrementBox('small')"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 active:scale-95">
                                <i class="fa-solid fa-minus text-xs"></i>
                            </button>
                            <span class="text-sm font-bold text-gray-900 w-8 text-center">
                                {{ $boxes_small }}
                            </span>
                            <button type="button" wire:click="incrementBox('small')"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-teal-500 bg-teal-600 text-white hover:bg-teal-700 active:scale-95">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                        @if ($boxes_small > 0)
                            <div class="mt-2 text-end text-[11px] font-bold text-teal-700">
                                = {{ money($boxes_small * ($this->rates['box_small_heavy_duty'] ?? 15)) }}
                            </div>
                        @endif
                    </div>

                    {{-- 2. Caja Mediana --}}
                    <div class="rounded-xl border {{ $boxes_medium > 0 ? 'border-teal-500 bg-teal-50/80 ring-1 ring-teal-400' : 'border-gray-200 bg-white' }} p-3 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-900">{{ __('Caja Mediana') }}</span>
                            <span class="rounded bg-teal-100 px-2 py-0.5 text-xs font-extrabold text-teal-800">
                                ${{ number_format($this->rates['box_medium_heavy_duty'] ?? 20, 2) }}
                            </span>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-0.5">Heavy Duty</p>

                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" wire:click="decrementBox('medium')"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 active:scale-95">
                                <i class="fa-solid fa-minus text-xs"></i>
                            </button>
                            <span class="text-sm font-bold text-gray-900 w-8 text-center">
                                {{ $boxes_medium }}
                            </span>
                            <button type="button" wire:click="incrementBox('medium')"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-teal-500 bg-teal-600 text-white hover:bg-teal-700 active:scale-95">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                        @if ($boxes_medium > 0)
                            <div class="mt-2 text-end text-[11px] font-bold text-teal-700">
                                = {{ money($boxes_medium * ($this->rates['box_medium_heavy_duty'] ?? 20)) }}
                            </div>
                        @endif
                    </div>

                    {{-- 3. Caja Larga --}}
                    <div class="rounded-xl border {{ $boxes_large > 0 ? 'border-teal-500 bg-teal-50/80 ring-1 ring-teal-400' : 'border-gray-200 bg-white' }} p-3 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-900">{{ __('Caja Larga') }}</span>
                            <span class="rounded bg-teal-100 px-2 py-0.5 text-xs font-extrabold text-teal-800">
                                ${{ number_format($this->rates['box_large_heavy_duty'] ?? 25, 2) }}
                            </span>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-0.5">Heavy Duty</p>

                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" wire:click="decrementBox('large')"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 active:scale-95">
                                <i class="fa-solid fa-minus text-xs"></i>
                            </button>
                            <span class="text-sm font-bold text-gray-900 w-8 text-center">
                                {{ $boxes_large }}
                            </span>
                            <button type="button" wire:click="incrementBox('large')"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-teal-500 bg-teal-600 text-white hover:bg-teal-700 active:scale-95">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                        @if ($boxes_large > 0)
                            <div class="mt-2 text-end text-[11px] font-bold text-teal-700">
                                = {{ money($boxes_large * ($this->rates['box_large_heavy_duty'] ?? 25)) }}
                            </div>
                        @endif
                    </div>
                </div>

                @if ($this->packagingTotal > 0)
                    <div class="mt-3 rounded-lg bg-teal-100/80 p-2 text-center text-xs font-bold text-teal-950 flex items-center justify-between px-3">
                        <span>{{ __('Subtotal de Embalaje Seleccionado:') }}</span>
                        <span class="text-sm font-black text-teal-800">{{ money($this->packagingTotal) }}</span>
                    </div>
                @endif
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

