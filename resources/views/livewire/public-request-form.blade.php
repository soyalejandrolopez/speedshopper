<div class="overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-xl shadow-emerald-100/30 ring-1 ring-black/5">
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
            <button type="button" wire:click="$set('sent', false)" class="btn-ghost mt-6">{{ __('Send another request') }}</button>
        </div>
    @else
        <div class="border-b border-gray-100 bg-gradient-to-r from-emerald-50 via-teal-50/60 to-white px-6 py-4">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-200">
                    <svg class="h-5.5 w-5.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                    </svg>
                </span>
                <div>
                    <h3 class="font-bold text-gray-900">{{ __('Send your request') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('Complete the form and we will send you a quote.') }}</p>
                </div>
            </div>
        </div>

        <form wire:submit="submit" class="space-y-5 px-6 py-5">
            <div>
                <p class="mb-2 flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    {{ __('Your details') }}
                </p>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="label" for="name-{{ $this->getId() }}">{{ __('Your full name') }} *</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <input id="name-{{ $this->getId() }}" name="name" type="text" wire:model="form.name" autocomplete="name"
                                   class="input ps-9" placeholder="{{ __('Name') }}">
                        </div>
                        @error('form.name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="email-{{ $this->getId() }}">{{ __('Your email') }} *</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <input id="email-{{ $this->getId() }}" name="email" type="email" wire:model="form.email" autocomplete="email"
                                   class="input ps-9" placeholder="you@example.com">
                        </div>
                        @error('form.email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="whatsapp-{{ $this->getId() }}">{{ __('Your phone or WhatsApp') }}</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                            <input id="whatsapp-{{ $this->getId() }}" name="whatsapp" type="text" wire:model="form.whatsapp" autocomplete="tel"
                                   class="input ps-9" placeholder="+502 5555 0000">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <p class="mb-2 flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                    {{ __('Your product') }}
                </p>
                <div class="space-y-3">
                    <div>
                        <label class="label" for="product_name-{{ $this->getId() }}">{{ __('Product') }} *</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                            </svg>
                            <input id="product_name-{{ $this->getId() }}" name="product_name" type="text" wire:model="form.product_name"
                                   class="input ps-9" placeholder="Nike Air Max 270 — Talla 7.5">
                        </div>
                        @error('form.product_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="product_url-{{ $this->getId() }}">{{ __('Product link') }}</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                            </svg>
                            <input id="product_url-{{ $this->getId() }}" name="product_url" type="url" wire:model="form.product_url"
                                   class="input ps-9" placeholder="https://...">
                        </div>
                        @error('form.product_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="description-{{ $this->getId() }}">{{ __('Message') }}</label>
                        <textarea id="description-{{ $this->getId() }}" name="description" wire:model="form.description" rows="3"
                                  class="input" placeholder="{{ __('Size, color, brand, anything we should know...') }}"></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-3 text-base">
                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
                {{ __('Send Request') }}
            </button>
            <p class="flex items-center justify-center gap-1.5 text-center text-xs text-gray-400">
                <svg class="h-3.5 w-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('We quote before buying. No payment is required to request a quote.') }}
            </p>
        </form>
    @endif
</div>
