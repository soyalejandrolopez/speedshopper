<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' : '' }}{{ \App\Models\Setting::get('company_name', config('app.name')) }}</title>

        <x-brand-favicon />

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <x-theme-color />
    </head>
    <body class="font-sans antialiased flex min-h-screen flex-col bg-gray-50 text-gray-800">
        <x-public-header />

        <div class="relative flex flex-1 flex-col items-center justify-center overflow-hidden px-4 py-12">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute -start-32 -top-24 h-96 w-96 rounded-full bg-emerald-200/50 blur-3xl"></div>
                <div class="absolute -bottom-32 -end-32 h-96 w-96 rounded-full bg-teal-200/50 blur-3xl"></div>
                <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(5,150,105,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(5,150,105,0.04)_1px,transparent_1px)] bg-[size:56px_56px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,black,transparent)]"></div>
            </div>

            <div class="relative w-full sm:max-w-md">
                <div class="animate-fade-up overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 shadow-2xl shadow-emerald-100/60 sm:p-8">
                    <div class="h-1 w-full rounded-t-2xl bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-400"></div>
                    {{ $slot }}
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
