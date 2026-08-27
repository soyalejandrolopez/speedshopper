@props(['url'])
<tr>
<td class="header">
    <a href="{{ $url }}" style="display: inline-block;">
        @if ($logo = brand_logo_url())
            <img src="{{ $logo }}" alt="{{ \App\Models\Setting::get('company_name', config('app.name')) }}"
                 style="max-height: 56px; max-width: 200px; width: auto; height: auto;">
        @else
            <span style="color: #047857; font-size: 22px; font-weight: 800;">{{ \App\Models\Setting::get('company_name', config('app.name')) }}</span>
        @endif
    </a>
</td>
</tr>
