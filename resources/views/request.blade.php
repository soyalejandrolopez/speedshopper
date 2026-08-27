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
                    <span class="section-eyebrow">{{ __('Chat with the assistant') }}</span>
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
                    <div class="w-full overflow-hidden rounded-[2rem] bg-white shadow-[0_24px_70px_-20px_rgba(16,185,129,0.35)] ring-1 ring-black/5">
                        <div class="relative flex items-center justify-between overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-600 to-teal-600 px-5 py-4 text-white">
                            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(255,255,255,0.18)_0%,transparent_55%)]"></div>
                            <div class="pointer-events-none absolute -bottom-10 -end-6 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                            <div class="pointer-events-none absolute -top-8 -start-8 h-24 w-24 rounded-full bg-white/10 blur-xl"></div>

                            <div class="relative flex items-center gap-3">
                                <span class="relative flex h-11 w-11 items-center justify-center rounded-full bg-white/20 ring-2 ring-white/40 backdrop-blur">
                                    <svg class="h-5.5 w-5.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                                    </svg>
                                    <span class="absolute -bottom-0.5 -end-0.5 h-3 w-3 rounded-full border-2 border-emerald-600 bg-emerald-300"></span>
                                </span>
                                <div>
                                    <p class="text-sm font-bold">{{ \App\Models\Setting::get('company_name', config('app.name')) }} {{ __('Assistant') }}</p>
                                    <p class="flex items-center gap-1.5 text-xs text-emerald-100">
                                        <span class="h-1.5 w-1.5 animate-pulse-dot rounded-full bg-emerald-300"></span>
                                        {{ __('Online') }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('home') }}" class="relative rounded-lg p-1.5 text-emerald-100 transition-colors hover:bg-white/10 hover:text-white" title="{{ __('Close') }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </div>

                        <div x-data="{ scrollBody(el) { el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' }) } }"
                             x-init="scrollBody($el); new MutationObserver(() => scrollBody($el)).observe($el, { childList: true, subtree: true })"
                             class="max-h-[70vh] space-y-5 overflow-y-auto bg-gray-50/70 p-5 sm:p-6">
                            <div class="text-center">
                                <span class="inline-block rounded-full border border-gray-100 bg-white px-3.5 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400 shadow-sm">
                                    {{ __('Today') }}
                                </span>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white ring-2 ring-white shadow-sm">
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                                    </svg>
                                </span>
                                <div class="max-w-[85%]">
                                    <div class="rounded-2xl rounded-tl-sm border border-gray-100 bg-white px-4 py-3 text-sm leading-relaxed text-gray-700 shadow-sm">
                                        {{ __('Hi! I am the SpeedShopper assistant. Let me ask you a few questions to send you your quote.') }}
                                    </div>
                                    <p class="mt-1 ps-1 text-[10px] text-gray-400">{{ __('now') }}</p>
                                </div>
                            </div>

                            <livewire:chat-request-form />
                        </div>

                        <div class="flex items-center gap-2 border-t border-gray-100 bg-white px-5 py-3">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                            </span>
                            <p class="text-xs font-semibold text-gray-600">{{ __('Assistant online') }}</p>
                            <p class="ms-auto text-xs text-gray-400">{{ __('We reply in less than 1 hour') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
