@props(['url', 'logoCid' => null])
<tr>
<td class="header">
    <a href="{{ $url }}" style="display: inline-block;">
        @if ($logoCid)
            <img src="{{ $logoCid }}" alt="{{ \App\Models\Setting::get('company_name', config('app.name')) }}"
                 style="max-height: 56px; max-width: 200px; width: auto; height: auto;">
        @elseif ($logo = brand_logo_data_uri())
            <img src="{{ $logo }}" alt="{{ \App\Models\Setting::get('company_name', config('app.name')) }}"
                 style="max-height: 56px; max-width: 200px; width: auto; height: auto;">
        @elseif ($logo = brand_logo_url())
            <img src="{{ str_starts_with($logo, 'http') ? $logo : url($logo) }}" alt="{{ \App\Models\Setting::get('company_name', config('app.name')) }}"
                 style="max-height: 56px; max-width: 200px; width: auto; height: auto;">
        @else
            <span style="color: #047857; font-size: 22px; font-weight: 800;">{{ \App\Models\Setting::get('company_name', config('app.name')) }}</span>
        @endif
    </a>
</td>
</tr>
