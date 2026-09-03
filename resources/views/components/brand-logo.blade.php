@props(['size' => 'md', 'class' => ''])

@php
    $dimensionMap = [
        'sm' => ['maxH' => '32px', 'maxW' => '120px', 'box' => 'h-8 w-8', 'icon' => 'h-5 w-5'],
        'md' => ['maxH' => '44px', 'maxW' => '160px', 'box' => 'h-11 w-11', 'icon' => 'h-6 w-6'],
        'lg' => ['maxH' => '56px', 'maxW' => '220px', 'box' => 'h-14 w-14', 'icon' => 'h-8 w-8'],
        'xl' => ['maxH' => '68px', 'maxW' => '280px', 'box' => 'h-16 w-16', 'icon' => 'h-9 w-9'],
        '2xl' => ['maxH' => '88px', 'maxW' => '360px', 'box' => 'h-22 w-22', 'icon' => 'h-12 w-12'],
    ];
    $cfg = $dimensionMap[$size] ?? $dimensionMap['md'];
    $name = \App\Models\Setting::get('company_name', config('app.name'));
@endphp

@if ($logo = brand_logo_url())
    <div class="flex items-center justify-center shrink-0 overflow-hidden" style="max-height: {{ $cfg['maxH'] }};">
        <img src="{{ $logo }}" alt="{{ $name }}"
             style="max-height: {{ $cfg['maxH'] }}; max-width: {{ $cfg['maxW'] }}; height: auto; width: auto; object-fit: contain;"
             class="shrink-0 rounded-lg {{ $class }}">
    </div>
@else
    <span class="flex {{ $cfg['box'] }} shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-200 {{ $class }}">
        <svg class="{{ $cfg['icon'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
        </svg>
    </span>
@endif
