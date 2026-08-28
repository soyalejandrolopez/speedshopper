@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
@php
    $styles = [
        'primary' => ['background-color:#059669;', 'box-shadow:0 8px 18px rgba(16,185,129,0.28);'],
        'blue' => ['background-color:#059669;', 'box-shadow:0 8px 18px rgba(16,185,129,0.28);'],
        'success' => ['background-color:#16a34a;', 'box-shadow:0 8px 18px rgba(22,163,74,0.28);'],
        'green' => ['background-color:#16a34a;', 'box-shadow:0 8px 18px rgba(22,163,74,0.28);'],
        'error' => ['background-color:#dc2626;', 'box-shadow:0 8px 18px rgba(220,38,38,0.28);'],
        'red' => ['background-color:#dc2626;', 'box-shadow:0 8px 18px rgba(220,38,38,0.28);'],
    ];
    [$buttonBg, $buttonShadow] = $styles[$color] ?? $styles['primary'];
@endphp
<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<a href="{{ $url }}" class="button button-{{ $color }}" target="_blank" rel="noopener"
   style="display:inline-block; padding:12px 28px; border-radius:999px; background-color:#059669; {{ $buttonBg }} {{ $buttonShadow }} color:#ffffff; font-size:14px; font-weight:700; text-decoration:none; text-align:center;">{!! $slot !!}</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
