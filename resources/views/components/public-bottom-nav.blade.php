@php
    $accountUrl = auth()->check()
        ? (auth()->user()->isAdmin() ? route('dashboard') : route('portal.dashboard'))
        : route('login');

    $items = [
        ['label' => __('Home'), 'url' => route('home'), 'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75', 'active' => request()->routeIs('home'), 'navigate' => true],
        ['label' => __('How it works'), 'url' => route('home').'#how-it-works', 'icon' => 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z', 'active' => false],
        ['label' => __('Fees and Pricing'), 'url' => route('home').'#fees', 'icon' => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3zM6 6h.008v.008H6V6z', 'active' => false],
        ['label' => __('Request'), 'url' => route('request'), 'icon' => 'M12 4.5v15m7.5-7.5h-15', 'active' => request()->routeIs('request'), 'navigate' => true],
        ['label' => auth()->check() ? __('My Account') : __('Log in'), 'url' => $accountUrl, 'icon' => 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 006 21h7.5a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9', 'active' => request()->routeIs('login', 'register'), 'navigate' => true],
    ];
@endphp

<x-bottom-nav :items="$items" />
