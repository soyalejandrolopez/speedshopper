<footer class="border-t border-gray-100 bg-gradient-to-b from-white to-emerald-50/60">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-2 lg:grid-cols-4">
        <div>
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-bold text-gray-900">
                <x-brand-logo size="md" />
                <span>{{ \App\Models\Setting::get('company_name', config('app.name')) }}</span>
            </a>
            <p class="mt-3 max-w-xs text-sm leading-relaxed text-gray-500">{{ __('We buy, receive and ship your US purchases to your door in Latin America.') }}</p>
            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-medium text-gray-500">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-base text-emerald-500"></i>
                    Zelle · PayPal
                </span>
                <a href="{{ \App\Models\Setting::get('instagram_url', 'https://www.instagram.com/speedingshopper') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-gray-600 transition-colors hover:text-pink-600" aria-label="Instagram">
                    <i class="fa-brands fa-instagram text-base text-pink-600"></i>
                    <span>@speedingshopper</span>
                </a>
            </div>
        </div>

        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-gray-900">{{ __('Navigation') }}</p>
            <div class="mt-3 flex flex-col gap-2 text-sm text-gray-500">
                <a href="{{ route('home') }}" class="transition-colors hover:text-emerald-700">{{ __('Home') }}</a>
                <a href="{{ route('home') }}#how-it-works" class="transition-colors hover:text-emerald-700">{{ __('How it works') }}</a>
                <a href="{{ route('home') }}#fees" class="transition-colors hover:text-emerald-700">{{ __('Fees and Pricing') }}</a>
                <a href="{{ route('request') }}" class="transition-colors hover:text-emerald-700" wire:navigate>{{ __('Request a Purchase') }}</a>
            </div>
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
            <p class="text-sm font-semibold uppercase tracking-wider text-gray-900">{{ __('Contact') }}</p>
            <p class="mt-3 text-sm leading-relaxed text-gray-500">{{ __('Questions about your order? Send us a message and we will reply right away.') }}</p>
            <div class="mt-4">
                <a href="{{ route('contact') }}"
                   class="btn-primary inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold shadow-md shadow-emerald-500/20 transition-all hover:shadow-lg hover:-translate-y-0.5"
                   wire:navigate>
                    <i class="fa-solid fa-envelope text-base"></i>
                    {{ __('Contacto') }}
                </a>
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
