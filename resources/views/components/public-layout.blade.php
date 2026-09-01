@props(['title' => null])

<x-hamster-ascii />
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title.' | ' : __('Compras en USA y Envíos a LATAM | ') }}{{ \App\Models\Setting::get('company_name', config('app.name')) }}</title>

        <x-brand-favicon />

        <x-seo :title="$title" />

        @if (request()->routeIs('home'))
            <link rel="preload" as="image" href="{{ asset('images/hero-bg-mobile.webp') }}" type="image/webp" media="(max-width: 640px)" fetchpriority="high">
            <link rel="preload" as="image" href="{{ asset('images/hero-bg.webp') }}" type="image/webp" media="(min-width: 641px)" fetchpriority="high">
        @endif

        <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
        <link rel="stylesheet" href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <x-theme-color />
    </head>
    <body class="font-sans antialiased bg-white text-gray-800">
        <x-public-header />

        <main>
            {{ $slot }}
        </main>

        <x-public-footer />

        <div class="h-20 lg:hidden" aria-hidden="true"></div>

        <x-chat-fab />

        <x-public-bottom-nav />

        <x-toaster />
    </body>
</html>
