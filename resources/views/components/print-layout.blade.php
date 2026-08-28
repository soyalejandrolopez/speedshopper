<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $docTitle ?? '' }} — {{ \App\Models\Setting::get('company_name', config('app.name')) }}</title>

        <x-brand-favicon />

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <x-theme-color />

        <style>
            @page {
                margin: 12mm;
            }

            @media print {
                body {
                    background: #fff !important;
                }

                .no-print {
                    display: none !important;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-800 print:bg-white">
        <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 sm:py-10">
            <div class="no-print mb-4 flex items-center justify-between">
                <a href="{{ $backUrl }}" wire:navigate class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700 transition-colors hover:text-emerald-900">
                    <i class="fa-solid fa-arrow-left text-base"></i>
                    {{ __('Back') }}
                </a>
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-700">
                    <i class="fa-solid fa-print text-base"></i>
                    {{ __('Print / Save PDF') }}
                </button>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-lg shadow-gray-200/60 print:rounded-none print:shadow-none">
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 px-8 py-6">
                    <div class="flex items-center gap-3">
                        <span class="shrink-0">
                            <x-brand-logo size="lg" />
                        </span>
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ \App\Models\Setting::get('company_name', config('app.name')) }}</p>
                            <p class="text-xs text-gray-500">{{ __('Personal Shopper in Baytown, TX') }}</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <p class="text-sm font-extrabold uppercase tracking-widest text-emerald-600">{{ $docTitle }}</p>
                        <p class="mt-1 font-mono text-sm font-semibold text-gray-900">{{ $docNumber }}</p>
                        <p class="text-xs text-gray-500">{{ __('Date') }}: {{ now()->format('M d, Y') }}</p>
                    </div>
                </div>

                <div class="px-8 py-6">
                    {{ $slot }}
                </div>

                <div class="border-t border-gray-100 px-8 py-5 text-center text-xs text-gray-400">
                    {{ \App\Models\Setting::get('company_name', config('app.name')) }} — {{ \App\Models\Setting::get('warehouse_address') }}
                    @if ($wa = \App\Models\Setting::get('whatsapp_phone'))
                        · {{ __('WhatsApp') }} {{ $wa }}
                    @endif
                </div>
            </div>
        </div>

        @isset($autoPrint)
            <script>
                window.addEventListener('load', () => setTimeout(() => window.print(), 350));
            </script>
        @endisset
    </body>
</html>
