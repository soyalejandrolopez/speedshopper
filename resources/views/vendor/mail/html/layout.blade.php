<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
}

.footer {
width: 100% !important;
}

.content-cell {
padding-left: 22px !important;
padding-right: 22px !important;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
}
}
</style>
{!! $head ?? '' !!}
</head>
<body style="margin:0; padding:0; background-color:#f0fdf4;">

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" bgcolor="#f0fdf4" style="background-color:#f0fdf4; padding:28px 0;">
<tr>
<td align="center" bgcolor="#f0fdf4">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
{!! $header !!}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" bgcolor="#f0fdf4" style="background-color:#f0fdf4;">
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" bgcolor="#ffffff" style="background-color:#ffffff; border:1px solid #e2e8f0; border-radius:16px; box-shadow:0 12px 32px rgba(2,44,34,0.10), 0 2px 6px rgba(2,44,34,0.05);">
<!-- Accent bar -->
<tr>
<td class="accent-bar" height="6" style="height:6px; line-height:0; font-size:0; background-color:#059669; background-image:linear-gradient(90deg,#059669 0%,#10b981 100%);">&nbsp;</td>
</tr>
<!-- Body content -->
<tr>
<td class="content-cell" style="padding:34px 36px 38px;">
{!! Illuminate\Mail\Markdown::parse($slot) !!}

{!! $subcopy ?? '' !!}
</td>
</tr>
</table>
</td>
</tr>

{!! $footer ?? '' !!}
</table>
</td>
</tr>
</table>
</body>
</html>
