<x-hamster-ascii />
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' | ' : __('Compras en USA y Envíos a LATAM | ') }}{{ \App\Models\Setting::get('company_name', config('app.name')) }}</title>

        <x-brand-favicon />

        <x-seo />

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <x-theme-color />
    </head>
    <body class="font-sans antialiased flex min-h-screen flex-col bg-gray-50 text-gray-800">
        <x-public-header />

        <div class="relative flex flex-1 flex-col items-center justify-center overflow-hidden px-4 py-12">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 via-white to-teal-50"></div>
                <div class="absolute -start-32 -top-24 h-[30rem] w-[30rem] animate-float-slow rounded-full bg-emerald-300/30 blur-[100px]"></div>
                <div class="absolute -bottom-32 -end-32 h-[30rem] w-[30rem] animate-float-slow rounded-full bg-teal-300/30 blur-[100px]" style="animation-delay: -3s"></div>
                <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(5,150,105,0.03)_1px,transparent_1px),linear-gradient(to_bottom,rgba(5,150,105,0.03)_1px,transparent_1px)] bg-[size:40px_40px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_0%,black,transparent)]"></div>
            </div>

            <div class="relative w-full sm:max-w-md z-10">
                <div class="animate-fade-up glass-panel p-6 sm:p-10">
                    <div class="absolute top-0 inset-x-0 h-1.5 w-full bg-gradient-to-r from-emerald-400 via-teal-500 to-emerald-400 opacity-90"></div>
                    <div class="relative z-10">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>

        <x-public-footer />

        <div class="h-20 md:hidden" aria-hidden="true"></div>

        <x-chat-fab />

        <x-public-bottom-nav />

        <x-toaster />
    </body>
</html>
