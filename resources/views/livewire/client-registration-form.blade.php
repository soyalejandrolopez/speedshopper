<div wire:key="client-registration-form">
    @if ($sent)
        <div class="flex flex-col items-center px-6 py-12 text-center">
            <span class="relative flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-200 opacity-60"></span>
                <i class="fa-solid fa-check text-3xl relative text-emerald-600"></i>
            </span>
            <h3 class="mt-4 text-lg font-bold text-gray-900">{{ __('Request sent successfully') }}</h3>
            <p class="mt-1 max-w-xs text-sm text-gray-500">{{ __('We will contact you soon') }}</p>
            <button type="button" wire:click="resetForm" class="btn-ghost mt-6">{{ __('Start over') }}</button>
        </div>
    @else
        <div class="mb-5">
            <div class="mb-1.5 flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-gray-400">
                <span>{{ __('Step') }} {{ $this->step }} {{ __('of') }} {{ \App\Livewire\ClientRegistrationForm::TOTAL_STEPS }}</span>
                <span>{{ $this->progressPercent() }}%</span>
            </div>
            <div class="h-1.5 overflow-hidden rounded-full bg-gray-200">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500" style="width: {{ $this->progressPercent() }}%"></div>
            </div>
            <div class="mt-2 flex items-center justify-between text-xs font-medium text-gray-500">
                <span class="{{ $this->step === 1 ? 'text-emerald-600' : '' }}">1 · {{ __('Personal') }}</span>
                <span class="{{ $this->step === 2 ? 'text-emerald-600' : '' }}">2 · {{ __('Request') }}</span>
                <span class="{{ $this->step === 3 ? 'text-emerald-600' : '' }}">3 · {{ __('Confirm') }}</span>
            </div>
        </div>

        <form wire:submit="submit" class="space-y-5">
            @if ($this->step === 1)
                <div>
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Personal Information') }}</h3>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="label flex items-center gap-1.5" for="name">
                                <i class="fa-solid fa-user text-emerald-600 text-xs"></i>
                                <span>{{ __('Full name') }} *</span>
                            </label>
                            <input id="name" type="text" wire:model="form.name" class="input" placeholder="María González">
                            @error('form.name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label flex items-center gap-1.5" for="whatsapp">
                                <i class="fa-solid fa-phone text-emerald-600 text-xs"></i>
                                <span>{{ __('WhatsApp number') }} *</span>
                            </label>
                            <input id="whatsapp" type="text" wire:model="form.whatsapp" class="input" placeholder="+502 5555 0000">
                            @error('form.whatsapp') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label flex items-center gap-1.5" for="email">
                                <i class="fa-solid fa-envelope text-emerald-600 text-xs"></i>
                                <span>{{ __('Email address') }} *</span>
                            </label>
                            <input id="email" type="email" wire:model="form.email" class="input" placeholder="you@example.com">
                            @error('form.email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label flex items-center gap-1.5" for="country">
                                <i class="fa-solid fa-location-dot text-emerald-600 text-xs"></i>
                                <span>{{ __('Destination country') }} *</span>
                            </label>
                            <select id="country" wire:model="form.country" class="input">
                                @foreach ($this->countries() as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('form.country') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label flex items-center gap-1.5" for="city">
                                <i class="fa-solid fa-city text-emerald-600 text-xs"></i>
                                <span>{{ __('City / State / Province') }}</span>
                            </label>
                            <input id="city" type="text" wire:model="form.city" class="input">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label flex items-center gap-1.5" for="address">
                                <i class="fa-solid fa-map-pin text-emerald-600 text-xs"></i>
                                <span>{{ __('Delivery address') }}</span>
                            </label>
                            <textarea id="address" rows="2" wire:model="form.address" class="input"></textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-gray-900">{{ __('What service do you need?') }} *</h3>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Select one or more options.') }}</p>
                    <div class="mt-3 grid gap-2 sm:grid-cols-2">
                        @foreach ($this->serviceOptions() as $key => $label)
                            <label class="flex items-start gap-2.5 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 hover:bg-emerald-50/40">
                                <input type="checkbox" value="{{ $key }}" wire:model="form.services"
                                       class="mt-0.5 h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('form.services') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif

            @if ($this->step === 2)
                <div>
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Request Information') }}</h3>
                    <div class="mt-3 space-y-3">
                        <div>
                            <label class="label flex items-center gap-1.5" for="products">
                                <i class="fa-solid fa-cart-shopping text-emerald-600 text-xs"></i>
                                <span>{{ __('What products do you want to buy or send?') }} *</span>
                            </label>
                            <textarea id="products" rows="3" wire:model="form.products" class="input" placeholder="{{ __('Ej. Nike Air Max 270, Zapatos Zara...') }}"></textarea>
                            @error('form.products') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label flex items-center gap-1.5" for="preferred_stores">
                                <i class="fa-solid fa-store text-emerald-600 text-xs"></i>
                                <span>{{ __('Preferred store(s)') }}</span>
                            </label>
                            <input id="preferred_stores" type="text" wire:model="form.preferred_stores" class="input" placeholder="Amazon, Nike, Zara...">
                        </div>
                        <div x-data="{
                            budget: @entangle('form.budget'),
                            get tier() {
                                const val = parseFloat(this.budget);
                                if (isNaN(val) || val <= 0) return 0;
                                if (val >= 100 && val <= 699) return 1;
                                if (val >= 700 && val <= 1499) return 2;
                                if (val >= 1500) return 3;
                                return 0;
                            },
                            get feePercent() {
                                if (this.tier === 1) return 20;
                                if (this.tier === 2 || this.tier === 3) return 15;
                                return 0;
                            },
                            get estimatedFee() {
                                const val = parseFloat(this.budget);
                                if (isNaN(val) || val <= 0) return '0.00';
                                return ((val * this.feePercent) / 100).toFixed(2);
                            }
                        }" class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="label mb-0 flex items-center gap-1.5" for="budget">
                                    <i class="fa-solid fa-dollar-sign text-emerald-600 text-xs"></i>
                                    <span>{{ __('Approximate budget') }} (USD)</span>
                                </label>
                                <template x-if="tier > 0">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 animate-fade-in">
                                        <i class="fa-solid fa-calculator text-xs"></i>
                                        {{ __('Fee estimado') }}: $<span x-text="estimatedFee"></span>
                                    </span>
                                </template>
                            </div>
                            <div class="flex">
                                <span class="inline-flex items-center px-3.5 text-sm font-bold text-gray-500 bg-gray-100 border border-e-0 border-gray-300 rounded-s-lg">$</span>
                                <input id="budget" type="number" step="0.01" min="0" wire:model.live="form.budget" x-model="budget" class="input rounded-none rounded-e-lg" placeholder="0.00">
                            </div>
                            @error('form.budget') <p class="helper-error">{{ $message }}</p> @enderror

                            <!-- Guía de Fees por Presupuesto -->
                            <div class="mt-2 rounded-xl border border-gray-200 bg-gray-50/90 p-3 text-xs shadow-sm">
                                <p class="mb-2 font-bold text-gray-800 flex items-center gap-1.5">
                                    <i class="fa-solid fa-tags text-emerald-600"></i>
                                    {{ __('Tarifas según presupuesto:') }}
                                </p>
                                <div class="grid gap-2">
                                    <!-- Tier 1 -->
                                    <div class="rounded-lg border p-2.5 transition-all duration-200"
                                         :class="tier === 1 ? 'border-emerald-500 bg-emerald-50/80 ring-2 ring-emerald-400/20 shadow-sm' : 'border-gray-200 bg-white'">
                                        <div class="flex items-center justify-between font-bold text-gray-900">
                                            <span class="flex items-center gap-1.5">
                                                <template x-if="tier === 1">
                                                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                                </template>
                                                {{ __('Compras de $100 a $699') }}
                                            </span>
                                            <span class="rounded bg-emerald-100 px-2 py-0.5 font-extrabold text-emerald-700">{{ __('Fee: 20%') }}</span>
                                        </div>
                                        <p class="mt-1 text-[11px] text-gray-500">{{ __('Incluye hasta 2 tiendas y 2 horas de servicio.') }}</p>
                                    </div>

                                    <!-- Tier 2 -->
                                    <div class="rounded-lg border p-2.5 transition-all duration-200"
                                         :class="tier === 2 ? 'border-emerald-500 bg-emerald-50/80 ring-2 ring-emerald-400/20 shadow-sm' : 'border-gray-200 bg-white'">
                                        <div class="flex items-center justify-between font-bold text-gray-900">
                                            <span class="flex items-center gap-1.5">
                                                <template x-if="tier === 2">
                                                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                                </template>
                                                {{ __('Compras de $700 a $1,499') }}
                                            </span>
                                            <span class="rounded bg-emerald-100 px-2 py-0.5 font-extrabold text-emerald-700">{{ __('Fee: 15%') }}</span>
                                        </div>
                                        <p class="mt-1 text-[11px] text-gray-500">{{ __('Incluye hasta 3 tiendas y 3 horas de servicio.') }}</p>
                                    </div>

                                    <!-- Tier 3 -->
                                    <div class="rounded-lg border p-2.5 transition-all duration-200"
                                         :class="tier === 3 ? 'border-emerald-500 bg-emerald-50/80 ring-2 ring-emerald-400/20 shadow-sm' : 'border-gray-200 bg-white'">
                                        <div class="flex items-center justify-between font-bold text-gray-900">
                                            <span class="flex items-center gap-1.5">
                                                <template x-if="tier === 3">
                                                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                                </template>
                                                {{ __('Compras de $1,500 o más') }}
                                            </span>
                                            <span class="rounded bg-emerald-100 px-2 py-0.5 font-extrabold text-emerald-700">{{ __('Fee: 15%') }}</span>
                                        </div>
                                        <p class="mt-1 text-[11px] text-gray-500">{{ __('Incluye hasta 4 tiendas y 4 horas de servicio.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="label">{{ __('Do you have product links?') }}</span>
                            <div class="flex gap-4 text-sm text-gray-700">
                                <label class="inline-flex items-center gap-2"><input type="radio" value="yes" wire:model="form.has_links" class="text-emerald-600 focus:ring-emerald-500"> {{ __('Yes') }}</label>
                                <label class="inline-flex items-center gap-2"><input type="radio" value="no" wire:model="form.has_links" class="text-emerald-600 focus:ring-emerald-500"> {{ __('No') }}</label>
                            </div>
                            @if ($this->form['has_links'] === 'yes')
                                <textarea rows="2" wire:model="form.product_links" class="input mt-2" placeholder="{{ __('Paste the links here') }}"></textarea>
                            @endif
                        </div>

                        <div>
                            <span class="label">{{ __('Would you like us to look for deals or better prices?') }}</span>
                            <div class="flex gap-4 text-sm text-gray-700">
                                <label class="inline-flex items-center gap-2"><input type="radio" value="yes" wire:model="form.find_deals" class="text-emerald-600 focus:ring-emerald-500"> {{ __('Yes') }}</label>
                                <label class="inline-flex items-center gap-2"><input type="radio" value="no" wire:model="form.find_deals" class="text-emerald-600 focus:ring-emerald-500"> {{ __('No') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-gray-900">{{ __('For Online Shopping or Package Reception') }}</h3>
                    <div class="mt-3 space-y-3">
                        <div>
                            <span class="label">{{ __('Did you already make the purchase?') }}</span>
                            <div class="flex gap-4 text-sm text-gray-700">
                                <label class="inline-flex items-center gap-2"><input type="radio" value="yes" wire:model="form.already_purchased" class="text-emerald-600 focus:ring-emerald-500"> {{ __('Yes') }}</label>
                                <label class="inline-flex items-center gap-2"><input type="radio" value="no" wire:model="form.already_purchased" class="text-emerald-600 focus:ring-emerald-500"> {{ __('No') }}</label>
                            </div>
                        </div>
                        @if ($this->form['already_purchased'] === 'yes')
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div><label class="label" for="store_name">{{ __('Store name') }}</label><input id="store_name" type="text" wire:model="form.store_name" class="input"></div>
                                <div><label class="label" for="order_number">{{ __('Order number') }}</label><input id="order_number" type="text" wire:model="form.order_number" class="input"></div>
                                <div><label class="label" for="tracking_number">{{ __('Tracking number') }}</label><input id="tracking_number" type="text" wire:model="form.tracking_number" class="input"></div>
                                <div><label class="label" for="approx_packages">{{ __('Approximate number of packages') }}</label><input id="approx_packages" type="number" min="0" wire:model="form.approx_packages" class="input"></div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($this->step === 3)
                <div>
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Shipping Preferences') }}</h3>
                    <div class="mt-3 space-y-3">
                        <div>
                            <span class="label">{{ __('Do you have a preferred shipping company?') }}</span>
                            <div class="flex gap-4 text-sm text-gray-700">
                                <label class="inline-flex items-center gap-2"><input type="radio" value="yes" wire:model="form.courier" class="text-emerald-600 focus:ring-emerald-500"> {{ __('Yes') }}</label>
                                <label class="inline-flex items-center gap-2"><input type="radio" value="no" wire:model="form.courier" class="text-emerald-600 focus:ring-emerald-500"> {{ __('No') }}</label>
                            </div>
                        </div>
                        @if ($this->form['courier'] === 'yes')
                            <div><label class="label" for="courier_name">{{ __('Company name') }}</label><input id="courier_name" type="text" wire:model="form.courier_name" class="input"></div>
                        @endif
                        <div>
                            <span class="label">{{ __('Do you need help coordinating the shipment?') }}</span>
                            <div class="flex gap-4 text-sm text-gray-700">
                                <label class="inline-flex items-center gap-2"><input type="radio" value="yes" wire:model="form.need_shipping_coordination" class="text-emerald-600 focus:ring-emerald-500"> {{ __('Yes') }}</label>
                                <label class="inline-flex items-center gap-2"><input type="radio" value="no" wire:model="form.need_shipping_coordination" class="text-emerald-600 focus:ring-emerald-500"> {{ __('No') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Comments or Special Instructions') }}</h3>
                    <textarea rows="3" wire:model="form.comments" class="input mt-3"></textarea>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-gray-900">{{ __('Confirmation') }}</h3>
                    <div class="mt-3 space-y-2">
                        <label class="flex items-start gap-2.5 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700">
                            <input type="checkbox" wire:model="form.confirm_correct" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            {{ __('I confirm that the information provided is correct.') }}
                        </label>
                        @error('form.confirm_correct') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        <label class="flex items-start gap-2.5 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700">
                            <input type="checkbox" wire:model="form.accept_costs" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            {{ __('I understand that product costs, taxes, shipping, packing, storage, international shipping and other applicable charges may be additional to the service fees.') }}
                        </label>
                        @error('form.accept_costs') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        <label class="flex items-start gap-2.5 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700">
                            <input type="checkbox" wire:model="form.accept_contact" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            {{ __('I agree to receive communication related to my purchase, packages and shipments via WhatsApp, email or phone.') }}
                        </label>
                        @error('form.accept_contact') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-between gap-3 border-t border-gray-100 pt-4">
                @if ($this->step > 1)
                    <button type="button" wire:click="back" class="btn-ghost">
                        <i class="fa-solid fa-arrow-left text-base"></i>
                        {{ __('Back') }}
                    </button>
                @else
                    <span></span>
                @endif

                @if ($this->step < \App\Livewire\ClientRegistrationForm::TOTAL_STEPS)
                    <button type="button" wire:click="next" class="btn-primary">
                        {{ __('Next') }}
                        <i class="fa-solid fa-chevron-right text-base"></i>
                    </button>
                @else
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-paper-plane text-base"></i>
                        {{ __('Submit Request') }}
                    </button>
                @endif
            </div>
        </form>
    @endif
</div>
