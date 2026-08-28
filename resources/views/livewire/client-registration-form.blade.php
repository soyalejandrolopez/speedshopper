<div wire:key="client-registration-form">
    @if ($sent)
        <div class="flex flex-col items-center px-6 py-12 text-center">
            <span class="relative flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-200 opacity-60"></span>
                <svg class="relative h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
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
                            <label class="label" for="name">{{ __('Full name') }} *</label>
                            <input id="name" type="text" wire:model="form.name" class="input" placeholder="María González">
                            @error('form.name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label" for="whatsapp">{{ __('WhatsApp number') }} *</label>
                            <input id="whatsapp" type="text" wire:model="form.whatsapp" class="input" placeholder="+502 5555 0000">
                            @error('form.whatsapp') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label" for="email">{{ __('Email address') }} *</label>
                            <input id="email" type="email" wire:model="form.email" class="input" placeholder="you@example.com">
                            @error('form.email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label" for="country">{{ __('Destination country') }} *</label>
                            <select id="country" wire:model="form.country" class="input">
                                <option value="">—</option>
                                @foreach ($this->countries() as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('form.country') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label" for="city">{{ __('City / State / Province') }}</label>
                            <input id="city" type="text" wire:model="form.city" class="input">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label" for="address">{{ __('Delivery address') }}</label>
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
                            <label class="label" for="products">{{ __('What products do you want to buy or send?') }} *</label>
                            <textarea id="products" rows="3" wire:model="form.products" class="input" placeholder="{{ __('Ej. Nike Air Max 270, Zapatos Zara...') }}"></textarea>
                            @error('form.products') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label" for="preferred_stores">{{ __('Preferred store(s)') }}</label>
                            <input id="preferred_stores" type="text" wire:model="form.preferred_stores" class="input" placeholder="Amazon, Nike, Zara...">
                        </div>
                        <div>
                            <label class="label" for="budget">{{ __('Approximate budget') }}</label>
                            <input id="budget" type="number" step="0.01" min="0" wire:model="form.budget" class="input" placeholder="0.00">
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
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        {{ __('Back') }}
                    </button>
                @else
                    <span></span>
                @endif

                @if ($this->step < \App\Livewire\ClientRegistrationForm::TOTAL_STEPS)
                    <button type="button" wire:click="next" class="btn-primary">
                        {{ __('Next') }}
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                @else
                    <button type="submit" class="btn-primary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        {{ __('Submit Request') }}
                    </button>
                @endif
            </div>
        </form>
    @endif
</div>
