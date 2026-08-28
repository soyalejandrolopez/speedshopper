<tr>
<td style="padding: 22px 0 36px;">
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="width:570px; margin:0 auto;">
<tr>
<td align="center" style="padding: 24px 36px 0;">
{{ Illuminate\Mail\Markdown::parse($slot) }}
<div style="height:1px; background-color:#e2e8f0; margin: 20px 0 16px;"></div>
<p style="margin:0; font-size:13px; font-weight:700; color:#047857;">{{ \App\Models\Setting::get('company_name', config('app.name')) }}</p>
<p style="margin:4px 0 0; font-size:12px; color:#94a3b8;">{{ __('Personal Shopper in Baytown, TX') }}</p>
<p style="margin:10px 0 0; font-size:11px; color:#cbd5e1;">
    &copy; {{ date('Y') }} {{ \App\Models\Setting::get('company_name', config('app.name')) }}. {{ __('All rights reserved.') }}
</p>
</td>
</tr>
</table>
</td>
</tr>
