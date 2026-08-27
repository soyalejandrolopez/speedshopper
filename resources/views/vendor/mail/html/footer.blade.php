<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
{{ Illuminate\Mail\Markdown::parse($slot) }}
<p>{{ \App\Models\Setting::get('company_name', config('app.name')) }} — {{ __('Personal Shopper in Baytown, TX') }}</p>
</td>
</tr>
</table>
</td>
</tr>
