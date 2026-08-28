@php($mailLogoCid = mail_logo_cid($message))
<x-mail::message :logoCid="$mailLogoCid">
# {{ __('Hello') }}!

{{ __('Here is a message from our team:') }}

<x-mail::panel>
{!! nl2br(e($body)) !!}
</x-mail::panel>

{{ __('If you have any questions, just reply to this email and we will be happy to help.') }}<br>
**{{ \App\Models\Setting::get('company_name', config('app.name')) }}**
</x-mail::message>
