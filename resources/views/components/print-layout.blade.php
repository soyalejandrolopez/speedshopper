@props([
    'docTitle' => '',
    'docNumber' => '',
    'backUrl' => '#',
    'autoPrint' => false,
    'qrData' => null,
])

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
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-800 print:bg-white" data-autoprint="{{ !empty($autoPrint) ? 'true' : 'false' }}">
        <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 sm:py-10">
            <div class="no-print mb-4 flex items-center justify-between">
                <a href="{{ $backUrl }}" wire:navigate class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700 transition-colors hover:text-emerald-900">
                    <i class="fa-solid fa-arrow-left text-base"></i>
                    {{ __('Back') }}
                </a>
                <button type="button" data-print-btn
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-700 cursor-pointer">
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

                <div class="border-t border-gray-100 px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500">
                    <div class="text-center sm:text-left">
                        <p class="font-semibold text-gray-800">{{ \App\Models\Setting::get('company_name', config('app.name')) }}</p>
                        <p class="text-gray-400">{{ \App\Models\Setting::get('warehouse_address') }}</p>
                        @if ($wa = \App\Models\Setting::get('whatsapp_phone'))
                            <p class="text-emerald-700 font-medium mt-0.5">WhatsApp: {{ $wa }}</p>
                        @endif
                    </div>
                    @php
                        $cleanWa = preg_replace('/[^0-9]/', '', (string) \App\Models\Setting::get('whatsapp_phone', ''));
                        $qrPayload = $qrData ?: ($cleanWa ? "https://wa.me/{$cleanWa}?text=" . urlencode("Hola, consulta sobre {$docTitle} {$docNumber}") : url()->current());
                    @endphp
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="text-end hidden sm:block">
                            <p class="font-bold text-[10px] uppercase text-emerald-700 tracking-wider">{{ __('Scan to contact / verify') }}</p>
                            <p class="text-[9px] text-gray-400 font-mono">{{ $docNumber }}</p>
                        </div>
                        <x-qr-code :data="$qrPayload" :size="64" />
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
