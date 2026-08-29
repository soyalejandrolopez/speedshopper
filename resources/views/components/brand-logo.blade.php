@props(['size' => 'md', 'class' => ''])

@php
    $sizes = [
        'sm' => ['box' => 'h-8 w-8', 'icon' => 'h-4.5 w-4.5', 'img' => 'max-h-8 max-w-[120px]'],
        'md' => ['box' => 'h-9 w-9', 'icon' => 'h-5 w-5', 'img' => 'max-h-9 max-w-[140px]'],
        'lg' => ['box' => 'h-11 w-11', 'icon' => 'h-6 w-6', 'img' => 'max-h-12 max-w-[180px]'],
        'xl' => ['box' => 'h-14 w-14', 'icon' => 'h-7 w-7', 'img' => 'max-h-16 max-w-[220px]'],
    ];
    $box = $sizes[$size]['box'] ?? $sizes['md']['box'];
    $icon = $sizes[$size]['icon'] ?? $sizes['md']['icon'];
    $img = $sizes[$size]['img'] ?? $sizes['md']['img'];
    $name = \App\Models\Setting::get('company_name', config('app.name'));
@endphp

@if ($logo = brand_logo_url())
    <img src="{{ $logo }}" alt="{{ $name }}"
         class="{{ $img }} w-auto object-contain {{ $class }}">
@else
    <span class="flex {{ $box }} items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-200 {{ $class }}">
        <svg class="{{ $icon }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
        </svg>
    </span>
@endif
