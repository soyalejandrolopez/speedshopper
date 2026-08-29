@php($mailLogoCid = mail_logo_cid($message))
<x-mail::message :logoCid="$mailLogoCid">
# {{ __('Hello') }}!

{{ __('Here is a message from our team:') }}

<x-mail::panel>
{!! nl2br(e($body)) !!}
</x-mail::panel>

@if (! empty($attachmentFiles) && count($attachmentFiles) > 0)
<x-mail::subcopy>
📎 **{{ __('Archivos adjuntos') }}:** {{ count($attachmentFiles) }} {{ __('archivo(s) adjunto(s). Revisa los archivos adjuntos en este correo para ver las fotos, videos o documentos enviados.') }}
</x-mail::subcopy>
@endif

{{ __('If you have any questions, just reply to this email and we will be happy to help.') }}<br>
**{{ \App\Models\Setting::get('company_name', config('app.name')) }}**
</x-mail::message>
