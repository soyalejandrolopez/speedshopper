<x-hamster-ascii />
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' | ' : __('Portal | ') }}{{ \App\Models\Setting::get('company_name', config('app.name')) }}</title>

        <x-brand-favicon />

        <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
        <link rel="stylesheet" href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" media="print" onload="this.media='all'" />
        <noscript>
            <link rel="stylesheet" href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" />
        </noscript>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <x-theme-color />
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">
            <aside class="fixed inset-y-0 start-0 z-40 w-64 print:hidden border-e border-white/40 bg-white/80 backdrop-blur-2xl shadow-[4px_0_24px_rgba(0,0,0,0.02)] transition-transform -translate-x-full sm:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'">
                <div class="flex h-16 items-center justify-between gap-2 px-4 border-b border-gray-100/80">
                    <a href="{{ route('portal.dashboard') }}" class="group flex items-center gap-2.5 font-bold text-gray-900 min-w-0 overflow-hidden" wire:navigate>
                        <x-brand-logo size="md" class="transition-transform duration-300 group-hover:scale-105" />
                        <span class="truncate text-sm font-bold text-gray-900">{{ \App\Models\Setting::get('company_name', config('app.name')) }}</span>
                    </a>
                    <button @click="sidebarOpen = false" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 sm:hidden shrink-0">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <nav class="space-y-1 p-3">
                    <p class="px-3 pb-1 pt-2 text-[11px] font-bold uppercase tracking-widest text-gray-400">{{ __('My Account') }}</p>

                    <a href="{{ route('portal.dashboard') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('portal.dashboard') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-table-cells text-xl w-5 text-center"></i>
                        {{ __('My Account') }}
                    </a>

                    <p class="px-3 pb-1 pt-4 text-[11px] font-bold uppercase tracking-widest text-gray-400">{{ __('My Shipments') }}</p>

                    <a href="{{ route('portal.requests.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('portal.requests.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-clipboard-list text-xl w-5 text-center"></i>
                        {{ __('My Requests') }}
                        @php $myOpenRequests = auth()->user()->customer?->purchaseRequests()->whereNotIn('status', ['delivered', 'cancelled'])->count() ?? 0; @endphp
                        @if ($myOpenRequests > 0)
                            <span class="nav-badge {{ request()->routeIs('portal.requests.*') ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700' }}">{{ $myOpenRequests }}</span>
                        @endif
                    </a>

                    <a href="{{ route('portal.packages.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('portal.packages.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-box text-xl w-5 text-center"></i>
                        {{ __('My Packages') }}
                    </a>

                    <a href="{{ route('portal.shipments.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('portal.shipments.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-truck-fast text-xl w-5 text-center"></i>
                        {{ __('My Shipments') }}
                        @php $myInTransit = auth()->user()->customer?->shipments()->where('status', 'in_transit')->count() ?? 0; @endphp
                        @if ($myInTransit > 0)
                            <span class="nav-badge {{ request()->routeIs('portal.shipments.*') ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-700' }}">{{ $myInTransit }}</span>
                        @endif
                    </a>

                    <a href="{{ route('portal.payments.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('portal.payments.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-credit-card text-xl w-5 text-center"></i>
                        {{ __('Facturación') }}
                    </a>
                </nav>
            </aside>

            <div class="flex flex-1 flex-col sm:ms-64 print:ms-0 print:flex-none">
                <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-white/40 bg-white/60 px-4 backdrop-blur-2xl shadow-sm sm:px-6 print:hidden">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 sm:hidden">
                            <i class="fa-solid fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-lg font-bold tracking-tight text-gray-900">{{ $header ?? '' }}</h1>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1 border-s border-gray-200 ps-3">
                            <a href="{{ route('locale.switch', 'es') }}" class="text-xs {{ app()->getLocale() === 'es' ? 'font-semibold text-emerald-700' : 'text-gray-400 hover:text-gray-600' }}">ES</a>
                            <span class="text-xs text-gray-300">/</span>
                            <a href="{{ route('locale.switch', 'en') }}" class="text-xs {{ app()->getLocale() === 'en' ? 'font-semibold text-emerald-700' : 'text-gray-400 hover:text-gray-600' }}">EN</a>
                        </div>

                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-2 rounded-full p-1.5 transition-colors hover:bg-gray-100">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-sm font-semibold text-white shadow-sm shadow-emerald-200">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                    <span class="hidden text-sm font-medium text-gray-700 lg:block">{{ auth()->user()->name }}</span>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile')" wire:navigate>{{ __('Profile') }}</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </header>

                <main class="flex-1 p-4 pb-24 sm:p-6 md:pb-6 print:p-0 flex flex-col">
                    <div class="flex-1">
                        {{ $slot }}
                    </div>
                    <div class="mt-8 text-center text-xs text-gray-400 print:hidden">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
                        <div class="mt-1">
                            Powered by <a href="https://www.hamstersoftware.com" target="_blank" class="hover:text-emerald-600 transition-colors">Hamster Software</a>
                        </div>
                    </div>
                </main>
            </div>

            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" print:hidden class="fixed inset-0 z-30 bg-gray-900/50 sm:hidden"></div>
        </div>

        @php
            $portalNavItems = [
                ['label' => __('My Account'), 'url' => route('portal.dashboard'), 'icon' => 'fa-solid fa-table-cells', 'active' => request()->routeIs('portal.dashboard'), 'navigate' => true],
                ['label' => __('Requests'), 'url' => route('portal.requests.index'), 'icon' => 'fa-solid fa-clipboard-list', 'active' => request()->routeIs('portal.requests.*'), 'navigate' => true],
                ['label' => __('Packages'), 'url' => route('portal.packages.index'), 'icon' => 'fa-solid fa-box', 'active' => request()->routeIs('portal.packages.*'), 'navigate' => true],
                ['label' => __('Shipments'), 'url' => route('portal.shipments.index'), 'icon' => 'fa-solid fa-truck-fast', 'active' => request()->routeIs('portal.shipments.*'), 'navigate' => true],
                ['label' => __('Facturación'), 'url' => route('portal.payments.index'), 'icon' => 'fa-solid fa-credit-card', 'active' => request()->routeIs('portal.payments.*'), 'navigate' => true],
            ];
        @endphp

        <x-bottom-nav :items="$portalNavItems" />

        <x-toaster />
    </body>
</html>

