@php($mailLogoCid = mail_logo_cid($message))
<x-mail::message :logoCid="$mailLogoCid">
# {{ \App\Models\Setting::get('company_name', config('app.name')) }}

<x-mail::panel>
{!! nl2br(e($body)) !!}
</x-mail::panel>

{{ __('If you have any questions, reply to this email or contact us.') }}
</x-mail::message>
