<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <x-brand-favicon />

        @vite(['resources/css/app.css'])
    </head>
    <body class="font-sans antialiased bg-gray-50 min-h-screen flex items-center justify-center p-4 text-gray-700">
        <div class="max-w-md w-full text-center p-8 bg-white rounded-2xl border border-gray-200 shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 mb-4">
                <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mb-2">@yield('title')</h1>
            <p class="text-sm text-gray-500 mb-6">@yield('message')</p>
            <a href="/" class="btn-primary inline-flex">
                {{ __('Back to Home') }}
            </a>
        </div>
    </body>
</html>
