@php($mailLogoCid = mail_logo_cid($message))
<x-mail::message :logoCid="$mailLogoCid">
# {{ __('Hello') }}!

{{ __('This is a test email sent from the settings. If you are reading this, your SMTP configuration is working.') }}

{{ __('Thanks for trusting us with your orders.') }}
</x-mail::message>
