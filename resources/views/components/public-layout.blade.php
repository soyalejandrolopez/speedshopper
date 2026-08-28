@props(['title' => null])

<x-hamster-ascii />
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title.' — ' : '' }}{{ \App\Models\Setting::get('company_name', config('app.name')) }}{{ $title ? '' : ' - ' . __('Compras en USA y Envíos a Latinoamérica') }}</title>

        <x-brand-favicon />

        <x-seo :title="$title" />

        @if (request()->routeIs('home'))
            <link rel="preload" as="image" href="{{ asset('images/hero-bg.jpg') }}" fetchpriority="high">
        @endif

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <x-theme-color />
    </head>
    <body class="font-sans antialiased bg-white text-gray-800">
        <x-public-header />

        <main>
            {{ $slot }}
        </main>

        <x-public-footer />

        <div class="h-20 md:hidden" aria-hidden="true"></div>

        <x-chat-fab />

        <x-public-bottom-nav />

        <x-toaster />
    </body>
</html>
