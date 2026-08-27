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
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    {{ __('Back') }}
                </a>
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
                    </svg>
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
