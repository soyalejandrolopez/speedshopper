@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'canonical' => null,
])

@php
    $site = \App\Models\Setting::get('company_name', config('app.name'));
    $siteTitle = $title ? $title.' — '.$site : $site;
    $desc = $description ?? __('Personal shopper in Baytown, TX: we buy, receive and ship your US purchases to Latin America.');
    $image = $image ?? asset('images/hero-bg.jpg');
    $canonical = $canonical ?? url()->current();
@endphp

<meta name="description" content="{{ $desc }}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $site }}">
<meta property="og:title" content="{{ $siteTitle }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $image }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $siteTitle }}">
<meta name="twitter:description" content="{{ $desc }}">
<meta name="twitter:image" content="{{ $image }}">
