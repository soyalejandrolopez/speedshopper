@php($mailLogoCid = mail_logo_cid($message))
<x-mail::message :logoCid="$mailLogoCid">
# {{ __('Hello') }}!

{{ __('Your email configuration is working perfectly.') }}

<x-mail::panel>
{{ __('This is a test email sent from the settings. If you are reading this, your SMTP configuration is ready to deliver all our communications.') }}
</x-mail::panel>

{{ __('Thanks for trusting us with your orders.') }}<br>
**{{ \App\Models\Setting::get('company_name', config('app.name')) }}**
</x-mail::message>
