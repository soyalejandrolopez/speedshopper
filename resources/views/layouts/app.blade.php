<x-hamster-ascii />
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' | ' : __('Admin | ') }}{{ \App\Models\Setting::get('company_name', config('app.name')) }}</title>

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
            <aside class="fixed inset-y-0 start-0 z-40 flex w-64 flex-col print:hidden border-e border-gray-200/80 bg-white/90 backdrop-blur-2xl shadow-[4px_0_24px_rgba(0,0,0,0.02)] transition-transform -translate-x-full sm:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'">
                <div class="flex h-16 shrink-0 items-center justify-between gap-3 border-b border-gray-100 px-4 bg-white/60">
                    <a href="{{ route('dashboard') }}" class="group flex min-w-0 items-center gap-2.5 overflow-hidden" wire:navigate>
                        <x-brand-logo size="md" class="transition-transform duration-200 group-hover:scale-105" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-gray-900 leading-tight">{{ \App\Models\Setting::get('company_name', config('app.name')) }}</p>
                            <p class="truncate text-[10px] font-semibold uppercase tracking-wider text-emerald-600 leading-none mt-0.5">{{ __('Admin Panel') }}</p>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 sm:hidden shrink-0" aria-label="{{ __('Close') }}">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto p-3">
                    <p class="px-3 pb-1 pt-2 text-[11px] font-bold uppercase tracking-widest text-gray-400">{{ __('Overview') }}</p>

                    <a href="{{ route('dashboard') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-table-cells text-xl w-5 text-center"></i>
                        {{ __('Dashboard') }}
                    </a>

                    <p class="px-3 pb-1 pt-4 text-[11px] font-bold uppercase tracking-widest text-gray-400">{{ __('Operations') }}</p>

                    <a href="{{ route('admin.customers.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-users text-xl w-5 text-center"></i>
                        {{ __('Customers') }}
                    </a>

                    <a href="{{ route('admin.requests.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('admin.requests.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-clipboard-list text-xl w-5 text-center"></i>
                        {{ __('Purchase Requests') }}
                        @php $openRequestsCount = \App\Models\PurchaseRequest::whereNotIn('status', ['delivered', 'cancelled'])->count(); @endphp
                        @if ($openRequestsCount > 0)
                            <span class="nav-badge {{ request()->routeIs('admin.requests.*') ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700' }}">{{ $openRequestsCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.packages.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('admin.packages.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-box text-xl w-5 text-center"></i>
                        {{ __('Packages') }}
                        @php $storedPackagesCount = \App\Models\Package::whereIn('status', ['received', 'storing', 'packing', 'ready'])->count(); @endphp
                        @if ($storedPackagesCount > 0)
                            <span class="nav-badge {{ request()->routeIs('admin.packages.*') ? 'bg-white/20 text-white' : 'bg-sky-100 text-sky-700' }}">{{ $storedPackagesCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.shipments.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('admin.shipments.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-truck-fast text-xl w-5 text-center"></i>
                        {{ __('Shipments') }}
                        @php $inTransitCount = \App\Models\Shipment::where('status', 'in_transit')->count(); @endphp
                        @if ($inTransitCount > 0)
                            <span class="nav-badge {{ request()->routeIs('admin.shipments.*') ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-700' }}">{{ $inTransitCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.payments.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-credit-card text-xl w-5 text-center"></i>
                        {{ __('Payments') }}
                    </a>

                    <a href="{{ route('admin.inquiries.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('admin.inquiries.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-envelope text-xl w-5 text-center"></i>
                        {{ __('Mensajes') }}
                        @php $unreadInquiriesBadge = \App\Models\ContactInquiry::unread()->count(); @endphp
                        @if ($unreadInquiriesBadge > 0)
                            <span class="nav-badge {{ request()->routeIs('admin.inquiries.*') ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700' }}">{{ $unreadInquiriesBadge }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.mail.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('admin.mail.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-paper-plane text-xl w-5 text-center"></i>
                        {{ __('Enviar Correo') }}
                    </a>

                    <p class="px-3 pb-1 pt-4 text-[11px] font-bold uppercase tracking-widest text-gray-400">{{ __('Insights') }}</p>

                    <a href="{{ route('admin.reports.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-chart-simple text-xl w-5 text-center"></i>
                        {{ __('Reports') }}
                    </a>

                    <p class="px-3 pb-1 pt-4 text-[11px] font-bold uppercase tracking-widest text-gray-400">{{ __('Configuration') }}</p>

                    <a href="{{ route('admin.settings.index') }}" wire:navigate
                       class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="fa-solid fa-gear text-xl w-5 text-center"></i>
                        {{ __('Settings') }}
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
                        <a href="{{ route('home') }}" target="_blank" class="hidden items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50/50 hover:text-emerald-700 md:inline-flex">
                            <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                            {{ __('Public Website') }}
                        </a>

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
            $adminNavItems = [
                ['label' => __('Dashboard'), 'url' => route('dashboard'), 'icon' => 'fa-solid fa-table-cells', 'active' => request()->routeIs('dashboard'), 'navigate' => true],
                ['label' => __('Customers'), 'url' => route('admin.customers.index'), 'icon' => 'fa-solid fa-users', 'active' => request()->routeIs('admin.customers.*'), 'navigate' => true],
                ['label' => __('Requests'), 'url' => route('admin.requests.index'), 'icon' => 'fa-solid fa-clipboard-list', 'active' => request()->routeIs('admin.requests.*'), 'navigate' => true],
                ['label' => __('Packages'), 'url' => route('admin.packages.index'), 'icon' => 'fa-solid fa-box', 'active' => request()->routeIs('admin.packages.*'), 'navigate' => true],
                ['label' => __('Shipments'), 'url' => route('admin.shipments.index'), 'icon' => 'fa-solid fa-truck-fast', 'active' => request()->routeIs('admin.shipments.*'), 'navigate' => true],
                ['label' => __('Payments'), 'url' => route('admin.payments.index'), 'icon' => 'fa-solid fa-credit-card', 'active' => request()->routeIs('admin.payments.*'), 'navigate' => true],
                ['label' => __('Reports'), 'url' => route('admin.reports.index'), 'icon' => 'fa-solid fa-chart-simple', 'active' => request()->routeIs('admin.reports.*'), 'navigate' => true],
                ['label' => __('Mail'), 'url' => route('admin.mail.index'), 'icon' => 'fa-solid fa-paper-plane', 'active' => request()->routeIs('admin.mail.*'), 'navigate' => true],
                ['label' => __('Settings'), 'url' => route('admin.settings.index'), 'icon' => 'fa-solid fa-gear', 'active' => request()->routeIs('admin.settings.*'), 'navigate' => true],
            ];
        @endphp

        <x-bottom-nav :items="$adminNavItems" />

        <x-toaster />
    </body>
</html>
