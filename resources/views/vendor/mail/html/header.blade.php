@props(['url', 'logoCid' => null])
<tr>
<td class="header" style="padding: 34px 24px 6px; text-align: center;">
    <a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
        @if ($logoCid)
            <img src="{{ $logoCid }}" alt="{{ \App\Models\Setting::get('company_name', config('app.name')) }}"
                 style="max-height: 64px; max-width: 220px; width: auto; height: auto;">
        @elseif ($logo = brand_logo_data_uri())
            <img src="{{ $logo }}" alt="{{ \App\Models\Setting::get('company_name', config('app.name')) }}"
                 style="max-height: 64px; max-width: 220px; width: auto; height: auto;">
        @elseif ($logo = brand_logo_url())
            <img src="{{ str_starts_with($logo, 'http') ? $logo : url($logo) }}" alt="{{ \App\Models\Setting::get('company_name', config('app.name')) }}"
                 style="max-height: 64px; max-width: 220px; width: auto; height: auto;">
        @else
            <span style="color: #047857; font-size: 22px; font-weight: 800;">{{ \App\Models\Setting::get('company_name', config('app.name')) }}</span>
        @endif
    </a>
    <p style="margin: 10px 0 0; font-size: 11px; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: #94a3b8;">
        {{ __('Personal Shopper · Baytown, TX') }}
    </p>
</td>
</tr>
