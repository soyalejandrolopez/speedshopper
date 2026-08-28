<header x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 12"
        class="sticky top-0 z-40 border-b bg-white/80 backdrop-blur-lg transition-shadow duration-300"
        :class="scrolled ? 'border-gray-200/80 shadow-lg shadow-gray-200/40' : 'border-transparent'">
    <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6">
        <a href="{{ route('home') }}" class="group flex items-center gap-2.5 font-bold text-gray-900">
            <x-brand-logo size="md" class="transition-transform duration-300 group-hover:scale-105" />
            <span class="text-lg">{{ \App\Models\Setting::get('company_name', config('app.name')) }}</span>
        </a>

        <div class="hidden items-center gap-7 text-sm font-medium text-gray-600 lg:flex">
            <a href="{{ route('home') }}#how-it-works" class="relative transition-colors hover:text-emerald-700 after:absolute after:-bottom-1 after:start-0 after:h-0.5 after:w-0 after:bg-emerald-600 after:transition-all after:duration-300 hover:after:w-full">{{ __('How it works') }}</a>
            <a href="{{ route('home') }}#fees" class="relative transition-colors hover:text-emerald-700 after:absolute after:-bottom-1 after:start-0 after:h-0.5 after:w-0 after:bg-emerald-600 after:transition-all after:duration-300 hover:after:w-full">{{ __('Fees and Pricing') }}</a>
            <a href="{{ route('contact') }}" class="relative transition-colors hover:text-emerald-700 after:absolute after:-bottom-1 after:start-0 after:h-0.5 after:w-0 after:bg-emerald-600 after:transition-all after:duration-300 hover:after:w-full">{{ __('Contact') }}</a>
        </div>

        <div class="hidden items-center gap-3 md:flex">
            <div class="flex items-center gap-1 border-e border-gray-200 pe-3 text-xs">
                <a href="{{ route('locale.switch', 'es') }}" class="{{ app()->getLocale() === 'es' ? 'font-semibold text-emerald-700' : 'text-gray-400 hover:text-gray-600' }}">ES</a>
                <span class="text-gray-300">/</span>
                <a href="{{ route('locale.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'font-semibold text-emerald-700' : 'text-gray-400 hover:text-gray-600' }}">EN</a>
            </div>
            <a href="{{ route('request') }}" class="btn-primary px-4 py-2" wire:navigate>
                <i class="fa-solid fa-plus text-base"></i>
                {{ __('Request a Purchase') }}
            </a>
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('dashboard') : route('portal.dashboard') }}"
                   class="btn-soft px-4 py-2">
                    {{ auth()->user()->isAdmin() ? __('Admin Panel') : __('My Account') }}
                </a>
            @else
                <a href="{{ route('login') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50/50 hover:text-emerald-700">
                    {{ __('Log in') }}
                </a>
                <a href="{{ route('register') }}" class="rounded-lg border border-emerald-600 px-4 py-2 text-sm font-medium text-emerald-700 transition-colors hover:bg-emerald-50">
                    {{ __('Register') }}
                </a>
            @endauth
        </div>

        <div class="flex items-center gap-4 lg:hidden">
            <div class="flex items-center gap-1 text-xs">
                <a href="{{ route('locale.switch', 'es') }}" class="{{ app()->getLocale() === 'es' ? 'font-semibold text-emerald-700' : 'text-gray-400 hover:text-gray-600' }}">ES</a>
                <span class="text-gray-300">/</span>
                <a href="{{ route('locale.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'font-semibold text-emerald-700' : 'text-gray-400 hover:text-gray-600' }}">EN</a>
            </div>

            <button @click="open = ! open" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100" aria-label="{{ __('Toggle navigation') }}">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
        </div>
    </nav>

    <div x-show="open" x-cloak class="border-t border-gray-100 bg-white px-4 py-4 lg:hidden">
        <div class="flex flex-col gap-3 text-sm font-medium text-gray-600">
            <a href="{{ route('request') }}" class="btn-primary px-4 py-2.5 text-center" @click="open = false" wire:navigate>
                <i class="fa-solid fa-plus text-base"></i>
                {{ __('Request a Purchase') }}
            </a>
            <a href="{{ route('home') }}#how-it-works" @click="open = false">{{ __('How it works') }}</a>
            <a href="{{ route('home') }}#fees" @click="open = false">{{ __('Fees and Pricing') }}</a>
            <a href="{{ route('contact') }}" @click="open = false">{{ __('Contact') }}</a>

            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('dashboard') : route('portal.dashboard') }}"
                   class="btn-primary px-4 py-2 text-center">
                    {{ auth()->user()->isAdmin() ? __('Admin Panel') : __('My Account') }}
                </a>
            @else
                <a href="{{ route('login') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-center text-gray-700">{{ __('Log in') }}</a>
                <a href="{{ route('register') }}" class="btn-primary px-4 py-2 text-center">{{ __('Register') }}</a>
            @endauth
        </div>
    </div>
</header>
