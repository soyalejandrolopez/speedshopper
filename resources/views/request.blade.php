<x-public-layout title="{{ __('Send us your purchase request') }}">
    <section class="relative overflow-hidden bg-gradient-to-b from-emerald-50/70 via-white to-white py-16 lg:py-24">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -start-32 top-0 h-96 w-96 rounded-full bg-emerald-100/80 blur-3xl"></div>
            <div class="absolute -end-24 bottom-0 h-96 w-96 rounded-full bg-teal-100/80 blur-3xl"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(5,150,105,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(5,150,105,0.04)_1px,transparent_1px)] bg-[size:56px_56px] [mask-image:radial-gradient(ellipse_70%_50%_at_50%_0%,black,transparent)]"></div>
        </div>

        <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
            <div class="grid gap-12 lg:grid-cols-2 lg:gap-16">
                <div class="order-2 flex flex-col justify-center lg:order-1" data-reveal>
                    <span class="section-eyebrow">{{ __('Quick Quote') }}</span>
                    <h1 class="mt-4 text-4xl font-extrabold leading-[1.1] tracking-tight text-gray-900 sm:text-5xl">
                        {{ __('Send us your purchase request') }}
                    </h1>
                    <p class="mt-4 max-w-md text-lg leading-relaxed text-gray-500">
                        {{ __('Tell us what you want and we will quote you the total cost before buying anything.') }}
                    </p>

                    <ul class="mt-8 space-y-4">
                        <li class="flex items-start gap-3.5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">{{ __('Free quote, no commitment') }}</p>
                                <p class="text-sm text-gray-500">{{ __('Send the product link and we take care of the rest.') }}</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3.5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48zM12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">{{ __('We find the best deals and discounts for you.') }}</p>
                                <p class="text-sm text-gray-500">{{ __('Fast reply by chat') }} · {{ __('No payment upfront') }}</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3.5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-teal-100 text-teal-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">{{ __('Package consolidation') }}</p>
                                <p class="text-sm text-gray-500">{{ __('We consolidate all your packages into one box.') }}</p>
                            </div>
                        </li>
                    </ul>

                    <div class="mt-10 grid max-w-md grid-cols-3 gap-4 border-t border-gray-200/70 pt-6">
                        @php
                            $countries = count(array_filter(array_map('trim', explode(',', \App\Models\Setting::get('countries_served', 'MX,GT')))));
                        @endphp
                        <div>
                            <p class="text-2xl font-extrabold text-gray-900"><span data-count="{{ $countries }}">{{ $countries }}</span>+</p>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Countries We Ship To') }}</p>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-gray-900">&lt; 1h</p>
                            <p class="mt-1 text-xs text-gray-500">{{ __('We reply in less than 1 hour by chat.') }}</p>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-gray-900">100%</p>
                            <p class="mt-1 text-xs text-gray-500">{{ __('No payment upfront') }}</p>
                        </div>
                    </div>
                </div>

                <div class="order-1 flex items-start lg:order-2 lg:sticky lg:top-24" data-reveal style="--reveal-delay: 120ms">
                    <div class="w-full rounded-[1.75rem] border border-gray-200 bg-white p-6 shadow-xl shadow-emerald-100/50 ring-1 ring-black/5 sm:p-8">
                        <h2 class="text-lg font-bold text-gray-900">{{ __('Client Registration') }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Complete the following information to get started. This helps us identify your request and offer you the right service.') }}</p>
                        <div class="mt-5">
                            <livewire:client-registration-form />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
