<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Laravel') }}</title>

        <x-brand-favicon />

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <x-theme-color />
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">
            <aside class="fixed inset-y-0 start-0 z-40 w-64 print:hidden border-e border-white/40 bg-white/80 backdrop-blur-2xl shadow-[4px_0_24px_rgba(0,0,0,0.02)] transition-transform -translate-x-full sm:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'">
                <div class="flex h-16 items-center justify-between px-4">
                    <a href="{{ route('portal.dashboard') }}" class="group flex items-center gap-2.5 font-bold text-gray-900" wire:navigate>
                        <x-brand-logo size="md" class="transition-transform duration-300 group-hover:scale-105" />
                        <span class="text-sm">{{ config('app.name') }}</span>
                    </a>
                    <button @click="sidebarOpen = false" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 sm:hidden">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="space-y-1 p-3">
                    <p class="px-3 pb-1 pt-2 text-[11px] font-bold uppercase tracking-widest text-gray-400">{{ __('My Account') }}</p>

                    <a href="{{ route('portal.dashboard') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('portal.dashboard') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        {{ __('My Account') }}
                    </a>

                    <p class="px-3 pb-1 pt-4 text-[11px] font-bold uppercase tracking-widest text-gray-400">{{ __('My Shipments') }}</p>

                    <a href="{{ route('portal.requests.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('portal.requests.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                        </svg>
                        {{ __('My Requests') }}
                        @php $myOpenRequests = auth()->user()->customer?->purchaseRequests()->whereNotIn('status', ['delivered', 'cancelled'])->count() ?? 0; @endphp
                        @if ($myOpenRequests > 0)
                            <span class="nav-badge {{ request()->routeIs('portal.requests.*') ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700' }}">{{ $myOpenRequests }}</span>
                        @endif
                    </a>

                    <a href="{{ route('portal.packages.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('portal.packages.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                        {{ __('My Packages') }}
                    </a>

                    <a href="{{ route('portal.shipments.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('portal.shipments.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        {{ __('My Shipments') }}
                        @php $myInTransit = auth()->user()->customer?->shipments()->where('status', 'in_transit')->count() ?? 0; @endphp
                        @if ($myInTransit > 0)
                            <span class="nav-badge {{ request()->routeIs('portal.shipments.*') ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-700' }}">{{ $myInTransit }}</span>
                        @endif
                    </a>

                    <a href="{{ route('portal.payments.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('portal.payments.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                        {{ __('My Payments') }}
                    </a>
                </nav>
            </aside>

            <div class="flex flex-1 flex-col sm:ms-64 print:ms-0 print:flex-none">
                <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-white/40 bg-white/60 px-4 backdrop-blur-2xl shadow-sm sm:px-6 print:hidden">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 sm:hidden">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
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

                <main class="flex-1 p-4 pb-24 sm:p-6 md:pb-6 print:p-0">
                    {{ $slot }}
                </main>
            </div>

            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" print:hidden class="fixed inset-0 z-30 bg-gray-900/50 sm:hidden"></div>
        </div>

        @php
            $portalNavItems = [
                ['label' => __('My Account'), 'url' => route('portal.dashboard'), 'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z', 'active' => request()->routeIs('portal.dashboard'), 'navigate' => true],
                ['label' => __('Requests'), 'url' => route('portal.requests.index'), 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z', 'active' => request()->routeIs('portal.requests.*'), 'navigate' => true],
                ['label' => __('Packages'), 'url' => route('portal.packages.index'), 'icon' => 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9', 'active' => request()->routeIs('portal.packages.*'), 'navigate' => true],
                ['label' => __('Shipments'), 'url' => route('portal.shipments.index'), 'icon' => 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12', 'active' => request()->routeIs('portal.shipments.*'), 'navigate' => true],
                ['label' => __('Payments'), 'url' => route('portal.payments.index'), 'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z', 'active' => request()->routeIs('portal.payments.*'), 'navigate' => true],
            ];
        @endphp

        <x-bottom-nav :items="$portalNavItems" />

        <x-toaster />
    </body>
</html>
