<footer class="border-t border-gray-100 bg-gradient-to-b from-white to-emerald-50/60">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-2 lg:grid-cols-4">
        <div>
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-bold text-gray-900">
                <x-brand-logo size="md" />
                <span>{{ \App\Models\Setting::get('company_name', config('app.name')) }}</span>
            </a>
            <p class="mt-3 max-w-xs text-sm leading-relaxed text-gray-500">{{ __('We buy, receive and ship your US purchases to your door in Latin America.') }}</p>
            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs font-medium text-gray-500">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-base text-emerald-500"></i>
                    Zelle · PayPal
                </span>
            </div>
        </div>

        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-gray-900">{{ __('Contact') }}</p>
            <p class="mt-3 max-w-xs text-sm leading-relaxed text-gray-500">{{ __('Questions about your order? Send us a message and we will reply right away.') }}</p>
            <a href="{{ route('contact') }}"
               class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 transition-colors hover:text-emerald-700">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                    <i class="fa-solid fa-comments text-base"></i>
                </span>
                {{ __('Send us a message') }}
            </a>
        </div>

        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-gray-900">{{ __('My Account') }}</p>
            <div class="mt-3 flex flex-col gap-2 text-sm text-gray-500">
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('dashboard') : route('portal.dashboard') }}" class="transition-colors hover:text-emerald-700">{{ __('Dashboard') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="transition-colors hover:text-emerald-700">{{ __('Log Out') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="transition-colors hover:text-emerald-700">{{ __('Log in') }}</a>
                    <a href="{{ route('register') }}" class="transition-colors hover:text-emerald-700">{{ __('Create Account') }}</a>
                @endauth
            </div>
        </div>

        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-gray-900">{{ __('We ship to') }}</p>
            <div class="mt-3 flex flex-wrap gap-1.5">
                @php
                    $rawCountries = \App\Models\Setting::get('countries_served', 'VE,CO,EC,PE,CL,CR,PA,DO,SV,HN,MX');
                    $footerCodes = explode(',', $rawCountries);
                    $footerList = collect($footerCodes)->map(fn ($c) => strtoupper(trim($c)))->filter()->reject(fn ($c) => $c === 'VE')->prepend('VE');
                @endphp
                @foreach ($footerList as $code)
                    <span class="rounded-full border border-emerald-100 bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700">{{ country_name($code) }}</span>
                @endforeach
            </div>
        </div>
    </div>
    <div class="border-t border-gray-200 py-4 text-center text-xs text-gray-400">
        © {{ date('Y') }} {{ \App\Models\Setting::get('company_name', config('app.name')) }} — {{ __('Personal Shopper in Baytown, TX') }}
        <div class="mt-1">
            Powered by <a href="https://www.hamstersoftware.com" target="_blank" class="hover:text-emerald-600 transition-colors">Hamster Software</a>
        </div>
    </div>
</footer>
