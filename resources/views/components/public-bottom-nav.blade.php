@php
    $accountUrl = auth()->check()
        ? (auth()->user()->isAdmin() ? route('dashboard') : route('portal.dashboard'))
        : route('login');

    $items = [
        ['label' => __('Home'), 'url' => route('home'), 'icon' => 'fa-solid fa-house', 'active' => request()->routeIs('home'), 'navigate' => true],
        ['label' => __('How it works'), 'url' => route('home').'#how-it-works', 'icon' => 'fa-solid fa-circle-info', 'active' => false],
        ['label' => __('Fees and Pricing'), 'url' => route('home').'#fees', 'icon' => 'fa-solid fa-tags', 'active' => false],
        ['label' => __('Request'), 'url' => route('request'), 'icon' => 'fa-solid fa-plus', 'active' => request()->routeIs('request'), 'navigate' => true],
        ['label' => auth()->check() ? __('My Account') : __('Log in'), 'url' => $accountUrl, 'icon' => 'fa-solid fa-right-to-bracket', 'active' => request()->routeIs('login', 'register'), 'navigate' => true],
    ];
@endphp

<x-bottom-nav :items="$items" />

