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
                    <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Zelle · PayPal · Card
                </span>
            </div>
        </div>

        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-gray-900">{{ __('Contact') }}</p>
            <p class="mt-3 max-w-xs text-sm leading-relaxed text-gray-500">{{ __('Questions about your order? Send us a message and we will reply right away.') }}</p>
            <a href="{{ route('request') }}"
               class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 transition-colors hover:text-emerald-700">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                    </svg>
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
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach (array_slice(explode(',', \App\Models\Setting::get('countries_served', 'MX,GT,HN,SV,NI,CR,PA,CO,EC,PE,CL,AR')), 0, 6) as $code)
                    <span class="rounded-full border border-emerald-100 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">{{ country_name(trim($code)) }}</span>
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
