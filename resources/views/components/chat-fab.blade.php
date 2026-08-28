@php
    $waPhone = preg_replace('/\D+/', '', \App\Models\Setting::get('whatsapp_phone', '13462333199'));
    $waMsg = urlencode(__('¡Hola! Quisiera solicitar información sobre sus servicios de compras y envíos.'));
@endphp

<a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}"
   target="_blank"
   rel="noopener noreferrer"
   class="fixed bottom-20 end-4 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-xl shadow-emerald-600/30 transition-all duration-300 hover:scale-110 hover:bg-[#20ba59] hover:shadow-2xl hover:shadow-emerald-600/40 md:bottom-6 md:end-6 group"
   aria-label="{{ __('Chat on WhatsApp') }}"
   title="{{ __('Chat on WhatsApp') }}">
    <span class="absolute -top-1 -end-1 flex h-3.5 w-3.5">
        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white opacity-75"></span>
        <span class="relative inline-flex h-3.5 w-3.5 rounded-full bg-white border-2 border-[#25D366]"></span>
    </span>
    <i class="fa-brands fa-whatsapp text-3xl transition-transform duration-300 group-hover:scale-110"></i>
</a>
